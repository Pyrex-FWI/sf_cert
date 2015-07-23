<?php

namespace Cpyree\TagBundle\Manager;
use Cpyree\TagBundle\Entity\Artist;

class TagBundleManager {
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em){
        $this->em = $em;
    }
    
    public function countDbsFile(){
        return $this->getDbsFileRepo()->count();
    }
    
    public function countSaparFile(){
        return $this->getSaparFileRepo()->count();
    }
    
    /**
     * 
     * @param type $obj
     * @return type
     */
    public function persist($obj){
        return $this->em->persist($obj);
    }
    
    /**
     * 
     * @param type $entity
     * @return type
     */
    public function flush($entity = null){
        return $this->em->flush($entity);
    }
    
    public function clear(){
        return $this->em->clear();
    }
    /**
     * 
     * @param \Cpyree\TagBundle\Entity\TagData $td
     * @return \Cpyree\TagBundle\Entity\TagData
     */
    public function checkArtistsCombinaison(\Cpyree\TagBundle\Entity\TagData $td){
        foreach($td->explodeArtistName() as $artistName){
            if(is_null($artistName)) continue;
            $artist = $this->getArtistRepo()->getOrCreateNewArtist($artistName);
            $td->addArtist($artist);
            if($artist->getId()){
                $this->em->persist($artist);
            }
        }
        return $td;
    }
    
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getDbsFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:DbsFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getSaparFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:SaparFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getTagDataRepo(){
        return $this->em->getRepository("CpyreeTagBundle:TagData");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\MediaFileRepository
     */
    public function getMediaFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:MediaFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\CoverRepository
     */
    public function getCoverFileRepo(){
        return $this->em->getRepository("CpyreeTagBundle:Cover");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\ArtistRepository
     */
    public function getArtistRepo(){
        return $this->em->getRepository("CpyreeTagBundle:Artist");
    }
}

?>