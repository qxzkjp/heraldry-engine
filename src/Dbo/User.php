<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 19/04/2018
 * Time: 19:32
 */

namespace HeraldryEngine\Dbo;
use Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\Common\Collections\Collection;


/**
 * @Entity @Table(name="users")
 **/
class User
{
    /**
     * @ID @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;
    /**
     * @Column(type="string", length=50)
     * @var string
     */
    protected $userName;
    /**
     * @Column(type="string", length=255)
     * @var string
     */
    protected $pHash;
    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $accessLevel;

    /**
     * Many Users have Many Permissions.
     * @ManyToMany(targetEntity="Permission", inversedBy="users")
     * @JoinTable(name="users_permissions")
     * @var Collection|Permission[]
     */
    protected $permissions;

    public function __construct($username, $password, $accessLevel=ACCESS_LEVEL_USER)
    {
        $this->permissions = new ArrayCollection();
        $this->userName = $username;
        $this->setPassword($password);
        $this->accessLevel = $accessLevel;
    }

    /**
     * @return int
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return int
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
    }

    /**
     * @param int $accessLevel
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;
    }

    /**
     * @param string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        return password_verify($password, $this->pHash);
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->pHash = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param Permission $perm
     * @return bool
     */
    public function addPermission(Permission $perm){
        //don't add permission if we already have it -- they're unique
        if(!$this->permissions->contains($perm)) {
            $this->permissions[] = $perm;
            $perm->addAssociatedUser($this);
            return true;
        }else{
            return false;
        }
    }

    public function getPermissionNames(){
        /**
         * @var Permission $permission
         * @var string[] $list;
         */
        $list = [];
        foreach($this->permissions as $permission){
            $list[] = $permission->getName();
        }
        return $list;
    }

}