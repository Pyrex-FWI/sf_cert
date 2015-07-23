<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 10/11/2014
 * Time: 10:39
 */

namespace Cpyree\AuthBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User

 * @ORM\Entity()
 * @ORM\Table(name="auth_group")
 * @package Cpyree\AuthBundle\Entity
 */
class Group implements RoleInterface {


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var
     */
    private $id;

    /**
     * @ORM\Column(name="name", length=30, type="string")
     * @var
     */
    private $name;

    /**
     * @ORM\Column(name="role", type="string", length=20, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="3")
     * @var
     */
    private $role;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     * @var
     */
    private $users;

    /**
     * @ORM\Column(name="created", type="datetime")
     * @var
     */
    private $created;

    /**
     * Returns the role.
     *
     * This method returns a string representation whenever possible.
     *
     * When the role cannot be represented with sufficient precision by a
     * string, it should return null.
     *
     * @return string|null A string representation of the role, or null
     */
    public function getRole()
    {
        return $this->role;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreated(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setRole('ROLE_'.strtoupper($this->name));
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Group
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Add users
     *
     * @param \Cpyree\AuthBundle\Entity\User $users
     * @return Group
     */
    public function addUser(\Cpyree\AuthBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Cpyree\AuthBundle\Entity\User $users
     */
    public function removeUser(\Cpyree\AuthBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Group
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
}
