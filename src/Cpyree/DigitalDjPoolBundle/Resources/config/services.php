<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;


$container->setDefinition(
    'cpyree_digital_dj_pool.track_expert_search_type',
    new Definition(
        'Cpyree\DigitalDjPoolBundle\Form\Type\TrackExpertSearchType',
        array(
            new Reference('translator')
        )
    )
);



$acmeDefinition = new Definition('Cpyree\DigitalDjPoolBundle\Twig\Extension\PlayerExtension', array(new Reference('service_container')));
$acmeDefinition->addTag('twig.extension');
$container->setDefinition('cpyree.twig.player_extension', $acmeDefinition);

$acmeDefinition = new Definition('Cpyree\DigitalDjPoolBundle\Twig\Extension\CoverExtension', array(new Reference('service_container')));
$acmeDefinition->addTag('twig.extension');
$container->setDefinition('cpyree.twig.cover_extension', $acmeDefinition);

$acmeDefinition = new Definition('Cpyree\DigitalDjPoolBundle\Service\CleanDir', array(new Reference('service_container')));
$container->setDefinition('cpyree_digital_dj_pool.clean_dir', $acmeDefinition);

$acmeDefinition = new Definition('Cpyree\DigitalDjPoolBundle\Service\FixPath', array(new Reference('service_container')));
$container->setDefinition('cpyree_digital_dj_pool.fix_path', $acmeDefinition);


$container
    ->setDefinition('cpyree.digitaldjpool.track.play', new Definition('Cpyree\DigitalDjPoolBundle\Listener\TrackPlayListener',array(new Reference('service_container'))))
    ->addTag('kernel.event_listener', array('event' => \Cpyree\DigitalDjPoolBundle\DigitalDjPoolEvents::TRACK_PLAY, 'method' => 'onTrackPlayed'));

$container
    ->setDefinition('cpyree.digitaldjpool.track.approval', new Definition('Cpyree\DigitalDjPoolBundle\Listener\TrackApprovalListener',array(new Reference('service_container'))))
    ->addTag('kernel.event_listener', array('event' => \Cpyree\DigitalDjPoolBundle\DigitalDjPoolEvents::TRACK_APPROVAL, 'method' => 'onApprovalAction'));

$container
    ->setDefinition('cpyree.digitaldjpool.track.subscriber', new Definition('Cpyree\DigitalDjPoolBundle\Event\TrackSubscriber'/*,array(new Reference('service_container'))*/))
    ->addTag('kernel.event_subscriber', array('event' => \Cpyree\DigitalDjPoolBundle\DigitalDjPoolEvents::TRACK_APPROVAL, 'method' => 'onApprovalAction'));

