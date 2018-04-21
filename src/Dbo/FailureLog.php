<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 19/04/2018
 * Time: 19:50
 */

namespace HeraldryEngine\Dbo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Exception;
use InvalidArgumentException;
use Silex\Application;


/**
 * @Entity @Table(name="FailureLogs")
 **/
class FailureLog
{
    /**
     * @ID @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $logNum;
    /**
     * @Column(type="string", length=50)
     * @var string
     */
    protected $userName;
    /**
     * @Column(type="datetime")
     * @var \DateTime
     */
    protected $accessTime;
    /**
     * @Column(type="binary", length=16)
     * @var resource
     */
    protected $IP;
    /**
     * @Column(type="boolean")
     * @var bool
     */
    protected $isIPv6;

    /**
     * @var string
     */
    protected $addr;

    /**
     * FailureLog constructor.
     * @param string $userName
     * @param \DateTime $accessTime
     * @param string $IP
     */
    public function __construct($userName, $accessTime, $IP)
    {
        $this->setUserName($userName);
        $this->setAccessTime($accessTime);
        $this->setIP($IP);
    }

    /**
     * @return int
     */
    public function getLogNum()
    {
        return $this->logNum;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    protected function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return \DateTime
     */
    public function getAccessTime()
    {
        return $this->accessTime;
    }

    /**
     * @param \DateTime $accessTime
     */
    protected function setAccessTime($accessTime)
    {
        $this->accessTime = $accessTime;
    }

    /**
     * @return resource
     */
    public function getIP()
    {
        if(!isset($this->addr))
            $this->addr = inet_ntop(stream_get_contents($this->IP));
        return $this->addr;
    }

    /**
     * @param string $IP
     */
    protected function setIP($IP)
    {
        $binAddr = inet_pton($IP);
        $len = strlen($binAddr);
        if($len!=4 && $len!=16)
            throw new InvalidArgumentException();
        if($len == 4)
            $this->isIPv6 = false;
        else
            $this->isIPv6 = true;
        $this->IP = fopen('php://memory','r+');
        fwrite($this->IP, $binAddr);
        rewind($this->IP);
    }

    /**
     * @param Application $app
     */
    public static function trimLogs($app){
        /**
         * @var EntityManager $em
         * @var EntityRepository $logRepo
         * @var QueryBuilder $qb
         * @var \DateTime $now
         * @var Query $query
         */
        $em = $app['entity_manager'];
        $now = ($app['clock'])();
        $logRepo = $em->getRepository('HeraldryEngine\Dbo\FailureLog');
        $qb=$logRepo->createQueryBuilder('l');
        try {
            $qb->delete()->where(
                $qb->expr()->lt('l.accessTime', ':oneWeekAgo')
            )->setParameter('oneWeekAgo', $now->add(new \DateInterval('P7D')));
        } catch (Exception $e) {
            die('database error!');
        }
        $query = $qb->getQuery();
        $query->execute();
    }

}
