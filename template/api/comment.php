<?php
/**
 *
 *
 */

include_once 'function.php';
$cls = '';
if ( isset( $o ) && isset( $o['comment_ID'] ) ) {
    // $comment = comment()->set( get_comment($o['comment_ID']) );
    $comment = get_comment($o['comment_ID']);
    $depth = 1;
    $cls .= ' new';
}
else {
    // $comment = comment()->set( $comment );
    $depth = $comment->depth < 10 ? $comment->depth : 10 ;
}
?>

<div class="comment<?php echo $cls?>" no="<?php echo $comment->comment_ID?>" depth="<?php echo $depth?>" content-type="<?php echo $comment->content_type?>">
    <div class="comment-meta">
        <span class="no">
            <span class="caption">No.</span>
            <span class="text"><?php echo $comment->comment_ID?></span>
        </span>
        <span class="author">
            <span class="caption">Author</span>
            <span class="text"><?php echo $comment->comment_author?></span>
        </span>

        <div class="buttons">
            <span class="comment-edit-button">edit</span>
            <span class="comment-delete-button">delete</span>
            <span class="comment-like-button">like<span class="no"></span></span>
            <span class="comment-report-button">report</span>
            <span class="comment-copy-button">copy</span>
            <span class="comment-move-button">move</span>
            <span class="comment-blind-button">blind</span>
            <span class="comment-block-button">block</span>
        </div>

    </div>

    <div class="comment-content"><?php

        $content = $comment->comment_content;
        $content = nl2br( $content );
        /*
        if ( $comment->content_type == 'text/plain' ) {
            $content = nl2br( $content );
        }
        */

        echo $content;

        ?></div>





    <?php get_comment_form()?>
</div>
