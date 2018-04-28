<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 01:38
 */

namespace HeraldryEngine\Controllers;


use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DownloadBlazonController
{
    public function SendBlazon(Gpc $gpc, RequestHandlerInterface $handler, Request $req){
        if($gpc->PostHas($req, 'blazon')){
            $blazon = $gpc->Post($req, 'blazon');
            $response = new Response(
                $blazon,
                Response::HTTP_OK,
                array(
                    'content-type' => 'image/svg+xml',
                    'Content-Disposition' => 'attachment; filename=blazon.svg',
                )
            );
        }else{
            $response = $handler->redirect('/');
        }
        return $response;
    }
}