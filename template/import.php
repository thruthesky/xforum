<?php

?>
<div class="wrap xforum setting">
    <h2>XForum Settings</h2>
    <div class="forum-create">
        <?php if ( in('success') ) { ?>
            SUCCESS
        <?php } ?>
        <?php if ( in('error_code') ) { ?>
            ERROR (<?php echo in('error_code')?>) : <?php echo in('error_message'); ?>
        <?php } ?>
        <form action="?" method="post">
            <input type="hidden" name="forum" value="import_submit">
            <input type="hidden" name="return_url" value="<?php forum()->urlAdminImport()?>&amp;success=true">
            <input type="hidden" name="return_url_on_error" value="<?php forum()->urlAdminImport()?>">


            <fieldset class="form-group">
                <label for="xforum_slug">Category slug to save</label>
                <input id='xforum_slug' class='form-control' type="text" name="slug" placeholder="Input forum slug">
            </fieldset>

            <fieldset class="form-group">
                <label for="forum-data">FORUM DATA ( JSON string )</label>
                <textarea id='forum-data' class='form-control' type="text" name="data"></textarea>
                <small class="text-muted">Input JSON string.</small>
            </fieldset>

            <input type="submit" class="btn btn-primary" value="Import forum data">
        </form>
    </div>
</div>


