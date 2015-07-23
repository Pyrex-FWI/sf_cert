<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('acme_hello_php_homepage', new Route('/hellophp/{name}', array(
    '_controller' => 'AcmeHelloBundle:DefaultPhp:index',
)));
$collection->add('acme_hello_php_notfound', new Route('/hellophp_notfound', array(
    '_controller' => 'AcmeHelloBundle:DefaultPhp:notfound',
)));
$collection->add('acme_hello_php_exception', new Route('/hellophp_exception', array(
    '_controller' => 'AcmeHelloBundle:DefaultPhp:exception',
)));

return $collection;
