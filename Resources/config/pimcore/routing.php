<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

$collection->add('personalized_search_homepage', new Route('/personalized_search', array(
    '_controller' => 'PersonalizedSearchBundle:Default:index',
)));

return $collection;
