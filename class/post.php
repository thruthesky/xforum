<?php
/**
 * Class post
 * @file class/post.php
 *
 * @warning 2016-07-01. it does not extends forum class anymore.
 */
class post {

    static $cu_data = []; // create / update data.
    static $post = []; // WP_Post object for loading a post.

    public function __construct()
    {

        self::$cu_data = [];

    }








    /**
     *
     * @return $this
     *
     * @todo add test code. assigned to viel.
     */
    public function create() {
        $post_ID = wp_insert_post( self::$cu_data );
        return $this->returnResult( $post_ID );
    }

    public function update()
    {
        $post_ID = wp_update_post( self::$cu_data );
        return $this->returnResult( $post_ID );
    }


    private function returnResult($post_ID)
    {
        if ( is_wp_error( $post_ID ) ) {
            return $post_ID->get_error_message();
        }
        else if ( $post_ID  == 0 ) {
            return "wp_insert_post() returned 0. Post data may be empty.";
        }
        return $post_ID;
    }





    /**
     * @param $key
     * @param $value
     * @return post
     *
     *
     */
    public function set( $key, $value ) {
        self::$cu_data[ $key ] = $value;
        return $this;
    }

    /**
     * @deprecated use create()
     *
     * @return int|string - positive integer on success. string on error.
     *
     * @todo unit test. assigned by viel.
     * @todo save extra form data. copy code from k-forum.
     * @todo hook on media file. media files may uploaded thru ajax and need to be hooked.
     */
    public function save() {
        return self::create();
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


    /**
     *
     * Loads a post and return this.
     *
     *
     *
     * @param $post_ID
     * @return post|null - if there is error, it returns null.
     */
    public function load( $post_ID )
    {
        self::$post = get_post( $post_ID );
        if ( self::$post ) return $this;
        else return null;
    }

    public function title()
    {
        if ( self::$post ) {
            return self::$post->post_title;
        }
        else return null;
    }
    public function content()
    {
        if ( self::$post ) {
            return self::$post->post_content;
        }
        else return null;
    }



}


/**
 *
 * @param null $post_ID
 * @return post
 * @todo add test code.
 */
function post( $post_ID = null ) {
    if ( $post_ID ) {
        $post = new post();
        return $post->load( $post_ID );
    }
    return new post();
}
