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
    if ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) === $etag_version ) {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
        if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0' ) ) ) {
            $protocol = 'HTTP/1.0';
        }
        header( "$protocol 304 Not Modified" );
        exit();
    }
}

$files = [
    'js/jquery-2.2.4.min.js',
    'js/js.cookie.js',
    'bootstrap/tether.min.js',
    'bootstrap/bootstrap.min-2016-07-13.js',
    'js/underscore.string.min.js',
    'js/underscore-1.8.3.min.js',
    'js/lockr.js',
    'js/locutus.js',
    'js/xapp.js',
    'js/xapp.cache.js',
    'js/xapp.markup.js',
    'js/xapp.wp_query.js',
    'js/xapp.endless.js',
    'js/xapp.function.js',
    'js/xapp.callback.js',
    'js/xapp.element.js',
    'js/xapp.post-list.js',
    'js/xapp.user.js',
    'js/xapp.ready.js',
];


if ($_GET['compile'] == 'true') {
    foreach ( $files as $file ) {
        $out .= "\n/** @file $file */" . file_get_contents($file) . "\n";
    }
}
else {
    $out =<<<EOH
function add_javascript(url) {
   var scriptTag = document.createElement('script');
   scriptTag.src = url;
   document.body.appendChild(scriptTag);
}

EOH;

    $path = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    $pi = pathinfo( $path );
    $url_dir = $pi['dirname'].'/';
    foreach ( $files as $file ) {
        $out .= "add_javascript('$url_dir$file?version=$_GET[version]');\n";
    }

}


header("Etag: $etag_version");
header('Content-Type: application/javascript; charset=UTF-8');
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
