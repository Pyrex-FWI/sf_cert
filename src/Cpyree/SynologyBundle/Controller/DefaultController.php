<?php

namespace Cpyree\SynologyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CpyreeSynologyBundle:Default:index.html.twig', array('name' => $name));
    }
}
