<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();




$collection->add('cpyree_digital_dj_pool_home', new Route('/', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:index',
    )
));

$collection->add('cpyree_digital_dj_pool_stream', new Route('/track/stream/{id}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:stream',
    )
));
$collection->add('cpyree_digital_dj_pool_download', new Route('/track/download/{id}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:download',
    )
));

$collection->add('cpyree_digital_dj_pool_approve_view', new Route('/track/approve_view', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:approveView',
        'page' => 1,
        'sort' => 't.uid',
        'direction' => 'desc'
    )
));
$collection->add('cpyree_digital_dj_pool_approve', new Route('/track/approve/{track_uid}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:approve',
    )
));
$collection->add('cpyree_digital_dj_pool_neutralapprove', new Route('/track/reset_appproval/{track_uid}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:neutralapprove',
    )
));
$collection->add('cpyree_digital_dj_pool_disapprove', new Route('/track/disapprove/{track_uid}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:disapprove',
    )
));

$collection->add('cpyree_digital_dj_pool_approve_view', new Route('/track/approve_view/{page}/{sort}/{direction}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:approveView',
        'page' => 1,
        'sort' => 't.uid',
        'direction' => 'desc'
    )
));

$collection->add('cpyree_digital_dj_pool_track', new Route('/track/{page}/{sort}/{direction}', array(
        '_controller' => 'CpyreeDigitalDjPoolBundle:Default:list',
        'page' => 1,
        'sort' => 't.uid',
        'direction' => 'desc'
    )
));


return $collection;
