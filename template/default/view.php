<?php

$id = post()->getViewPostID();
setup_postdata(get_post( $id ));

get_header();
?>

<h1><?php echo in('slug') ?> VIEW PAGE : TITLE : <?php the_title()?></h1>

files:
<?php

di( post()->meta( get_the_ID(), 'files' ) );

?>
<hr>

content


<hr>

<div class="post-content">
    <?php the_content()?>
</div>


<hr>
<a class="btn btn-primary" href="<?php forum()->urlEdit( get_the_ID() )?>">EDIT</a>
<a class="btn btn-primary" href="<?php forum()->urlList()?>">LIST</a>
<?php forum()->list_menu_user()?>
<hr>
No of views: <?php echo $GLOBALS['post_view_count']?>
<hr>


<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>


<?php get_footer(); ?>

