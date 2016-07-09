<?php


/**
 *
 * @todo change 'do' to 'forum'.
 */
add_action('init', function() {
    if ( in('do') || in('forum') ) forum()->submit();
//    wp_enqueue_script( 'wp-util' );
    wp_enqueue_script('jquery3', URL_XFORUM . 'js/jquery-2.2.4.min.js', array(), false, true );
    wp_enqueue_script('underscore', URL_XFORUM . 'js/underscore-1.8.3.min.js', array('jquery3'), false, true);
    wp_enqueue_script('underscore-string', URL_XFORUM . 'js/underscore.string.min.js', array('jquery3', 'underscore'), false, true);
    wp_enqueue_style( 'font-awesome', URL_XFORUM . 'css/font-awesome/css/font-awesome.min.css' );
    wp_enqueue_style( 'bootstrap', URL_XFORUM . 'css/bootstrap/css/bootstrap.min.css');
    wp_enqueue_script( 'tether', URL_XFORUM . 'css/bootstrap/js/tether.min.js', array(), false, true );
    wp_enqueue_script( 'bootstrap', URL_XFORUM . 'css/bootstrap/js/bootstrap.min.js', array('jquery3'), false, true );
    wp_enqueue_script( 'xforum', URL_XFORUM . 'js/forum.js', array(), false, true);
});

add_action('wp_head', function() {
    $home_url = home_url();
    $write_url = forum()->getUrlWrite();

    echo <<<EOH
<script>
var home_url="$home_url";
var xforum_write_url="$write_url";
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
