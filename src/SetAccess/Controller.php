<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 23/04/2018
 * Time: 21:50
 */

namespace HeraldryEngine\SetAccess;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    /**
     * @var int
     */
    private $error;
    const ERROR_SUCCESS = 0;
    const ERROR_USER_NOT_FOUND = 1;
    const ERROR_DATABASE = 2;
    const ERROR_UNKNOWN = 3;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param User|null $user
     * @param $al
     * @return bool
     */
    public function setAccess(User $user = null, $al){
        if(isset($user)){
            $user->setAccessLevel($al);
        }else{
            $this->error = $this::ERROR_USER_NOT_FOUND;
            return false;
        }
        $this->error = $this::ERROR_SUCCESS;
        return true;
    }
}