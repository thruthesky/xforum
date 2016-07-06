<?php get_header(); ?>

<?php
//di( forum()->getCategory() );

wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');

if ( in('post_ID') ) {
    forum()->setCategoryByPostID( in('post_ID') );
    $post = post( in('post_ID') );
}
else {
    $post = post();
}


?>

    <h1><?php echo in('slug') ?> EDIT PAGE</h1>

<style>
    .post-edit-box {
        position: relative;
    }
    .post-edit-box .buttons {
        text-align:right;
    }
    .file-upload-form {
        position: absolute;
        bottom: 0;
    }
</style>
<div class="post-edit-box">

<form action="?" method="post">
    <input type="hidden" name="forum" value="edit_submit">
    <input type="hidden" name="response" value="view">
    <?php if ( in('slug') ) { ?>
        <input type="hidden" name="slug" value="<?php echo in('slug')?>">
    <?php } else { ?>
        <input type="hidden" name="post_ID" value="<?php echo in('post_ID')?>">
    <?php } ?>
    <input type="hidden" name="on_error" value="alert_and_go_back">
    <fieldset class="form-group">
        <label for="post-title">Title</label>
        <input type="text" class="form-control" id="post-title" name="title" placeholder="Input title..." value="<?php echo esc_html( $post->title() )?>">
        <small class="text-muted">Please, input post title.</small>
    </fieldset>
    <fieldset class="form-group">
        <label for="post-content">Content</label>
        <?php
        if ( $post ) {
            $content = $post->content();
        }
        else {
            $content = '';
        }
        $editor_id = 'new-content';
        $settings = array(
            'textarea_name' => 'content',
            'media_buttons' => false,
            'textarea_rows' => 5,
            'quicktags' => false
        );
        wp_editor( $content, $editor_id, $settings );
        ?>
    </fieldset>


    <div class="buttons">
        <input type="submit">
    </div>
</form>

<?php file_upload()?>

</div>

<?php get_footer(); ?>