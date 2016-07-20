<?php
//include_once DIR_XFORUM . 'template/its/init.php'; // this is done by core.

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
            <dt><?php _text('Author') ?>:</dt>
            <dd><address rel="author"><?php the_author()?></address></dd>
            <dt><?php _text('Date') ?>:</dt>
            <dd><time pubdate datetime="<?php echo get_the_date("Y-m-d")?>" title="<?php echo get_the_date("h:i a on F dS, Y")?>"><?php echo get_the_date("h:i a - F dS, Y")?></time></dd>
            <dt><?php _text('No of views') ?>:</dt>
            <dd><?php _text('No of views') ?>: <?php echo $GLOBALS['post_view_count']?></dd>
            <dt><?php _text('All posts by author') ?>:</dt>
            <dd><a href="http://www.blog.net/authors/remy-schrader/"><?php _text('Link') ?></a></dd>
            <dt><?php _text('Contact') ?>:</dt>
            <dd><a href="javascript:alert('Message is not yet, implemented');"><?php _text('Send Message & SMS Text Message') ?></a></dd>

            <dt><?php _text('Worker') ?></dt><dd><?php echo post()->worker; ?></dd>
            <dt><?php _text('Deadline') ?></dt><dd><?php echo date( 'M d, Y', strtotime( post()->deadline) );?></dd>
            <dt><?php _text('Work Status') ?></dt>
            <dd><?php
                $p = post()->meta( 'process' );
                if ( $p ) {
                    echo its::$process[ $p ];
                    if ( $p == 'P' ) {
                        $percentage = post()->percentage;
                        echo "<progress value='$percentage' max='100'></progress> $percentage%";
                    }
                }
                else {
                    echo "No process code";
                }

                ?>

                <?php
                $evaluation = post()->evaluate;
                $comment = post()->evaluate_comment;
                if ( $evaluation ) {
                    ?>
                    <dt><?php _text('Work Evaluation') ?></dt>
                    <?php
                    echo "<progress value='$evaluation' max='100'></progress> $evaluation%";
                }
                if ( $comment ) {
                    echo "<br/><b><?php _text('valuation Comment') ?>E:</b> $comment";
                }

                ?>
            </dd>
            <dt><?php _text('In Charge') ?></dt><dd><?php echo post()->meta( 'incharge' ); ?></dd>
            <dt><?php _text('Prority') ?></dt><dd><?php echo @its::$priority[ post()->priority ]?></dd>
            <dt><?php _text('Dependency Parent') ?></dt><dd>
            <?php
            $parent_ID = post()->parent;
            if ( $parent_ID ) {
                $parent = get_post( $parent_ID );
                if ( $parent ) {
                    ?>
            <a href="<?php echo forum()->urlView( $parent_ID)?>"><?php echo $parent->post_title?></a>
            <?php
                }
            }
            else {
                ?>
                <form>
                <input type="text" name="parent" value="" placeholder="<?php _text('Search a post and put it as dependency parent') ?>">
                <input type="submit">
                </form>
                <?php
            }
            ?>
            </dd>
        </dl>
    </header>
    <main class="content">
        <?php the_content()?>
        </main>

</article>

<nav class="buttons">
    <?php forum()->button_new(['text'=>'Create Dependent', 'query'=>"parent=".get_the_ID()])?>
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

