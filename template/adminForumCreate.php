<?php
?>
<div class="wrap xforum create">


    <h2>X Forum - Create</h2>

    <div class="forum-create">
        <form action="?" method="post">
            <input type="hidden" name="do" value="forum_create">
            <input type="hidden" name="on_error" value="alert_and_go_back">
            <input type="hidden" name="return_url" value="<?php echo urlencode( url_admin_page() )?>">
            <?php forum_edit_line_category_nicename() ?>
            <?php forum_edit_line_cat_name() ?>
            <?php forum_edit_line_category_description() ?>
            <?php forum_edit_line_category_parent() ?>
            <?php forum_edit_line_template() ?>
            <input type="submit" class="btn btn-primary" value="Create Forum">
            <a href="<?php echo forum()->urlAdminPage()?>" type="button" class="btn btn-secondary forum-create-cancel-button">Cancel</a>
        </form>
    </div>

</div>
