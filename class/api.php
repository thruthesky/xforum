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

}

function api()  {
    return new api();
}