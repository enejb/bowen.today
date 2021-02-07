<?php
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
            'time'   => $date_time,
            'height' => $entry->height_m,
            'type'   => $this->get_tide_type( $entry->height_m , $previous_entry ),
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
      $this->enties[0]['type'] = $this->enties[1]['type'] == 'high' ? 'low' : 'high';

      // Correct previous tide.
      if ( $this->enties[0]['time']->format( 'U' ) == $this->previous_tide['time']->format( 'U' ) ) {
        $this->previous_tide['type'] = $this->enties[0]['type'];
      }
      
    }

    private function get_tide_type( $height, $previous_entry ) {
      if ( empty( $previous_entry ) ) {
        return null;
      }

      if ( $height > $previous_entry['height'] ) {
        return 'high';
      }
      return 'low';
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
}
