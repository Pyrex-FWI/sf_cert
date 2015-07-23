<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 07/10/14
 * Time: 18:04
 */


namespace Cpyree\TagBundle\Entity;
use \Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Class Genre

 * @ORM\Entity(repositoryClass="Cpyree\TagBundle\Entity\GenreRepository")
 * @ORM\Table(name="genre",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="unique_name", columns={"name"})}
 *
 * )
 * @UniqueEntity(
 *		fields={"name"},
 * 		message="{value} already exist, please provide other name"
 * )
 * @package Cpyree\TagBundle\Entity
 */
class Genre {
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
	 * @ORM\Column(name="name", type="string", length=70, nullable=false)
	 * @Assert\NotBlank(message="You must provide a Genre name")
	 * @Assert\Length(
	 * 		min="3",
	 * 		minMessage="Your must write a signifiant title"
	 * )                              

	 */
	private $name;

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
     * @return Genre
     */
    public function setName($name)
    {
        $this->name = $name;

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
}
