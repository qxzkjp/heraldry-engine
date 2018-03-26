<?php
namespace HeraldryEngine;

class PrivilegeCheck {
	public static function requireAuth(){
		if(!array_key_exists('accessLevel',$_SESSION)
			|| $_SESSION["accessLevel"] > 2){
			header('Location: login.php', TRUE, 303);
			exit("tried to redirect");
		}
	}
	public static function requireAdmin(){
		PrivilegeCheck::requireAuth();
		if($_SESSION["accessLevel"] != 0 ){
			header('HTTP/1.0 403 Forbidden');
			exit("Fuck off, Chris.");
		}
	}
	public static function logOut(){
		session_unset();
		session_destroy();
		header('Location: login.php', TRUE, 303);
		die();
	}
}