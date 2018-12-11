<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 22/04/2018
 * Time: 19:37
 */

namespace HeraldryEngine\Controllers;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Dbo\User;
use HeraldryEngine\Http\Gpc;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ChangePasswordController
{
    /**
     * @var int
     */
    protected $errorCode;
    /**
     * @var int
     */
    protected $id = 0;
    /**
     * @var array
     */
    protected $params;
    const ERROR_SUCCESS = 0;
    const ERROR_BAD_PASSWORD = 1;
    const ERROR_BAD_USER = 2;
    const ERROR_UNKNOWN = 3;

    /**
     * Controller constructor.
     */
    public function __construct(){
        $this->params = [];
    }

    /**
     * @param EntityManager $em
     * @param int $id
     * @param string $newPassword
     * @param string $checkPassword
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeUserPassword(EntityManager $em, int $id, string $newPassword, string $checkPassword) : bool {
        /**
         * @var EntityManager $em
         * @var EntityRepository $userRepo
         * @var User $user
         */
        $this->id = $id;
        if($newPassword != $checkPassword){
            $this->errorCode = $this::ERROR_BAD_PASSWORD;
            return false;
        }
        $userRepo = $em->getRepository(User::class);
        $user = $userRepo->find($id);
        if(isset($user)){
            $user->setPassword($newPassword);
        }else{
            $this->errorCode = $this::ERROR_BAD_USER;
            return false;
        }
        $em->flush();
        $this->errorCode = $this::ERROR_SUCCESS;
        return true;
    }

    /**
     * @return int
     */
    public function getLastError() : int
    {
        return $this->errorCode;
    }

    /**
     * @param Request $request
     * @param SecurityContext $ctx
     * @param ClockInterface $clock
     * @param Session $sesh
     * @param int|null $id
     * @return string | Response
     */
    public function show(Request $request, SecurityContext $ctx, ClockInterface $clock, Session $sesh, $id = null) {
        $view = new View();
        $view->setTemplate("templates/template.php");
        $view->setParam("content","changePasswordContent.php");
        $view->setParam("pageName","/changepassword");
        $view->setParam("primaryHead","Change");
        $view->setParam("secondaryHead","Password");
        $view->setParam("scriptList",[
            "ui",
            "enable"
        ]);
        $view->setParam("cssList",[
            [
                "name" => "narrow"
            ]
        ]);

        if($ctx->GetAccessLevel() == ACCESS_LEVEL_ADMIN){
            $view->appendParam("menuList",[
                "href" => "/admin",
                "label" => "Secret admin shit"
            ]);
        }else{
            $view->appendParam("menuList",[
                "href" => "/",
                "label" => "Back to blazonry"
            ]);
        }
        if( null !== $id ){
            $view->setParam("changeID", $id);
        }
        return $view->render($request, $ctx, $clock, $sesh, $this->params);
    }

    /**
     * @param Request $req
     * @param Gpc $gpc
     * @param EntityManager $em
     * @param SecurityContext $ctx
     * @param ClockInterface $clock
     * @param Session $sesh
     * @param int|null $id
     * @return string|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function DoPasswordChange(Request $req,
                                     Gpc $gpc,
                                     EntityManager $em,
                                     SecurityContext $ctx,
                                     ClockInterface $clock,
                                     Session $sesh,
                                     int $id = null){
        if($gpc->PostHas($req, 'newPassword') && $gpc->postHas($req, 'checkPassword')){
            //TODO: permission-based check
            if(null !== $id)
                $this->params['changeID'] = $id;
            $success = $this->changeUserPassword(
                $em,
                isset($id)?$id:$ctx->GetUserID(),
                $gpc->Post($req, 'newPassword'),
                $gpc->Post($req, 'checkPassword')
            );
            if($success){
                $this->params['successMessage'] = 'Password changed';
            }else{
                $this->params['errorMessage'] = 'Password not changed';
            }
        }
        return $this->show($req, $ctx, $clock, $sesh, $id);
    }
}