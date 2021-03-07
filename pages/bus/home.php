<?php 

// page variables
$title = 'Bowen Bus Schedules';

require_once PAGES_DIR . '/templates/header.php';

$arrow = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16"><path fill-rule="evenodd" d="M8.22 2.97a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06l2.97-2.97H3.75a.75.75 0 010-1.5h7.44L8.22 4.03a.75.75 0 010-1.06z"></path></svg>';
?>
</div>
</div>
<div id="home" class="shell">
    <h3>Bus Routes</h3>
    <?php foreach( $routes  as $route ) { ?>
        <a href="/bus/<?php echo $route->id();?>" class="link-button"><?php echo $route->bus_number();?> <?php echo $route->from();?> <?php echo $arrow; ?> <?php echo $route->to(); ?></a>
    <?php } ?>
  
    <h3>Popular Stops</h3>
    <a href="/bus/stop/snug-code" class="link-button">Snug Cove</a>
    <a href="/bus/stop/artisan-square" class="link-button">Artisan Square</a>
    <a href="/bus/stop/horseshoe-bay" class="link-button">Horseshoe Bay</a>
</div>
<?php 
require_once PAGES_DIR . '/templates/footer.php';