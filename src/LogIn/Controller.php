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
use HeraldryEngine\Dbo\FailureLog;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use UAParser\Parser;

class Controller
{
    /**
     * @var array
     */
    private $params;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->params = [];
    }

    /**
     * @param Request $request
     * @param ClockInterface $clock
     * @param EntityManager $em
     * @param Gpc $gpc
     * @param \DateInterval $session_lifetime
     * @return SecurityContext
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws Exception
     */
    public function authenticateUser(Request $request, ClockInterface $clock, EntityManager $em, Gpc $gpc, $session_lifetime){
        /**
         * @var ClockInterface $clock
         * @var EntityManager $em
         * @var \DateInterval $session_lifetime
         */
        //we don't need CSRF protection on the login form. at least not yet.
        $uname = $gpc->UnsafePost($request, 'username');
        $pword = $gpc->UnsafePost($request, 'password');
        $uname=strtolower($uname);
        /**
         * @var EntityRepository $logRepo
         */
        $logRepo = $em->getRepository('\HeraldryEngine\Dbo\FailureLog');
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
            $userRepo = $em->getRepository('\HeraldryEngine\Dbo\User');
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
                    $log = new FailureLog($uname, $now, $request->server->get('REMOTE_ADDR'));
                    $em->persist($log);
                    if(!$em-flush()){
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
            $securityInfo['userIP'] = $request->server->get('REMOTE_ADDR');
            try {
                $parser = Parser::create();
                $result = $parser->parse(
                    $request->server->get('HTTP_USER_AGENT')
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
            return new SecurityContext($clock, $session_lifetime, $securityInfo);
        }else{
            return new SecurityContext($clock, $session_lifetime);
        }
    }

    public function GetParams(){
        return $this->params;
    }

    public function Show(Request $req, SecurityContext $ctx, Session $sesh, ClockInterface $clock){
        $view = new View();
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
        return new Response($view->render($req, $ctx, $clock, $sesh, $this->params), Response::HTTP_OK);
    }

    /**
     * @param Request $req
     * @param Gpc $gpc
     * @param ClockInterface $clock
     * @param EntityManager $em
     * @param Session $sesh
     * @param SecurityContext $ctx
     * @param RequestHandlerInterface $handler
     * @param $session_lifetime
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function DoLogin(Request $req,
                            Gpc $gpc,
                            ClockInterface $clock,
                            EntityManager $em,
                            Session $sesh,
                            SecurityContext $ctx,
                            RequestHandlerInterface $handler,
                            $session_lifetime){

        if($gpc->UnsafePostHas($req ,'username') &&
            $gpc->UnsafePostHas($req ,'password')){
            $newCtx = $this->authenticateUser($req, $clock, $em, $gpc, $session_lifetime);
            if($newCtx->GetUserID() != 0){
                $ctx->clone($newCtx);
                $ctx->StoreContext($sesh);
                $uri = "/";
                if($sesh->has("previousPage"))
                    $uri = $sesh->get("previousPage");
                //redirect to the index page
                return $handler->redirect($uri);
            }else{
                return $this->Show($req, $ctx, $sesh, $clock);
            }
        }else{
            return $this->Show($req, $ctx, $sesh, $clock);
        }
    }
}