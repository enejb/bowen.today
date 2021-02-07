<?php
// page variables
$title = 'Bowen - Tides';

require_once PAGES_DIR . '/templates/header.php';

$tide_info = get_current_bowen_tide();

function get_relative_width( $height ) {
    echo ( $height * 80 ) . 'px';
}

?>
<div class="header">
    <div class="header-shell">
        <a href="/"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16" height="16">
                <path fill-rule="evenodd"
                    d="M9.78 12.78a.75.75 0 01-1.06 0L4.47 8.53a.75.75 0 010-1.06l4.25-4.25a.75.75 0 011.06 1.06L6.06 8l3.72 3.72a.75.75 0 010 1.06z">
                </path>
            </svg>Back</a>
    </div>
</div>
    <div class="shell">
    <div class="next-ferry">
        <div class="route">
            <div class="to">last tide:<h2><?php echo $tide_info->get_previous_tide()['type']; ?> tide - <?php echo $tide_info->get_previous_tide()['height']; ?>m</h2>
            </div>
            <div class="from">next tide: <h2><?php echo $tide_info->get_next_tide()['type']; ?> tide - <?php echo $tide_info->get_next_tide()['height']; ?>m</h2>
            </div>
        </div>
        <div class="time">            
        </div>
    </div>

    <ul>
        <?php foreach( $tide_info->get_tide_entries() as $tide ) { ?>
        <li style="position: relative;" >
        <div style="width: <?php get_relative_width( $tide['height'] ); ?>; background: #EEE; height: 40px; position: absolute; z-index: 1;"></div>
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
