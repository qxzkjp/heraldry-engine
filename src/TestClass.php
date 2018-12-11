<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 17/05/2018
 * Time: 21:10
 */

namespace HeraldryEngine;


use Doctrine\ORM\EntityManager;
use HeraldryEngine\Interfaces\ClockInterface;

class TestClass
{
    public function __construct(ClockInterface $clock, EntityManager $em )
    {
    }
}