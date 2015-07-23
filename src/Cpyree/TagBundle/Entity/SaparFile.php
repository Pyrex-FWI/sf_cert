<?php

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SaparFile
 * @ORM\Entity(repositoryClass="Cpyree\TagBundle\Entity\SaparFileRepository")
 */
class SaparFile extends MediaFile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="smallint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
 

}