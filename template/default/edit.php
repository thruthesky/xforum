<?php get_header(); ?>

<?php
//di( forum()->getCategory() );



if ( in('post_ID') ) {
    forum()->setCategoryByPostID( in('post_ID') );
    $post = post( in('post_ID') );
}
else {
    $post = post();
}


?>

    <h1><?php echo in('slug') ?> EDIT PAGE</h1>

<form action="?">
    <input type="hidden" name="forum" value="edit_submit">
    <?php if ( in('slug') ) { ?>
        <input type="hidden" name="slug" value="<?php echo in('slug')?>">
    <?php } else { ?>
        <input type="hidden" name="post_ID" value="<?php echo in('post_ID')?>">
    <?php } ?>
    <input type="hidden" name="on_error" value="alert_and_go_back">
    <input type="hidden" name="return_url" value="<?php echo forum()->urlForumList()?>">

    <fieldset class="form-group">
        <label for="post-title">Title</label>
        <input type="text" class="form-control" id="post-title" name="title" placeholder="Input title..." value="<?php echo esc_html( $post->title() )?>">
        <small class="text-muted">Please, input post title.</small>
    </fieldset>
    <fieldset class="form-group">
        <label for="post-content">Content</label>
        <textarea name="content" class="form-control" id="post-content" rows="3" placeholder="Please, input content."><?php echo esc_html( $post->content() )?></textarea>
    </fieldset>

    <input type="submit">
</form>

<?php get_footer(); ?>