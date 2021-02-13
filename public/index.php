<?php


// ini_set( 'display_errors', '1' );
// ini_set( 'display_startup_errors', '1' );
if ( file_exists( '../secrets.php' ) ) {
    require_once '../secrets.php';
}

define( 'LIB_PATH', realpath ( dirname( __FILE__ ) . '/../lib' ) );
define( 'CACHE_PATH', realpath ( dirname( __FILE__ ) . '/../cache' ));

require_once '../pages/index.php';

// Take the server path and
$path = ( isset( $_GET['path'] ) ?
    $_GET['path'] :
    ( isset( $_SERVER['REQUEST_URI'] ) ?
        $_SERVER['REQUEST_URI'] : '' ) );

$exploded_path = array_values( array_filter( explode( '/', $path ) ) );

if ( empty( $exploded_path ) ) {
    $exploded_path[] = 'home';
}

switch ( $exploded_path[0] ) {
    case "api":
        api_entry( $exploded_path );
        die();
    case "f":
    case "ferry":
        page_ferry( $exploded_path );
        die();
    case 'weather':
        page_weather();
        die();
    case 'tide':
        page_tide();
        die();
    case 'home':
        page_home();
        die();
    default:
        page_404();
}
