<?php
$app = require 'bootstrap/bootstrap.php';

use HeraldryEngine\AdminPanel\AdminPanelModel;
use HeraldryEngine\AdminPanel\AdminPanelView;
use HeraldryEngine\AdminPanel\AdminPanelController;

/**
 * @var array $config
 * @var SessionHandler $handler
 */
$config = $app['config'];
$handler = $app['session_handler'];

$model = new AdminPanelModel($config, $handler);
$controller = new AdminPanelController($model);
$view = new AdminPanelView($controller,$model);

if(array_key_exists('username', $_POST)
	&& array_key_exists('password', $_POST)
){
	$uname = $_POST['username'];
	$pword = $_POST['password'];
	$row = $controller->authenticateUser($uname, $pword);
	if($row !== false){
		$result = $controller->createUserSession($row);
		if($result){
			//redirect to the index page
			header('Location: index.php', TRUE, 303);
			die();
		}
	}
}

$view->setTemplate("templates/template.php");
$view->setParam("content","loginContent.php");
$view->setParam("pageName","login.php");
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
		"name" => "style"
	],
	[
		"name" => "narrow"
	]
	]);
$view->setParam("menuList",[]);

$view->render();