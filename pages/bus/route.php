<?php 


$route = $routes[ $route_slug ];
// page variables
$title = 'Bowen Bus Schedules > ' . $route->bus_number() . ' ';

$arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M8.22 2.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06l2.97-2.97H3.75a.75.75 0 010-1.5h7.44L8.22 4.03a.75.75 0 010-1.06z"></path></svg>';
$switch_direction_url = '/bus/'. $route->opposite_direction();
require_once PAGES_DIR . '/templates/header.php';
?>
<a class="btn" href="<?php echo $switch_direction_url;?>" target="_self" >
    Switch Directions
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
        <path
            d="M5.22 14.78a.75.75 0 001.06-1.06L4.56 12h8.69a.75.75 0 000-1.5H4.56l1.72-1.72a.75.75 0 00-1.06-1.06l-3 3a.75.75 0 000 1.06l3 3zm5.56-6.5a.75.75 0 11-1.06-1.06l1.72-1.72H2.75a.75.75 0 010-1.5h8.69L9.72 2.28a.75.75 0 011.06-1.06l3 3a.75.75 0 010 1.06l-3 3z">
        </path>
    </svg>
</a>
</div>
</div>
<div id="home" class="shell">
    <div id="map"></div>
    <h1 class="link-button"><?php echo $route->bus_number();?> <?php echo $route->from();?> <?php echo $arrow; ?> <?php echo $route->to(); ?></h1>
    <?php 
    $stop_counter = 1;
    foreach ( $route->get_stops() as $stop ) {         
        ?>
        <a href="/bus/stop/<?php echo $route_slug; ?>/<?php echo $stop->id();?>" class="link-button"><?php echo $stop_counter; ?> - <?php echo $stop->name() ?></a>
    <?php $stop_counter++; } ?>
</div>

<link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js"></script>
<style>
#map { 
  height: 280px; 
  background: gray; 
  margin: -30px -30px 20px -30px; 
}
.marker {
    background-color: #FFF;
    background-size: cover;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    cursor: pointer;
    text-align:center;
    border: 1px solid #AAA;
    line-height: 20px;
    text-decoration: none;
    color: color: #333;
}
.current_marker {
  background-color: $red;
  border: 1px solid $red-900;
  color: #FFF;
}
</style>
<script>
var stops = <?php echo json_encode( array_values( $route->get_stops() ) ); ?>;
mapboxgl.accessToken = 'pk.eyJ1IjoiZW5lamJhamdvcmljMiIsImEiOiJja2xweGg4MXAweGg0MnVvNDZreGk2ZHVuIn0.4-52w7OSbrfZRtEber53tQ';
var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/enejbajgoric2/cklpz12655q1917ol8qyo2ibw',
    center: find_center( stops ), // [ -123.370154, 49.365900 ]
    zoom: 11
});

function find_center( stops ) {
    var lng_values = stops.map( function( stop ) { return stop.location[0] } );
    var max_lng = Math.max.apply( null, lng_values );
    var min_lng = Math.min.apply( null, lng_values );
    var lat_values = stops.map( function( stop ) { return stop.location[1] } );
    var max_lat = Math.max.apply( null, lat_values );
    var min_lat = Math.min.apply( null, lat_values );

    var mid_lng = ( ( max_lng - min_lng ) / 2 ) + min_lng;
    var mid_lat =  ( ( max_lat - min_lat ) / 2 ) + min_lat;
    return [ mid_lng, mid_lat ];
}

// 49.376765 

var i = 0;
while ( stops[ i ] ) {
    var stop = stops[ i ];
    i++;

    var el = document.createElement( 'a' );
    var text = document.createTextNode( i );

    // add the text node to the newly created div
    el.appendChild(text);
    el.className = 'marker';
    el.href = '/bus/stop/<?php echo $route_slug; ?>/' + stop.properties.code;
    // el.innerHtml = 'as';

    var marker = new mapboxgl.Marker(el)
    .setLngLat( stop.location )
    .addTo(map);
}
</script>

<?php 
require_once PAGES_DIR . '/templates/footer.php';