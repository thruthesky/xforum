<?php

/**
 *
 * @note By default it returns null if the key does not exist.
 *
 * @param $name
 * @param null $default
 * @return null
 *
 */
function in( $name, $default = null ) {
    if ( isset( $_POST[$name] ) ) return $_POST[$name];
    else if ( isset( $_GET[$name] ) ) return $_GET[$name];
    else return $default;
}




/**
 * Leaves a log message on WordPress log file on when the debug mode is enabled on WordPress. ( wp-content/debug.log )
 *
 * @param $message
 */
function xlog( $message ) {
    static $count_log = 0;
    $count_log ++;
    if( WP_DEBUG === true ){
        if( is_array( $message ) || is_object( $message ) ){
            $message = print_r( $message, true );
        }
        else {

        }
    }
    $message = "[$count_log] $message";
    error_log( $message ); //
}

