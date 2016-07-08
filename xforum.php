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
include_once DIR_CLASS . 'comment.php';
include_once DIR_CLASS . 'user.php';
include_once DIR_XFORUM . 'etc/action.php';
include_once DIR_XFORUM . 'etc/filter.php';
include_once DIR_XFORUM . 'etc/config.php';
include_once DIR_XFORUM . 'etc/init.php';





/**
 * 아래의 filter 는 main query 를 하지 않는다.
 * 단, 글 읽기 페이지에서는 main query 를 한다.
 */
function xforum_remove_main_query( $sql, WP_Query &$wpQuery ) {
    if ( ! is_single() && $wpQuery->is_main_query() ) {
        /* prevent SELECT FOUND_ROWS() query*/
        $wpQuery->query_vars['no_found_rows'] = true;

        /* prevent post term and meta cache update queries */
        $wpQuery->query_vars['cache_results'] = false;

        return false;
    }
    return $sql;
}
add_filter( 'posts_request', 'xforum_remove_main_query', 10, 2 );


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













