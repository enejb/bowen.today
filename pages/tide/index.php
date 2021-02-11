<?php
// page variables
$title = 'Bowen - Tides';

require_once PAGES_DIR . '/templates/header.php';

$tide_info = get_current_bowen_tide();

function get_relative_width( $height, $max, $min ) {
    echo ( ($height / $max) * 100 ) . '%';
}

?>
</div>
</div>
<div id="tide" class="shell">
    <div>
        <div class="padding-bottom">last tide:<h2><?php echo $tide_info->get_previous_tide()['type']; ?> tide -
                <?php echo $tide_info->get_previous_tide()['height']; ?>m</h2>
        </div>
        <div class="padding-bottom">current tide: <h2>
                <?php 
            echo $tide_info->get_current_height( 
                $tide_info->get_previous_tide()['height'],
                $tide_info->get_next_tide()['height'],
                $tide_info->get_previous_tide()['time']->format( 'U' ),
                $tide_info->get_next_tide()['time']->format( 'U' ) 
                ); ?>m
                and
                <?php echo $tide_info->get_current_movement( $tide_info->get_previous_tide()['height'], $tide_info->get_next_tide()['height'] ); ?>
            </h2>
        </div>
        <div class="padding-bottom">next tide: <h2><?php echo $tide_info->get_next_tide()['type']; ?> tide -
                <?php echo $tide_info->get_next_tide()['height']; ?>m</h2>
        </div>
    </div>
    <label>Today</label>
    <ul>
        <?php foreach( $tide_info->get_tide_entries() as $tide ) { ?>
        <li style="position: relative;">
            <?php if( $tide['different_day'] ) {?>
            <label style="margin: 20px 0 5px; "><?php echo $tide['time']->format( 'l - F j, Y' ); ?></label>
            <?php } ?>
            <div
                style="width: <?php get_relative_width( $tide['height'], $tide_info->get_max_tide(), $tide_info->get_min_tide() ); ?>; background: #e1e4e8; height: 40px; position: absolute; z-index: 1;">
            </div>
            <div class="flex space-between" style="z-index: 2; position: relative; height: 25px; padding-top: 10px;">
                <div style="padding-left: 15px;"><?php echo $tide['type']; ?> tide</div>
                <div><?php echo $tide['height']; ?>m</div>
                <div>@<?php echo $tide['time']->format( 'g:i A' ); ?></div>
            </div>
        </li>
        <?php } ?>
    </ul>
</div>
<?php

require_once PAGES_DIR . '/templates/footer.php';