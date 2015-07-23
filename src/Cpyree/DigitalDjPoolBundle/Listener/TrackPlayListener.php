<?php
/**
 * Created by PhpStorm.
 * User: Angie
 * Date: 15/03/2015
 * Time: 15:05
 */

namespace Cpyree\DigitalDjPoolBundle\Listener;


use Cpyree\DigitalDjPoolBundle\Event\TrackEventDispatcher;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Container;

class TrackPlayListener {

    public function __construct(Container $service_container){
        $this->service_container = $service_container;
    }
    public function onTrackPlayed(TrackEventDispatcher $event){
        /** @var ObjectManager $em */
        $em = $this->service_container->get('doctrine')->getManager('ddp_manager');
        $track = $event->getTrack();
        $track->IncreasePlayCoun();
        $em->persist($track);
        $em->flush();
    }
}