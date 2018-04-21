<?php

use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\Mvc\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

$app = require 'bootstrap/bootstrap.php';

/**
 * @param Request $request
 * @param \Silex\Application $app
 * @return \Symfony\Component\HttpFoundation\RedirectResponse
 */
$requireLoggedIn = function (Request $request, Silex\Application $app ){
    if($app['security']->getAccessLevel()==ACCESS_LEVEL_NONE){
        return $app->redirect('/login');
    }
};

$app->get('/', function(Application $app, Request $request){
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
})->before($requireLoggedIn);

$app->get('/login', function(Application $app, Request $request){
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

$app->post('/login', function(Application $app, Request $request){
    $controller = new \HeraldryEngine\LogIn\Controller($app['entity_manager'], $request);
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

$app->get('/logout', function(Application $app){
    $app['session']->clear();
    $app['security'] =  new \HeraldryEngine\SecurityContext($app['clock'], $app['session_lifetime']);
    $app['security']->StoreContext($app['session']);
    return $app->redirect('/login');
});

$requireAdmin = function(Request $request, Silex\Application $app){
    if($app['security']->getAccessLevel()!=ACCESS_LEVEL_ADMIN){
        $view = new View($app, $request);
        $view->setTemplate("templates/template.php");
        $view->setParam("content","forbidden.php");
        $view->setParam("pageName","login");
        $view->setParam("primaryHead","Forbidden");
        $view->setParam("secondaryHead","");
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
        $content = $view->render();
        return new Response($content, Response::HTTP_FORBIDDEN);
    }
};

/**
 * @var \Silex\ControllerCollection $adminPages
 */
$adminPages = $app['controllers_factory'];
$adminPages->before($requireLoggedIn)->before($requireAdmin);

$app->get('/permissions/view', function(Application $app, Request $request){
    $view = new View($app, $request);
    $view->setTemplate("templates/template.php");
    $view->setParam("content","viewPermissions.php");
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
    //$app['db']->prepareModel($app);
    $controller = new \HeraldryEngine\Permissions\DisplayController();
    $controller->listPermissions($app['entity_manager']);
    $app['params'] = array_merge($app['params'], $controller->getParams());
    return $view->render();
});

$app->get('/user/{id}',
    function(Application $app, Request $request, $id) : string {
    $view = new View($app, $request);
    $view->setTemplate("templates/template.php");
    $view->setParam("content","viewUser.php");
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
    /**
     * @var \Doctrine\ORM\EntityManager $em
     * @var User $user
     */
    $em = $app['entity_manager'];
    if(is_numeric($id)) {
        $user = $em->getRepository(User::class)->find((int)$id);
        $view->setParam("menuList", []);
    }else if(is_string($id)){
        /**
         * @var User[] $userList
         */
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
    return $view->render();
});

$app->run();


