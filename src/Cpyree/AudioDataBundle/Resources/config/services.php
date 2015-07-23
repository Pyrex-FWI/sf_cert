<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;

if(is_array($container->getParameter('album_cover'))){
    $albumCover = $container->getParameter('album_cover');
    foreach($albumCover as $name => $param){
        $container->setDefinition(
            'cpyree_audio_data.album_cover.' . $name,
            new Definition(
                'Cpyree\AudioDataBundle\Services\AlbumCover',
                array(new Reference('service_container'), $name)
            )
        );
    }
}
/*
$container->setDefinition(
    'cpyree_audio_data.album_cover.track_expert_search_type',
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
*/