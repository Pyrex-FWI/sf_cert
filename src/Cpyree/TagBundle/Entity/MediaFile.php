<?php

namespace Cpyree\TagBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cpyree\TagBundle\Entity\BaseRepository;
use Cpyree\TagBundle\Entity\Context;
use Cpyree\TagBundle\Lib\Id3;

/**
 * MediaFile
 *
 * @ORM\Table(name="media_file", 
 *              indexes={ 
 *                      @ORM\Index(name="context_idx", columns={"context_id"})
 *                      },
 *              uniqueConstraints={
 *                      @ORM\UniqueConstraint(name="unique_hash", columns={"hash"})
 *                      }
 * )
 * @ORM\Entity(repositoryClass="Cpyree\TagBundle\Entity\MediaFileRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="context_id", type="integer") 
 * @ORM\DiscriminatorMap({"1" = "DbsFile", "2" = "SaparFile"})
 */
class MediaFile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=32, nullable=true)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="text", nullable=true)
     */
    private $filepath;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tag_pass", type="boolean", nullable=true)
     */
    private $tagPass;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exist", type="boolean", nullable=true)
     */
    private $exist = '1';

    /**
     * #var \Context
     * #ORM\ManyToOne(targetEntity="Context")
     * #ORM\JoinColumn(name="context_id", referencedColumnName="id")
     */
    private $context;
    
    /**
     * @var \TagData
     * bidirectionnal relation
     * @ORM\OneToOne(targetEntity="TagData", mappedBy="mediaFile", fetch="EAGER")
     */
    private $tagData;
    

    
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
     * Set hash
     *
     * @param string $hash
     * @return MediaFile
     */
    public function setHash($filePath)
    {
        $this->hash = hash('md5', $filePath);
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set filepath
     *
     * @param string $filepath
     * @return MediaFile
     */
    public function setFilepath($filepath)
    {
        $this->filepath = trim($filepath);
        return $this;
    }

    /**
     * Get filepath
     *
     * @return string 
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Set tagPass
     *
     * @param boolean $tagPass
     * @return MediaFile
     */
    public function setTagPass($tagPass)
    {
        $this->tagPass = $tagPass;

        return $this;
    }

    /**
     * Get tagPass
     *
     * @return boolean 
     */
    public function getTagPass()
    {
        return $this->tagPass;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return MediaFile
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * 
     * @return DateTime
     */
    public function getCreated(){
        return $this->created;
    }
    /**
     * Get exist
     *
     * @return boolean 
     */
    public function getExist()
    {
        return $this->exist;
    }
    
    
    public function setExist($exist){
        $this->exist = $exist;
        return $this;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return MediaFile
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    public function getBulkLine(Context $context){
        $ENCLOSE = '"';
        $SEP = ";";
        $line = "";
        $line .=  $ENCLOSE.$this->getHash().$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->filepath.$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE.$this->created->format("Y-m-d H:i:s").$ENCLOSE;
        $line .=  $SEP;
        $line .=  $ENCLOSE."1".$ENCLOSE;//exist
        $line .=  $SEP;
        $line .=  $ENCLOSE."0".$ENCLOSE;//tagPass
        $line .=  $SEP;
        $line .=  $ENCLOSE.$context->getId().$ENCLOSE;//tagPass
        $line .=  $SEP;
        
        
        return $line;        
    }
    
    /**
     * 
     * @return TagData
     */
    public function getTagData(){
        return $this->tagData;
    }
    
    /**
     * get Id3 data from Mediafile
     * @return \Cpyree\TagBundle\Lib\Id3
     */
    public function getId3(){
        if(file_exists($this->getFilepath())){
            $id3 = new Id3($this->getFilepath());
            $id3->read();
            return $id3;
        }
        return null;
        
    }
    
    /**
     * 
     * @return bool
     */
    public function exist(){
        return file_exists($this->filepath)? true:false;
    }
    

}
