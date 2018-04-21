<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 20/04/2018
 * Time: 21:38
 */

namespace HeraldryEngine\Dbo;
use Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\Common\Collections\Collection;

/**
 * @Entity @Table(name="Permissions")
 **/
class Permission
{
    /**
     * @ID @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;
    /**
     * @Column(type="string", length=50, unique=true)
     * @var string
     */
    protected $name;

    /**
     * Many Permissions have Many Users.
     * @ManyToMany(targetEntity="User", mappedBy="permissions")
     * @var collection|User[]
     */
    protected $users;

    public function __construct($name){
        $this->name = $name;
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param User $user
     */
    public function addAssociatedUser(User $user){
        $this->users[] = $user;
    }
}
