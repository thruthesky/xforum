<?php
/**
 *
 *
 * ---------------------------------------------------------------------------
 *
 *
 * W A R N I N G : Theme Development on Desktop version has been discontinued.
 *
 * B E C A U S E : People use mobile for web browsing
 *
 *      And 'Mobile theme version' can handle desktop also.
 *
 *      So, we develop mobile theme version only which works on desktop also.
 *
 *
 * ---------------------------------------------------------------------------
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
?>
<?php
get_header();
?>

<?php
$cat = forum()->getXForumCategory();
$categories = lib()->get_categories_with_depth( $cat->term_id );
?>
    <div class="wrap xforum">

        <h2><?php _text('X Forum - List') ?></h2>


        <div class="forum-list container">
            <div class="row">
                <div class="col-xs-3 col-sm-3"><?php _text('Category') ?></div>
                <div class="col-xs-3 col-sm-3"><?php _text('View(Slug)') ?></div>
                <div class="col-xs-2 col-sm-2"><?php _text('Posts') ?></div>
                <div class="col-xs-4 col-sm-4"><?php _text('Description') ?></div>
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

<?php
get_footer();
