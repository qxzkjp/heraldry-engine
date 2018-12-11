<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 22/04/2018
 * Time: 21:36
 */

namespace HeraldryEngine\Controllers;


use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CreateUserController
{
    /**
     * @var array
     */
    protected $params;

    public function __construct()
    {
        $this->params = [];
    }

    /**
     * @param EntityManager $em
     * @param string $name
     * @param int $accessLevel
     * @param string $password
     * @param string|null $checkPassword
     * @return bool
     */
    public function createUser(EntityManager $em, $name, $accessLevel, $password, $checkPassword = null){
        if(strlen($password)<6){
            $this->addParam(
                'errorMessage',
                'User not created: password too weak');
            return false;
        }
        if(!isset($checkPassword) || $password == $checkPassword) {
            $user = new User($name, $password, $accessLevel);
            try {
                $em->persist($user);
                $em->flush();
            } catch (ORMException $e) {
                $this->addParam(
                    'errorMessage',
                    'User not created: A user with that name already exists');
                return false;
            }
            /** @noinspection PhpRedundantCatchClauseInspection
             *
             * This exception is in fact thrown, there must be a bug in the docs
             *
             */ catch (DriverException $e){
                $this->addParam(
                    'errorMessage',
                    'User not created: database error');
                return false;
            }
        }
        $this->addParam(
            'successMessage',
            "User '$name' created");
        return true;
    }

    /**
     * @param SecurityContext $ctx
     * @param ClockInterface $clock
     * @param Session $sesh
     * @param Request $request
     * @return string|Response
     */
    public function Show(SecurityContext $ctx, ClockInterface $clock, Session $sesh, Request $request){
        $view = new View();
        $view->setTemplate("templates/template.php");
        $view->setParam("content","createUserContent.php");
        $view->setParam("pageName","/createuser");
        $view->setParam("primaryHead","Create");
        $view->setParam("secondaryHead","User");
        $view->setParam("scriptList",[
            "ui",
            "enable"
        ]);
        $view->setParam("cssList",[
            [
                "name" => "narrow"
            ]]);
        $view->setParam("menuList",[
            [
                "href" => "/admin",
                "label" => "Secret admin shit"
            ]
        ]);
        return $view->render($request, $ctx, $clock, $sesh, $this->params);
    }

    public function Create( Gpc $gpc,
                            SecurityContext $ctx,
                            ClockInterface $clock,
                            Session $sesh,
                            EntityManager $em,
                            Request $req){
        if($gpc->PostHas($req, 'newUser')) {
            if($gpc->PostHas($req, 'newPassword') &&
                $gpc->PostHas($req, 'checkPassword')){
                $this->createUser(
                    $em,
                    $gpc->Post($req, 'newUser'),
                    $gpc->PostHas($req, 'asAdmin') ? ACCESS_LEVEL_ADMIN : ACCESS_LEVEL_USER,
                    $gpc->Post($req, 'newPassword'),
                    $gpc->Post($req, 'checkPassword')
                );
            }else{
                $this->addParam('errorMessage','Something weird went wrong. User not created.');
            }
        }
        return $this->Show($ctx, $clock, $sesh, $req);
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addParam($key, $value){
        $this->params[$key] = $value;
    }
}