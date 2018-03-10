<?php
	$lifetime=600;
	session_set_cookie_params ( $lifetime, '/' , '.heraldryengine.com' , TRUE );
	session_start();
	setcookie(session_name(),session_id(),time()+$lifetime);
	session_unset();
	session_destroy();
	header('Location: login.php', TRUE, 303);
?>