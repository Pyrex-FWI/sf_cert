<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TagData
 *
 * @ORM\Table(name="context")
 * @ORM\Entity
 */
class Context {
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
     * @ORM\Column(name="name", type="string", length=45, nullable=true)
     */
    private $name;
    
    
    public function __construct($id = null){
        if($id) $this->id = $id;
    }
    
    public function setName($name){
        $this->name = $name;
        return $this;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function setId($id){
        $this->id = $id;
        return $this;
    }
}
