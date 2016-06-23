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


/**
 * @deprecated use forum()->url...
 */
function url_forum_create() {           echo get_url_forum_create(); }

/**
 * @deprecated use forum()->url...
 */
function get_url_forum_create() {       return forum()->urlForumCreate(); }

/**
 * @deprecated use forum()->url...
 */
function url_admin_page() {           echo get_url_admin_page(); }

/**
 * @deprecated use forum()->url...
 */
function get_url_admin_page() {       return forum()->urlAdminPage(); }


/**
 * It echoes / displays / alerts / goes back depending on the input.
 *
 * @param $code
 * @param $message
 *
 * @note if in('on_error') == 'alert_and_go_back', it alerts error and go back.
 */
function ferror( $code, $message ) {

    if ( in('on_error') == 'alert_and_go_back' ) {
        echo <<<EOH
            <script>
            alert("ERROR: $code, $message");
            history.go(-1);
            </script>
EOH;
        exit;
    }
    else {
        wp_send_json_error(['code'=>$code,'message'=>$message]);
    }
}


/**
 * Alerts a message in JS and go back & Exits the script.
 * @param $msg
 */
function jsBack($msg) {
    echo <<<EOH
        <script>
            alert("$msg");
            history.go(-1);
        </script>
EOH;
    exit;
}