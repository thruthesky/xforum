<?php
include DIR_XFORUM . 'etc/admin-function.php';
if ( in('template') ) $template = in('template').'.php';
else $template = 'adminForumList.php';

wp_enqueue_style( 'font-awesome', URL_XFORUM . 'css/font-awesome/css/font-awesome.min.css' );
wp_enqueue_style( 'bootstrap', URL_XFORUM . 'css/bootstrap/css/bootstrap.min.css');

include $template;


