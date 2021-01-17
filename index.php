<?php

// require_once( 'lib/next-ferry/index.php' );

namespace NextFerry;

require_once( 'lib/next-ferry/routes.php' );

$routes = define_routes();

echo "<h1>Vancouver bowen</h1>";
echo "<br />NOW : <pre>";
$now = time();
var_dump( $routes[ 'vancouver-bowen' ]->get_next_departures_time( $now) );
var_dump( $routes[ 'vancouver-bowen' ]->get_remaining_departures_times( $now ) );

var_dump(  $routes[ 'vancouver-bowen' ]->get_availability( $now ) );

// echo '<br />Tomorrow : <pre>';
// var_dump( $routes[ 'vancouver-bowen' ]->get_next_departures_time( strtotime( 'tomorrow' ) ) );


 echo "<h1>Bowen Vancouver</h1>";
 echo "<br />NOW : <pre>";
var_dump( $routes[ 'bowen-vancouver' ]->get_next_departures_time( $now) );
var_dump( $routes[ 'bowen-vancouver' ]->get_remaining_departures_times( $now ) );
var_dump( $routes[ 'bowen-vancouver' ]->get_availability( $now ) );

// echo '<br />Tomorrow : <pre>';
// var_dump( $routes[ 'bowen-vancouver' ]->get_next_departures_time( strtotime( 'tomorrow' ) ) );

