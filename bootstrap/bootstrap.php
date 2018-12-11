<?php
require __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use HeraldryEngine\Http\Session;
use HeraldryEngine\SecurityContext;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Application;
use Pimple\Exception\FrozenServiceException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

$app['service_factory'] = function($app){
    return new \HeraldryEngine\ServiceFactory($app);
};

//$app['clock'] = function(){ return new \HeraldryEngine\Clock(); };

$app['service_factory']->register('clock', \HeraldryEngine\Clock::class);

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
            'cookie_secure' => $app['config']['cookie_secure'],
            'cookie_domain' => '.'.$app['config']['domain']
        ],
        $app['session_handler']
        )
    );
};

$app['gpc'] = function(Application $app){
    return new \HeraldryEngine\Http\Gpc($app->security);
};

$app['security'] = function(Application $app){
    return new SecurityContext($app['clock'], $app['session_lifetime'], $app['session']);
};

// database stuff
$app['db_config'] = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/../src"), $app['config']['debug']);

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

$app['params'] = [];

if($app['config']['debug']){
    unset($app['exception_handler']);
}

//$app['request_handler'] = function(Application $app){
//    return new \HeraldryEngine\Http\RequestHandler($app);
//};

$app['service_factory']->register('request_handler', \HeraldryEngine\Http\RequestHandler::class);

$app['entity_logger'] = function(Application $app){
    return new \HeraldryEngine\Logger($app['entity_manager']);
};

$app['controller.main_page'] = function(){
    return new HeraldryEngine\Controllers\MainPageController();
};
$app['controller.logout'] = function(){
    return new HeraldryEngine\Controllers\LogoutController();
};
$app['controller.login'] = function(){
    return new HeraldryEngine\Controllers\LoginController();
};
$app['controller.admin_panel'] = function(){
    return new HeraldryEngine\Controllers\AdminPanelController();
};
$app['controller.permissions'] = function(){
    return new HeraldryEngine\Controllers\PermissionsDisplayController();
};
$app['controller.create_user'] = function(){
    return new HeraldryEngine\Controllers\CreateUserController();
};
$app['controller.view_user'] = function(){
    return new HeraldryEngine\Controllers\ViewUserController();
};
$app['controller.change_password'] = function(){
    return new HeraldryEngine\Controllers\ChangePasswordController();
};
$app['controller.download_blazon'] = function(){
    return new HeraldryEngine\Controllers\DownloadBlazonController();
};
$app['controller.collect_garbage'] = function(){
    return new HeraldryEngine\Controllers\CollectGarbageController();
};
$app['controller.set_access'] = function(){
    return new HeraldryEngine\Controllers\SetAccessController();
};
$app['controller.delete_user'] = function(){
    return new HeraldryEngine\Controllers\DeleteUserController();
};

$app['argument_resolver'] = function(Application $app) {
    return new ArgumentResolver( null,  array(
            new RequestAttributeValueResolver(),
            new RequestValueResolver(),
            new SessionValueResolver(),
            new \HeraldryEngine\Resolvers\ApplicationResolver($app),
            new \HeraldryEngine\Resolvers\GpcResolver($app),
            new \HeraldryEngine\Resolvers\SecurityContextResolver($app),
            new \HeraldryEngine\Resolvers\ClockResolver($app),
            new \HeraldryEngine\Resolvers\EntityManagerResolver($app),
            new \HeraldryEngine\Resolvers\RequestHandlerResolver($app),
            new \HeraldryEngine\Resolvers\SessionHandlerResolver($app),
            new \HeraldryEngine\Resolvers\UserObjectResolver($app),
            new \HeraldryEngine\Resolvers\ApplicationParameterResolver($app),
            new \HeraldryEngine\Resolvers\LoggerResolver($app),
            new \HeraldryEngine\Resolvers\RepositoryResolver($app),
            new DefaultValueResolver(),
            new VariadicValueResolver(),
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

/**
 * This middleware checks if the entity manager has been instantiated and flushes it if so.
 * The noinspection doc is there because the container expects there to be two parameters on the callback.
 */
$app->finish(
    function(
        /** @noinspection PhpUnusedParameterInspection */
        Request $req,
        Response $resp
        ) use ($app) {
        //we check if the entity manager has been  used. What an ugly hack for what should be simple functionality.
        try{
            $app->extend('entity_manager', function($dummy){});
        }catch(FrozenServiceException $e){
            $app['entity_manager']->flush();
        }

        //save the security context into the session
        $app->security->StoreContext($app->session);
});

return $app;
