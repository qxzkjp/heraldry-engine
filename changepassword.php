<?php
$app = require 'bootstrap/bootstrap.php';

use HeraldryEngine\AdminPanel\AdminPanelModel;
use HeraldryEngine\AdminPanel\AdminPanelView;
use HeraldryEngine\AdminPanel\AdminPanelController;
use HeraldryEngine\PrivilegeCheck;

/**
 * @var array $config
 * @var SessionHandler $handler
 */
$config = $app['config'];
$handler = $app['session_handler'];

$model = new AdminPanelModel($config, $handler, $app['request']);
$controller = new AdminPanelController($model);
$view = new AdminPanelView($controller,$model);

PrivilegeCheck::requireAuth($controller);

if(array_key_exists("newPassword", $_POST) && array_key_exists("checkPassword", $_POST)){
	if(array_key_exists('ID',$_POST)){
		$id=$_POST['ID'];
	}else{
		$id=$_SESSION['userID'];
	}
	$controller->changeUserPassword(
		$id,
		$_POST['newPassword'],
		$_POST['checkPassword']
		);
}

$view->setTemplate("templates/template.php");
$view->setParam("content","changePasswordContent.php");
$view->setParam("pageName","changepassword.php");
$view->setParam("primaryHead","Change");
$view->setParam("secondaryHead","Password");
$view->setParam("scriptList",[
	"vendor/jquery-3.2.1.min",
	"ui",
	"enable"
	]);
$view->setParam("cssList",[
	[
		"name" => "narrow"
	]
	]);

if($_SESSION["accessLevel"]==0){
	$view->appendParam("menuList",[
		"href" => "admin.php",
		"label" => "Secret admin shit"
	]);
}else{
	$view->appendParam("menuList",[
		"href" => "index.php",
		"label" => "Back to blazonry"
	]);
}
if(array_key_exists('ID', $_POST)){
	$view->setParam("changeID", $_POST['ID']);
}

echo $view->render();
