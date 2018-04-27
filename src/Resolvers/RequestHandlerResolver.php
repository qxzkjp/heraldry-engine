<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 21:03
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use HeraldryEngine\Http\RequestHandler;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestHandlerResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * RequestHandlerResolver constructor.
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
        return ResolverUtility::TypeCheck($argument, RequestHandler::class) &&
            $this->app['request_handler'] instanceof RequestHandler;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator|mixed
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['request_handler'];
    }
}