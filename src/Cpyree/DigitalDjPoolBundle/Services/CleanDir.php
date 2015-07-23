<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 14/03/2015
 * Time: 19:57
 */

namespace Cpyree\DigitalDjPoolBundle\Service;


use Symfony\Component\DependencyInjection\Container;

class CleanDir {
    /** @var Container */
    private $service_container;
    /** @var  Logger */
    private $logger;

    public function __construct($service_container)
    {
        $this->service_container = $service_container;

    }

    public function clean(){
        $this->service_container->getParameter('ddp.files_path');
    }
}