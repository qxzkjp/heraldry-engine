<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 22/04/2018
 * Time: 22:48
 */

namespace HeraldryEngine;


use HeraldryEngine\Interfaces\ClockInterface;

class MockClock implements ClockInterface
{
    /**
     * @var int
     */
    private $fakeTime;
    public function __construct($time = null)
    {
        $this->fakeTime = isset($time)?$time:0;
    }
    public function setTime($time){
        $this->fakeTime=$time;
    }
    public function addTime($time){
        $this->fakeTime += $time;
    }
    public function __invoke(): \DateTime
    {
        return new \DateTime('@' . $this->fakeTime);
    }
}