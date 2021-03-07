<?php 

define( 'DAY_IN_SECONDS', 86400 );
/**
 * Simple set of caching functions.
 */
class Cache {

    function get( $key, $expiry = 60 ) { // Cache for one minute.
        $file_path = self::get_file_path( $key );
        if ( file_exists( $file_path ) && filemtime( $file_path ) > time() - $expiry ) { 
            $handle = fopen( $file_path, "r" );
            $content = fread( $handle, filesize( $file_path ) );
            fclose( $handle );
            return json_decode( $content );
        }
        return false;
    }
    
    function set( $key, $content ) {
        $file_path = self::get_file_path( $key );
        $handle = fopen( $file_path, "w" );
        fwrite( $handle, json_encode( $content ) );
        fclose( $handle );
    }
    
    function get_file_path( $key ) {
        $hash = md5( $key );
        return CACHE_PATH . "/{$hash}.cache";
    }
}
