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

        get_post(1);

        foreach( $posts as $post ) {
            if ( $post->post_author ) {
                $user = get_user_by( 'id', $post->post_author );
                $post->author_name = $user->user_nicename;
                $meta = get_post_meta( $post->ID );
                foreach( $meta as $k => $arr ) {
                    $post->$k = $arr[0];
                }
                $post->comments = comment()->get_nested_comments_with_meta( $post->ID );
                /*
                if ( get_comments_number( $post->ID ) ) {

                    $comment_args = array(
                        'post_id' => $post->ID,
                    );
                    $comments = get_comments( $comment_args );
                    // load comment meta
                    foreach( $comments as $comment ) {
                        $meta = get_comment_meta( $comment->comment_ID );
                        foreach( $meta as $k => $arr ) {
                            $comment->$k = $arr[0];
                        }
                    }

                    $post->comments = $comments;

                }
                */
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
}

function api()  {
    return new api();
}