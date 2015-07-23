<?php

namespace Cpyree\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Cpyree\TagBundle\Entity\Artist;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Cpyree\TagBundle\Form\Type\ArtistType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * @Route("/artist")
 */
class ArtistController extends BaseController
{
    
    /**
     * @Route("/edit/{id}", name="artist_edit", requirements={"id" = "\d+"}, defaults={"id" = 1})
     * @Template
     */
    public function artist_editAction($id, Request $request)
    {
        /** @var $artist Artist */
        //$artist = $this->getArtistRepo()->find($id);
        $artist = $this->getArtistRepo()->findTagDatas($id);
        //$td = $this->getTagDataRepo()->findByArtist($artist);
        //$this->getTagDataRepo()->findByArtists($artist);
        $form = $this->createForm(new ArtistType(), $artist);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            //return new Response('News updated successfully');
        }     
        
        //print_r(\Doctrine\Common\Util\Debug::dump($this->getArtistRepo()->findTagDatas($id)));
        //die();
        
        return array(
            'form'  =>  $form->createView(),
            'artist'=>  $artist,
            //'songs'=>  $this->getArtistRepo()->findTagDatas($id)
            //'songs'=>  $artist->getTagDatas()->slice(0,10)
            );
    }
    
    /**
     * @Route("/list/{page}",name="artist_list", requirements={"id" = "\d+"}, defaults={"page" = 1})
     * @Template
     */
    public function artist_listAction($page)
    {

        $artistRepo = $this->getArtistRepo();
        $limit = 20;
        
        //$nb = $mediaFile->count();
        
        $nb = $artistRepo->count();
        
        $pagination = array(
        'page' => $page,
        'route' => 'artist_list',
        'pages_count' => ceil($nb / $limit),
        'route_params' => array()
        );
        
        $mediaFile_list = $artistRepo->getPage($page, $limit);
        
        return array('paginationElements' => $mediaFile_list, "pagination" =>$pagination);
    }
}
