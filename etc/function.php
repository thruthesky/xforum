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
global $_in;
/**
 * Merge $_GET, $_POST into $_in
 *
 * Use this function if you change $_GET, $_POST.
 *
 * @note it does not use $_REQUEST
 *
 * @important @attention @use when you need to change $_GET or $_POST
 *
 * @code
 *          $_POST['response'] = 'list';
 *          reset_http_query();
 * @endcode
 */
function reset_http_query() {
    global $_in;
    $_in = array_merge( $_GET, $_POST );
}

/**
 *
 * @use when you need to add a query variable and its value in HTTP Query Vars programatically.
 *
 * @see forum()->post_delete_submit() for use case.
 *
 * @todo test this code.
 * @param $vars
 * @code
 *              reset_http_query_with(['response'=>'list']);
 * @endcode
 */
function reset_http_query_with( $vars ) {
    foreach ( $vars as $k => $v ) {
        $_POST[$k] = $v;
    }
    reset_http_query();
}
function in( $name = null, $default = null ) {
    global $_in;
    if ( !isset($_in) ) reset_http_query();
    if ( empty($name) ) $re = $_in;
    else if ( isset($_in[$name]) ) $re = $_in[$name];
    else $re = $default;

    return $re;
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
 * It echoes JSON of error message or redirects to $_REQUEST['return_url_on_error'];
 *
 * @param $code - error code
 * @param $message - message.
 *
 * @note use of $_REQUEST['on_error'] has deprecated.
 *
 */
function ferror( $code, $message ) {

    forum()->errorResponse( $code, $message );
    /*
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
    */
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



function file_upload($type='post') {
    include_once DIR_XFORUM . 'template/file-upload.php';
}



function e( $string ) {
    echo esc_html( $string );
}



function _text( $str, $args = null ) {
    if ( $args ) {
        $str = sprintf( $str, $args);
    }
    echo $str;


}