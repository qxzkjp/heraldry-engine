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
	$blazon="";
	if($controller->checkGUID($_POST['UUID'])){
		if(array_key_exists('blazon', $_POST)){
			$blazon=$_POST['blazon'];
		}
	}
	header("Content-type: image/svg+xml");
	header("Content-Disposition: attachment; filename=blazon.SVG");
	echo $blazon;
