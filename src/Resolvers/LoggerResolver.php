<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 29/04/2018
 * Time: 13:53
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\Logger;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class LoggerResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;
    /**
     * GpcResolver constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if(!(ResolverUtility::TypeCheck($argument, Logger::class)))
            return false;
        return $this->app['entity_logger'] instanceof Logger;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['entity_logger'];
    }
}