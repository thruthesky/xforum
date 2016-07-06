<?php
/**
 * Class comment
 * @file class/comment.php
 *
 *
 */
class comment
{


    /**
     *
     * Saves data into 'comment_meta'.
     * @note it automatically serialize and un-serialize.
     *
     * @param $comment_ID
     * @param $key
     * @param null $value
     * @return mixed|null
     */
    public function meta($comment_ID, $key, $value = null)
    {
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

function comment() {
    return new comment();
}