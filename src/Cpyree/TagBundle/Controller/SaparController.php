<?php

namespace Cpyree\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cpyree\TagBundle\Entity\TagData;
use Cpyree\TagBundle\Entity\DbsFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
/**
 * @Route("/sapar")
 */
class SaparController extends Controller
{
    /**
     *
     * @var \Cpyree\TagBundle\Entity\SaparFileRepository
     */
    public $saparFileRepo;
    /**
     *
     * @var \Cpyree\TagBundle\Entity\TagDataRepository
     */
    public $tagDataRepo;
    
    public function __construct() {
        
    }
    
    private function setSaparFileRepo(){
        $this->saparFileRepo = $this->getDoctrine()->getRepository("CpyreeTagBundle:SaparFile");
        return $this;
    }
    
    private function setTagDataRepo(){
        $this->tagDataRepo = $this->getDoctrine()->getRepository("CpyreeTagBundle:TagData");
        return $this;
    }
    
    /**
     * @Route("/", name="sapar_home")
     * @Template
     */
    public function sapar_homeAction()
    {
        $this->setSaparFileRepo()->setTagDataRepo();
        return array(
            "genreStat" => $this->tagDataRepo->countGenreWithAverage()
        );
    }
    
    /**
     * @Route("/list/{page}",name="sapar_list", requirements={"id" = "\d+"}, defaults={"page" = 1})
     * @Template
     */
    public function sapar_listAction($page)
    {
        $saparFile = $this->getDoctrine()->getRepository("CpyreeTagBundle:SaparFile");
        $limit = 20;
        
        //$nb = $mediaFile->count();
        
        $nb = $saparFile->count();
        $pagination = array(
        'page' => $page,
        'route' => 'dbs_list',
        'pages_count' => ceil($nb / $limit),
        'route_params' => array()
        );
        
        $mediaFile_list = $saparFile->getPage($page, $limit);
        
        return array('tagData_list' => $mediaFile_list, "pagination" =>$pagination);
    }
}
