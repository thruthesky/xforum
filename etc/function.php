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


function _getUserLanguage() {

    /**
     * @todo check if the user chosen a language.
     */


    return get_browser_language();
}


/**
 *
 * Returns the language code of browser in two letter. ie) 'en', 'ko', 'jp', 'cn', etc...
 *
 */
function get_browser_language()
{
    return substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}




/**
 * Admin can only edit the text. so it lets the admin to use css and javascript.
 * @param $str
 * @param null $language - language to get from database.
 */
function _text($str, $language = null) {
    $org = esc_html($str);
    if ( empty($language) ) $language = _getUserLanguage();
    $option_name = _getLanguageCode($language, $str);


    if ( user()->admin() && isset($_COOKIE['site-edit']) && $_COOKIE['site-edit'] == 'Y' ) {
        $str = _getText($str, $option_name);
        echo "
<div class='translate-text' original-text='$org' code='$option_name'><i class='fa fa-pencil-square-o' aria-hidden='true'></i>
<div class='html-content'>$str</div>
</div>
";
    }
    else {
        $str = _getText($str, $option_name);
        echo $str;
    }


}

/**
 * @param $dir
 * @param bool $re
 * @param null $pattern
 * @return array
 */
function getFiles($dir, $re=true, $pattern=null)
{
    $tmp = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $file_path = $dir . DIRECTORY_SEPARATOR . $file;
                if ( is_dir($file_path) ) {
                    if ( $re ) {
                        $tmp2 = getFiles($file_path, $re, $pattern);
                        if ( $tmp2 ) $tmp = array_merge($tmp, $tmp2);
                    }
                }
                else {
                    if ( $pattern ) {
                        if ( preg_match($pattern, $file) ) {
                        }
                        else continue;
                    }
                    array_push($tmp, $dir . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($handle);
        return $tmp;
    }
    return $tmp;
}


function _getText($str, $option_name) {
    $data = get_option( $option_name );
    $org = esc_html($str);
    if ( empty($data) ) $str = $org;
    else {
        $content = null;
        if ( isset($data['content']) ) $content = trim($data['content']);
        if ( empty($content) ) $str = $org;
        else $str = $data['content'];
    }
    return $str;
}



function _getLanguageCode( $language, $str ) {
    return $language . '-' . md5($str);
}

    
