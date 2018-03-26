<?php
namespace HeraldryEngine;

class PrivilegeCheck {
	public static function requireAuth($controller){
		if(!$controller->checkPrivNotLess(1)){
			header('Location: login.php', TRUE, 303);
			exit("tried to redirect");
		}
	}
	public static function requireAdmin($controller){
		PrivilegeCheck::requireAuth($controller);
		if(!$controller->checkPrivNotLess(0)){
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