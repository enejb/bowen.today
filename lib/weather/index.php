<?php 
/**
 * A librarie that returns bowen weather.
 * 
 * todo: Make sure that we escape all the test that we return... ( there should be no HTML here... );
 * 
 */
function get_current_bowen_weather() {
    // 
    $BOWEN_LAT = '49.376765';
    $BOWEN_LON = '-123.370154';

    $WEATHER_API_KEY = defined( 'OPENWEATHERMAP_API_KEY' ) ? OPENWEATHERMAP_API_KEY: '';
    $api_reponse = file_get_contents( "http://api.openweathermap.org/data/2.5/onecall?lat={$BOWEN_LAT}&lon={$BOWEN_LON}&units=metric&appid={$WEATHER_API_KEY}" );

    $args = json_decode( $api_reponse );
    return new Current_Weather( $args );
}

class Current_Weather {
    private $args;

    public function __construct( $args ) {
        $this->args = $args;
    }

    public function temperature() {
        return round( $this->args->current->temp );
    }

    public function temp_feels_like() {
        return round( $this->args->current->feels_like );
    }

    public function presure() {
        return $this->args->current->pressure;
    }

    public function humidity() {
        return $this->args->current->humidity;
    }

    public function visibility() {
        return $this->args->current->visibility; // 
    }

    public function wind_speed() {
        return round( $this->args->current->wind_speed, 2 );
    }

    public function wind_direction() {
        $degrees = $this->args->current->wind_deg;
        $directions = array('N','NNE','NE','ENE','E','ESE','SE','SSE','S','SSW','SW','WSW','W','WNW','NW','NNW','N2');
        $cardinal = $directions[round($degrees / 22.5)];
        if ( $cardinal == 'N2' ){ 
            $cardinal = 'N';
        }
        return $cardinal;
    }

    public function icon() {
        $icon = $this->args->current->weather[0]->icon;
        return "https://openweathermap.org/img/wn/{$icon}@2x.png";
    }

    public function text() {
        return $this->args->current->weather[0]->main;
    }

    public function description() {
        return $this->args->current->weather[0]->description;
    }

    public function sunrise() {
        $date = new \DateTime( $this->args->current->sunrise, new \DateTimeZone( $this->args->timezone ) );
        return $date;
    }

    public function sunset() {
        $date = new \DateTime($this->args->current->sunset, new \DateTimeZone( $this->args->timezone ) );
        return $date;
    }
}
