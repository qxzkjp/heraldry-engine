<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 21/04/2018
 * Time: 20:35
 */

namespace HeraldryEngine;


use HeraldryEngine\Interfaces\ClockInterface;

class Clock implements ClockInterface
{
    public function __invoke(): \DateTime
    {
        return new \DateTime();
    }
}
