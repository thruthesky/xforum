<?php

$id = post()->getViewPostID();
setup_postdata(get_post( $id ));

get_header();
?>

<h1><?php echo in('slug') ?> VIEW PAGE : TITLE : <?php the_title()?></h1>

content

<hr>

<div class="post-content">
    <?php the_content()?>
</div>


<hr>
<a class="btn btn-primary" href="<?php echo forum()->urlPostEdit( get_the_ID() )?>">EDIT</a>
<a class="btn btn-primary" href="<?php echo forum()->urlForumList()?>">LIST</a>
<hr>


<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>


<?php get_footer(); ?>

