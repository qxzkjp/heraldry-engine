<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 23/04/2018
 * Time: 23:16
 */

namespace HeraldryEngine\Utility;


use DateInterval;
use DateTimeImmutable;

class DateUtility
{
    /**
     * @param DateInterval $dateInterval
     * @return int seconds
     */
    public static function dateIntervalToSeconds($dateInterval)
    {
        try {
            $reference = new DateTimeImmutable;
        } catch (\Exception $e) {
            return -1;
        }
        $endTime = $reference->add($dateInterval);
        return $endTime->getTimestamp() - $reference->getTimestamp();
    }
}