<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 20/04/2018
 * Time: 22:44
 */

namespace HeraldryEngine\Permissions;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use HeraldryEngine\Application;
use HeraldryEngine\Dbo\Permission;
use HeraldryEngine\Interfaces\ClockInterface;
use HeraldryEngine\Mvc\View;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DisplayController
{
    /**
     * @var array
     */
    private $params;

    /**
     * DisplayController constructor.
     */
    public function __construct()
    {
        $this->params=[];
    }

    /**
     * @param EntityManager $em
     */
    public function listPermissions(EntityManager $em){
        /**
         * @var EntityRepository $permRepo
         */
        $permRepo = $em->getRepository('\HeraldryEngine\Dbo\Permission');
        $qb = $permRepo->createQueryBuilder('p');
        $qb->select()->orderBy('p.id','ASC');
        $query = $qb->getQuery();
        $query->execute();
        $permissions = $query->getResult();
        $names = [];
        /**
         * @var Permission $permission
         */
        foreach($permissions as $permission){
            $names[] = $permission->getName();
        }
        $this->params['permission_names'] = $names;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function Show(EntityManager $em, SecurityContext $ctx, ClockInterface $clock, Session $sesh, Request $request){
        $view = new View();
        $view->setTemplate("templates/template.php");
        $view->setParam("content","viewPermissions.php");
        $view->setParam("pageName","login");
        $view->setParam("primaryHead","Log");
        $view->setParam("secondaryHead","In");
        $view->setParam("scriptList",[
            "ui",
            "enable",
        ]);
        $view->setParam("cssList",[
            [
                "name" => "narrow"
            ]
        ]);
        $view->setParam("menuList",[]);
        $this->listPermissions($em);
        return $view->render($request, $ctx, $clock, $sesh, $this->params);
    }
}