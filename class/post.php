<?php

/**
 * Class post
 *
 * @file class/post.php
 *
 * @warning 2016-07-01. it does not extends forum class anymore.
 *
 *
 * @cycle
 *
 *      - construct
 *          - if it is post view page,
 *                  'the_post()' is called on 'filter'.
 *              It already has the 'post'.
 *              the constructor then, it automatically save the post into self::$post.
 *              so, it does not need to 'load()' for the post.
 *              You can use 'post()->ID' directly.
 *      - load()
 *          - if you call 'post()->load(123)', then the main 'post' will be voided.
 */
class post {

    public $version = "0.1";
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


    /**
     * post constructor.
     *
     * @warning clarify the use of get_post()
     * @return post
     *
     */
    public function __construct()
    {
        self::$cu_data = [];
        $post_ID = get_the_ID();
        if ( $post_ID ) {
            self::$post = get_post();
        }

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


    /**
     * Returns error from wp_create_post(), wp_update_post()
     * @param $post_ID
     * @return string
     */
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
     *
     * @todo delete blogs if you have on blog api.
     *
     *
     *
     *
     *
     * @param $post_ID
     * @return array|false|WP_Post False on failure. - same as wp_delete_post
     */
    public function delete( $post_ID ) {

        // $post = wp_delete_post($post_ID);
        // return $post;

        $post_ID = wp_update_post( ['ID'=> $post_ID, 'post_title' => forum::deleted, 'post_content' => forum::deleted ] );
        return $this->returnResult( $post_ID );
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
     * @deprecated use setup()
     *
     * @param $post_ID
     * @return post|null - if there is error, it returns null.
     *
     * @todo add test code. load()
     *
     * @todo @warning loading a post in the middle of 'main query' or when post has setup, is a bad choice.
     *
     *      WordPress prepares the main post on view ( or edit ) page.
     *
     *      When the post is already loaded, what is the use of loading the same post again?
     *
     *          And you are doing it in wrong way.
     *
     *          All the dependent codes already got wrong direction.
     *
     * @todo you need to fix it. remove post::load() first.
     *
     * @deprecated don't use this method.
     */
    public function load( $post_ID )
    {
        $post = get_post( $post_ID );
        if ( $post ) {
            self::$post = $post;
            setup_postdata( $post );
            return $this;
        }
        else return null;
    }



    /**
     * Returns the post of the input post ID.
     *
     *
     *
     * @param $post_ID - post ID
     * @return WP_Post
     */
    public function get( $post_ID ) {

        return get_post( $post_ID );

        /**
        return current (
            get_posts( [
                'post__in' => [ $post_ID ]
            ] )
        );
         * */

        /*
        global $wpdb;
        $_post = $wpdb->get_row( "SELECT * FROM $wpdb->posts WHERE ID = $post_ID LIMIT 1" );
        return $_post;
        */
    }


    /**
     *
     * @attention post::$post is an instance of WP_Post. It is prepared by post() or post( $post_ID )
     * @return null|string
     */
    public function title()
    {
        if ( self::$post ) {
            return self::$post->post_title;
        }
        else return null;
    }

    /**
     * @return null|string
     */
    public function content()
    {
        if ( self::$post ) {
            $content = self::$post->post_content;
            if ( $this->content_type == 'text/plain' ) {
                $content = nl2br($content);
            }
            return $content;
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
     *
     * @code The two codes below are the same.
            post()->setup( $post ); // OR   post()->setup( $wp_query )
            post()->meta('worker')
            post()->worker
     * @endcode
     *
     *
     */
    public function __get( $property ) {

        if ( empty( self::$post ) ) return false;

        if ( self::$post->$property ) {
            return self::$post->$property;
        }
        else {
            return $this->meta( self::$post->ID, $property );
        }

    }


    /**
     * @WARNING to do __set() magic, you need to make things clear between WP_Post properties and post_meta().
     */
    /*
    public function __set( $name, $value ) {
        //
    }
    */


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
     *
     * @note it automatically serialize and un-serialize.
     * @Attention This returns on 'single' value.
     *
     * @param $post_ID
     * @param $key
     * @param null $value - If it is not null, then it updates meta data.
     *
     * @return mixed|null
     *
     * @code
     *          post()->meta( $post_ID, 'files', $files );          /// SAVE
     *          $this->meta( self::$post->ID, $property );          /// GET meta of post->ID
     *          $p = post()->meta( 'process' );                     /// GET meta of self::$post->ID
     * @endcode
     *
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
     * This method gets a WP_Query Object or WP_Post Object.
     *
     *  and do 'the_post()' or 'setup_postdata()' to do the post template tags like 'the_ID()' and sets into current post Object's 'self::$post'
     *
     * @USE when you need to get a 'post' Object and to 'setup_posts()' at the same time.
     *
     *
     * @param WP_Post|WP_Query $query
     *
     *
     * @code example of best use
     *
            $mq = new WP_Query( $args );
            if ( $mq->have_posts() ) {
                while ( $mq->have_posts() ) {
                    post()->setup( $mq );
                    post()->worker;
                }
            }
     *
     * @endcode
     *
     * @todo add test code on setup( WP_Query ) and setup( WP_Post )
     */
    public function setup($query)
    {
        if ( is_numeric( $query ) ) {
            $post_ID = $query;
            $post = get_post( $post_ID );
            self::$post = $post;
            setup_postdata( $post );
        }
        else if ( $query instanceof WP_Post ) {
            self::$post = $query;
            setup_postdata( $query );
        }
        else if ( $query instanceof WP_Query ) {
            $query->the_post();
            self::$post = get_post();
        }
//        self::$post = $post;
//        setup_postdata( $post );
        return $this;
    }

    /**
     *
     * @param $key
     * @param $value
     * @return post
     *
     * @code
     *      post()->set('a','b');
     * @endcode
     *
     */
    public function set( $key, $value ) {
        self::$cu_data[ $key ] = $value;
        return $this;
    }




    public function count_comments( $post_ID ) {
        $count = $this->get_count_comments( $post_ID );
        if ( $count )  echo "($count)";
        echo '';
    }

    public function get_count_comments( $post_ID ) {
        $count = wp_count_comments( $post_ID );
        if ( $count->approved )  return $count->approved;
        else return 0;
    }


    /**
     *
     * Returns true if the post exists and is not in trash.
     *
     * @param $post_ID
     * @return bool
     *
     * @code
     *      post()->exists( $update_ID );
     * @endcode
     */
    public function exists( $post_ID ) {

        $status = get_post_status ( $post_ID );
        if ( $status === false ) return false;
        if ( $status == 'trash' ) return false;
        return true;

    }

    public function isMine()
    {
        return $this->post_author == forum()->get_user_id();
    }

    public function author() {
        echo user($this->post_author)->user_nicename;
    }
    public function date_short($post_id=0) {
        echo $this->get_date_short( $post_id );
    }

    public function get_date_short( $post_id = 0 ) {
        if ( empty($post_id) ) $post_id = $this->ID;
        $time = get_the_time( 'U', $post_id );
        $date = date('Y-m-d', $time);
        $today_date = date('Y-m-d');
        if ( $date == $today_date ) return date("h:i a", $time);
        else return $date;
    }
}


/**
 *
 * @param null $deprecated
 * @return post
 * @todo add test code.
 *
 * @todo @WARNING It is a big mistake that post::load() methods was created in a wrong way.
 *
 *      all the dependent code which uses post::load() got wrong direction.
 *
 * @see post:load()
 *
 */
function post( $pid = null ) {
    if ( $pid ) {
        $post = new post();
        /**
         *
         */
        return $post->setup( $pid );
//        return $post->load( $deprecated );
    }
    else return new post();
}
