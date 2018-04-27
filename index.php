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

$app->get('/', 'controller.main_page:Show')->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireLoggedIn']);

$app->get('/login', 'controller.login:Show');

$app->post('/login', 'controller.login:DoLogin');

$app->post('/logout', 'controller.logout:DoLogout');



/**
 * @var \Silex\ControllerCollection $adminPages
 */
$adminPages = $app['controllers_factory'];
/**
 * This is here because PHPStorm expects "Silex\mixed" instead of mixed. Pretty sure that's a bug.
 * @noinspection PhpParamsInspection
 */
$adminPages->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireLoggedIn'])->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireAdmin']);

$adminPages->get('/permissions/view', 'controller.permissions:Show');

$app->get('/admin', function(Application $app){
    return $app->redirect('/admin/');
});

$adminPages->get("/", 'controller.admin_panel:Show');

$adminPages->get("/createuser", function(Application $app){
    $controller = new \HeraldryEngine\CreateUser\Controller($app);
    return $controller->show();
});

$adminPages->post("/createuser", function(Application $app, Request $request){
    $controller = new \HeraldryEngine\CreateUser\Controller($app);
    if($app['gpc']->PostHas($request, 'newUser')) {
        if($app['gpc']->PostHas($request, 'newPassword') &&
            $app['gpc']->PostHas($request, 'checkPassword')){
            $controller->createUser(
                $app['gpc']->Post($request, 'newUser'),
                $app['gpc']->PostHas($request, 'asAdmin') ? ACCESS_LEVEL_ADMIN : ACCESS_LEVEL_USER,
                $app['gpc']->Post($request, 'newPassword'),
                $app['gpc']->Post($request, 'checkPassword')
            );
        }else{
            $app->addParam('errorMessage','Something weird went wrong. User not created.');
        }
    }
    return $controller->show();
})->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireAdmin']);//TODO: change this to a permission-based check

$app->get('/user/{id}',
    function(Application $app, Request $request, $id) : string {
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
    return $view->render($request, $app->security, $app->clock, $app->session. $app->params);
});

$adminPages->get('/changepassword/{id}', function(Application $app, Request $request, $id){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app);
    $app->addParam('changeID', $id);
    return $controller->show(true);
});

$adminPages->post('/changepassword/{id}', function(Application $app, Request $request, $id){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app);
    $controller->doPasswordChange($request, $id);
    return $controller->show(true);
});

$app->get('/changepassword', function(Application $app, Request $request){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app);
    return $controller->show();
})->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireLoggedIn']);

$app->post('/changepassword', function(Application $app, Request $request){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app);
    $controller->doPasswordChange($request, $app->security->GetUserID());
    return $controller->show();
})->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireLoggedIn']);

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
    if($app['gpc']->CheckCsrf($request, $app->session) && $app['gpc']->PostHas($request, 'blazon')){
        $blazon = $app['gpc']->Post($request, 'blazon');
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
