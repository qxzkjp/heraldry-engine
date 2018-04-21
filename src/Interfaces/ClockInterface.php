<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 21/04/2018
 * Time: 20:33
 */

namespace HeraldryEngine\Interfaces;

/**
 * Basic interface for clock objects. Supports invocation returning a DateTime object. The returned value should be
 * "the current time". When running unit tests, this might not be the actual current time.
 * Interface ClockInterface
 * @package HeraldryEngine\Interfaces
 */
interface ClockInterface
{
    public function __invoke() : \DateTime;
}
