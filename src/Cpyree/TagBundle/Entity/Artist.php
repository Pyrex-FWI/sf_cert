<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cover
 *

 * @ORM\Entity(repositoryClass="Cpyree\TagBundle\Entity\ArtistRepository")
 * @ORM\Table(name="artist",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="unique_name", columns={"name"})}
 * 
 * )
 *
 * 
 */
class Artist {
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
	 * @Assert\NotBlank(message="You must provide a Artist name")
     */
    private $name;

    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     *  @var \Doctrine\Common\Collections\Collection
     *   @ORM\ManyToMany(targetEntity="TagData", mappedBy="artists", fetch="EXTRA_LAZY")
     */
    private $tagDatas;
    
    public function __construct($name = null){
        if($name){ $this->name = trim($name);}
        $this->created = new \DateTime('now');
    }
    public function getId(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = $id;
        return $this;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Cover
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
     * Set name
     *
     * @param string $name
     * @return Artist
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
    
    
    public function __toString(){
        return $this->name;
    }
    

    
    public function getBulkLine(){
        $ENCLOSE = '"';
        $SEP = ";";
        $line = "";
        $line .=  $ENCLOSE.$this->name.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->created->format("Y-m-d H:i:s").$ENCLOSE;
        
        return $line;
    }


    /**
     * Add tagDatas
     *
     * @param \Cpyree\TagBundle\Entity\TagData $tagDatas
     * @return Artist
     */
    public function addTagData(\Cpyree\TagBundle\Entity\TagData $tagDatas)
    {
        $this->tagDatas[] = $tagDatas;

        return $this;
    }

    /**
     * Remove tagDatas
     *
     * @param \Cpyree\TagBundle\Entity\TagData $tagDatas
     */
    public function removeTagData(\Cpyree\TagBundle\Entity\TagData $tagDatas)
    {
        $this->tagDatas->removeElement($tagDatas);
    }

    /**
     * Get tagDatas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagDatas()
    {
        return $this->tagDatas;
    }
}
