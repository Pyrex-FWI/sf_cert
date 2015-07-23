<?php

namespace Cpyree\TagBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultController extends Controller
{
    
    public function id3Action(){
        $file = "/vagrant/sfTools/src/Cpyree/TagBundle/Resources/demo_tag.mp3";
        $id3_1 = new \Cpyree\TagBundle\Lib\Id3($file);
        
        $file = "/vagrant/sfTools/src/Cpyree/TagBundle/Resources/demo_tag2.mp3";
        $id3_2 = new \Cpyree\TagBundle\Lib\Id3($file);
        
        
        echo "<pre>";
        print_r($id3_1->read()->getTags());
        echo "</pre>";
        die();
        return $this->render('CpyreeTagBundle:Default:id3.html.twig', array('file1' => $id3_1->read()->getTags(), 'file2'=>$id3_2->read()->getTags()));
    }
    
    public function indexAction()
    {
        $em    = $this->getDoctrine()->getManager();
        $dql   = "SELECT td FROM CpyreeTagBundle:TagData td";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('CpyreeTagBundle:Default:index.html.twig', array('pagination' => $pagination));
    }
    
    public function listAction(){
        $dql   = "SELECT td FROM CpyreeTagBundle:TagData td";
        $query = $this->getDoctrine()->getManager()->createQuery($dql)
                               ->setFirstResult(0)
                               ->setMaxResults(100);

        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $c = count($paginator);
        foreach ($paginator as $post) {
            echo $post->getTitle() . "\n";
        }
        //return $this->render('CpyreeTagBundle:Default:index.html.twig');
        
    }
}
