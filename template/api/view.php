<?php
include_once 'function.php';

$cls = 'post';
if ( isset( $o ) && isset( $o['post_ID'] ) ) {
    post( $o['post_ID'] );
}
else {
    post( $post );
}
if ( post()->isDeleted() ) {
    $cls .= ' deleted';
}
?>

<div class="<?php echo $cls?>" no="<?php echo post()->ID?>">
    <div class="data">
        <div class="meta">
            <div>
                No. <?php echo post()->ID?>
                Date. <?php post()->date_short()?>
                Author. <?php post()->author()?>
            </div>
            <div class="buttons">
                <ul>
                    <li class="post-edit-button">edit</li>
                    <li class="post-delete-button">delete</li>
                    <li class="post-like-button">like</li>
                    <li class="post-spam-button">spam</li>
                    <li class="post-move-button">move</li>
                    <li class="post-copy-button">copy</li>
                    <li class="post-block-button">block</li>
                    <li class="post-blind-button">blind</li>
                </ul>
            </div>
        </div>
        <div class="title">
            <?php echo post()->title()?>
        </div>
        <div class="content">
            <?php echo post()->content()?>
        </div>
        <?php get_comment_form(['first'=>'yes'])?>
    </div>
    <div class="comments">
        <div class="comments-meta">
            <div class="count" no="<?php echo post()->comment_count?>"></div>
        </div>
        <div class="comment-list">
            <?php
            $comments = comment()->get_nested_comments_with_meta( post()->ID );
            if ( $comments ) {
                foreach ( $comments as $comment ) {
                    include 'comment.php';
                }
            }
            ?>
        </div>
    </div>
</div>
