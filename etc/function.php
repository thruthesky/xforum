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
reset_http_query();
/**
 * Merge $_GET, $_POST into $_in
 *
 * Use this function if you change $_GET, $_POST.
 *
 * @note it does not use $_REQUEST
 *
 */
function reset_http_query() {
    global $_in;
    $_in = array_merge( $_GET, $_POST );
}
function in( $name = null, $default = null ) {
    global $_in;
    if ( empty($name) ) return $_in;
    else if ( isset($_in[$name]) ) return $_in[$name];
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
 * @param $code - Code to alert or ajax-return
 * @param $message - Message to alert or ajax-return
 *
 * @attention if $_REQUEST['on_error'] == 'alert_and_go_back', then it alerts and goes previous page.
 *
 *      Or, it echoes json error code and exits.
 *
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



function file_upload() {
    echo <<<EOH
<div class="file-upload">
<form action="http://onfis.com/file-upload/index.php" target="hidden_iframe_file_upload" method="post" enctype="multipart/form-data">
<input type="file" name="userfile" placeholder="Choose file" onchange="submit();">
</form>
</div>
<script>


window.addEventListener("message", receiveMessage, false);
function receiveMessage( re ) {
    alert( re.data.url );


        var url = re.data.url;
        var filename = re.data.filename;
        var m = '<img class="file-upload" alt="'+filename+'" src="'+url+'"/>';

        tinymce.activeEditor.insertContent(m);

}
</script>
<iframe name="hidden_iframe_file_upload" src="javascript:;"></iframe>
EOH;
}

