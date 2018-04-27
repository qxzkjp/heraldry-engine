<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 19:50
 */

namespace HeraldryEngine\Resolvers;


use HeraldryEngine\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ApplicationParameterResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * ApplicationParameterResolver constructor.
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
        return $this->app->offsetExists($argument->getName());
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app[$argument->getName()];
    }

}