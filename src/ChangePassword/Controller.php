<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 22/04/2018
 * Time: 19:37
 */

namespace HeraldryEngine\ChangePassword;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Mvc\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    /**
     * @var \HeraldryEngine\Application
     */
    protected $app;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var int
     */
    protected $errorCode;
    const ERROR_SUCCESS = 0;
    const ERROR_BAD_PASSWORD = 1;
    const ERROR_BAD_USER = 2;
    const ERROR_UNKNOWN = 3;

    /**
     * Controller constructor.
     * @param \HeraldryEngine\Application $app
     * @param Request $request
     */
    public function __construct(\HeraldryEngine\Application $app, Request $request){
        $this->app=$app;
        $this->request=$request;
    }

    /**
     * @param int $id
     * @param string $newPassword
     * @param string $checkPassword
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeUserPassword(int $id, string $newPassword, string $checkPassword) : bool {
        /**
         * @var EntityManager $em
         * @var EntityRepository $userRepo
         * @var User $user
         */
        if($newPassword != $checkPassword){
            $this->errorCode = $this::ERROR_BAD_PASSWORD;
            return false;
        }
        $em = $this->app['entity_manager'];
        $userRepo = $em->getRepository(User::class);
        $user = $userRepo->find($id);
        if(isset($user)){
            $user->setPassword($newPassword);
        }else{
            $this->errorCode = $this::ERROR_BAD_USER;
            return false;
        }
        $em->flush();
        $this->errorCode = $this::ERROR_SUCCESS;
        return true;
    }

    /**
     * @return int
     */
    public function getLastError() : int
    {
        return $this->errorCode;
    }

    /**
     * @return string | Response
     */
    public function show() {
        $view = new View($this->app, $this->request);
        $view->setTemplate("templates/template.php");
        $view->setParam("content","changePasswordContent.php");
        $view->setParam("pageName","/changepassword");
        $view->setParam("primaryHead","Change");
        $view->setParam("secondaryHead","Password");
        $view->setParam("scriptList",[
            "vendor/jquery-3.2.1.min",
            "ui",
            "enable"
        ]);
        $view->setParam("cssList",[
            [
                "name" => "narrow"
            ]
        ]);

        if($this->app['security']->GetAccessLevel() == ACCESS_LEVEL_ADMIN){
            $view->appendParam("menuList",[
                "href" => "admin.php",
                "label" => "Secret admin shit"
            ]);
        }else{
            $view->appendParam("menuList",[
                "href" => "index.php",
                "label" => "Back to blazonry"
            ]);
        }
        if( $this->request->request->has('ID')){
            $view->setParam("changeID", $this->request->request->get('ID'));
        }
        return $view->render();
    }
}