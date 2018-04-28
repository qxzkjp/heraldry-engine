<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 23/04/2018
 * Time: 22:48
 */

namespace HeraldryEngine\DeleteUser;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\User;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    /**
     * @var int
     */
    private $error = 0;
    const ERROR_SUCCESS = 0;
    const ERROR_BAD_USER = 1;
    const ERROR_BAD_DB = 2;
    const ERROR_UNKNOWN = 3;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param EntityManager $em
     * @param int $id
     * @return bool
     */
    public function DeleteUser(EntityManager $em, $id){
        try {
            $user = $em->getReference(User::class, $id);
            $em->remove($user);
        } catch (ORMException $e) {
            $this->error = $this::ERROR_BAD_DB;
            return false;
        }
        $this->error = $this::ERROR_SUCCESS;
        return true;
    }
}