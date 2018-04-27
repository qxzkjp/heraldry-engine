<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 20:04
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class SessionResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * SessionResolver constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return ResolverUtility::TypeCheck($argument, Session::class) &&
            $this->app['session'] instanceof Session;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['session'];
    }

}