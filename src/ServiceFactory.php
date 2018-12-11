<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 17/05/2018
 * Time: 20:57
 */

namespace HeraldryEngine;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ServiceFactory
{
    protected $app;

    /**
     * ServiceFactory constructor.
     * @param Application $app
     */
    public function __construct(Application $app){
        $this->app = $app;
    }

    /**
     * @param string $name
     * @param string $class_name
     */
    public function register($name, $class_name){
        $this->app[$name] = function($app) use($class_name){
            /** @var ArgumentResolverInterface $argument_resolver */
            $argument_resolver = $app['argument_resolver'];
            try {
                $arguments = $argument_resolver->getArguments(new Request(), [$class_name, '__construct']);
                return new $class_name(...$arguments);
            }catch (\ReflectionException $e){
                return new $class_name();
            }
        };
    }
}