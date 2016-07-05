<?php
/**
 * Plugin Name: X Forum
 * Plugin URI: http://it.philgo.com
 * Author: JaeHo Song
 * Description: This is X Forum.
 * Version: 0.0.1
 *
 *
 *
 */


// defines
define( 'FILE_XFORUM', __FILE__ );
define( 'DIR_XFORUM', plugin_dir_path( __FILE__ ) );
define( 'URL_XFORUM',  plugin_dir_url( __FILE__ ) );
define( 'DIR_CLASS',  DIR_XFORUM . 'class/' );
define( 'FORUM_CATEGORY_SLUG',  'xforum' );
include_once DIR_XFORUM . 'etc/function.php';
include_once DIR_CLASS . 'library.php';
include_once DIR_CLASS . 'forum.php';
include_once DIR_CLASS . 'post.php';
include_once DIR_CLASS . 'user.php';
include_once DIR_XFORUM . 'etc/action.php';
include_once DIR_XFORUM . 'etc/filter.php';
include_once DIR_XFORUM . 'etc/config.php';
include_once DIR_XFORUM . 'etc/init.php';

//wp_set_password( '1111', 1 );



xlog("xforum.php begins on " . date("H:i:s") . ' -----------------------');
xlog( in() );
if ( in('test') ) {
    add_action('wp_loaded', function() {
        include DIR_XFORUM . 'test/main.php';
    });
}
else if ( $script = in('script') ) {
    add_action('wp_loaded', function() use ( $script) {
        include DIR_XFORUM . "script/$script.php";
        xlog("xforum.php ends -----------------------");
        exit;
    });
}
else {

}
xlog("xforum.php ends -----------------------");













