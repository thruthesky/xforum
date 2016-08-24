<?php
function get_comment_form( $o = [] )
{
    $default = [
        'first' => 'no'
    ];
    $o = array_merge( $default, $o );
    if ( $o['first'] == 'yes' ) $first = ' first="yes"';
    else $first = '';
    $file_server_domain = get_option('xforum_file_server_domain');
    ?>
    <div class="form comment-write"<?php echo $first?>>
        <form enctype="multipart/form-data" action="" method="POST">
            <input type="hidden" name="content_type" value="text/plain">
            <input type="hidden" name="domain" value="<?php echo $file_server_domain?>">
            <input type="hidden" name="session_id" value="<?php echo in('session_id') ?>">
            <input type="hidden" name="forum" value="comment_edit_submit">
            <input type="hidden" name="response" value="ajax">
            <input type="hidden" name="post_ID" value="">
            <input type="hidden" name="comment_parent" value="">
            <input type="hidden" name="comment_ID" value="">
            <table>
                <tr valign="top">
                    <td>
                        <div class="file-upload">
                            <input type="file" name="userfile" onchange="post.on_file_upload(this);">
                            <i class="icon fa fa-camera"></i>
                        </div>
                    </td>
                    <td width="99%">
                        <textarea name="comment_content"></textarea>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="files"></div>
                        <div class="message loader"></div>
                        <div class="buttons">
                            <button class="comment-edit-submit" type="button">Submit</button>
                            <button class="comment-edit-cancel" type="button">Cancel</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php
}
