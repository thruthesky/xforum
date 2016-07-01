<?php

$id = post()->getViewPostID();
setup_postdata(get_post( $id ));

get_header();
?>

<h1><?php echo in('id') ?> VIEW PAGE : TITLE : <?php the_title()?></h1>

content

<hr>

<p>
    <?php the_content()?>
</p>

<hr>
    <a class="btn btn-primary" href="<?php echo forum()->urlPostEdit( get_the_ID() )?>">EDIT</a>
    <a class="btn btn-primary" href="<?php echo forum()->urlForumList()?>">LIST</a>
<hr>

<?php get_footer(); ?>

