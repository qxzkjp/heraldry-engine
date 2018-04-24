<?php

use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\Utility\DateUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @var HeraldryEngine\Application $app
 */
$app = require 'bootstrap/bootstrap.php';

$app->before(function(Request $request, Application $app){
    /**
     * @var \HeraldryEngine\Http\Session
     */
    $session = $app['session'];
    $app['unsafe_post'] = $request->request;
    if(!$request->request->has('CSRF') ||
        !$app['session']->has('CSRF') ||
        $request->request->get('CSRF') != $session->get('CSRF')){
        $request->request = new \Symfony\Component\HttpFoundation\ParameterBag();
        $app['CSRF'] = false;
    }else{
        $app['CSRF'] = true;
    }
});

/**
 * This is here because Silex requires the callback to take a request and an application
 * @noinspection PhpUnusedParameterInspection
 * @param Request $request
 * @param Application $app
 * @return \Symfony\Component\HttpFoundation\RedirectResponse
 */
$requireLoggedIn = function (Request $request, Application $app ){
    $app->session->remove("previousPage");
    if($app['security']->getAccessLevel()==ACCESS_LEVEL_NONE){
        $uri = $request->getRequestUri();
        if($uri != "/login")
            $app->session->set("previousPage", $uri);
        return $app->redirect('/login');
    }
    return null;
};

$app->get('/', [HeraldryEngine\MainPage\Controller::class,'Show'])->before($requireLoggedIn);

$app->get('/login', [HeraldryEngine\LogIn\Controller::class,'Show']);

$app->post('/login', [HeraldryEngine\LogIn\Controller::class,'DoLogin']);

$app->post('/logout', function(Application $app){
    if($app['CSRF']){
        $app['session']->clear();
        $app['security'] =  new \HeraldryEngine\SecurityContext($app['clock'], $app['session_lifetime']);
        $app['security']->StoreContext($app['session']);
        return $app->redirect('/login');
    }else{
        return $app->redirect('/');
    }
});

$requireAdmin = function(Request $request, Application $app){
    if($app['security']->getAccessLevel()!=ACCESS_LEVEL_ADMIN){
        $view = new View($app, $request);
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
        $content = $view->render();
        return new Response($content, Response::HTTP_FORBIDDEN);
    }
    return null;
};

/**
 * @var \Silex\ControllerCollection $adminPages
 */
$adminPages = $app['controllers_factory'];
/**
 * This is here because PHPStorm expects "Silex\mixed" instead of mixed. Pretty sure that's a bug.
 * @noinspection PhpParamsInspection
 */
$adminPages->before($requireLoggedIn)->before($requireAdmin);

$adminPages->get('/permissions/view', function(Application $app, Request $request){
    $view = new View($app, $request);
    $view->setTemplate("templates/template.php");
    $view->setParam("content","viewPermissions.php");
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
    $view->setParam("menuList",[]);
    $controller = new \HeraldryEngine\Permissions\DisplayController();
    $controller->listPermissions($app['entity_manager']);
    $app['params'] = array_merge($app['params'], $controller->getParams());
    return $view->render();
});

$app->get('/admin', function(Application $app){
    return $app->redirect('/admin/');
});

$adminPages->get("/", function(Application $app, Request $request){
    $view = new View($app,$request);
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
    $userRepo = $app['entity_manager']->getRepository(User::class);
    $users = $userRepo->findAll();
    $view->setParam("users", $users);
    $view->setParam("sessions", $app['session_handler']->get_all());
    return $view->render();
});

$adminPages->get("/createuser", function(Application $app, Request $request){
    $controller = new \HeraldryEngine\CreateUser\Controller($app, $request);
    return $controller->show();
});

$adminPages->post("/createuser", function(Application $app, Request $request){
    $controller = new \HeraldryEngine\CreateUser\Controller($app, $request);
    if($request->request->has('newUser')) {
        if($request->request->has('newPassword') &&
        $request->request->has('checkPassword')){
            $controller->createUser(
                $request->request->get('newUser'),
                $request->request->has('asAdmin') ? ACCESS_LEVEL_ADMIN : ACCESS_LEVEL_USER,
                $request->request->get('newPassword'),
                $request->request->get('checkPassword')
            );
        }else{
            $app->addParam('errorMessage','Something weird went wrong. User not created.');
        }
    }
    return $controller->show();
})->before($requireAdmin);//TODO: change this to a permission-based check

$app->get('/user/{id}',
    function(Application $app, Request $request, $id) : string {
    /**
     * @var User $user
     * @var User[] $userList
     */
    $view = new View($app, $request);
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
        $user = $app['entity_manager']->getRepository(User::class)->find((int)$id);
        $view->setParam("menuList", []);
    }else if(is_string($id)){
        $userList = $app['entity_manager']->getRepository(User::class)->findBy(['userName'=>$id]);
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

$adminPages->get('/changepassword/{id}', function(Application $app, Request $request, $id){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app, $request);
    $app->addParam('changeID', $id);
    return $controller->show(true);
});

$adminPages->post('/changepassword/{id}', function(Application $app, Request $request, $id){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app, $request);
    $controller->doPasswordChange($id);
    return $controller->show(true);
});

$app->get('/changepassword', function(Application $app, Request $request){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app, $request);
    return $controller->show();
})->before($requireLoggedIn);

$app->post('/changepassword', function(Application $app, Request $request){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app, $request);
    $controller->doPasswordChange($app->security->GetUserID());
    return $controller->show();
})->before($requireLoggedIn);

$adminPages->post('/setaccess/{al}/{id}', function(Application $app, Request $request, $al, $id){
    $controller = new \HeraldryEngine\SetAccess\Controller($app, $request);
    //TODO: permission-based check
    if($app['CSRF']){
        $controller->setAccess($id, $al);
    }
    return $app->redirect('/admin/');
});

$adminPages->post('/deleteuser/{id}', function(Application $app, Request $request, $id){
    $controller = new \HeraldryEngine\DeleteUser\Controller($app, $request);
    //TODO: permission-based check
    if($app['CSRF']){
        $controller->deleteUser($id);
    }
    return $app->redirect('/admin/');
});

$adminPages->post('/collectgarbage',function(Application $app, Request $request){
    if($app['CSRF']) {
        $app['session_handler']->gc(DateUtility::dateIntervalToSeconds($app['session_lifetime']));
        $app->addParam('successMessage', "Garbage collected successfully.");
    }
    return $app->redirect('/admin/');
});

$app->post('/download', function(Application $app, Request $request){
    if($app['CSRF'] && $request->request->has('blazon')){
        $blazon = $request->request->get('blazon');
        $response = new Response(
            $blazon,
            Response::HTTP_OK,
            array(
                'content-type' => 'image/svg+xml',
                'Content-Disposition' => 'attachment; filename=blazon.svg',
            )
        );
    }else{
        $response = $app->redirect('/');
    }
    return $response;
});

$app->mount('/admin', $adminPages);

$app->run();
