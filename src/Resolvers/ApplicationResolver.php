<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 17/05/2018
 * Time: 21:45
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ApplicationResolver
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
        if(!(ResolverUtility::TypeCheck($argument, Application::class)))
            return false;
        return $this->app instanceof Application;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app;
    }
}