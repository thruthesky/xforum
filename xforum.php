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
include DIR_XFORUM . 'etc/function.php';
include_once DIR_CLASS . 'library.php';
include_once DIR_CLASS . 'forum.php';
include_once DIR_CLASS . 'post.php';



xlog("xforum.php begins on " . date("H:i:s") . ' -----------------------');
if ( in('test') ) { // must be here
    include DIR_XFORUM . 'test/main.php';
    xlog("xforum.php ends -----------------------");
    exit;
}
else {
    include DIR_XFORUM . 'etc/action.php';
    xlog("xforum.php ends -----------------------");
}













