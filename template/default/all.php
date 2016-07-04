<?php
get_header();
?>

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


            <h2><?php _e('Forum List', 'xforum')?></h2>

            <div class="forum-list container">
                <div class="row">
                    <div class="col-xs-3 col-sm-3">Category</div>
                    <div class="col-xs-3 col-sm-3">View(Slug)</div>
                    <div class="col-xs-2 col-sm-2">Posts</div>
                    <div class="col-xs-4 col-sm-4">Description</div>
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
                            <div class="col-xs-3 col-sm-3"><a href="<?php echo forum()->listURL($category->slug)?>"><?php echo $category->slug?></a></div>
                            <div class="col-xs-2 col-sm-2"><?php echo $category->count?></div>
                            <div class="col-xs-4 col-sm-4"><?php echo $category->description?></div>
                        </div>
                    <?php } } ?>

            </div>
        </div>
    </div>


<?php
get_footer();
