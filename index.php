<?php
$app = require 'bootstrap/bootstrap.php';

use HeraldryEngine\Mvc\Model;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\Mvc\Controller;
use HeraldryEngine\PrivilegeCheck;

//the model and controller do nothing here, as it's essentially a static page
$model = new Model($app['request']);
$controller = new Controller($model);
$view = new View($controller,$model);

PrivilegeCheck::requireAuth($controller);

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
if($_SESSION["accessLevel"]==0){
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

echo $view->render();
