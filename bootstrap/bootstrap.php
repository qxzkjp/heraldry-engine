<?php
require __DIR__ . '/../vendor/autoload.php';

use HeraldryEngine\Http\Request;

$app = [];

// Config
$config = require(__DIR__ . '/../config/config.default.php');

if (file_exists(__DIR__ . '/../config/config.php'))
	$config = array_merge($config, require(__DIR__ . '/../config/config.php'));

$app['config'] = $config;

// Session handler
$app['session_handler'] = new \HeraldryEngine\SessionHandler($config['session.dir']);

// if a session isn't open, set one up
if (session_status() === PHP_SESSION_NONE) {
	ini_set("session.serialize_handler", "php_serialize");
	session_set_save_handler($app['session_handler'], true);

	//custom session handler to allow reading sessions
	$lifetime=(int)ini_get('session.gc_maxlifetime');
	session_set_cookie_params($lifetime, '/', '.heraldryengine.com', true);
	session_start();
	if (array_key_exists("expiry",$_SESSION)) {
		if ($_SESSION["expiry"] < time()) {
			\HeraldryEngine\PrivilegeCheck::logOut();
		}
	}
	$_SESSION["expiry"]=time() + $lifetime;
	setcookie(session_name(), session_id(), $_SESSION["expiry"]);
}

//put this here so that any changes to the cookies are registered
$app['request'] = Request::createFromSuperGlobals();

return $app;
