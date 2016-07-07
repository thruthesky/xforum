<?php

/**
 * Class post
 *
 * @file class/post.php
 *
 * @warning 2016-07-01. it does not extends forum class anymore.
 */
class post {

    static $cu_data = []; // create / update data.
    static $post = []; // WP_Post object for loading a post.
    static $fields = [
        'ID',
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count'
    ];

    public function __construct()
    {

        self::$cu_data = [];

    }








    /**
     *
     * Returns post_ID on success.
     *
     *
     * @todo add test code. assigned to viel.
     */
    public function create() {
        $post_ID = wp_insert_post( self::$cu_data );
        return $this->returnResult( $post_ID );
    }

    /**
     * Returns post_ID on success.
     * @return string
     */
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

    /**
     *
     * Magical methods __get()
     *
     * @Warning to use this 'magical __get()', the Object must be instantiated with 'post()' and must have a valid post data.
     *
     *
     * @param $property
     * @return mixed|null
     *
     * @todo add unit tet test. add a test on property_exists or not. by viel.
     */
    public function __get( $property ) {
        if ( empty( self::$post ) || ! property_exists( self::$post, 'ID' ) ) return false;
        if ( isset( self::$fields[$property] ) ) return self::$fields[$property];
        else {
            return $this->meta( self::$post->ID, $property );
        }
    }


    /**
     * Increase post view count and returns new number.
     *
     * @param $post_ID
     * @return int|mixed
     * @code Use this on post list plugin code ( not in theme code )
     *      $GLOBALS['post_view_count'] = post()->increaseNoOfView( $id );
     * @endcode
     */
    public function increaseNoOfView( $post_ID )
    {
        $count_key = 'no_of_views';
        $count = get_post_meta($post_ID, $count_key, true);
        if( empty($count) ) {
            $count = 1;
            delete_post_meta($post_ID, $count_key);
            add_post_meta($post_ID, $count_key, $count);
        }
        else{
            $count ++;
            update_post_meta($post_ID, $count_key, $count);
        }
        return $count;
    }


    /**
     *
     * Saves data into 'post_meta'.
     * @note it automatically serialize and un-serialize.
     *
     * @param $post_ID
     * @param $key
     * @param null $value
     * @return mixed|null
     */
    public function meta($post_ID, $key = null, $value = null)
    {
        if ( $key === null ) {
            $key = $post_ID;
            if ( self::$post ) {
                $post_ID = self::$post->ID;
            }
            else return FALSE;
        }
        if ( $value !== null ) {
            if ( ! is_string($value) && ! is_numeric( $value ) && ! is_integer( $value ) ) {
                $value = serialize($value);
            }
            update_post_meta($post_ID, $key, $value);
            return null;
        }
        else {
            $value = get_post_meta($post_ID, $key, true);
            if ( is_serialized( $value ) ) {
                $value = unserialize( $value );
            }
            return $value;
        }
    }


    /**
     *
     * This method saves all the input into post_meta except those are already saved in wp_posts table.
     *
     * @attention This will save everything except wp_posts fields,
     *      so you need to be careful not to add un-wanted form values.
     *
     * @param $post_ID
     */
    public function saveAllMeta( $post_ID )
    {
        $in = in();
        foreach ( $in as $k => $v ) {
            if ( in_array( $k, self::$fields ) ) continue;
            if ( in_array( $k, forum::$query_vars) ) continue;
            $this->meta( $post_ID, $k, $v );
        }
    }




    /**
     * Returns post view count.
     * @note Use this code in theme code
     * @param $post_ID
     * @return int|mixed
     */
    public function getNoOfView($post_ID)
    {
        $count_key = 'no_of_views';
        $count = get_post_meta($post_ID, $count_key, true);
        return $count ? $count : 0;
    }

    /**
     * This post gets a WP_Post Object
     *  and sets into current post Object's 'self::$post' and sets as 'setup_postdata'
     * @USE when you need to get a 'post' Object and to 'setup_posts()' at the same time.
     *
     *
     * @param $post
     *
     * @code example of best use
            $posts = get_posts( [ ... ] );  // Query
                foreach ( $posts as $post ) { // Loop
                    post()->setup( $post );     // set post.
                        the_ID(); the_title();      //  use
                        post()->meta('no-of-view'); //  use
     *
     * @endcode
     *
     */
    public function setup($post)
    {
        self::$post = $post;
        setup_postdata( $post );
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
