<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 19:36
 */

namespace HeraldryEngine\Resolvers;


use Doctrine\ORM\EntityManager;
use HeraldryEngine\Application;
use HeraldryEngine\Utility\ResolverUtility;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class EntityManagerResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    public $app;

    /**
     * EntityManagerResolver constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return ResolverUtility::TypeCheck($argument, EntityManager::class) &&
            $this->app['entity_manager'] instanceof EntityManager;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['entity_manager'];
    }
}