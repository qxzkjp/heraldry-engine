<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 26/04/2018
 * Time: 19:49
 */

namespace HeraldryEngine\AdminPanel;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class Controller
{
    /**
     * Controller constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param Request $request
     * @param SecurityContext $ctx
     * @param ClockInterface $clock
     * @param EntityManager $em
     * @param SessionHandler $shandle
     * @param Session $sesh
     * @return string
     */
    public function Show(
            Request $request,
            SecurityContext $ctx,
            ClockInterface $clock,
            EntityManager $em,
            SessionHandler $shandle,
            Session $sesh){
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
        $userRepo = $em->getRepository(User::class);
        $users = $userRepo->findAll();
        $view->setParam("users", $users);
        $view->setParam("sessions", $shandle->get_all());
        $view->setParam('userRepo', $em->getRepository(User::class));
        return $view->render($request, $ctx, $clock, $sesh, []);
    }
}