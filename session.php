<?php
	//if a session is already open, do nothing
	if (session_status() == PHP_SESSION_NONE) {
		//custom session handler to allow reading sessions
		require "sessionhandler.php";
		$lifetime=(int)ini_get('session.gc_maxlifetime');
		session_set_cookie_params ( $lifetime, '/' , '.heraldryengine.com' , TRUE );
		session_start();
		if(array_key_exists("expiry",$_SESSION)){
			if($_SESSION["expiry"] < time()){
				require "logout.php";
			}
		}
		$_SESSION["expiry"]=time()+$lifetime;
		setcookie(session_name(),session_id(),$_SESSION["expiry"]);
	}
?>