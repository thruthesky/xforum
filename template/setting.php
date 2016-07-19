<?php
include DIR_XFORUM . 'etc/admin-function.php';
?>
<div class="wrap xforum setting">
<h2><?php _text('XForum Settings') ?></h2>
    <div class="forum-create">
        <form action="?" method="post">
            <input type="hidden" name="forum" value="setting_submit">
            <input type="hidden" name="on_error" value="alert_and_go_back">
            <input type="hidden" name="return_url" value="<?php forum()->urlAdminSetting()?>">

            <fieldset class="form-group">
                <label for="xforum_admins"><?php _text('XForum Admins') ?></label>
                <input id='xforum_admins' class='form-control' type="text" name="xforum_admins" placeholder="<?php _text('Input forum admins separated by comman(,)') ?>" value="<?php echo get_option('xforum_admins', null)?>"><br>
                <small class="text-muted"><?php _text('Input admins ID. Put many separated by comma(,)') ?></small>
            </fieldset>


            <fieldset class="form-group">
                <label for="xforum_url_file_server"><?php _text('File Server URL') ?></label>
                <input id='xforum_url_file_server' class='form-control' type="text" name="xforum_url_file_server" placeholder="<?php _text('Please input forum template postfix') ?>" value="<?php echo get_option('xforum_url_file_server', null)?>">
                <small class="text-muted"><?php _text("Input full file server url beginning with 'http://' and ending with 'index.php'") ?></small>
            </fieldset>

            <fieldset class="form-group">
                <label for="xforum_file_server_domain"><?php _text('File Server Domain( Folder )') ?></label>
                <input id='xforum_file_server_domain' class='form-control' type="text" name="xforum_file_server_domain" placeholder="<?php _text('input file server domain') ?>" value="<?php echo get_option('xforum_file_server_domain', null)?>">
                <small class="text-muted"><?php _text('Input file server domain.') ?></small>
            </fieldset>



            <input type="submit" class="btn btn-primary" value="Update Settings">
            <a href="<?php echo forum()->urlAdminPage()?>" type="button" class="btn btn-secondary forum-create-cancel-button"><?php _text('Cancel') ?></a>
        </form>
    </div>
</div>


