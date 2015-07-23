<?php
namespace Cpyree\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Cpyree\TagBundle\Manager\TagBundleManager;

abstract class BaseController extends Controller{
    
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getDbsFileRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:DbsFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getSaparFileRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:SaparFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\DbsFileRepository
     */
    public function getTagDataRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:TagData");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\MediaFileRepository
     */
    public function getMediaFileRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:MediaFile");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\CoverRepository
     */
    public function getCoverFileRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:Cover");
    }
    /**
     * 
     * @return \Cpyree\TagBundle\Entity\ArtistRepository
     */
    public function getArtistRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:Artist");
    }

    /**
     * 
     * @return \Cpyree\TagBundle\Entity\ArtistInTagDataRepository
     */
    public function getArtistInTagDataRepo(){
        return $this->getDoctrine()->getRepository("CpyreeTagBundle:ArtistInTagData");
    }
}