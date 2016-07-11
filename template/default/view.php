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
<article class="forum">
    <header>
        <h1><?php the_title()?></h1>
        <dl class="meta">
            <dt>Author:</dt>
            <dd><address rel="author"><?php the_author()?></address></dd>
            <dt>Date:</dt>
            <dd><time pubdate datetime="<?php echo get_the_date("Y-m-d")?>" title="<?php echo get_the_date("h:i a on F dS, Y")?>"><?php echo get_the_date()?></time></dd>
            <dt>No of views:</dt>
            <dd>No of views: <?php echo $GLOBALS['post_view_count']?></dd>
            <dt>All posts by author:</dt>
            <dd><a href="http://www.blog.net/authors/remy-schrader/">Link</a></dd>
            <dt>Contact:</dt>
            <dd><a href="javascript:alert('Message is not yet, implemented');">Send Message & SMS Text Message</a></dd>
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

