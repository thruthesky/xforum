<?php


/**
 *
 * @todo change 'do' to 'forum'.
 */
add_action('init', function() {
    if ( in('do') || in('forum') ) forum()->submit();
});


add_action('wp_enqueue_scripts', function() {

    wp_deregister_script('jquery');
    wp_register_script('jquery', URL_XFORUM . 'js/jquery-2.2.4.min.js', [], false, true );
    wp_enqueue_script('jquery');

    wp_deregister_script('underscore');
    wp_register_script('underscore', URL_XFORUM . 'js/underscore-1.8.3.min.js', ['jquery'], false, true);
    wp_enqueue_script('underscore');

    wp_enqueue_script('underscore-string', URL_XFORUM . 'js/underscore.string.min.js', ['jquery', 'underscore'], false, true);

    wp_enqueue_script('js.cookie', URL_XFORUM . 'js/js.cookie.js', ['jquery'], false, true );

    wp_enqueue_style( 'font-awesome', URL_XFORUM . 'css/font-awesome/css/font-awesome.min.css' );
    wp_enqueue_style( 'bootstrap', URL_XFORUM . 'css/bootstrap/css/bootstrap.min.css');
    wp_enqueue_script( 'tether', URL_XFORUM . 'css/bootstrap/js/tether.min.js', ['jquery'], false, true );
    wp_enqueue_script( 'bootstrap', URL_XFORUM . 'css/bootstrap/js/bootstrap.min.js', ['jquery', 'tether'], false, true );
    wp_enqueue_script( 'xforum', URL_XFORUM . 'js/forum.js', ['jquery', 'bootstrap'], false, true);

});





/**
 *
 *
 * 아래의 filter 는 main query 를 하지 않는다.
 * 단, 글 읽기 페이지와 관리자 페이지에서는 main query 를 한다.
 */
function xforum_remove_main_query( $sql, WP_Query &$wpQuery ) {
    if ( ! is_admin() && ! is_single() && $wpQuery->is_main_query() ) {
        /* prevent SELECT FOUND_ROWS() query*/
        $wpQuery->query_vars['no_found_rows'] = true;

        /* prevent post term and meta cache update queries */
        $wpQuery->query_vars['cache_results'] = false;

        return false;
    }
    return $sql;
}
add_filter( 'posts_request', 'xforum_remove_main_query', 10, 2 );



/**
 *
 *
 * @see README.md for OG tags.
 */
add_action('wp_head', function() {
    $home_url = home_url();
    $write_url = forum()->getUrlWrite();


    $title = null;
    $permalink = null;
    $og_image = null;
    $og_sitename = get_bloginfo('name');
    if ( is_single() ) {
        $title = esc_attr(get_the_title());
        $permalink = get_the_permalink();
        // @todo get the first image or featured image for the content.
        $og_image = '';
        $description = esc_attr( forum()->getCategory()->description) ;
    }
    else if ( in('forum') == 'list' ) {
        $title = esc_attr(forum()->getCategory()->name);
        $permalink = forum()->getUrlList();
        $description = esc_attr( forum()->getCategory()->description) ;
    }
    else {

    }
    if ( $title ) {
        echo <<<EOH
        <meta property="og:title" content="$title" />
        <meta property="og:url" content="$permalink" />
        <meta property="og:type" content="website" />
        <meta property="og:description" content="$description" />
        <meta property="og:site_name" content="$og_sitename" />
EOH;
        if ( $og_image ) {
            echo<<<EOH
        <meta property="og:image" content="$og_image" />
EOH;
        }
    }


    echo <<<EOH




<script>
var home_url="$home_url";
// var xforum_write_url="$write_url"; // @depcreated.
</script>
EOH;
});

add_action('wp_footer', function(){
    echo <<<EOH
<iframe name="xforum_hidden_iframe" src="javascript:;" width="0" height="0" style="width:0; height:0; display: none;"></iframe>
EOH;

});


add_action( 'wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->add_menu( array(
        'id' => 'xforum_toolbar',
        'title' => __('XForum', 'xforum'),
        'href' => forum()->adminURL()
    ) );
});




add_action('admin_menu', function () {
    add_menu_page(
        __('XForum', 'xforum'), // page title. ( web browser title )
        __('XForum', 'xforum'), // menu name on admin page.
        'manage_options', // permission
        'xforum/template/admin.php', // slug id. what to open
        '',
        'dashicons-text',
        '23.45' // list priority.
    );
    add_submenu_page(
        'xforum/template/admin.php', // parent slug id
        __('Settings', 'xforum'),
        __('Settings', 'xforum'),
        'manage_options',
        'xforum/template/setting.php',
        ''
    );


    add_submenu_page(
        'xforum/template/admin.php', // parent slug id
        __('Text Translation', 'xforum'),
        __('Text Translation', 'xforum'),
        'manage_options',
        'xforum/template/text-translation.php',
        ''
    );



    add_submenu_page(
        'xforum/template/admin.php', // parent slug id
        __('Blog Posting', 'xforum'),
        __('Blog Posting', 'xforum'),
        'manage_options',
        'xforum/template/admin-blog-posting.php',
        ''
    );

    add_submenu_page(
        'xforum/template/admin.php', // parent slug id
        __('Import', 'xforum'),
        __('Import', 'xforum'),
        'manage_options',
        'xforum/template/import.php',
        ''
    );
} );
