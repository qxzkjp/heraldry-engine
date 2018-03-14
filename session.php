<?php
	//custom session handler to allow reading sessions
	require "sessionhandler.php";
	$lifetime=3600;
	session_set_cookie_params ( $lifetime, '/' , '.heraldryengine.com' , TRUE );
	session_start();
	setcookie(session_name(),session_id(),time()+$lifetime);
	if(!array_key_exists("startTime",$_SESSION)){
	//date_default_timezone_set('Europe/London');
	//$date=date('m/d/Y h:i:s a');
		$_SESSION["startTime"]=time();
	}
?>