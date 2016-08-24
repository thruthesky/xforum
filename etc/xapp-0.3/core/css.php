<?php
/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging
 */
error_reporting(0);

$compress = true;
$force_gzip = true;
$expires_offset = 360; // 6 minutes only ; 31536000; // 1 year
$etag_version = $_GET['version'];
$out = '';

if ( ! isset($_GET['debug']) ) {
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) === $etag_version) {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
        if (!in_array($protocol, array('HTTP/1.1', 'HTTP/2', 'HTTP/2.0'))) {
            $protocol = 'HTTP/1.0';
        }
        header("$protocol 304 Not Modified");
        exit();
    }
}


$files = [
    'bootstrap/bootstrap.min-2016-07-13.css',
    'font-awesome/css/font-awesome.min.css',
    'css/main.css',
];




header("Etag: $etag_version");
if ($_GET['compile'] == 'true') {
    header('Content-Type: text/css; charset=UTF-8');
    foreach ( $files as $file ) {
        $out .= "\n/** @file $file */\n" . file_get_contents($file) . "\n";
    }
    $out = str_replace("url('../fonts/", "url('font-awesome/fonts/", $out);
}
else {
    header('Content-Type: application/javascript; charset=UTF-8');
    $out = <<<EOH
function add_css(url){
   var link = document.createElement('link');
   link.setAttribute('rel', 'stylesheet');
   link.setAttribute('type', 'text/css');
   link.setAttribute('href', url);
   document.getElementsByTagName('head')[0].appendChild(link);
}

EOH;

    $path = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $pi = pathinfo( $path );
    $url_dir = $pi['dirname'].'/';
    foreach ( $files as $file ) {
        $out .= "add_css('$url_dir$file?version=$_GET[version]');\n";
    }

}




header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
header("Cache-Control: public, max-age=$expires_offset");

if ( $compress && ! ini_get('zlib.output_compression') && 'ob_gzhandler' != ini_get('output_handler') && isset($_SERVER['HTTP_ACCEPT_ENCODING']) ) {
    header('Vary: Accept-Encoding'); // Handle proxies
    if ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') && function_exists('gzdeflate') && ! $force_gzip ) {
        header('Content-Encoding: deflate');
        $out = gzdeflate( $out, 3 );
    } elseif ( false !== stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && function_exists('gzencode') ) {
        header('Content-Encoding: gzip');
        $out = gzencode( $out, 3 );
    }
}

echo $out;
exit;
