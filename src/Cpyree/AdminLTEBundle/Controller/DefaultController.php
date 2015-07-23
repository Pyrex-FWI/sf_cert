<?php

namespace Cpyree\AdminLTEBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/widget")
     * @Template()
     */
    public function widgetAction()
    {
        return array('name' => "");
    }
    /**
    /**
     * @Route("/chart_morris")
     * @Template()
     */
    public function chartMorrisAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/chart_flot")
     * @Template()
     */
    public function chartFlotAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/chart_inline")
     * @Template()
     */
    public function chartInlineAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/advanced")
     * @Template()
     */
    public function advancedAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/ui_general")
     * @Template()
     */
    public function uiGeneralAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/ui_icons")
     * @Template()
     */
    public function uiIconsAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/ui_buttons")
     * @Template()
     */
    public function uiButtonsAction()
    {
        return array('name' => "");
    }

    /**
     * @Route("/ui_sliders")
     * @Template()
     */
    public function uiSlidersAction()
    {
        return array('name' => "");
    }
    /**
     * @Route("/form_general")
     * @Template()
     */
    public function formGeneralAction()
    {
        return array('name' => "");
    }


}
