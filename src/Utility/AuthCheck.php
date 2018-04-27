<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 26/04/2018
 * Time: 19:13
 */

namespace HeraldryEngine\Utility;


use HeraldryEngine\Application;
use HeraldryEngine\Mvc\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCheck
{
    /**
     * This is here because Silex requires the callback to take a request and an application
     * @noinspection PhpUnusedParameterInspection
     * @param Request $request
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public static function RequireLoggedIn(Request $request, Application $app ){
        $app->session->remove("previousPage");
        if($app['security']->getAccessLevel()==ACCESS_LEVEL_NONE){
            $uri = $request->getRequestUri();
            if($uri != "/login")
                $app->session->set("previousPage", $uri);
            return $app->redirect('/login');
        }
        return null;
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return null|Response
     */
    public static function RequireAdmin(Request $request, Application $app){
        if($app['security']->getAccessLevel()!=ACCESS_LEVEL_ADMIN){
            $view = new View();
            $view->setTemplate("templates/template.php");
            $view->setParam("content","forbidden.php");
            $view->setParam("pageName","login");
            $view->setParam("primaryHead","Forbidden");
            $view->setParam("secondaryHead","");
            $view->setParam("scriptList",[
                "ui",
                "enable",
            ]);
            $view->setParam("cssList",[
                [
                    "name" => "narrow"
                ]
            ]);
            $view->setParam("menuList",[]);
            $content = $view->render($request, $app->security, $app->clock, $app->session, $app->params);
            return new Response($content, Response::HTTP_FORBIDDEN);
        }
    return null;
    }
}