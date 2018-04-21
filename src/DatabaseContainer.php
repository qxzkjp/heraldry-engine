<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 16/04/2018
 * Time: 21:06
 */

namespace HeraldryEngine;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Exception;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\SessionHandler;
use HeraldryEngine\Mvc\Model;
use mysqli;
use Symfony\Component\HttpFoundation\Request;
use Silex;

class DatabaseContainer
{
    /**
     * @var mysqli
     */
    private $mysqli;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    public $users;

    /**
     * @var array
     */
    public $userRows;

    /**
     * @var array
     */
    public $sessions;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var string
     */
    public $successMessage;

    /**
     * @var string
     */
    public $debugMessage;

    /**
     * @var SessionHandler
     */
    public $handler;

    /**
     * @var callable
     */
    private $clock;

    /**
     * Create a new admin panel model.
     *
     * @param Silex\Application $app
     */
    public function __construct($app)
    {
        $config=$app['config'];
        $this->handler=$app['session_handler'];
        $this->em = $app['entity_manager'];
        $this->clock = $app['clock'];
        $this->mysqli = new mysqli(
            $config['db.host'],
            $config['db.user'],
            $config['db.pass'],
            $config['db.name']
        );
    }

    /**
     * Prepare the model.
     * @param Silex\Application $app
     */
    public function prepareModel(Silex\Application $app)
    {
        $userRepo = $this->em->getRepository('HeraldryEngine\Dbo\User');
        /**
         * @var QueryBuilder $qb
         */
        $qb = $userRepo->createQueryBuilder('u');
        $qb->select()->orderBy('u.id', 'ASC');
        /**
         * @var Query $query
         */
        $query = $qb->getQuery();
        $query->execute();
        $users = $query->getResult();

        $sessions = $app['session_handler']->get_all();

        $userNames = [];
        $userRows = [];
        /**
         * @var User $user
         */
        foreach ($users as $user){
            $userNames[$user->getID()] = $user->getUserName();
            $userRows[$user->getID()] = [
                "id" => $user->getID(),
                "userName" => $user->getUserName(),
                "accessLevel" => $user->getAccessLevel()
            ];
        }

        $app['params'] = array_merge(
            $app['params'],
            [
                'users'=>$userNames,
                'userRows'=>$userRows,
                'sessions'=>$sessions
            ]
        );
    }

    public function prepareQuery($query){
        return $this->mysqli->prepare($query);
    }

    public function getLastSqlError(){
        return $this->mysqli->error;
    }
}