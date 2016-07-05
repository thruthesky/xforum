<?php
$cat = forum()->getXForumCategory();
$categories = lib()->get_categories_with_depth( $cat->term_id );
?>
<div class="wrap xforum">

    <h2>X Forum - List</h2>

    <div class="forum-list">


        <a class="button btn-primary forum-create-button" href="<?php url_forum_create()?>">
            Create Forum
        </a>


        <a class="button btn-primary" href="<?php echo home_url('?test=all')?>" target="_blank">
            TEST XForum ( Unit Testing )
        </a>


        <h2><?php _e('Forum List', 'xforum')?></h2>

        <div class="forum-list container">
            <div class="row">
                <div class="col-xs-4 col-sm-4">Category</div>
                <div class="col-xs-2 col-sm-1">View(Slug)</div>
                <div class="col-xs-2 col-sm-1">Edit</div>
                <div class="col-xs-2 col-sm-1">Delete</div>
                <div class="col-xs-2 col-sm-1">Posts</div>
                <div class="col-xs-12 col-sm-4">Description</div>
            </div>
            <?php
            if ( $categories ) {
                foreach($categories as $category) {
                    ?>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4">
                            <a href="<?php echo forum()->listURL($category->slug)?>" target="_blank">
                                <?php
                                $pads = str_repeat( '----', $category->depth );
                                echo $pads;
                                ?>
                                <?php echo $category->name?>
                            </a>
                        </div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->listURL($category->slug)?>"><?php echo $category->slug?></a></div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->urlAdminForumEdit($category->term_id)?>">Edit</a></div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->urlForumDo('forum_delete')?>&cat_ID=<?php echo $category->term_id?>&return_url=<?php echo urlencode(forum()->adminURL())?>">Delete</a></div>
                        <div class="col-xs-2 col-sm-1"><?php echo $category->count?></div>
                        <div class="col-xs-12 col-sm-4"><?php echo $category->description?></div>
                    </div>
                <?php } } ?>

        </div>
    </div>
</div>
