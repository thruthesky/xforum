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


    public function set( WP_Comment $comment ) {
        self::$comment = $comment;
    }


    /**
     * @param $prop
     * @return bool
     *
     * @code
     *      $comment_ID = comment()->comment_ID;
     * @endcode
     *
     * @todo add test code
     */
    public function __get( $prop ) {
        if ( isset( self::$comment ) && isset( self::$comment->$prop ) ) return self::$comment->$prop;
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
        if ( $key === null ) {
            $key = $comment_ID;
            $comment_ID = self::$comment->comment_ID;
        }

        if ( $value !== null ) {
            if ( ! is_string($value) && ! is_numeric( $value ) && ! is_integer( $value ) ) {
                $value = serialize($value);
            }
            update_comment_meta($comment_ID, $key, $value);
            return null;
        }
        else {
            $value = get_comment_meta($comment_ID, $key, true);
            if ( is_serialized( $value ) ) {
                $value = unserialize( $value );
            }
            return $value;
        }
    }



}

function comment( ) {
    return new comment();
}