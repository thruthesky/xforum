<?php

class api {
    public function __construct()
    {

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
        $_posts = get_posts($args);
        $posts = [];
        foreach( $_posts as $post ) {
            if ( $post->post_author ) {
                $user = get_user_by( 'id', $post->post_author );
                $post->author_name = $user->user_nicename;
                $meta = get_post_meta( $post->ID );
                foreach( $meta as $k => $arr ) {
                    $post->$k = $arr[0];
                }
                $post->comments = comment()->get_nested_comments_with_meta( $post->ID,
                    ['like', 'comment_ID', 'comment_author', 'comment_content', 'depth'] );
                $p = new stdClass();
                $p->ID = $post->ID;
                $stamp = strtotime($post->post_date);
                $date = date('Y-m-d', $stamp);
                if ( $date == date('Y-m-d') ) $date = date("h:i a", $stamp);
                $p->date = $date;

                if ( $post->post_title == forum::post_title_deleted ) $p->deleted = 1;
                $p->author = $post->author_name;
                $p->post_title = $post->post_title;
                $p->post_content = $post->post_content;
                ///$p->post_title = $post->post_title;
                $p->like = $post->like;
                $p->comments = $post->comments;
                $posts[] = $p;
            }
        }
        wp_send_json_success( [
            'in' => $in,
            'file_server_url' => get_option('xforum_url_file_server'),
            'category' => [
                'name' => $category->name,
                'description' => $category->description
            ],
            //'args' => $args,
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

    /**
     *
     * Loads a page script in 'template/api/' folder.
     *
     *
     *
     */
    public function page() {
        $page_name = in('name');
        include DIR_XFORUM . 'template/api/' . $page_name . '.php';

    }


    /**
     * Sets/Update a meta value of the meta key on a post.
     * @note  users of admin, owner of the post ( with login and session_id check ) can set/update meta.
     *
     * @since 2016-08-06
     * @note how to call
     *      http://work.org/wordpress/?forum=api&action=meta_update&post_ID=1094&key=parent&value=1042
     *
     * @attention it only echoes in json string.
     * @use when you need to update a single meta key.
     *
     * @todo add test on permission check.
     *
     */
    public function meta_update() {
        forum()->endIfNotMyPost( in('post_ID') );
        post()->meta( in('post_ID'), in('key'), in('value') );
        wp_send_json_success(in());
    }

    /**
     *
     * @note how to call/get
     *  http://work.org/wordpress/?forum=api&action=meta_update&post_ID=1094&key=abc&value=def
     *
     * @use when you need to get meta value.
     * @use together ( combination of ) with meta_update
     *
     */
    public function meta_get() {
        $in = in();
        $in['value'] = post()->meta( in('post_ID'), in('key') );
        wp_send_json_success( $in );

    }

}

function api()  {
    return new api();
}