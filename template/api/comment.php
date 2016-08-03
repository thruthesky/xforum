<?php
/**
 *
 *
 */

include_once 'function.php';
if ( isset( $o ) && isset( $o['comment_ID'] ) ) {
    $comment = comment()->set( get_comment($o['comment_ID']) );
}
?>

<div class="comment" no="<?php $comment->comment_ID?>">

</div>
