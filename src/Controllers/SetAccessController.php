<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 10:54
 */

namespace HeraldryEngine\Controllers;


use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class SetAccessController
{
    public function SetAccess(Request $req, Gpc $gpc, User $usr = null, RequestHandlerInterface $handler, $al){
        if($gpc->CheckCsrf($req) && isset($usr))
            $usr->setAccessLevel($al);
        return $handler->redirect('/admin/');
    }
}