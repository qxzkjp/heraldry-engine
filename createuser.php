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

HeraldryEngine\PrivilegeCheck::requireAdmin($controller);

if(array_key_exists("newUser",$_POST)){
	$username=strtolower($_POST["newUser"]);
	if( array_key_exists("newPassword", $_POST)
			&& array_key_exists("checkPassword", $_POST) ){
			$al=1;
			if(array_key_exists("asAdmin",$_POST)){
				$al=0;
			}
			$controller->createUser(
				$username,
				$_POST['newPassword'],
				$_POST['checkPassword'],
				$al);
	}
}

$view->setTemplate("templates/template.php");
$view->setParam("content","createUserContent.php");
$view->setParam("pageName","createuser.php");
$view->setParam("primaryHead","Create");
$view->setParam("secondaryHead","User");
$view->setParam("scriptList",[
	"vendor/jquery-3.2.1.min",
	"ui",
	"enable"
	]);
$view->setParam("cssList",[
	[
		"name" => "narrow"
	]]);
$view->setParam("menuList",[
	[
		"href" => "admin.php",
		"label" => "Secret admin shit"
	]
]);
$view->setParam("debug",true);
$view->render();