<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 17/05/2018
 * Time: 21:39
 */

namespace HeraldryEngine;


use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory as SymfonyMetadataFactory;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactoryInterface;

class ArgumentMetadataFactory implements ArgumentMetadataFactoryInterface
{
    protected $symfony_factory;
    public function __construct(Application $app){
        $this->symfony_factory = new SymfonyMetadataFactory();
    }

    public function createArgumentMetadata($controller)
    {
        // TODO: Implement createArgumentMetadata() method.
        try{
            return $this->symfony_factory->createArgumentMetadata($controller);
        }catch(\ReflectionException $e){

        }

        $arguments = array();

    }
}