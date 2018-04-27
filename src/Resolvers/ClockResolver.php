<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 19:28
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ClockResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * ClockResolver constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        // TODO: Implement supports() method.
        if(!(ResolverUtility::TypeCheck($argument, ClockInterface::class)))
            return false;
        return $this->app['clock'] instanceof ClockInterface;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['clock'];
    }

}