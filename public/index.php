<?php 


// ini_set( 'display_errors', '1' );
// ini_set( 'display_startup_errors', '1' );

define( 'LIB_PATH', realpath ( dirname( __FILE__ ) . '/../lib' ) );

require_once LIB_PATH . '/pages/index.php';

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
        ferry_api( $exploded_path );
        die();
    case "f":
    case "ferry":
        page_ferry( $exploded_path );
        die();
    case 'home':
        page_home();
        die();
    default:
        page_404();

}
