<?php

/**
 * This file containts all the route definitions.
 * That we support. 
 */
namespace NextFerry;

require_once 'class-route.php' ;
require_once LIB_PATH . '/utils/cache/index.php';

function get_routes() {
    // This is where we define all the possible routes.
    $routes['bowen-vancouver'] = new Route( [
        'id' => 'bowen-vancouver',
        'to' => 'Vancouver, Hourseshoe Bay',
        'from' => 'Bowen island, Snug Cove', 
        'slug' => 'bowen-island-snug-cove-vancouver-horseshoe-bay/BOW-HSB',
        'current' => false,
        'map' => 'route5',
    ] );
    
    $routes['vancouver-bowen'] = new Route( [
        'id' => 'vancouver-bowen',
        'to' => 'Bowen island, Snug Cove', 
        'from' => 'Vancouver, Hourseshoe Bay',
        'slug' => 'vancouver-horseshoe-bay-bowen-island-snug-cove/HSB-BOW',
        'current' => true,
        'map' => 'route5',
    ] );

    // 
    
    return $routes;
}