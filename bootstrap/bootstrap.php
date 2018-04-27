<?php
require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\Http\Session;
use HeraldryEngine\SecurityContext;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Application;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\SessionValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
/**
 * @var Application $app
 */
$app = new Application();

// Config
$config = require(__DIR__ . '/../config/config.default.php');

if (file_exists(__DIR__ . '/../config/config.php'))
	$config = array_merge($config, require(__DIR__ . '/../config/config.php'));

$app['config'] = $config;
unset($config);
$app['clock'] = function(){ return new \HeraldryEngine\Clock(); };

//session setup

try {
    $app['session_lifetime'] = new DateInterval('PT' . ini_get('session.gc_maxlifetime') . 'S');
} catch (Exception $e) {
    die("Bad session lifetime!");
}

$app['domain'] = 'heraldryengine.com';
$app['session_handler'] = function (Application $app){
    return new SessionHandler($app['config']['session.dir']);
};
$app['session'] = function(Application $app){
    return new Session(
    $app,
    new NativeSessionStorage([
        'serialize_handler' => 'php_serialize',
        'cookie_httponly' => true,
        'cookie_secure' => true,
        'cookie_domain' => '.'.$app['domain']
    ],
    $app['session_handler']
    )
);
};
//unnecessary, session starts on demand
//$app['session']->start();

$app['gpc'] = function(Application $app){
    return new \HeraldryEngine\Http\Gpc($app);
};

$app['security'] = function(Application $app){
    return new SecurityContext($app['clock'], $app['session_lifetime'], $app['session']);
};

$isDevMode = $app['config']['debug'];

// database stuff
$app['db_config'] = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), $isDevMode);

$app['conn'] = array(
    'dbname' => $app['config']['db.name'],
    'user' => $app['config']['db.user'],
    'password' => $app['config']['db.pass'],
    'host' => $app['config']['db.host'],
    'driver' => $app['config']['db.driver'],
);
$app['entity_manager'] = function (Application $app){
    return EntityManager::create($app['conn'], $app['db_config']);
};

//TODO: remove this
$app['db'] = new DatabaseContainer($app);

$app['params'] = [];

if($app['config']['debug']){
    unset($app['exception_handler']);
}

$app['request_handler'] = function(Application $app){
    return new \HeraldryEngine\Http\RequestHandler($app);
};

$app['controller.main_page'] = function(){
    return new HeraldryEngine\MainPage\Controller();
};
$app['controller.logout'] = function(){
    return new HeraldryEngine\LogOut\Controller();
};
$app['controller.login'] = function(){
    return new HeraldryEngine\LogIn\Controller();
};
$app['controller.admin_panel'] = function(Application $app){
    return new HeraldryEngine\AdminPanel\Controller($app);
};
$app['controller.permissions'] = function(Application $app){
    return new HeraldryEngine\Permissions\DisplayController($app);
};
$app['controller.create_user'] = function(Application $app){
    return new HeraldryEngine\CreateUser\Controller($app);
};
$app['controller.view_user'] = function(Application $app){
    return new HeraldryEngine\ViewUser\ViewUserController();
};

$app['argument_resolver'] = function(Application $app) {
    return new ArgumentResolver( null,  array(
            new RequestAttributeValueResolver(),
            new RequestValueResolver(),
            new SessionValueResolver(),
            new DefaultValueResolver(),
            new VariadicValueResolver(),
            new \HeraldryEngine\Resolvers\GpcResolver($app),
            new \HeraldryEngine\Resolvers\SecurityContextResolver($app),
            new \HeraldryEngine\Resolvers\ClockResolver($app),
            new \HeraldryEngine\Resolvers\EntityManagerResolver($app),
            new \HeraldryEngine\Resolvers\SessionResolver($app),
            new \HeraldryEngine\Resolvers\RequestHandlerResolver($app),
            new \HeraldryEngine\Resolvers\ApplicationParameterResolver($app)
        )
    );
};

$app['kernel'] = function(Application $app){
    return new HttpKernel(
        $app['dispatcher'],
        $app['resolver'],
        null,
        $app['argument_resolver']
    );
};

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

return $app;
