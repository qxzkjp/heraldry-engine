<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 26/04/2018
 * Time: 19:49
 */

namespace HeraldryEngine\AdminPanel;


use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    /**
     * @var Application
     */
    public $app;

    /**
     * Controller constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function Show(Request $request, SecurityContext $ctx, ClockInterface $clock){
        $view = new View();
        $view->setTemplate("templates/template.php");
        $view->setParam("content","adminpanel.php");
        $view->setParam("pageName","admin.php");
        $view->setParam("primaryHead","Secret Admin Shit");
        $view->setParam("scriptList",[
            "ui",
            "enable",
        ]);
        $view->setParam("cssList",[
            [
                "name" => "wide"
            ],
            [
                "name" => "adminpanel"
            ]]);
        $view->setParam("menuList",[
            [
                "href" => "/",
                "label" => "Back to blazonry"
            ],
            [
                "href" => "/admin/createuser",
                "label" => "Create new user",
            ]
        ]);
        /**
         * @var EntityRepository $userRepo
         */
        $userRepo = $this->app['entity_manager']->getRepository(User::class);
        $users = $userRepo->findAll();
        $view->setParam("users", $users);
        $view->setParam("sessions", $this->app['session_handler']->get_all());
        $view->setParam('userRepo', $this->app['entity_manager']->getRepository(User::class));
        return $view->render($request, $ctx, $clock, $this->app->session, []);
    }
}