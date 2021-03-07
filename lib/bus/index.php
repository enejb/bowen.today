<?php 

/**
 * This file containts all the route definitions.
 * That we support. 
 */
namespace NextBus;

require_once 'class-route.php';
require_once 'class-stop.php';
require_once LIB_PATH . '/utils/cache/index.php';

function get_routes() {

    $routes['280:snug-cove:bluewater'] = new Route( [
        'id' => '280:snug-cove:bluewater',
        'bus_number' => '280',
        'name' => '280 Bluewater',
        'from' => 'Snug Cove',
        'to' => 'Bluewater',
        'outbound' => true,
        'retun_id' => '280:bluewater:snug-cove',
    ] );

    $routes['280:bluewater:snug-cove'] = new Route( [
        'id' => '280:bluewater:snug-cove',
        'bus_number' => '280',
        'name' => '280 Snug Cove',
        'from' => 'Bluewater',
        'to' => 'Snug Cove',
        'outbound' => false,
        'retun_id' => '280:snug-cove:bluewater',
    ] );

    // This is where we define all the possible routes.
    $routes['281:snug-cove:eagle-cliff'] = new Route( [
        'id' => '281:snug-cove:eagle-cliff',
        'bus_number' => '281',
        'name' => '281 Eagle Cliff',
        'from' => 'Snug Cove', 
        'to' => 'Eagle Cliff',
        'outbound' => false,
        'retun_id' => '281:eagle-cliff:snug-cove',
    ] );
    
    $routes['281:eagle-cliff:snug-cove'] = new Route( [
        'id' => '281:eagle-cliff:snug-cove',
        'bus_number' => '281',
        'name' => '281 Snug Cove',
        'from' => 'Eagle Cliff', 
        'to' => 'Snug Cove',
        'outbound' => true,
        'retun_id' => '281:snug-cove:eagle-cliff',
    ] );

    $routes['282:snug-code:mt-gardner'] = new Route( [
        'id' => '282:snug-code:mt-gardner',
        'bus_number' => '282',
        'name' => '282 mt-gardner',
        'from' => 'Snug Cove',
        'to' => 'Mt Gardner',
        'outbound' => false,
        'retun_id' => '282:mt-gardner:snug-cove',
    ] );

    $routes['282:mt-gardner:snug-cove'] = new Route( [
        'id' => '282:mt-gardner:snug-cove',
        'bus_number' => '282',
        'name' => '282 Snug Cove',
        'from' => 'Mt Gardner',
        'to' => 'Snug Cove',
        'outbound' => true,
        'retun_id' => '282:snug-code:mt-gardner',
    ] );
    
    return $routes;
}

function get_popular_stops()  {
    return [ '58013' => new Stop( (object)[
        'id' => '',
    ]
    ) ];
}

function get_popular_buses() {
    return [ 'inbound-281' ];
}