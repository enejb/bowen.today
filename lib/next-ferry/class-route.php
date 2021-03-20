<?php 
namespace NextFerry;

const BC_FERRY_URL = "https://www.bcferries.com";
const BC_TIMEZONE = 'America/Vancouver';
const TIME_FORMAT = 'g:i A';
const DATE_FORMAT = 'Y-m-d';
const DAY_IN_SECONDS = 86400;

require_once dirname( __FILE__ ) . "../../simplehtmldom_1_9_1/simple_html_dom.php";
require_once dirname( __FILE__ ) . "../../utils/time/index.php";

class Route {
    
    private $in_memory_cache = [];

    function __construct( $data ) {
        $this->id = $data['id'];
        $this->to = $data['to'];
        $this->from = $data['from'];
        $this->route_slug = $data['slug'];
        $this->has_current_conditions = $data['current'];
        $this->route_map = $data['map'];
    }

    private function current_conditions_data( $timestamp ) {
        return $this->has_current_conditions && $this->is_same_day( $timestamp );
    }

    public function get_next_departures_time( $timestamp ) {
        if ( $this->current_conditions_data( $timestamp ) ) {
            return $this->get_next_schedule_departure( $timestamp, $this->get_current_conditions_url() );
        }
        return $this->get_next_schedule_departure( $timestamp, $this->get_schedule_url( $timestamp ) );
    }

    public function get_api_reponse( $timestamp = null ) {
    
        $timestamp?? time();

       $departures = $this->get_remaining_departures_times_formated( $timestamp );
       $next_departure = array_shift( $departures );
       
       if ( empty( $next_departure ) ) {
            $next_departure = 'unknown';
       }
    
        return [
            'id' => $this->id,
            'to' => $this->to,
            'from' => $this->from,
            'current_time' => $this->_date( DATE_RFC822, $timestamp, BC_TIMEZONE ),
            'has_availibility' => $this->current_conditions_data( $timestamp ),
            'availability' => $this->get_availability( $timestamp ),
            'next_departure' => $next_departure,
            'remaining_departures' => $departures,
            'current_map' => $this->get_map_url( $timestamp ),
        ];
    }

    public function get_map_url( $timestamp ) {
        if ( $this->is_same_day( $timestamp ) && ! empty( $this->route_map ) ) {
            return 'https://apigateway.bcferries.com/api/currentconditions/1.0/images/vessels/' . $this->route_map . '.jpg';
        }
        return null;
    }

    public function get_remaining_departures_times( $timestamp ) {
        if ( $this->current_conditions_data( $timestamp ) ) {
            $next_scheduled = $this->get_next_schedule_departure_times( $timestamp, $this->get_current_conditions_url() );
        } else {
            $next_scheduled = $this->get_next_schedule_departure_times( $timestamp, $this->get_schedule_url( $timestamp ) );
        }
        
        return $next_scheduled;
    }

    public function has_current_conditions() {
        return $this->has_current_conditions;
    }

    public function get_availability( $timestamp ) {
        if ( ! $this->current_conditions_data( $timestamp ) ) { 
            return 'unknown';
        }

        $schedules_data = $this->fetch_raw_data( $this->get_current_conditions_url(), $timestamp );

        $date = null;
        if ( ! $this->is_same_day( $timestamp ) ) {
            $date = $this->_date( DATE_FORMAT, time() + DAY_IN_SECONDS, BC_TIMEZONE );
        }
        
        foreach ( $schedules_data as $row  ) {
            // the first column of the row is probably what we want in this case.
            if ( $this->starts_with_number( $row[0] ) ) {
             
                // Current data array contains tomorrows departure times.
                if ( $pos = strpos( $row[0], '(Tomorrow)' ) ) {
                    $departure_time = $this->str_to_date_object( trim( substr( $row[0], 0, $pos ) ), $this->_date( DATE_FORMAT, time() + DAY_IN_SECONDS, BC_TIMEZONE ) );
                } else {
                    $departure_time = $this->str_to_date_object( $row[0], $date );
                }
                
                if ( $timestamp >= $departure_time->format( 'U' ) ) {
                   continue;
                }
             
                // This assumes that the date in the table is always in the ascending order.
                return $this->format_percentage( $row[1] );
            }
        }
    }

    private function get_remaining_departures_times_formated( $timestamp ) {
        $departures = $this->get_remaining_departures_times( $timestamp );
        $formated_departured = [];
        foreach ( $departures as $departure ) {
            $formated_departured[] = $departure->format( DATE_RFC822 );
        } 
        return $formated_departured;
    }

    private function get_schedule_url( $timestamp ) {
        if ( $this->is_same_day( $timestamp ) ) {
            return BC_FERRY_URL . '/routes-fares/schedules/' . $this->route_slug ; 
        }
        return BC_FERRY_URL . '/routes-fares/schedules/' . $this->route_slug . '?scheduleDate=' . $this->_date( 'm/d/Y', $timestamp, BC_TIMEZONE );      
    }
    /**
     * Figures out if the current url exists. 
     */
    private function get_current_conditions_url() {
        return BC_FERRY_URL . '/current-conditions/' . $this->route_slug;
    }
    /**
     * FIgures out if we are on the same day.
     */
    private function is_same_day( $timestamp ) {
        return $this->_date( DATE_FORMAT, $timestamp, BC_TIMEZONE ) === $this->_date( DATE_FORMAT, time(), BC_TIMEZONE );
    }

    /**
     * Get the list of next departures.
     */
    private function get_next_schedule_departure_times( $timestamp, $url , $next_day_departures = true ) {
        // We don't have the data yet lets fetch it. 
        $schedules_data = $this->fetch_raw_data( $url, $timestamp );
        $next_schedules_departures =  $this->get_next_schedule_departure_times_from_schedule( $schedules_data, $timestamp );

        if ( count( $next_schedules_departures ) < 4 && $next_day_departures ) {
            // skip 4 hours 
            $next_day_scheduled = $this->get_next_schedule_departure_times( $timestamp + DAY_IN_SECONDS, $this->get_schedule_url( $timestamp ), false );
             
            $remainder = ( 3 - count( $next_schedules_departures ) );
            // This makes sure that we always display some info             
            return array_merge( $next_schedules_departures, array_slice( $next_day_scheduled, 0, $remainder ) );

        }
        return $next_schedules_departures;
    }
    
    /**
     * This function retuns an array of next depattures not just the next one.
     */
    private function get_next_schedule_departure_times_from_schedule( $schedules_data, $timestamp = null ) {
        if ( ! $timestamp ) {
            $timestamp = time(); // now
        }
        $date = null;
        if ( ! $this->is_same_day( $timestamp ) ) {
            $date = $this->_date( DATE_FORMAT, $timestamp, BC_TIMEZONE );
        }
        $next_departure_times = [];
        foreach ( $schedules_data as $row  ) {
            // the first column of the row is probably what we want in this case.
            if ( $this->starts_with_number( $row[0] ) ) {
                // Current data array contains tomorrows departure times.
                if ( $pos = strpos( $row[0], '(Tomorrow)' ) ) {
                    $departure_time = $this->str_to_date_object( trim( substr( $row[0], 0, $pos ) ), $this->_date( DATE_FORMAT, time() + DAY_IN_SECONDS, BC_TIMEZONE ) );
                } else {
                    $departure_time = $this->str_to_date_object( $row[0], $date );
                }
                
                if ( $timestamp >= $departure_time->format( 'U' ) && $this->is_same_day( $timestamp )) {
                   continue;
                }
                // This assumes that the date in the table is always in the ascending order.
                $next_departure_times[] = $departure_time;
            }
        }

        if ( ! empty( $schedules_data ) && count( $next_departure_times ) < 4 ) {
            // Lets grab the next days $departure times so that 

        }

        return $next_departure_times;
    }

    private function get_expiry_time( $timestamp ) {
        if ( $this->current_conditions_data( $timestamp ) ) {
            return 120; // Lets keep not fetch the date for more then every 2 minutes.
        } 
        if( $this->is_same_day( $timestamp ) ) { 
            return 300; // Lets keep it for 5 minutes
        }
    
        return DAY_IN_SECONDS; // if we are looking at data for the next day lets keep it.
    }
    /**
     * Fetches the raw table data with minimal clean up.
     * And strores it in memory cache so that we can get it quickly again if we need it.
     * 
     */
    private function fetch_raw_data( $url, $timestamp ) {
     
        if ( isset( $this->in_memory_cache[ $url ] ) ) { 
            // We could be more aggressive here since the scheduled data doesn't change that often
            // but the current data changes every minute. 
            return $this->in_memory_cache[ $url ];
        }

        $tableData = \Cache::get( $url, $this->get_expiry_time( $timestamp ) );
        if ( $tableData ) {
            $this->in_memory_cache[ $url ] = $tableData;
            return $tableData;
        }

        $html = file_get_html( $url );

        // Bowen Times are the first table.
        // todo: This files pretty hard if ther tabel doesn't exists.
        $table = $html->find( 'table' , 0 );

        $tableData = []; // each item the array is a new row

        foreach ( $table->find('tr') as $row ) {
            // initialize array to store the cell data from each row
            $columns = [];
            foreach( $row->find('td') as $cell ) {
               // Ignore tables with 3 column spans...
               if ( $cell->getAttribute ( 'colspan' ) == '3' ) {
                   continue;
               }
               // clean up data
               $column = trim( $cell->plaintext );
                if ( ! empty( $column ) ) {
                    $columns[] = $column;
                }
            }
            if ( ! empty( $columns ) ) {
                $tableData[] = $columns;
            }
        }

        $this->in_memory_cache[ $url ] = $tableData;
        \Cache::set( $url, $tableData );

        return $tableData;
    }
    // Utility functions. This could be in its own class or file.
    /**
     * This function take a time string in the and converst it to a nice date object. 
     * 
     * This works much better then strtotime() since we can specificy the timezone right away.
     * 
     */
    private function str_to_date_object( $time_string, $date_string = null ) {
        if ( $date_string ) {
            return \DateTime::createFromFormat( DATE_FORMAT . ' ' . TIME_FORMAT, $date_string . ' ' . $time_string, new \DateTimeZone( BC_TIMEZONE ) );
        }
        return \DateTime::createFromFormat( TIME_FORMAT, $time_string, new \DateTimeZone( BC_TIMEZONE ) );
    }
    /**
     * Hepr function to detemine if we are dealing with a number...
     */
    private function starts_with_number( $string ) {
        return strlen( $string ) > 0 && ctype_digit( substr( $string, 0, 1 ) );
    }

    /**
     * A helpful function that takes a timestamp and formats in the correct timezone.
     */
    private function _date( $format = "r", $timestamp = false, $timezone = false ) {
        return \_date( $format, $timestamp, $timezone );
    }
    /**
     * Helper function to get back just the percentage number.
     *  '90% Available' => '0.90'
     */
    private function format_percentage( $percentage_string ) {
        return '0.' . \str_replace( '% Available', '', $percentage_string ); 
    }

}