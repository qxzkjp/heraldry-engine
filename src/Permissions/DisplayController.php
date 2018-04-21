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
use HeraldryEngine\Dbo\Permission;

class DisplayController
{
    /**
     * @var array
     */
    private $args;

    /**
     * DisplayController constructor.
     */
    public function __construct()
    {
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
}