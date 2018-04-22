<?php
require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\Http\Session;
use HeraldryEngine\SecurityContext;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Application;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

/**
 * @var Application $app
 */
$app = new Application();

// Config
$config = require(__DIR__ . '/../config/config.default.php');

if (file_exists(__DIR__ . '/../config/config.php'))
	$config = array_merge($config, require(__DIR__ . '/../config/config.php'));

$app['config'] = $config;
$app['clock'] = $app->protect( new \HeraldryEngine\Clock() );

//session setup

try {
    $app['session_lifetime'] = new DateInterval('PT' . ini_get('session.gc_maxlifetime') . 'S');
} catch (Exception $e) {
    die("Bad session lifetime!");
}
$app['session_handler'] = new SessionHandler($config['session.dir']);
$app['session'] = new Session(
    $app,
    new NativeSessionStorage([
        'serialize_handler' => 'php_serialize',
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_domain' => '.heraldryengine.com'
    ],
    $app['session_handler']
    )
);
$app['session']->start();

$app['security'] = new SecurityContext($app['clock'], $app['session_lifetime'], $app['session']);

$isDevMode = $app['config']['debug'];

// database stuff
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), $isDevMode);
$conn = array(
    'dbname' => $app['config']['db.name'],
    'user' => $app['config']['db.user'],
    'password' => $app['config']['db.pass'],
    'host' => $app['config']['db.host'],
    'driver' => $app['config']['db.driver'],
);
try {
    $app['entity_manager'] = EntityManager::create($conn, $config);
} catch (\Doctrine\ORM\ORMException $e) {
    die("Database error");
}
unset($config);
unset($conn);
//TODO: remove this
$app['db'] = new DatabaseContainer($app);

$app['params'] = [];

if($app['config']['debug']){
    unset($app['exception_handler']);
}

return $app;
