<?php
/**
 * Class comment
 * @file class/comment.php
 *
 *
 */
class comment
{
    use UrlComment;


    static $comment = null;
    public static $nest_comments = [];
    public static $fields = [
        'comment_ID',
        'comment_post_ID',
        'comment_author',
        'comment_author_email',
        'comment_author_url',
        'comment_author_IP',
        'comment_date',
        'comment_date_gmt',
        'comment_content',
        'comment_karma',
        'comment_approved',
        'comment_agent',
        'comment_type',
        'comment_parent',
        'user_id',
    ];


    /**
     * $comment = comment()->set( get_comment($o['comment_ID']) );
     * @param WP_Comment $comment
     * @return $this
     *
     */
    public function set( WP_Comment $comment ) {
        self::$comment = $comment;
        return $this;
    }


    /**
     * @param $prop
     * @return bool
     *
     * @code
     *      $comment_ID = comment()->comment_ID;
     * @endcode
     *
     * @since Aug 3, 2016. It uses parent's magical get.
     *
     * @todo add test code
     *
     */
    public function __get( $prop ) {
        if ( self::$comment ) return self::$comment->$prop;
        else return false;
    }


    /**
     *
     * Saves data into 'comment_meta'.
     * @note it automatically serialize and un-serialize.
     *
     * @param $comment_ID
     * @param $key
     * @param null $value
     * @return mixed|null
     *
     * @update if $key is null, $comment_ID is the key.
     *
     *             It uses the self::$comment for the comment_ID.
     *
     * @code
     *
     *    $files = comment()->meta($comment->comment_ID, 'files');
     *
     * @endcode
     *
     * @code
     *
     *      comment()->set($comment);
     *
     *      $files = comment()->meta('files');
     *
     * @endcode
     *
     */
    public function meta($comment_ID, $key=null, $value = null)
    {
        if ( $key === null ) { // get
            $key = $comment_ID;
            $comment_ID = self::$comment->comment_ID;
        }

        if ( $value !== null ) { // update
            if ( ! is_string($value) && ! is_numeric( $value ) && ! is_integer( $value ) ) {
                $value = serialize($value);
            }
            update_comment_meta($comment_ID, $key, $value);
            return null;
        }
        else { // get
            $value = get_comment_meta($comment_ID, $key, true);
            if ( is_serialized( $value ) ) {
                $value = unserialize( $value );
            }
            return $value;
        }
    }

    public function saveAllMeta($comment_ID)
    {

        $in = in();
        foreach ( $in as $k => $v ) {
            if ( in_array( $k, self::$fields ) ) continue;
            if ( in_array( $k, forum::$query_vars) ) continue;
            $this->meta( $comment_ID, $k, $v );
        }

    }

    /**
     * Returns WP_Comment Object with comment meta.
     * @param $comment_ID
     * @return array|null|WP_Comment
     */
    public function get_comment_with_meta( $comment_ID ) {
        $comment = get_comment( $comment_ID );
        $meta = get_comment_meta( $comment_ID );
        foreach( $meta as $k => $arr ) {
            $comment->$k = $arr[0];
        }
        return $comment;
    }


    /**
     * @param $post_ID
     * @return array
     * @code
     *      di ( comment()->get_nested_comments_with_meta( get_the_ID() ) );
     * @endcode
     *
     */
    public function get_nested_comments_with_meta( $post_ID ) {
        self::$nest_comments = [];
        if ( ! get_comments_number( $post_ID ) ) return [];
        $comments = get_comments( [ 'post_id' => $post_ID ] );
        foreach ( $comments as $comment ) {
            $meta = get_comment_meta( $comment->comment_ID );
            foreach( $meta as $k => $arr ) {
                $comment->$k = $arr[0];
            }
        }
        ob_start();
        wp_list_comments(
            [
                'max_depth' => 10,
                'reverse_top_level' => 'asc',
                'avatar_size' => 0,
                'callback' => 'get_nested_comments_with_meta'
            ],
            $comments);
        $trash = ob_get_clean();
        return self::$nest_comments;
    }

    /**
     * @see README
     * @param $comment_ID
     * @return int
     */
    public function delete($comment_ID)
    {
        return wp_update_comment([
            'comment_ID' => $comment_ID,
            'comment_content' => forum::comment_deleted
        ]);
    }



    public function isMine($id)
    {
        $comment = get_comment( $id );
        return $comment->user_id == forum()->get_user_id();
    }



}
function get_nested_comments_with_meta( $comment, $args, $depth ) {
    $parent_comment = null;
    //if ( $comment->comment_parent ) $parent_comment = get_comment($comment->comment_parent);
    $comment->depth = $depth;
    comment::$nest_comments[] = $comment;
}


function comment( ) {
    return new comment();
}