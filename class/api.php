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
        $args = [
            'slug' => in('forum'),
            'posts_per_page' => 10,
            'paged' => 5,
        ];
        $q = get_posts($args);
        wp_send_json_success( $q );
    }
}

function api()  {
    return new api();
}