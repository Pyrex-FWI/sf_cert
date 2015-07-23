<?php

namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultPhpController extends Controller
{
    public function indexAction($name)
    {
        return $this->render("AcmeHelloBundle:Default:index.html.php", array('name' => $name));
    }

    public function notfoundAction(){
        throw $this->createNotFoundException('Le produit n\'existe pas');
    }

    public function exceptionAction(){
        throw new \Exception('Exception');
    }
}
