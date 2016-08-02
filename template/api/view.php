<?php

if ( isset( $o ) && isset( $o['post_ID'] ) ) {
    post( $o['post_ID'] );
};
?>

<div class="post" no="<?php echo post()->ID?>">
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
</div>
