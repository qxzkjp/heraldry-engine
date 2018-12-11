<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 24/04/2018
 * Time: 20:27
 */

namespace HeraldryEngine\Controllers;


use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class LogoutController
{
    public function __construct()
    {
    }

    public function DoLogout(Request $req,
                             Gpc $gpc,
                             Session $sesh,
                             SecurityContext $ctx,
                             RequestHandlerInterface $handler,
                             ClockInterface $clock,
                             $session_lifetime){
        if($gpc->CheckCsrf($req)){
            $sesh->clear();
            $ctx->clone(new SecurityContext($clock, $session_lifetime));
            $ctx->StoreContext($sesh);
            return $handler->redirect('/login');
        }else{
            return $handler->redirect('/');
        }
    }
}