<?php
$cat = forum()->getXForumCategory();
$categories = lib()->get_categories_with_depth( $cat->term_id );
?>
<div class="wrap xforum">

    <h2><?php _text('X Forum - List') ?></h2>

    <div class="forum-list">


        <a class="button btn-primary forum-create-button" href="<?php url_forum_create()?>">
            <?php _text('Create Forum') ?>
        </a>


        <a class="button btn-primary forum-create-button" href="<?php echo home_url('?forum=all')?>">
            <?php _text('List All Forum') ?>
        </a>



        <a class="button btn-primary" href="<?php echo home_url('?test=all')?>" target="_blank">
            <?php _text('TEST XForum ( Unit Testing )') ?>
        </a>


        <h2><?php _text('Forum List')?></h2>

        <div class="forum-list container">
            <div class="row">
                <div class="col-xs-3 col-sm-3"><?php _text('Category') ?></div>
                <div class="col-xs-2 col-sm-1"><?php _text('View(Slug)') ?></div>
                <div class="col-xs-2 col-sm-1"><?php _text('Edit') ?></div>
                <div class="col-xs-2 col-sm-1"><?php _text('Delete') ?></div>
                <div class="col-xs-2 col-sm-1"><?php _text('Posts') ?></div>
                <div class="col-xs-1 col-sm-1"><?php _text('Export') ?></div>
                <div class="col-xs-12 col-sm-4"><?php _text('Description') ?></div>
            </div>
            <?php
            if ( $categories ) {
                foreach($categories as $category) {
                    ?>
                    <div class="row">
                        <div class="col-xs-3 col-sm-3">
                            <a href="<?php echo forum()->listURL($category->slug)?>" target="_blank">
                                <?php
                                $pads = str_repeat( '----', $category->depth );
                                echo $pads;
                                ?>
                                <?php echo $category->name?>
                            </a>
                        </div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->listURL($category->slug)?>"><?php echo $category->slug?></a></div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->urlAdminForumEdit($category->term_id)?>"><?php _text('Edit') ?></a></div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->urlForumDo('forum_delete')?>&term_id=<?php echo $category->term_id?>&return_url=<?php echo urlencode(forum()->adminURL())?>"><?php _text('Delete') ?></a></div>
                        <div class="col-xs-2 col-sm-1"><?php echo $category->count?></div>
                        <div class="col-xs-1 col-sm-1"><a href="<?php forum()->urlExport( $category->slug )?>" target="_blank"><?php _text('Export') ?></a></div>
                        <div class="col-xs-12 col-sm-4"><?php echo $category->description?></div>
                    </div>
                <?php } } ?>

        </div>
    </div>
</div>
