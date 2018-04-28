<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 28/04/2018
 * Time: 13:34
 */

namespace HeraldryEngine\Controllers;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class DeleteUserController
{
    /**
     * @var int
     */
    private $error;
    const ERROR_SUCCESS = 0;
    const ERROR_UNKNOWN = 1;
    const ERROR_BAD_USER = 2;
    const ERROR_BAD_DB = 3;
    const ERROR_BAD_CSRF = 4;

    public function DeleteUser(Gpc $gpc, EntityManager $em, RequestHandlerInterface $handler, Request $request, $id)
    {
        //TODO: permission-based check
        if ($gpc->CheckCsrf($request)) {
            try {
                $user = $em->getReference(User::class, $id);
                $em->remove($user);
                $em->flush();
            } catch (ORMException $e) {
                $this->error = $this::ERROR_BAD_DB;
                return false;
            }
            $this->error = $this::ERROR_SUCCESS;
        }else
            $this->error = $this::ERROR_BAD_CSRF;
        return $handler->redirect('/admin/');
    }
}