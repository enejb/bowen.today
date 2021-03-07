<?php
namespace NextBus;

require_once dirname( __FILE__ ) . "../../simplehtmldom_1_9_1/simple_html_dom.php";
require_once dirname( __FILE__ ) . "../../utils/time/index.php";
require_once dirname( __FILE__ ) . "../../utils/cache/index.php";

class Stop {

    private $name = '';
    private $id = '';

    function __construct( $data ) {
        $this->name = $data->name;
        $this->id = $data->id;
        $this->buses = $data->buses;
        $this->bus_id = $data->bus_id;
        $this->bus_number = explode( ':', $data->bus_id )[0];
        $this->name = $data->properties->name;
        $this->properties = $data->properties;
        $this->location = $data->geometry->coordinates;
    }

    /**
     * 
     */
    function get_next_scheduled_bus() {
        
    }

    public function id() {
        return $this->id;
    }

    public function name() {
        return $this->name;
    }

    function get_next_bus_schedules() {
        // http://m.transitdb.ca/nextbus/58018/

        $this->fetch_next_bus();
    }

    public function fetch_next_bus() {
        $url = "http://m.transitdb.ca/nextbus/{$this->id}/?show=50";
        $html = file_get_html( $url );
        
        // Find the links...
        $table = $html->find( 'table' , 0 );

        $tableData = []; // each item the array is a new row
        $column_duplicate_check = '';
        foreach ( $table->find('tr') as $row ) {
            // initialize array to store the cell data from each row
            $columns = [];
            
            $new_column_duplicate = '';
            foreach ( $row->find('td') as $cell ) {
               // Ignore tables with column spans...
               if ( $cell->getAttribute ( 'colspan' ) ) {
                   continue;
               }
               // clean up data
               $column = trim( $cell->plaintext );
                if ( ! empty( $column ) ) {
                    $new_column_duplicate .= $column;
                    $columns[] = $column;
                }
            }
            if ( ! empty( $columns ) && $column_duplicate_check !== $new_column_duplicate ) {
                $column_duplicate_check  = $new_column_duplicate;
                $tableData[] = $columns;
            }
        }
        return $tableData;
    }

    public function get_today_schedule() {
       $timetable = $this->fetch_timetable();
       $today = _date( "l" );
       
       if ( 'Saturday' === $today ) {
        return $timetable['saturday'];
       }
       if ( 'Sunday' === $today ) {
        return $timetable['sunday'];
       }

       return $timetable[ 'weekday' ];
    }

    private function fetch_timetable() {
        $url = "http://m.transitdb.ca/timetable/{$this->bus_number}/{$this->id}/";
        $html = file_get_html( $url );
        
        $tables_map = [ 'weekday' => 1, 'saturday' => 2, 'sunday' => 3 ];
        $tableData = [];
        foreach( $tables_map  as $day => $table_number ) {
            $tableData[ $day ] = $this->get_table_data_from_html( $table_number, $html );
        }

        return $tableData;
    }

    private function get_table_data_from_html( $table_number, $html ) {
        // Find the table.
        $table = $html->find( 'table' , 1 );
        $tableData = [];
        foreach ( $table->find('tr') as $row ) {
             foreach ( $row->find( 'td' ) as $cell ) {
               $list_items = $cell->find( 'li' );
               foreach ( $list_items  as $list_item ) {
                    $column = trim( $list_item->plaintext );
                    if ( ! empty( $column ) ) {
                        $tableData[] = $column;
                    }
               }
            }
        }
        return $tableData;
    }

    // Array of scedules. 
    function get_timetable() { 
        // http://m.transitdb.ca/timetable/281/58018/
        return '';
    }
}