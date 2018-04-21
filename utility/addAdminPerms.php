<?php

use HeraldryEngine\Dbo\Permission;
use HeraldryEngine\Dbo\User;

require dirname(__FILE__).'/../bootstrap/bootstrap.php';
/**
 * @var \Doctrine\ORM\EntityManager $em
 * @var User $user
 * @var Permission $perm
 */
$em = $app['entity_manager'];
$user = $em->getRepository(User::class)->find(1);
$perms = $em->getRepository(Permission::class)->findAll();
foreach($perms as $perm){
    if($user->addPermission($perm)) {
        echo "added permission \"" . $perm->getName() ."\"\n";
    }else{
        echo "admin already has permission \"" . $perm->getName() ."\"\n";
    }
}
try {
    $em->flush();
} catch (\Doctrine\ORM\OptimisticLockException $e) {
} catch (\Doctrine\ORM\ORMException $e) {
    echo "Some duplicate entries. This is probably fine.\n";
}
