<?php
require "utility/session.php";
require "utility/requireAdmin.php";
require "utility/adminmvc.php";


$model = new AdminPanelModel();
$controller = new AdminPanelController($model);
$view = new AdminPanelView($controller,$model);

if(array_key_exists("action",$_POST)){
	if(array_key_exists('ID',$_POST)){
		if($_POST['action']=="deleteSession"){
			$controller->deleteSession($_POST['ID']);
		}else if($_POST['action']=="deleteUser"){
			$controller->deleteUser($_POST['ID']);
		}else if($_POST['action']=="disableUser"){
			$controller->changeUserAccess($_POST['ID'],2);
		}else if($_POST['action']=="demoteUser"){
			$controller->changeUserAccess($_POST['ID'],1);
		}else if($_POST['action']=="promoteUser"){
			$controller->changeUserAccess($_POST['ID'],0);
		}
	}else if($_POST['action']=="garbageCollect"){
		$controller->collectGarbage();
	}
}

$view->setTemplate("templates/template.php");
$view->setParam("content","adminpanel.php");
$view->setParam("pageName","admin.php");
$view->setParam("primaryHead","Secret Admin Shit");
$view->setParam("scriptList",[
	"jquery-3.2.1.min",
	"ui",
	"enable",
	"post"
	]);
$view->setParam("cssList",[
	[
		"name" => "style"
	],
	[
		"name" => "wide"
	]]);
$view->setParam("menuList",[
	[
		"href" => "index.php",
		"label" => "Back to blazonry"
	],
	[
		"href" => "createuser.php",
		"label" => "Create new user",
	]
]);

$view->render();

?>