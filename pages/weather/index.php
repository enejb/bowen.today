<?php 
// page variables
$title = 'Bowen - Weather';

require_once PAGES_DIR . '/templates/header.php';

$weather = get_current_bowen_weather();
// echo "<p>";
// echo $weather->temperature();
// echo "<br>";
// echo "Sunset : ". $weather->sunset()->format( 'r' );
// echo "<br>";
// echo $weather->sunrise()->format( 'r' );
// echo "<br>";
// echo $weather->wind_speed() . 'm/s';
// echo "<br>";
// echo $weather->wind_direction();

// echo "<br>";
// echo $weather->presure();

// echo "<br>";
// echo $weather->visibility() . 'Average visibility, metres';

// echo "<br>";
// echo $weather->humidity() .'% humidity' ;

// echo "<br>";

?>
    </div>
</div>
    <div class="shell">
        <div class="flex">
            <img src="<?php echo $weather->icon(); ?>" class="flex-image" height="82" width="82" alt="<?php echo $weather->text(); ?>" />
            <div>
                <h1 class="main-title"><?php echo $weather->temperature(); ?>&deg;C - <?php echo $weather->text(); ?></h1>
                <p><?php echo $weather->description(); ?> - <?php echo $weather->wind_speed() . 'm/s '; echo $weather->wind_direction(); ?> Wind</p>
            </div>
        </div>
    </div>
<?php 
require_once PAGES_DIR . '/templates/footer.php';