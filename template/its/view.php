<?php
include DIR_XFORUM . 'template/its/its.class.php';
$id = post()->getViewPostID();
setup_postdata(get_post( $id ));

get_header();
wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');
?>

files:
<?php

di( post()->meta( get_the_ID(), 'files' ) );

?>
<hr>
<div class="post_content">
    <div class="col-lg-8 card card-block">
        <div class="post-title text-uppercase">
            <h4><?php the_title(); ?></h4>
            <small>Posted on:<?php the_date(); ?></small>
            <br/><br/>
        </div>
        <div class="post-content">

            <b>Description: </b><?php the_content()?>
            <b>Incharge:</b> <?php echo post()->meta( get_the_id(),'incharge' ); ?><br/>
            <b>Work Priority:</b> <?php
            $priority = post()->meta( get_the_id(),'priority' );
            foreach ( its::$priority as $num => $text ) {
                if ( $priority == $num ) echo $text;
            } ?><br/>

        </div>
    </div>

    <div class="col-lg-4">
        <div class="col-lg-12 post-info">
            <div class="list-group">
                <div class="list-group-item">
                    <h5 class="list-group-item-heading">Worker</h5>
                    <p class="list-group-item-text"><?php echo post()->meta( get_the_id(),'worker' ); ?></p>
                </div>
                <div class="list-group-item">
                    <h5 class="list-group-item-heading">Deadline</h5>
                    <p class="list-group-item-text"><?php $deadline = post()->meta( get_the_id(),'deadline' ); echo date( 'M d, Y', strtotime($deadline) );?></p>
                </div>
                <div class="list-group-item">
                    <h5 class="list-group-item-heading">Work Status</h5>
                    <p class="list-group-item-text">
                        <?php $process = post()->meta( get_the_id(),'process' );
                        if ( $process == 'A' ) echo "ALL";
                        elseif ( $process == 'N' ) echo "Not yet started";
                        elseif ( $process == 'S' ) echo "Started";
                        elseif ( $process == 'P' ) {
                        echo "In progress";?>
                        <?php $percentage = post()->meta( get_the_id(),'percentage' ); ?>
                            <?php echo " : " .$percentage; ?>% <br/>
                            <progress class="progress progress-striped" value="<?php echo $percentage; ?>" max="100"></progress>
                        <?php
                        } elseif ( $process == 'F' ) echo "Finished"; ?><br/>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<hr>
<div class="col-lg-12">
    <a class="btn btn-success" href="<?php forum()->urlEdit( get_the_ID() )?>">EDIT</a>
    <a class="btn btn-primary" href="<?php forum()->urlList()?>">LIST</a>
    <?php forum()->list_menu_user()?>
    <hr>
    No of views: <?php echo $GLOBALS['post_view_count']?>
    <hr>
</div>

<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>


<?php get_footer(); ?>

