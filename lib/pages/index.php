<?php

define( 'PAGES_DIR', dirname(__FILE__) );

function page_404() {
    header("HTTP/1.0 404 Not Found");
    http_response_code(404);

    echo "<h1 style='margin: 100px; font-size: 44px; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\";'>404 Page not found</h1>";
    die();
}

function page_api_404() {
  http_response_code(404);
  header("HTTP/1.0 404 Not Found");
  header('Content-Type: application/json');
  echo '{ "error": "not_found", "message": "The specified path was not found. Please visit https://github.com/enejb/bowen.today/ for valid paths." }';
  die();
}
function api_entry( $path ) {
  $now = time();
  $default_path = [ 'api', 'none', 'type', 'vancouver-bowen', $now ];

  list( $root, $version, $api_entry, $route_slug, $day ) = default_args( $path, $default_path );
  if ( $version !== 'v1' ) {
      return page_api_404();
  }

  if ( $api_entry == 'ferry' ) {
    return ferry_api( $path );
  }
  return page_api_404();
}

function ferry_api( $path ) {
    $now = time();
    $default_path = [ 'api', 'none', 'ferry', 'vancouver-bowen', $now ];

    list( $root, $version, $api_entry, $route_slug, $day ) = default_args( $path, $default_path );
    if ( $version !== 'v1' ) {
        return page_api_404();
    }

    require_once LIB_PATH . '/next-ferry/index.php';

    $routes = \NextFerry\get_routes();

    if ( ! isset( $routes[ $route_slug ] ) ) {
        return page_404();
    }

    $route = isset( $_GET['route'] ) && isset( $routes[ $_GET['route'] ] ) ?
        $routes[ $_GET['route'] ] :
        $routes[ $route_slug ];

    if ( $day == $now ) {
        $timestamp = $day;
    } else {
        $date_time = date_create_from_format( \NextFerry\DATE_FORMAT . ' ' . \NextFerry\TIME_FORMAT, $day . ' 00:01 AM', new \DateTimeZone( \NextFerry\BC_TIMEZONE ) );
        $timestamp = $date_time ? $date_time->format( 'U' ) : $now;
    }

    if ( isset( $_GET['day'] ) ) {
        $date_time = date_create_from_format( \NextFerry\DATE_FORMAT . ' ' . \NextFerry\TIME_FORMAT, $_GET['day'] . ' 00:01 AM', new \DateTimeZone( \NextFerry\BC_TIMEZONE ) );
        $timestamp = $date_time ? $date_time->format( 'U' ) : $now;
    }

    header('Content-Type: application/json');
    http_response_code(200);
    echo json_encode( $route->get_api_reponse( $timestamp ) );
}

function page_ferry( $path ) {
    $now = time();
    $default_path = [ 'ferry', 'vancouver-bowen', $now ];
    list( $root, $route_slug, $day ) = default_args( $path, $default_path );

    require_once LIB_PATH . '/next-ferry/index.php';

    $routes = \NextFerry\get_routes();
    if ( ! isset( $routes[ $route_slug ] ) ) {
        return page_404();
    }

    $route = isset( $_GET['route'] ) && isset( $routes[ $_GET['route'] ] ) ?
        $routes[ $_GET['route'] ] :
        $routes[ $route_slug ];

    if ( $day == $now ) {
        $timestamp = $day;
    } else {
        $date_time = date_create_from_format( \NextFerry\DATE_FORMAT . ' ' . \NextFerry\TIME_FORMAT, $day . ' 00:01 AM', new \DateTimeZone( \NextFerry\BC_TIMEZONE ) );
        $timestamp = $date_time ? $date_time->format( 'U' ) : $now;
    }

    if ( isset( $_GET['day'] ) ) {
        $date_time = date_create_from_format( \NextFerry\DATE_FORMAT . ' ' . \NextFerry\TIME_FORMAT, $_GET['day'] . ' 00:01 AM', new \DateTimeZone( \NextFerry\BC_TIMEZONE ) );
        $timestamp = $date_time ? $date_time->format( 'U' ) : $now;
    }

    require_once PAGES_DIR . '/ferry/index.php';

}

function default_args( $args, $default ) {
    $noralized_values = [];
    for ( $i = 0; isset( $default[ $i ] ); $i++ ) {
        $noralized_values[ $i ] = isset( $args[ $i ] ) ? $args[ $i ] : $default[ $i ];
    }

    return $noralized_values;
}

function page_home() {
    require_once PAGES_DIR . '/home/index.php';
}

function page_weather() {
    require_once LIB_PATH . '/weather/index.php';
    require_once PAGES_DIR . '/weather/index.php';
}
