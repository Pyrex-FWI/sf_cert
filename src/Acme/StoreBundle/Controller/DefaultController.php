<?php

namespace Acme\StoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Acme\StoreBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{
    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction()
    {
        $p = new Product();
        $p->setDescription("Description");
        $p->setName("Produit");
        $p->setPrice(309,50);
        $em = $this->getDoctrine()->getManager();
        $em->persist($p);
        $em->flush();
        return new Response("<html><body>Produit {$p->getId()}</body>");

    }
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $ps = $this->getProductRepository()->findAll();
        return new Response(count($ps));
    }

    /**
     * @param $id
     * @Route("/show/{id}")
     * @return Response
     */
    public function showAction($id){
        /** @var Product $p */
        $p = $this->getProductRepository()->find($id);
        if(!$p){
            throw $this->createNotFoundException("Product not exist");
        }

        return new Response($p->getId() . " - ". $p->getName());
    }
}
