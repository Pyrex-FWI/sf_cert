<?php

namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultXmlController extends Controller
{
    public function indexAction($name)
    {
        return new \Symfony\Component\HttpFoundation\Response("<html><body>Xml $name (new HttpFoundation\\Response())</body>");
    }
}
