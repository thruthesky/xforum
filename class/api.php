<?php

class api {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
    }

    public function ping() {
        wp_send_json_success( ['data' => ['pong'=>time()] ] );
    }

    public function get_categories() {
        wp_send_json_success( get_categories() );
    }

    public function post_list() {
        forum()->setCategory( in('slug') );
        $args = [
            'cat' => forum()->term_id,
            'posts_per_page' => 10,
            'paged' => 5,
        ];

        $ars = $args;
        $in = in();
        $category = forum()->getCategory();
        $posts = get_posts($args);
        wp_send_json_success( [
            'in' => $in,
            'category' => $category,
            'args' => $args,
            'posts' => $posts
        ] );
    }
}

function api()  {
    return new api();
}