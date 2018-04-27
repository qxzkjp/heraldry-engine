<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 27/04/2018
 * Time: 19:40
 */

namespace HeraldryEngine\Utility;


use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ResolverUtility
{
    /**
     * @param ArgumentMetadata $argument
     * @param $className
     * @return bool
     */
    public static function TypeCheck(ArgumentMetadata $argument, $className){
        if($className !== $argument->getType() &&
            !is_subclass_of(
                $argument->getType(),
                $className
            )
        )
            return false;
        return true;
    }
}