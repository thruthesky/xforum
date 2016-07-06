<?php

$id = post()->getViewPostID();
setup_postdata(get_post( $id ));

get_header();
/*Custom CSS*/
wp_enqueue_style( 'xforum-view', URL_XFORUM . 'css/its/forum-view.css' );
?>

<?php forum()->list_menu_user()?>
<div class="col-lg-12">
    <small>Posted by: <?php the_author(); ?> </small>
    <small> || No of Views: <?php echo $GLOBALS['post_view_count']?></small>
</div>

<div class="col-lg-8 card card-block">
    <div class="post-title">
        <?php the_title(); ?>
    </div>
    <div class="post-content">
        <?php the_content()?>
    </div>
</div>

<div class="col-lg-4">
    <div class="col-lg-12 post-info">
        <div class="list-group">
            <div class="list-group-item">
                <h5 class="list-group-item-heading">Workers</h5>
                <p class="list-group-item-text">Member1, Member2</p>
            </div>
            <div class="list-group-item">
                <h5 class="list-group-item-heading">Deadline</h5>
                <p class="list-group-item-text">July 14, 2016</p>
            </div>
            <div class="list-group-item">
                <h5 class="list-group-item-heading">Work Status</h5>
                <p class="list-group-item-text">
                    Immediate <br/>
                    Medium <br/>
                    Work Started <br/>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <?php
        if( get_the_author() === wp_get_current_user()->user_login ):  ?>
            <a class="btn btn-success btn-block" href="<?php forum()->urlEdit( get_the_ID() )?>">Edit Issue</a>
        <?php endif; ?>
        <a class="btn btn-primary btn-block" href="<?php forum()->urlList()?>">Issue List</a>

    </div>
</div>

<div class="col-lg-12">
    <hr>
    <?php
    // If comments are open or we have at least one comment, load up the comment template.
    if ( comments_open() || get_comments_number() ) {
        comments_template();
    }

    ?>
</div>

<?php get_footer(); ?>

