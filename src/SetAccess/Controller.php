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
     * @var Application
     */
    private $app;
    /**
     * @var Request
     */
    private $request;
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
     * @param Application $app
     * @param Request $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * @param $id
     * @param $al
     * @return bool
     */
    public function setAccess($id, $al){
        /**
         * @var EntityManager $em
         * @var User $user
         */
        $em = $this->app['entity_manager'];
        $userRepo = $em->getRepository(User::class);
        $user = $userRepo->find($id);
        if(isset($user)){
            $user->setAccessLevel($al);
            try {
                $em->flush();
            } catch (Exception $e) {
                $this->error = $this::ERROR_DATABASE;
                return false;
            }
        }else{
            $this->error = $this::ERROR_USER_NOT_FOUND;
            return false;
        }
        $this->error = $this::ERROR_SUCCESS;
        return true;
    }
}