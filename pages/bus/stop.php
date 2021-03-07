<?php 
// page variables
$title = 'Bowen Bus Schedules';

$route = $routes[ $route_slug ];

$bus_stop = $route->get_stop( $bus_stop_number );
if ( ! $bus_stop ) {
    page_404();
}

$arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M8.22 2.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06l2.97-2.97H3.75a.75.75 0 010-1.5h7.44L8.22 4.03a.75.75 0 010-1.06z"></path></svg>';

require_once PAGES_DIR . '/templates/header-home.php';

$arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M8.22 2.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06l2.97-2.97H3.75a.75.75 0 010-1.5h7.44L8.22 4.03a.75.75 0 010-1.06z"></path></svg>';
?>
<div class="header">
    <div class="header-shell">
        <a class="btn" href="/bus/<?php echo $route->id(); ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"
                target="_self" title="Back to Bowen Today">
                <path fill-rule="evenodd"
                    d="M9.78 12.78a.75.75 0 01-1.06 0L4.47 8.53a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 1.06L6.06 8l3.72 3.72a.75.75 0 010 1.06z">
                </path>
            </svg>Back</a>
    </div>
</div>
<div id="home" class="shell">
    <div id="map"></div>
    <h3>Schedule for: <?php echo $bus_stop->name(); ?></h3>
    <p class="link-button"><?php echo $route->bus_number();?> <?php echo $route->from();?> <?php echo $arrow; ?> <?php echo $route->to(); ?></p>
    
    <label>Following Departures</label>
    <ul>
        <?php foreach( $bus_stop->get_today_schedule() as $departure ) { ?>
        <li>@<?php 
            $departure_time = DateTime::createFromFormat( 'H:i', $departure );

            echo $departure_time->format( 'g:i A' );
        ?>
        </li>
        <?php } ?>
    </ul>
</div>

<link href="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js"></script>
<script>
var current_stop = <?php echo json_encode( $bus_stop ); ?>;

var stops = <?php echo json_encode( array_values( $route->get_stops() ) ); ?>;
mapboxgl.accessToken = 'pk.eyJ1IjoiZW5lamJhamdvcmljMiIsImEiOiJja2xweGg4MXAweGg0MnVvNDZreGk2ZHVuIn0.4-52w7OSbrfZRtEber53tQ';
var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/enejbajgoric2/cklpz12655q1917ol8qyo2ibw',
    center: current_stop.location, // [ -123.370154, 49.365900 ]
    zoom: 11
});

var i = 0;
while ( stops[ i ] ) {
    var stop = stops[ i ];
    i++;

    var el = document.createElement( 'a' );
    var text = document.createTextNode( i );

    // add the text node to the newly created div
    el.appendChild(text);
    el.className = 'marker';
    if ( stop.properties.code == current_stop.properties.code ) {
        el.className = 'marker current_marker';
    }
    el.href = '/bus/stop/<?php echo $route_slug; ?>/' + stop.properties.code;
    // el.innerHtml = 'as';

    var marker = new mapboxgl.Marker(el)
    .setLngLat( stop.location )
    .addTo(map);
}
</script>

<?php 
require_once PAGES_DIR . '/templates/footer.php';