<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 10:37
 */

namespace HeraldryEngine\Resolvers;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserObjectResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;
    /**
     * @var EntityRepository
     */
    private $repo;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if($argument->getType() === User::class &&
            $request->attributes->has('id') &&
            $this->app['entity_manager'] instanceof EntityManager)
            return true;
        return false;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        if(!isset($this->repo))
            $this->repo = $this->app['entity_manager']->getRepository(User::class);
        yield $this->repo->find((int)$request->attributes->get('id'));
    }
}