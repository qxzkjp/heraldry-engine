<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 29/04/2018
 * Time: 14:07
 */

namespace HeraldryEngine\Resolvers;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\FailureLog;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RepositoryResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;
    const supportedTypes = [
        'userRepo' => User::class,
        'logRepo' => FailureLog::class
    ];
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
        if(!(ResolverUtility::TypeCheck($argument, ObjectRepository::class)))
            return false;
        return array_key_exists($argument->getName(), $this::supportedTypes) &&
            $this->app['entity_manager'] instanceof EntityManager;
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['entity_manager']->getRepository($this::supportedTypes[$argument->getName()]);
    }
}