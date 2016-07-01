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

    <h1><?php echo in('id') ?> EDIT PAGE</h1>

<form action="?">
    <input type="hidden" name="forum" value="edit_submit">
    <?php if ( in('id') ) { ?>
        <input type="hidden" name="id" value="<?php echo in('id')?>">
    <?php } else { ?>
        <input type="hidden" name="post_ID" value="<?php echo in('post_ID')?>">
    <?php } ?>
    <input type="hidden" name="on_error" value="alert_and_go_back">
    <input type="hidden" name="return_url" value="<?php echo forum()->urlForumList()?>">

    <div>
        <input type="text" name="title" value="<?php echo esc_html( $post->title() )?>">
    </div>
    <div><textarea name="content"><?php echo esc_html( $post->content() )?></textarea></div>
    <input type="submit">
</form>

<?php get_footer(); ?>