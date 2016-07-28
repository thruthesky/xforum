<?php
/**
 *
 *
 * ---------------------------------------------------------------------------
 *
 *
 * W A R N I N G : Theme Development on Desktop version has been discontinued.
 *
 * B E C A U S E : People use mobile for web browsing
 *
 *      And 'Mobile theme version' can handle desktop also.
 *
 *      So, we develop mobile theme version only which works on desktop also.
 *
 *
 * ---------------------------------------------------------------------------
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
?>
<?php
get_header();
wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');



?>
<style>
    article.forum header {
        margin: 0;
        padding: 1em;
        background-color: #dcdcdc;
        border: 0;
    }
    article.forum section.content {
        margin: 1em 0;
        background-color: white;
        padding: 1em;
    }
</style>
<article class="forum post" post-id="<?php the_ID()?>">
    <header>
        <h1><?php the_title()?></h1>
        <dl class="meta">
            <dt><?php _text('Author') ?>:</dt>
            <dd><address rel="author"><?php the_author()?></address></dd>
            <dt><?php _text('Date') ?>:</dt>
            <dd><time pubdate datetime="<?php echo get_the_date("Y-m-d")?>" title="<?php echo get_the_date("h:i a on F dS, Y")?>"><?php echo get_the_date()?></time></dd>
            <dt><?php _text('No of views') ?>:</dt>
            <dd><?php _text('No of views') ?>: <?php echo $GLOBALS['post_view_count']?></dd>
            <dt><?php _text('All posts by author') ?>:</dt>
            <dd><a href="http://www.blog.net/authors/remy-schrader/"><?php _text('Link') ?></a></dd>
            <dt><?php _text('Contact') ?>:</dt>
            <dd><a href="javascript:alert('Message is not yet, implemented');"><?php _text('Send Message & SMS Text Message') ?></a></dd>
        </dl>
    </header>
<?php

// di( post()->meta( get_the_ID(), 'files' ) );

?>


<main class="content">
    <?php the_content()?>
</main>

    <nav class="buttons">
        <?php forum()->button_edit()?>
        <?php forum()->button_delete()?>
        <?php forum()->button_list()?>
        <?php forum()->button_like( ['no'=>get_post_meta( get_the_ID(), 'like', true)] )?>
        <?php forum()->list_menu_user()?>
    </nav>




</article>


<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>


<?php get_footer(); ?>

