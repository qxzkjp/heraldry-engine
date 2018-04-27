<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 22:28
 */

namespace HeraldryEngine\ViewUser;


use Doctrine\ORM\EntityManager;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ViewUserController
{
    public function Show(Request $req,
                         SecurityContext $ctx,
                         ClockInterface $clock,
                         Session $sesh,
                         EntityManager $em,
                         $id){
        /**
         * @var User $user
         * @var User[] $userList
         */
        $view = new View();
        $view->setTemplate("templates/template.php");
        $view->setParam("content","viewUser.php");
        $view->setParam("pageName","login");
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
        if(is_numeric($id)) {
            $user = $em->getRepository(User::class)->find((int)$id);
            $view->setParam("menuList", []);
        }else if(is_string($id)){
            $userList = $em->getRepository(User::class)->findBy(['userName'=>$id]);
            if(count($userList)==1) {
                $user = $userList[0];
            }
        }
        if(isset($user)) {
            $view->setParam('user.exists', true);
            $view->setParam('user.name', $user->getUserName());
            switch ($user->getAccessLevel()){
                case ACCESS_LEVEL_ADMIN:
                    $view->setParam('user.access', 'Administrator');
                    break;
                case ACCESS_LEVEL_USER:
                    $view->setParam('user.access', 'Standard user');
                    break;
                case ACCESS_LEVEL_NONE:
                    $view->setParam('user.access', 'Blocked');
                    break;
                default:
                    $view->setParam('user.access', 'Unknown');
                    break;
            }
            $view->setParam('user.permissions', $user->getPermissionNames());
        }else{
            $view->setParam('user.exists', false);
        }
        return $view->render($req, $ctx, $clock, $sesh, []);
    }
}