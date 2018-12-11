<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 18/04/2018
 * Time: 18:36
 */

namespace HeraldryEngine\Controllers;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\EntityRepository;

use Exception;

use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use HeraldryEngine\Logger;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use UAParser\Parser;

class LoginController
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
     * @param ClockInterface $clock
     * @param EntityRepository $userRepo
     * @param Logger $log
     * @param \DateInterval $session_lifetime
     * @param $uname
     * @param $pword
     * @param null $addr
     * @param null $ua
     * @return SecurityContext
     * @throws Exception
     */
    public function authenticateUser(ClockInterface $clock,
                                     EntityRepository $userRepo,
                                     Logger $log,
                                     $session_lifetime,
                                     $uname,
                                     $pword,
                                     $addr=null,
                                     $ua=null
    ){
        /**
         * @var ClockInterface $clock
         * @var EntityManager $em
         * @var \DateInterval $session_lifetime
         */
        $uname=strtolower($uname);
        $failureCount = $log->getNumFailures($clock, new \DateInterval('PT5M'), $uname);
        if($failureCount < 50){
            $matches = $userRepo->findBy(['userName' => $uname]);
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
                    $log->logFailure($uname, $clock(), $addr);
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
            $securityInfo['userIP'] = $addr;
            if(isset($ua))
                try {
                    $parser = Parser::create();
                    $result = $parser->parse($ua);

                    $securityInfo['OS'] = $result->os->toString();
                    $securityInfo['browser'] = $result->ua->family;
                    //get geolocation data from freegeoip (and drop line break)
                    $securityInfo['geoIP'] =
                        file_get_contents(
                        "http://api.ipstack.com/" . $securityInfo['userIP'] ."?access_key=8e1e3a5b61fa09a93cb09a41e28fbc97"
                        );
                    $sections = json_decode($securityInfo['geoIP'], true);
                    if (!empty($sections["country_code"])) {
                        $securityInfo['countryName'] = $sections["country_code"];
                    }
                    if (!empty($sections["city"])) {
                        $securityInfo['city'] = $sections["city"];
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
     * @param EntityRepository $userRepo
     * @param Session $sesh
     * @param SecurityContext $ctx
     * @param RequestHandlerInterface $handler
     * @param Logger $log
     * @param $session_lifetime
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws Exception
     */
    public function DoLogin(Request $req,
                            Gpc $gpc,
                            ClockInterface $clock,
                            EntityRepository $userRepo,
                            Session $sesh,
                            SecurityContext $ctx,
                            RequestHandlerInterface $handler,
                            Logger $log,
                            $session_lifetime){

        if($gpc->UnsafePostHas($req ,'username') &&
            $gpc->UnsafePostHas($req ,'password')){
            $uname = $gpc->UnsafePost($req, 'username');
            $pword = $gpc->UnsafePost($req, 'password');
            $newCtx = $this->authenticateUser(
                $clock,
                $userRepo,
                $log,
                $session_lifetime,
                $uname,
                $pword,
                $req->server->get('REMOTE_ADDR'),
                $req->server->get('HTTP_USER_AGENT')
            );
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