<?php 
namespace NextBus;

require_once dirname( __FILE__ ) . "../../simplehtmldom_1_9_1/simple_html_dom.php";
require_once dirname( __FILE__ ) . "../../utils/time/index.php";
require_once dirname( __FILE__ ) . "../../utils/cache/index.php";

class Route {
    
    private $in_memory_cache = [];

    private $id = null;
    private $name = null;
    private $from = null;
    private $to = null;
    private $outbound = null;      
    private $retun_id = null;


    public function __construct( $data ) {
        $this->id = $data['id'];
        $this->bus_number = $data['bus_number'];
        $this->name = $data['name'];
        $this->from = $data['from'];
        $this->to = $data['to'];
        $this->outbound = $data['outbound'];        
        $this->retun_id = $data['retun_id'];
    }

    public function id() {
        return $this->id;
    }

    public function to(){
        return $this->to;
    }

    public function from(){
        return $this->from;
    }

    public function bus_number(){
        return $this->bus_number;
    }

    public function opposite_direction() {
        return $this->retun_id;
    }

    /**
     * Grab the route infos.
     * 
     * returns array of route objects.
     */
    public function get_stops() {
        // 
        // http://m.transitdb.ca/routes/281/O/
        $stops = $this->fetch_stops( $this->get_route_url() );
 
        return array_map( function( $stop ) { return new Stop( $stop ); }, (array)$stops );
    }

    private function fetch_stops( $url ) {

        $stops = \Cache::get( $url );
        if ( $stops ) {
            return $stops;
        }
        $html = file_get_html( $url );
        
        // Find the links...
        $list_links = $html->find( 'ol li a');

        $stops = []; // each item the array is a new row
    
        foreach( $list_links as $link ) {
            $div = $link->first_child();
            $id = explode( '/', $link->href )[2];
            
            $buses_serving = $div->first_child()->plaintext;
            $stop_name = $div->last_child()->plaintext;

            $buses = [ $this->bus_number ];
            if ( ! empty(  $buses_serving ) ) {
               $buses_string = substr( $buses_serving, 1, -1 );
               $buses = explode( ',', $buses_string );
            }
            $busses_serving = $link->find( 'span.routes_serving' )->plaintext;

            $stops[ $id ] = (object) [ 'id' => $id, 'name' => $stop_name, 'buses' => $buses, 'bus_id' => $this->id ];    
        }

        $stops = $this->get_stop_coordianates( $stops );
        // Store
        \Cache::set( $url, $stops );

        return $stops;
    }

    private function get_stop_coordianates( $stop_ids ) {
        
        $api_reponse = file_get_contents( "https://raw.githubusercontent.com/carsonyl/translink-derived-datasets/master/datasets/stops.geojson" );
        
        $stops = json_decode( $api_reponse );
        $stop_coordinates = [];
        foreach ( $stops->features as $stop ) {
            if ( isset( $stop_ids[ $stop->properties->code ] ) ) {
                $stop_ids[ $stop->properties->code ]->geometry = $stop->geometry;
                $stop_ids[ $stop->properties->code ]->properties = $stop->properties;
            };
        }

        return $stop_ids;
    }

    private function get_route_url() {
        $direction = $this->outbound ? 'O' : 'I';
        return "http://m.transitdb.ca/routes/{$this->bus_number}/{$direction}/";
    }

    public function get_stop( $id ) {
        $stops = $this->get_stops();
        if ( isset( $stops[ $id ] ) ) {
            return $stops[ $id ];
        }
        return false;
    }


    public function get_direction() {

    }
}
