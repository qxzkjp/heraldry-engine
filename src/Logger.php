<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 29/04/2018
 * Time: 13:04
 */

namespace HeraldryEngine;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use HeraldryEngine\Dbo\FailureLog;
use HeraldryEngine\Interfaces\ClockInterface;

class Logger
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $uname
     * @param \DateTime $now
     * @param string $addr
     */
    public function logFailure($uname, \DateTime $now, $addr){
        $log = new FailureLog($uname, $now, $addr);
        $this->em->persist($log);
    }

    /**
     * @param ClockInterface $clock
     * @param \DateInterval $interval
     * @param string $uname
     * @return int
     */
    public function getNumFailures(ClockInterface $clock, \DateInterval $interval, $uname)
    {
        $now = $clock();
        /**
         * @var EntityRepository $logRepo
         */
        $logRepo = $this->em->getRepository(FailureLog::class);
        if (isset($logRepo)) {
            $qb = $logRepo->createQueryBuilder('l');
            $qb->select('count(l)')
                ->where(
                    $qb->expr()->eq('l.userName', ':un')
                )
                ->andWhere($qb->expr()->gt('l.accessTime', ':fiveMinutesAgo'))
                ->setParameter('un', $uname)
                ->setParameter('fiveMinutesAgo', $now->sub($interval));
            /**
             * @var Query $query
             */
            $query = $qb->getQuery();
            try {
                $failureCount = $query->getSingleScalarResult();
            } catch (NonUniqueResultException $e) {
                //if there's a database error, assume the worst
                $failureCount = PHP_INT_MAX;
            }

        }else{
            //if there's a database error, assume the worst
            $failureCount = PHP_INT_MAX;
        }
        return $failureCount;
    }
}