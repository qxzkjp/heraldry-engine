<?php
require __DIR__ . '/../vendor/autoload.php';

use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\SecurityContext;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Http\SessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

$app = new Silex\Application();

// Config
$config = require(__DIR__ . '/../config/config.default.php');

if (file_exists(__DIR__ . '/../config/config.php'))
	$config = array_merge($config, require(__DIR__ . '/../config/config.php'));

$app['config'] = $config;

// Session handler
$app['session_handler'] = new SessionHandler($config['session.dir']);
$app['session'] = new Session(new SessionStorage([], $app['session_handler']));
$app['session']->start();
$app['session_lifetime'] = ini_get('session.gc_maxlifetime');

$app['clock'] = $app->protect( function(){return time();} );
$app['security'] = new SecurityContext($app['clock'], $app['session_lifetime'], $app['session']);
$app['db'] = new DatabaseContainer($app);
$app['successMessage'] = $app['errorMessage'] = $app['debugMessage'] = '';
$app['params'] = [];

return $app;
