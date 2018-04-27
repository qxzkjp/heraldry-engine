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
use HeraldryEngine\Mvc\View;
use Symfony\Component\HttpFoundation\Request;

class DisplayController
{
    /**
     * @var array
     */
    private $args;

    /**
     * @var Application
     */
    private $app;

    /**
     * DisplayController constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->args=[];
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
        $this->args['permission_names'] = $names;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->args;
    }

    public function Show(Request $request){
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
        $this->listPermissions($this->app['entity_manager']);
        $this->app['params'] = array_merge($this->app['params'], $this->getParams());
        return $view->render($request, $this->app->security, $this->app->clock, $this->app->session, $this->app->params);
    }
}