<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 16/04/2018
 * Time: 21:06
 */

namespace HeraldryEngine;
use Exception;
use HeraldryEngine\Mvc\Model;
use HeraldryEngine\SessionHandler;
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
     * Create a new admin panel model.
     *
     * @param Silex\Application $app
     */
    public function __construct($app)
    {
        $this->handler=$app['session_handler'];
        $config=$app['config'];
        try {
            $this->mysqli = new mysqli(
                $config['db.host'],
                $config['db.user'],
                $config['db.pass'],
                $config['db.name']
            );
            $this->mysqli->set_charset("utf8mb4");
        } catch(Exception $e) {
            error_log($e->getMessage());
            exit('Error connecting to database'); //Should be a message a typical user could understand
        }

    }

    /**
     * Prepare the model.
     * @param Silex\Application $app
     */
    public function prepareModel(Silex\Application $app)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM users");
        $stmt->execute();
        $result = $stmt->get_result();
        $users=array();
        $userRows=array();

        while ($row = $result->fetch_assoc()) {
            array_push($userRows,$row);
            $users[(int)$row["ID"]]=$row["userName"];
        }

        $sessions = $app['session_handler']->get_all();

        $app['params'] = array_merge(
            $app['params'],
            [
                'users'=>$users,
                'userRows'=>$userRows,
                'sessions'=>$sessions
            ]
        );
    }


    public function trimLogs(){
        $stmt = $this->mysqli->prepare(
            "DELETE FROM failureLogs WHERE accessTime < (NOW() - INTERVAL 7 DAY);"
        );
        return $stmt->execute();
    }

    public function prepareQuery($query){
        return $this->mysqli->prepare($query);
    }

    public function getLastSqlError(){
        return $this->mysqli->error;
    }
}