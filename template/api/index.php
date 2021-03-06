<?php
include_once 'function.php';
$x = ['name'=>'index']; include 'header.php';
$cat = forum()->getXForumCategory();
$categories = lib()->get_categories_with_depth( $cat->term_id );
?>

    <h2><?php _text('X Forum - List') ?></h2>

    <div class="list-group">


        <?php
        if ( $categories ) {
            foreach($categories as $category) {
                $pads = str_repeat( '----', $category->depth );
                ?>
                <a class="list-group-item list-group-item-action" forum="<?php echo $category->slug?>" href="<?php echo forum()->listURL($category->slug)?>">
                    <h5 class="list-group-item-heading"><?php echo $pads?><?php echo $category->name?> (<?php echo $category->count?>)</h5>
                    <p class="list-group-item-text"><?php echo $category->slug?> : <?php echo $category->description?></p>
                </a>
            <?php }
        } ?>

    </div>

    <p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p>
    <p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p>
    <p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p>
    <p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p><p>.</p>

<?php
include 'footer.php';
