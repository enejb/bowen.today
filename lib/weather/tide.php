<?php

require_once LIB_PATH . '/utils/time/index.php';

/**
 * A librarie that returns bowen weather.
 *
 * todo: Make sure that we escape all the test that we return... ( there should be no HTML here... );
 *
 */
function get_current_bowen_tide() {
    //
    // Use the Gibsons location now.
    $api_reponse = file_get_contents( "https://apps.qedsystems.ca/weather/getTides?station=GIBSONS" );

    $args = json_decode( $api_reponse );
    return new Current_Tide( $args );
}

class Current_Tide {
    private $args;

    private $enties = [];
    private $previous_tide = [];
    private $next_tide = [];
    private $min_tide = null;
    private $max_tide = null;

    public function __construct( $args ) {
        $this->args = $args;
        $this->set_tide_info( $args );
    }

    private function set_tide_info( $args ) {
      $tide_entry = [];
      $previous_entry = [];
      $now = time();

      foreach ( $this->args->tide_entries as $entry ) {
          $date_time = DateTime::createFromFormat( 'Y-m-d\TH:i:s e', $entry->time .' PST' );

          $tide_entry = [
            'time'          => $date_time,
            'height'        => $entry->height_m,
            'type'          => $this->get_tide_type( $entry->height_m , $previous_entry ),
            'different_day' => $this->is_different_date( $date_time->format( 'U' ), $previous_entry ),
          ];

          if ( $tide_entry[ 'time' ]->format( 'U' ) < $now ) {
            $this->previous_tide = $tide_entry;
          } else if ( empty( $this->next_tide ) ){
            $this->next_tide = $tide_entry;
          }

          if ( $this->min_tide === null || $this->min_tide > $tide_entry['height'] ) {
            $this->min_tide = $tide_entry['height'];
          }

          if ( $this->max_tide === null || $this->max_tide < $tide_entry['height'] ) {
            $this->max_tide = $tide_entry['height'];
          } 

          $previous_entry = $tide_entry;
          $this->enties[] = $tide_entry;
      }
      
      // Back fill the first entry.
      $this->enties[0]['type'] = $this->enties[1]['type'] == 'High' ? 'Low' : 'High';

      // Correct previous tide.
      if ( $this->enties[0]['time']->format( 'U' ) == $this->previous_tide['time']->format( 'U' ) ) {
        $this->previous_tide['type'] = $this->enties[0]['type'];
      }
    }

    private function is_different_date( $current_time, $previous_entry ) {
      // return false
      if ( empty( $previous_entry ) ) {
        return false;
      }

      return ! is_same_day( $current_time, $previous_entry['time']->format( 'U' ) );
    }

    private function get_tide_type( $height, $previous_entry ) {
      if ( empty( $previous_entry ) ) {
        return null;
      }

      if ( $height > $previous_entry['height'] ) {
        return 'High';
      }
      return 'Low';
    }

    public function get_previous_tide() {
      return $this->previous_tide;
    }

    public function get_next_tide() {
      return $this->next_tide;
    }

    public function get_tide_entries() {
      return $this->enties;
    }

    public function get_min_tide() {
      return $this->min_tide;
    }

    public function get_max_tide() {
      return $this->max_tide;
    }

    public function get_current_height( $previous_height, $next_height, $previous_timestamp, $next_timestamp ) {
      $mid_point = ( $previous_height + $next_height ) / 2;
      $half_dutation = $previous_timestamp - $next_timestamp;

      $current_time_offset = $previous_timestamp - time();

      $amptitude = ( $previous_height < $next_height )  // low tide
        ? ( $previous_height - $next_height ) / 2
        : ( $next_height - $previous_height ) / 2;

      // this still needs to be varified... 
      return round ( $mid_point + ( $amptitude * cos( ( M_PI * $current_time_offset )  / $half_dutation ) ), 1 );
    }

    public function get_current_movement( $previous_height, $next_height ) {
      if ( $previous_height > $next_height ) {
        return 'falling <svg style="width: 28px; height:28px; margin-bottom: -6px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill-rule="evenodd" d="M4.97 13.22a.75.75 0 000 1.06l6.25 6.25a.75.75 0 001.06 0l6.25-6.25a.75.75 0 10-1.06-1.06l-4.97 4.97V3.75a.75.75 0 00-1.5 0v14.44l-4.97-4.97a.75.75 0 00-1.06 0z"></path></svg>';
      }
      return 'rising <svg style="width: 28px; height:28px; margin-bottom: -6px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill-rule="evenodd" d="M6.47 10.78a.75.75 0 010-1.06l5.25-5.25a.75.75 0 011.06 0l5.25 5.25a.75.75 0 11-1.06 1.06L13 6.81v12.44a.75.75 0 01-1.5 0V6.81l-3.97 3.97a.75.75 0 01-1.06 0z"></path></svg>';
    }

    
}
