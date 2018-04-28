<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 01:47
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class SessionHandlerResolver implements ArgumentValueResolverInterface
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
     * This specifically looks for our SessionHandler object because we need to guarantee the
     * get_all method exists.
     * TODO: Create an interface with the get_all method and switch this to checking for it
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if(!(ResolverUtility::TypeCheck($argument, SessionHandler::class)))
            return false;
        return $this->app['session_handler'] instanceof SessionHandler;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['session_handler'];
    }
}