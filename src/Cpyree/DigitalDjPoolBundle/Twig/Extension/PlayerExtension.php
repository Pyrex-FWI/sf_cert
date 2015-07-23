<?php
/**
 * Created by PhpStorm.
 * User: christophep
 * Date: 12/10/2014
 * Time: 16:06
 */

namespace Cpyree\DigitalDjPoolBundle\Twig\Extension;


use Cpyree\DigitalDjPoolBundle\Entity\Track;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Router;

class PlayerExtension extends \Twig_Extension{


    private $container;
    public function __construct(Container $service_container){
        $this->container = $service_container;
    }
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter(
                'player',
                array($this, 'playerFilter'),
                array(
                    'pre_escape' => 'html',
                    'is_safe' => array('html')
                )
            ),
        );
    }

    public function playerFilter(Track $track,$id=null)
    {

        if($this->container->getParameter('ddp.stream.type') == 'local'){
            /** @var Router $router */
            $router = $this->container->get('router');
            $playUrl =$router->generate($this->container->getParameter('ddp.stream.route'), array('id' => $track->getUid()));
        }else{
            $playUrl = $track->getRawData();
        }
        $link = sprintf('<a class="plr_ddp" href="%s" rel="nofollow"  id="playLink_'.$id.'">Play</a>',$playUrl);

        return $link;
    }
    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return str_replace("\\",'_','Cpyree\DigitalDjPoolBundle\Twig\Player');
    }
}