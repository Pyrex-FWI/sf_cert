<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 07/10/14
 * Time: 18:04
 */


namespace Cpyree\AuthBundle\Entity;
use \Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * Class User

 * @ORM\Entity(repositoryClass="Cpyree\AuthBundle\Entity\UserRepository")
 * @ORM\Table(name="auth_user",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="unique_name", columns={"email","username"})}
 *
 * )
 * @UniqueEntity(
 *		fields={"email","username"},
 * 		message="{value} already exist, please provide other name"
 * )
 * @package Cpyree\AuthBundle\Entity
 */
class User implements UserInterface, \Serializable{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="username", type="string", length=70, nullable=false)
	 * @Assert\NotBlank(message="You must provide a User name")
	 * @Assert\Length(
	 * 		min="3",
	 * 		minMessage="Your must write a signifiant title"
	 * )                              

	 */
	private $username;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255, nullable=false)
	 * @Assert\NotBlank(message="You must provide a password")
	 * @Assert\Length(
	 * 		min="3",
	 * 		minMessage="Your must write a signifiant password"
	 * )

	 */
	private $password;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=255, nullable=false)
	 * @Assert\NotBlank(message="You must provide email")
	 * @Assert\Length(
	 * 		min="3",
	 * 		minMessage="Your must write a signifiant email"
	 * )
     * @Assert\Email()
	 */
	private $email;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt = "";

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="auth_users_groups")
     * @var
     */
    private $groups;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive = false;

    /**
     * @ORM\Column(name="created", type="datetime")
     * @var
     */
    private $created;

    /**
     * @ORM\Column(name="activation_token", type="text", length=100)
     * @var string
     */
    private $activationToken;

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return $this->groups->toArray();
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized);
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreated(new \DateTime()) ;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Add groups
     *
     * @param \Cpyree\AuthBundle\Entity\Group $groups
     * @return User
     */
    public function addGroup(\Cpyree\AuthBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * set groups
     *
     * @param \Doctrine\Common\Collections\Collection $groups
     * @return User
     */
    /*public function setGroups(\Doctrine\Common\Collections\Collection $groups)
    {
        $this->groups = $groups;

        return $this;
    }
*/
    /**
     * Remove groups
     *
     * @param \Cpyree\AuthBundle\Entity\Group $groups
     */
    public function removeGroup(\Cpyree\AuthBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
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

    /**
     * Set activationToken
     *
     * @param string $activationToken
     * @return User
     */
    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    /**
     * Get activationToken
     *
     * @return string 
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }
}
