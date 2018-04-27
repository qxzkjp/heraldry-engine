<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 18:50
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\SecurityContext;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class SecurityContextResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;
    public function __construct(Application $app)
    {
        $this->app =$app;
    }


    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if(!(ResolverUtility::TypeCheck($argument, SecurityContext::class)))
            return false;
        return $this->app['security'] instanceof SecurityContext;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['security'];
    }
}