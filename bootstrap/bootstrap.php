<?php
require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\SecurityContext;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Http\SessionStorage;
use HeraldryEngine\Application;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @var Application $app
 */
$app = new Application();

// Config
$config = require(__DIR__ . '/../config/config.default.php');

if (file_exists(__DIR__ . '/../config/config.php'))
	$config = array_merge($config, require(__DIR__ . '/../config/config.php'));

$app['config'] = $config;

// Session handler
$app['session_handler'] = new SessionHandler($config['session.dir']);
$app['session'] = new Session(new SessionStorage([], $app['session_handler']));
$app['session']->start();
/** @noinspection PhpUnhandledExceptionInspection */
$app['session_lifetime'] = new DateInterval('PT' . ini_get('session.gc_maxlifetime') . 'S');

$app['clock'] = $app->protect( new \HeraldryEngine\Clock() );
$now = ($app['clock'])();
$app['security'] = new SecurityContext($app['clock'], $app['session_lifetime'], $app['session']);

$isDevMode = true;

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), $isDevMode);
// database configuration parameters
$conn = array(
    'dbname' => $app['config']['db.name'],
    'user' => $app['config']['db.user'],
    'password' => $app['config']['db.pass'],
    'host' => $app['config']['db.host'],
    'driver' => $app['config']['db.driver'],
);

// obtaining the entity manager
try {
    $entityManager = EntityManager::create($conn, $config);
} catch (\Doctrine\ORM\ORMException $e) {
    die("Database error");
}

$app['entity_manager'] = $entityManager;

$app['db'] = new DatabaseContainer($app);

$app['params'] = [];

return $app;
