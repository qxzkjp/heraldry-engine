<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 22/04/2018
 * Time: 21:36
 */

namespace HeraldryEngine\CreateUser;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Mvc\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * @param string $name
     * @param int $accessLevel
     * @param string $password
     * @param string|null $checkPassword
     * @return bool
     */
    public function createUser($name, $accessLevel, $password, $checkPassword = null){
        /**
         * @var EntityManager $em
         */
        $em = $this->app['entity_manager'];
        if(strlen($password)<6){
            $this->app->addParam(
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
                $this->app->addParam(
                    'errorMessage',
                    'User not created: A user with that name already exists');
                return false;
            }
        }
        $this->app->addParam(
            'successMessage',
            "User '$name' created");
        return true;
    }

    /**
     * @return string|Response
     */
    public function show(){
        $view = new View($this->app, $this->request);
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
        $view->setParam("debug",$this->app['config']['debug']);
        return $view->render();
    }
}