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



/**
 * Admin can only edit the text. so it lets the admin to use css and javascript.
 * @param $str
 * @return void
 */
function _text($str) {
    $md5 = md5($str);
    $option_name = get_text_translation_option_name( $md5 );
    $org = esc_html($str);

    if ( !isset($_COOKIE['site-edit']) || $_COOKIE['site-edit'] != 'Y' || ! user()->admin() ) {
        $str = _getText($str, true);
        echo $str;
    }
    else {
        $str = _getText($str);
        echo "
<div class='translate-text' md5='$md5' original-text='$org' code='$option_name'><span class='dashicons dashicons-welcome-write-blog'></span>
<div class='html-content'>$str</div>
</div>
";
    }

}
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
}
function get_text_translation_option_name($md5) {
    return get_text_translation_option_name_prefix() . $md5;
}

function get_text_translation_option_name_prefix() {
    $domain = get_opt('xforum[domain]', 'default');
    return 'translation-' . $domain . '-';
}

function _getText($str, $convert=false) {
    $md5 = md5($str);
    $option_name = get_text_translation_option_name( $md5 );
    $data = get_option( $option_name );
    $org = esc_html($str);
    if ( empty($data) ) $str = $org;
    else {
        $content = null;
        if ( isset($data['content']) ) $content = trim($data['content']);
        if ( empty($content) ) $str = $org;
        else $str = $data['content'];
    }

    if ( $convert ) {
        $str = convert_text_var( 'company name', 'company_name', $str );
        $str = convert_text_var( 'company address', 'company_address', $str );
        $str = convert_text_var( 'phone number', 'phone_number', $str );
        $str = convert_text_var( 'manager name', 'manager_name', $str );
        $str = convert_text_var( 'email', 'email', $str );
        $str = convert_text_var( 'skype', 'skype', $str );
        $str = convert_text_var( 'kakaotalk', 'kakaotalk', $str );
        $str = convert_text_var( 'bank', 'bank', $str );
    }


    return $str;
}

/**
 * Echoes the return value of 'get_opt'
 * @param $name
 * @param null $default
 * @param bool $escape
 * @code
 * <?php opt('lms[logo]', 'img/logo.jpg')?>
 * @endcode
 */
function opt($name, $default=null, $escape = true) {
    echo get_opt($name, $default, $escape);
}
/**
 *
 * Returns option value
 *
 * @param $name - is option name. It can be an element of array. like "abc[def]"
 * @param null $default - is the default value which will be returned if the value of the option name is empty.
 * @param bool $escape
 * @return mixed|null|void
 * @code
 *  echo opt('abc', 'def');
 *  echo opt('lms[logo]', 'img/logo.jpg');
 * @endcode
 *
 * @code
 *      "option('lms', 'company_name')" can be converted into "opt('lms[company_name]')"
 *      "get_option( 'lms' );" can be converted into "get_opt('lms')"
 * @endcode
 */
function get_opt($name, $default=null, $escape = true) {


    $value = null;
    if ( strpos( $name, '[' ) ) {
        list( $name, $rest ) = explode( '[', $name );
        $element = trim($rest, ']');
        $arr = get_option( $name );
        if ( isset( $arr[$element] ) ) $value = $arr[$element];
    }
    else {
        $value = get_option( $name );
    }


    if ( empty($value) ) $value = $default;

    if ( $escape ) $value = esc_attr( $value );

    return $value;
}
function convert_text_var($text_var, $option_name, $str) {
    if ( stripos( $str, "($text_var)") !== false ) {
        $v = get_opt("xforum[$option_name]");
        $str = str_ireplace("($text_var)", $v, $str);
    }
    return $str;
}