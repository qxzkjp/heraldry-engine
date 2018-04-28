<?php

use HeraldryEngine\Interfaces\RequestHandlerInterface;

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

$app->get('/admin', function(RequestHandlerInterface $handler){
    return $handler->redirect('/admin/');
});

$adminPages->get("/", 'controller.admin_panel:Show');

$adminPages->get("/createuser", 'controller.create_user:Show');

$adminPages->post("/createuser", 'controller.create_user:Create')
    ->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireAdmin']);//TODO: change this to a permission-based check

$app->get('/user/{id}','controller.view_user:Show');

$adminPages->get('/changepassword/{id}', 'controller.change_password:Show');

$adminPages->post('/changepassword/{id}', 'controller.change_password:DoPasswordChange');

$app->get('/changepassword', 'controller.change_password:Show')
    ->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireLoggedIn']);

$app->post('/changepassword', 'controller.change_password:DoPasswordChange')
    ->before([\HeraldryEngine\Utility\AuthCheck::class,'RequireLoggedIn']);

$adminPages->post('/setaccess/{al}/{id}', 'controller.set_access:SetAccess');

$adminPages->post('/deleteuser/{id}', 'controller.delete_user:DeleteUser');

$adminPages->post('/collectgarbage', 'controller.collect_garbage:CollectGarbage');

$app->post('/download', 'controller.download_blazon:SendBlazon');

$app->mount('/admin', $adminPages);

$app->run();
