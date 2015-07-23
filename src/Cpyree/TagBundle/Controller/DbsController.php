<?php

namespace Cpyree\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cpyree\TagBundle\Entity\TagData;
use Cpyree\TagBundle\Entity\DbsFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
/**
 * @Route("/dbs")
 */
class DbsController extends BaseController
{
    
    /**
     * @Route("/", name="dbs_home")
     * @Template
     */
    public function dbs_homeAction()
    {
        return array(
            'dbsFileCount'  => $this->getDbsFileRepo()->count(),
            'tagAverage'    => $this->getDbsFileRepo()->getTagedAverage() ,  
            'totalArtist'   => $this->getDbsFileRepo()->getCountArtist(),
            'totalRadio'   => $this->getDbsFileRepo()->getCountRadio()   
        );
    }
    
    /**
     * @Route("/list/{page}",name="dbs_list", requirements={"id" = "\d+"}, defaults={"page" = 1})
     * @Template
     */
    public function dbs_listAction($page)
    {

        $mediaFile = $this->getMediaFileRepo();
        $dbsFile = $this->getDbsFileRepo();
        $limit = 20;
        
        //$nb = $mediaFile->count();
        
        $nb = $dbsFile->count();
        
        $pagination = array(
        'page' => $page,
        'route' => 'dbs_list',
        'pages_count' => ceil($nb / $limit),
        'route_params' => array()
        );
        
        $mediaFile_list = $dbsFile->getPage($page, $limit);
        
        return array('tagData_list' => $mediaFile_list, "pagination" =>$pagination);
    }

    /**
     * @Route(path="/dbs/tag_data_form", name="dbs_tag_data_form")
     * @Template("CpyreeTagBundle:Dbs:tag_data_form.html.twig")
     */
    public function tagDataAction(){
        return array();
    }
}
