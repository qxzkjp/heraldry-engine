<?php
require __DIR__ . '/../vendor/autoload.php';

use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\Http\SessionContainer;
use HeraldryEngine\SecurityContext;

$app = new Silex\Application();

// Config
$config = require(__DIR__ . '/../config/config.default.php');

if (file_exists(__DIR__ . '/../config/config.php'))
	$config = array_merge($config, require(__DIR__ . '/../config/config.php'));

$app['config'] = $config;

// Session handler
$app['session_handler'] = new \HeraldryEngine\SessionHandler($config['session.dir']);

$app['session'] = SessionContainer::createFromSuperGlobal($app);
$app['security'] = new SecurityContext($_SESSION);
$app['db'] = new DatabaseContainer($app);
$app['successMessage'] = $app['errorMessage'] = $app['debugMessage'] = '';
$app['params'] = [];

return $app;
