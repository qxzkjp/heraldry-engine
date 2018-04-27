<?php

use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\RequestHandler;
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

$app->get('/admin', function(RequestHandler $handler){
    return $handler->redirect('/admin/');
});

$adminPages->get("/", 'controller.admin_panel:Show');

$adminPages->get("/createuser", 'controller.create_user:Show');

$adminPages->post("/createuser", 'controller.create_user:Create')
    ->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireAdmin']);//TODO: change this to a permission-based check

$app->get('/user/{id}','controller.view_user:Show');

$adminPages->get('/changepassword/{id}', function(Application $app, $id){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app);
    $app->addParam('changeID', $id);
    return $controller->show(true);
});

$adminPages->post('/changepassword/{id}', function(Application $app, Request $request, $id){
    $controller = new \HeraldryEngine\ChangePassword\Controller($app);
    $controller->doPasswordChange($request, $id);
    return $controller->show(true);
});

$app->get('/changepassword', function(Application $app){
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

$adminPages->post('/collectgarbage',function(Application $app){
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
