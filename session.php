<?php
	//if a session is already open, do nothing
	if (session_status() == PHP_SESSION_NONE) {
		//custom session handler to allow reading sessions
		require "sessionhandler.php";
		require "useragent.php";
		$lifetime=3600;
		session_set_cookie_params ( $lifetime, '/' , '.heraldryengine.com' , TRUE );
		session_start();
		if(array_key_exists("expiry",$_SESSION)){
			if($_SESSION["expiry"] < time()){
				require "logout.php";
			}
		}
		$_SESSION["expiry"]=time()+$lifetime;
		setcookie(session_name(),session_id(),$_SESSION["expiry"]);
		if(!array_key_exists("startTime",$_SESSION)){
			$_SESSION["startTime"]=time();
		}
		if(!array_key_exists("userIP",$_SESSION)){
			$_SESSION["userIP"]=$_SERVER["REMOTE_ADDR"];
		}
		if(!array_key_exists("OS",$_SESSION)){
			$_SESSION["OS"]=getOS();
		}
		if(!array_key_exists("browser",$_SESSION)){
			$_SESSION["browser"]=getBrowser();
		}
	}
?>