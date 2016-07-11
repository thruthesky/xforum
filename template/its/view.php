<?php
include DIR_XFORUM . 'template/its/its.class.php';


get_header();
wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');
?>


<?php

// uploaded files
// di( post()->meta( get_the_ID(), 'files' ) );

?>
<article class="forum its">
    <header>
        <h1><?php the_title()?></h1>
        <dl class="meta">
            <dt>Author:</dt>
            <dd><address rel="author"><?php the_author()?></address></dd>
            <dt>Date:</dt>
            <dd><time pubdate datetime="<?php echo get_the_date("Y-m-d")?>" title="<?php echo get_the_date("h:i a on F dS, Y")?>"><?php echo get_the_date("h:i a - F dS, Y")?></time></dd>
            <dt>No of views:</dt>
            <dd>No of views: <?php echo $GLOBALS['post_view_count']?></dd>
            <dt>All posts by author:</dt>
            <dd><a href="http://www.blog.net/authors/remy-schrader/">Link</a></dd>
            <dt>Contact:</dt>
            <dd><a href="javascript:alert('Message is not yet, implemented');">Send Message & SMS Text Message</a></dd>

            <dt>Worker</dt><dd><?php echo post()->worker; ?></dd>
            <dt>Deadline</dt><dd><?php echo date( 'M d, Y', strtotime( post()->deadline) );?></dd>
            <dt>Work Status</dt>
            <dd><?php
                $p = post()->meta( 'process' );
                echo its::$process[ $p ];
                if ( $p == 'P' ) {
                    $percentage = post()->percentage;
                    echo "<progress value='$percentage' max='100'></progress> $percentage%";
                }
                ?>
            </dd>
            <dt>In Charge</dt><dd><?php echo post()->meta( 'incharge' ); ?></dd>
            <dt>Prority</dt><dd><?php echo @its::$priority[ post()->priority ]?></dd>
        </dl>
    </header>
    <main class="content">
        <?php the_content()?>
        </main>

</article>

<nav class="buttons">
    <?php forum()->button_edit()?>
    <?php forum()->button_delete()?>
    <?php forum()->button_list()?>
    <?php forum()->list_menu_user()?>
</nav>


<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>


<?php get_footer(); ?>

