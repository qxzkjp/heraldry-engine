<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 18/04/2018
 * Time: 18:36
 */

namespace HeraldryEngine\LogIn;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Exception;
use HeraldryEngine\Application;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\Dbo\FailureLog;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use UAParser\Parser;

class Controller
{
    /**
     * @var DatabaseContainer
     */
    private $em;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var array
     */
    private $params;

    /**
     * Controller constructor.
     * @param EntityManager $em
     * @param Request $request
     */
    public function __construct(EntityManager $em, Request $request)
    {
        $this->em = $em;
        $this->request = $request;
        $this->params = [];
    }

    /**
     * @param callable $clock
     * @param \DateInterval $lifetime
     * @param string $uname
     * @param string $pword
     * @return SecurityContext
     * @throws Exception
     */
    public function authenticateUser($clock, \DateInterval $lifetime, $uname, $pword){
        $uname=strtolower($uname);
        /**
         * @var EntityRepository $logRepo
         */
        $logRepo = $this->em->getRepository('\HeraldryEngine\Dbo\FailureLog');
        /**
         * @var \Doctrine\ORM\QueryBuilder $qb
         * @var \DateTime $now
         */
        $now=($clock)();
        $qb = $logRepo->createQueryBuilder('l');
        $qb->select('count(l)')
            ->where(
                $qb->expr()->eq('l.userName', ':un')
            )
            ->andWhere($qb->expr()->gt('l.accessTime',':fiveMinutesAgo'))
            ->setParameter('un', 'admin')
            ->setParameter('fiveMinutesAgo',$now->sub(new \DateInterval('PT5M')));
        /**
         * @var Query $query
         */
        $query = $qb->getQuery();
        $failureCount = $query->getSingleScalarResult();
        if($failureCount < 50){
            $userRepo = $this->em->getRepository('\HeraldryEngine\Dbo\User');
            $qb = $userRepo->createQueryBuilder('u');
            $qb->select()
                ->where($qb->expr()->eq('u.userName',':name'))
                ->setParameter('name', $uname);
            $query = $qb->getQuery();
            $matches = $query->getResult();
            $numMatches = count($matches);
            if($numMatches > 1){
                $this->params['debugMessage'] =
                    "Login error: more than one user with the given username!";
            }else if($numMatches > 0){
                /**
                 * @var User $user
                 */
                $user = $matches[0];
                if(!$user->checkPassword($pword)){
                    $this->params['errorMessage'] = "Wrong username or password.";
                    $log = new FailureLog($uname, $now, $this->request->server->get('REMOTE_ADDR'));
                    $this->em->persist($log);
                    if(!$this->em-flush()){
                        $this->params['errorMessage'] .= "<br/>Database error!";
                    };
                    unset($user);
                }
            }
        } else {
            $this->params['errorMessage'] = "You are rate limited. Chillax, bruh.";
        }
        if(isset($user)) {
            $securityInfo = [];
            $securityInfo['userID'] = $user->getID();
            $securityInfo['accessLevel'] = $user->getAccessLevel();
            $securityInfo['userName'] = $user->getUserName();
            $securityInfo['startTime'] = ($clock)();
            $securityInfo['userIP'] = $this->request->server->get('REMOTE_ADDR');
            try {
                $parser = Parser::create();
                $result = $parser->parse(
                    $this->request->server->get('HTTP_USER_AGENT')
                );

                $securityInfo['OS'] = $result->os->toString();
                $securityInfo['browser'] = $result->ua->family;
                //get geolocation data from freegeoip (and drop line break)
                $securityInfo['geoIP'] =
                    substr(file_get_contents(
                        "https://freegeoip.net/csv/" .
                        $securityInfo['userIP']
                    ), 0, -2);
                $sections = explode(",", $securityInfo['geoIP']);
                if ($sections[2] != "") {
                    $securityInfo['countryName'] = $sections[2];
                }
                if ($sections[5] != "") {
                    $securityInfo['city'] = $sections[5];
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
            return new SecurityContext($clock, $lifetime, $securityInfo);
        }else{
            return new SecurityContext($clock, $lifetime);
        }
    }

    public function GetParams(){
        return $this->params;
    }

    public static function Show(Application $app, Request $req){
        $view = new View($app, $req);
        $view->setTemplate("templates/template.php");
        $view->setParam("content","loginContent.php");
        $view->setParam("pageName","/login");
        $view->setParam("primaryHead","Log");
        $view->setParam("secondaryHead","In");
        $view->setParam("scriptList",[
            "ui",
            "enable",
        ]);
        $view->setParam("cssList",[
            [
                "name" => "narrow"
            ]
        ]);
        $view->setParam("menuList",[]);
        return new Response($view->render(), Response::HTTP_OK);
    }

    /**
     * @param Application $app
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws Exception
     */
    public static function DoLogin(Application $app, Request $req){
        $controller = new Controller($app['entity_manager'], $req);
        //we don't need CSRF protection on the login form. at least not yet.
        $uname = $app['unsafe_post']->get('username');
        $pword = $app['unsafe_post']->get('password');
        if(isset($uname) && isset($pword)){
            $ctx = $controller->authenticateUser($app['clock'], $app['session_lifetime'], $uname, $pword);
            $app['params'] = array_merge($app['params'], $controller->GetParams());
            if($ctx->GetUserID() != 0){
                $app['security'] = $ctx;
                $app['security']->StoreContext($app['session']);
                $uri = "/";
                if($app->session->has("previousPage"))
                    $uri = $app->session->get("previousPage");
                //redirect to the index page
                return $app->redirect($uri);
            }else{
                $subRequest = Request::create('/login', 'GET');
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }
        }else{
            $subRequest = Request::create('/login', 'GET');
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }
    }
}