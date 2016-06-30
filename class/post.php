<?php
class post extends forum {

    static $entity = [];

    public function __construct()
    {

        parent::__construct();

    }


    /**
     *
     * @return $this
     *
     * @todo add test code. assigned to viel.
     */
    public function create() {
        self::$entity = [];
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     *
     *
     */
    public function set( $key, $value ) {
        self::$entity[ $key ] = $value;
        return $this;
    }

    /**
     *
     * @return int|string - positive integer on success. string on error.
     *
     * @todo unit test. assigned by viel.
     * @todo save extra form data. copy code from k-forum.
     * @todo hook on media file. media files may uploaded thru ajax and need to be hooked.
     */
    public function save() {



        $post_ID = wp_insert_post( self::$entity );

        if ( is_wp_error( $post_ID ) ) {
            return $post_ID->get_error_message();
        }
        else if ( $post_ID  == 0 ) {
            return "wp_insert_post() returned 0. Post data may be empty.";
        }

        return $post_ID;
    }



    public function load() {
        return $this;
    }


    /**
     *
     * @todo @warning very important. If you don't do permission check, google will delete all of your posts.
     *
     * @todo delete blogs if you have on blog api.
     *
     * @todo attachemnts ( media files )
     *
     *
     *
     *
     * @param $post_ID
     * @return array|false|WP_Post False on failure. - same as wp_delete_post
     */
    public function delete( $post_ID ) {
        $post = wp_delete_post($post_ID);
        return $post;
    }

    public function getViewPostID()
    {
        return get_the_ID();
    }

}




function post() {
    return new post();
}
