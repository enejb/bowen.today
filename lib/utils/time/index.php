<?php 

define( 'BC_TIMEZONE', 'America/Vancouver' );
define( 'DATE_FORMAT', 'Y-m-d' );

function _date( $format = "r", $timestamp = false, $timezone = false ) {
    $userTimezone = new \DateTimeZone( !empty( $timezone ) ? $timezone : BC_TIMEZONE );
    $gmtTimezone = new \DateTimeZone( 'GMT' );
    $myDateTime = new \DateTime( ( $timestamp != false ? date( "r", (int) $timestamp) : date("r") ), $gmtTimezone );
    $offset = $userTimezone->getOffset( $myDateTime );
    return date( $format, ( $timestamp != false ? (int) $timestamp : $myDateTime->format('U') ) + $offset );
}

function is_same_day( $date, $previous_day = null ) {
    if ( empty( $previous_day ) ) {
        $previous_day = time();
    }
    return _date( DATE_FORMAT, $date, BC_TIMEZONE ) === _date( DATE_FORMAT, $previous_day, BC_TIMEZONE );
}