<?php

use HeraldryEngine\AdminPanel\AdminPanelView;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\Mvc\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app = require 'bootstrap/bootstrap.php';

$app->get('/', function(Silex\Application $app, Request $request){
    if($app['security']->getAccessLevel()==ACCESS_LEVEL_NONE){
        return $app->redirect('/login');
    }
    $controller = new Controller($app);
    $view = new View($app, $request);
    $view->setTemplate("templates/template.php");
    $view->setParam("content","blazon.php");
    $view->setParam("primaryHead","Heraldry");
    $view->setParam("secondaryHead","Engine");
    $view->setParam("scriptList",[
        "vendor/path-data-polyfill",
        "vendor/jquery-3.2.1.min",
        "cubic",
        "syntax",
        "svg",
        "ui",
        "blazon",
        "post",
        "enable"
    ]);
    $view->setParam("cssList",[
        [
            "name" => "narrow"
        ],
        [
            "name" => "heraldry-not-shit",
            "id" => "heraldry-css"
        ]]);
    $uuid = $controller->createGUID();
    $view->setParam("menuList",[
        [
            "href" => "readme.md",
            "label" => "What is this?"
        ],
        [
            "href" => "#",
            "label" => "Example blazons",
            "expandable" => "demoBlazons.php"
        ],
        [
            "href" => "#",
            "label" => "Toggle syntax display",
            "id" => "toggleSyntax",
            "toggle" => true
        ],
        [
            "href" => "https://github.com/qxzkjp",
            "label" => "GitHub page"
        ],
        [
            "href" => "#",
            "label" => "Download Blazon",
            "onclick" => "clickPost('download.php',{'UUID':'$uuid', 'blazon':getDownloadBlazon()});"
        ]
    ]);
    if($app['security']->GetAccessLevel()==ACCESS_LEVEL_ADMIN){
        $view->appendParam("menuList",[
            "href" => "admin.php",
            "label" => "Secret admin shit"
        ]);
    }else{
        $view->appendParam("menuList",[
            "href" => "changepassword.php",
            "label" => "Change password"
        ]);
    }
    $view->setParam("demoBlazons", [
        [
            "label" => "<i>Scrope v Grosvenor</i> (arms of Baron Scrope)",
            "blazon" => "Azure, a bend Or"
        ],
        [
            "label" => "Arms of the town of Gerville, France",
            "blazon" => "Argent, on a bend Azure between two phrygian caps Gules three mullets of six points Or"
        ],
        [
            "label" => "Old arms of France",
            "blazon" => "Azure semy of fleurs-de-lys Or"
        ],
        [
            "blazon" => "Per pale Gules and Azure, on a bend sinister between two fleurs-de-lys Or three keys palewise Purpure"
        ],
        [
            "blazon" => "Per pale Azure on a bend between two mullets Or three roundels Vert and Argent three phrygian caps Gules"
        ],
        [
            "blazon" => "Per pale Sable and Or, three roundels counterchanged"
        ]
    ]);

    return $view->render();
});

$app->get('/login', function(Silex\Application $app, Request $request){
    $view = new View($app, $request);
    $view->setTemplate("templates/template.php");
    $view->setParam("content","loginContent.php");
    $view->setParam("pageName","login");
    $view->setParam("primaryHead","Log");
    $view->setParam("secondaryHead","In");
    $view->setParam("scriptList",[
        "vendor/jquery-3.2.1.min",
        "ui",
        "enable",
        "post"
    ]);
    $view->setParam("cssList",[
        [
            "name" => "narrow"
        ]
    ]);
    $view->setParam("menuList",[]);
    $app['db']->prepareModel($app);
    return $view->render();
});

$app->post('/login', function(Silex\Application $app, Request $request){
    $controller = new \HeraldryEngine\LogIn\Controller($app['db'], $request);
    $uname = $request->request->get('username');
    $pword = $request->request->get('password');
    if(isset($uname) && isset($pword)){
        $ctx = $controller->authenticateUser($app['clock'], $app['session_lifetime'], $uname, $pword);
        $app['params'] = array_merge($app['params'], $controller->GetParams());
        if($ctx->GetUserID() != 0){
            $app['security'] = $ctx;
            $ctx->StoreContext($app['session']);
            //redirect to the index page
            return $app->redirect('/');
        }else{
            $subRequest = Request::create('/login', 'GET');
            return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        }
    }else{
        $subRequest = Request::create('/', 'GET');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
});

$app->get('/logout', function(Silex\Application $app){
    $app['session']->clear();
    $app['security'] =  new \HeraldryEngine\SecurityContext($app['clock'], $app['session_lifetime']);
    $app['security']->StoreContext($app['session']);
    return $app->redirect('/login');
});

$app->run();


