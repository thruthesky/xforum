<?php
$categories = forum()->categories();
$term_id = in('term_id');
if ( empty($term_id) ) jsBack("term_id was not provided.");

$category = get_category( $term_id );
forum()->setCategory( $category->slug );


?>

<div class="wrap xforum">

    <h2><?php _text('X Forum - EDIT') ?></h2>

    <div class="forum-create">
        <form action="?" method="post">
            <input type="hidden" name="do" value="forum_edit">
            <input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']?>">
            <input type="hidden" name="on_error" value="alert_and_go_back">
            <input type="hidden" name="term_id"  value="<?php echo $term_id ?>">

            <?php forum_edit_line_slug($category->slug) ?>
            <?php forum_edit_line_cat_name( $category->name ) ?>
            <?php forum_edit_line_category_description( $category->description ) ?>
            <?php forum_edit_line_category_parent( $category->category_parent ) ?>
            <?php forum_edit_line_admins( $term_id ) ?>
            <?php forum_edit_line_members( $term_id ) ?>
            <?php forum_edit_line_template( $term_id ) ?>
            <?php forum_edit_line_category( $term_id ) ?>

            <?php forum_edit_line_posts_per_page( ) ?>


            <?php forum_edit_line_view( ) ?>
            <?php forum_edit_line_write( ) ?>
            <?php forum_edit_line_list( ) ?>

            <input type="submit" class="btn btn-primary" value="Edit Forum">
            <a href="<?php echo forum()->urlAdminPage()?>" type="button" class="btn btn-secondary forum-create-cancel-button"><?php _text('Cancel') ?></a>
        </form>
    </div>


</div>
