<?php 

$next_ferry = $route->get_api_reponse( $timestamp );
$next_departure = DateTime::createFromFormat( DATE_RFC822, $next_ferry['next_departure'] );
$now = new DateTime();

// page variables
$title = 'Bowen Island Ferry Schedule';

// $switch_direction_url 
$switch_direction_url = '/ferry/vancouver-bowen';

if( $route_slug == 'vancouver-bowen' ) {
    $switch_direction_url = '/ferry/bowen-vancouver';
}


require_once PAGES_DIR . '/templates/header.php';
?>
<a class="btn" href="<?php echo $switch_direction_url;?>" target="_self" title="<?php echo $switch_direction_url;?>">
    Switch Directions
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
        <path
            d="M5.22 14.78a.75.75 0 001.06-1.06L4.56 12h8.69a.75.75 0 000-1.5H4.56l1.72-1.72a.75.75 0 00-1.06-1.06l-3 3a.75.75 0 000 1.06l3 3zm5.56-6.5a.75.75 0 11-1.06-1.06l1.72-1.72H2.75a.75.75 0 010-1.5h8.69L9.72 2.28a.75.75 0 011.06-1.06l3 3a.75.75 0 010 1.06l-3 3z">
        </path>
    </svg>
</a>
</div>
</div>
<div class="shell">
    <div class="map"><img src="https://apigateway.bcferries.com/api/currentconditions/1.0/images/vessels/route5.jpg" />
    </div>
    <div class="next-ferry">
        <div class="route">
            <div class="to">to: <h2><?php echo $next_ferry['to']; ?></h2>
            </div>
            <div class="from">from: <h2><?php echo $next_ferry['from']; ?></h2>
            </div>
        </div>
        <div class="time">
            <div class="to">in<h2><?php 
            if ( $next_departure && $next_departure->diff( $now )->h > 1 ) {
                echo $next_departure->diff( $now )->h . ' hours and ';
            } else if ( $next_departure && $next_departure->diff( $now )->h ) {
                echo $next_departure->diff( $now )->h . ' hour and ';
            }
            if ( $next_departure && !empty( $next_departure->diff( $now )->i ) ) {
                echo ( $next_departure ? $next_departure->diff( $now )->i .' min' : '' );
            } ?></h2>
            </div>
            <div class="from">
                <h2><?php echo ( $next_departure ? '@' . $next_departure->format( 'g:i A' ) : '' ); ?></h2>
            </div>
        </div>
    </div>

    <label>Following Departures</label>
    <!-- Separate Departures Today from Departures Next Day -->
    <ul>
        <?php foreach( $next_ferry['remaining_departures'] as $departure ) { ?>
        <li>@<?php 
            $departure_time = DateTime::createFromFormat( DATE_RFC822, $departure );
            echo $departure_time->format( 'g:i A' );
        ?>
        </li>
        <?php } ?>
    </ul>
</div>

<?php 

require_once PAGES_DIR . '/templates/footer.php';