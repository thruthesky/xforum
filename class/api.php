<?php

class api {
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
    }

    public function ping() {
        wp_send_json_success( [
            'pong'=>time(),
            'server_name' => $_SERVER['SERVER_NAME'],
            'sever_addr' => $_SERVER['SERVER_ADDR']
        ] );
    }


    /**
     * Return WP_User object if the user logged in. Otherwise false will be returned.
     *
     * 
     */
    public function login() {
        if ( $session_id = in('session_id') ) {
            $user_id = user()->check_session_id( $session_id );
            if ( $user_id ) {
                return user( $user_id );
            }
        }
        return false;
    }


    /**
     * @attention There is no need to logout on server side.
     * For, xapp, it only need to delete session_id on client side.
     */
    public function logout() {
        
    }

    public function get_categories() {
        wp_send_json_success( get_categories() );
    }


    /**
     *
     * @note it adds author's nicename to 'author_name' property.
     * @note post meta data will be added as post property.
     *
     *      ( post meta 키가 post 속성으로 바로 추가 된다. 예: post->content_type )
     *
     *
     */
    public function post_list() {
        forum()->setCategory( in('slug') );
        $args = [
            'cat' => forum()->term_id,
            'posts_per_page' => in('posts_per_page', 10),
            'paged' => in('page'),
        ];
        $in = in();
        $category = forum()->getCategory();
        $posts = get_posts($args);
        foreach( $posts as $post ) {
            if ( $post->post_author ) {
                $user = get_user_by( 'id', $post->post_author );
                $post->author_name = $user->user_nicename;
                $meta = get_post_meta( $post->ID );
                foreach( $meta as $k => $arr ) {
                    $post->$k = $arr[0];
                }
                $post->comments = comment()->get_nested_comments_with_meta( $post->ID );
            }
        }
        wp_send_json_success( [
            'in' => $in,
            'file_server_url' => get_option('xforum_url_file_server'),
            'category' => $category,
            'args' => $args,
            'posts' => $posts
        ] );
    }


    public function kakao_login() {
        $user_id = user()->check_session_id( in('session_id') );
        if ( empty( $user_id ) ) wp_send_json_error(['code'=>-100500, 'message'=>'Wrong session id.']);
        $user = user( $user_id );
        if ( ! $user->exists() ) wp_send_json_error(['code'=>-100400, 'message'=>'User does not exists by that session id']);
        if ( !in('kakao_id') || !in('kakao_nickname') ) wp_send_json_error(['code'=>-100404, 'message'=>'Kakao information does not exists.']);
        $user->kakao_id = in('kakao_id');
        $user->kakao_nickname = in('kakao_nickname');
        wp_send_json_success();
    }

    public function page() {
        $page_name = in('name');
        include DIR_XFORUM . 'template/api/' . $page_name . '.php';

    }
}

function api()  {
    return new api();
}