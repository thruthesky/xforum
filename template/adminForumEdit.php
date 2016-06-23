<?php
$categories = forum()->categories();
$cat_ID = in('cat_ID');
if ( empty($cat_ID) ) jsBack("cat_ID was not provided.");
$category = get_category( $cat_ID );


?>

<div class="wrap xforum">

    <h2>X Forum - EDIT</h2>

    <div class="forum-create">
        <form action="?" method="post">
            <input type="hidden" name="do" value="forum_edit">
            <input type="hidden" name="return_url" value="<?php echo forum()->urlAdminPage()?>">
            <input type="hidden" name="on_error" value="alert_and_go_back">
            <input type="hidden" name="cat_ID"  value="<?php echo $cat_ID ?>">
            <?php forum_edit_line_category_nicename($category->slug) ?>
            <?php forum_edit_line_cat_name( $category->name ) ?>
            <?php forum_edit_line_category_description( $category->description ) ?>
            <?php forum_edit_line_category_parent( $category->category_parent ) ?>
            <?php forum_edit_line_template( $cat_ID ) ?>
            <input type="submit" class="btn btn-primary" value="Edit Forum">
            <a href="<?php echo forum()->urlAdminPage()?>" type="button" class="btn btn-secondary forum-create-cancel-button">Cancel</a>
        </form>
    </div>


</div>
