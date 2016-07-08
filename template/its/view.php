<?php
include DIR_XFORUM . 'template/its/its.class.php';
$id = post()->getViewPostID();
setup_postdata(get_post( $id ));

get_header();
wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');
?>

<h1><?php echo in('slug') ?> VIEW PAGE : TITLE : <?php the_title()?></h1>
<small>Posted on:<?php the_date(); ?></small> <br/>
files:
<?php

di( post()->meta( get_the_ID(), 'files' ) );

?>
<hr>

<div class="post-content">
    <b>Description: </b><?php the_content()?>
    <b>Worker:</b> <?php echo post()->meta( get_the_id(),'worker' ); ?><br/>
    <b>Incharge:</b> <?php echo post()->meta( get_the_id(),'incharge' ); ?><br/>
    <b>Deadline:</b> <?php $deadline = post()->meta( get_the_id(),'deadline' ); echo date( 'M d, Y', strtotime($deadline) );?><br/>
    <b>Progress:</b> <?php
    $process = post()->meta( get_the_id(),'process' );
    if ( $process == 'A' ) echo "ALL";
    elseif ( $process == 'N' ) echo "Not yet started";
    elseif ( $process == 'S' ) echo "Started";
    elseif ( $process == 'P' ) echo "In progress";
    elseif ( $process == 'F' ) echo "Finished";
    ?><br/>
    <b>Priority:</b> <?php
    $priority = post()->meta( get_the_id(),'priority' );
     foreach ( its::$priority as $num => $text ) {
         if ( $priority == $num ) echo $text;
     } ?><br/>
    <b>Percentage:</b> <?php echo post()->meta( get_the_id(),'percentage' ); ?> % <br/>

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

