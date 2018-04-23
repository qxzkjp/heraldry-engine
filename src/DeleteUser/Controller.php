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
    private $error = 0;
    const ERROR_SUCCESS = 0;
    const ERROR_BAD_USER = 1;
    const ERROR_BAD_DB = 2;
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
     * @param int $id
     * @return bool
     */
    public function deleteUser($id){
        /**
         * @var EntityManager $em;
         */
        $em = $this->app['entity_manager'];
        try {
            $user = $em->getReference(User::class, $id);
            $em->remove($user);
            $em->flush();
        } catch (ORMException $e) {
            $this->error = $this::ERROR_BAD_DB;
            return false;
        }
        $this->error = $this::ERROR_SUCCESS;
        return true;
    }
}