<?php

namespace Cpyree\AudioDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CpyreeAudioDataBundle:Default:index.html.twig', array('name' => $name));
    }
}
