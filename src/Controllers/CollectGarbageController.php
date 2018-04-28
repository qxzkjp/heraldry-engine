<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 01:45
 */

namespace HeraldryEngine\Controllers;


use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use HeraldryEngine\Utility\DateUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class CollectGarbageController
{
    /**
     * @param Gpc $gpc
     * @param Session $sesh
     * @param SessionHandler $shandle
     * @param RequestHandlerInterface $handler
     * @param \DateInterval $session_lifetime
     * @param Request $req
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function CollectGarbage(Gpc $gpc, Session $sesh, SessionHandler $shandle, RequestHandlerInterface $handler, \DateInterval $session_lifetime, Request $req){

        if($gpc->CheckCsrf($req, $sesh)) {
            $shandle->gc(DateUtility::dateIntervalToSeconds($session_lifetime));
            //$app->addParam('successMessage', "Garbage collected successfully.");
        }
        return $handler->redirect('/admin/');
    }

}