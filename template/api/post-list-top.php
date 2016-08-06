<?php
$file_server_domain = get_option('xforum_file_server_domain');
$file_server_url = get_option('xforum_url_file_server');
?>
<script>
    var file_server_url = '<?php echo $file_server_url?>';
    var post_title_deleted = '<?php echo forum::post_title_deleted?>';
    var post_content_deleted = '<?php echo forum::post_content_deleted?>';
    var comment_deleted = '<?php echo forum::comment_deleted?>';
</script>
<script type="text/template" id="post-write-template">
    <div class="form post-write">
        <form enctype="multipart/form-data" action="" method="POST">
            <input type="hidden" name="do" value="post_edit_submit">
            <input type="hidden" name="domain" value="<?php echo $file_server_domain?>">
            <input type="hidden" name="session_id" value="<?php echo in('session_id')?>">
            <input type="hidden" name="content_type" value="text/plain">
            <input type="hidden" name="response" value="template/api/view">
            <input type="hidden" name="slug" value="<?php echo in('slug')?>">
            <input type="hidden" name="post_ID" value="">
            <input type="text" name="title">
            <textarea name="content"></textarea>
            <div class="files"></div>
            <div class="message loader"></div>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <div class="file-upload">
                            <input type="file" name="userfile" onchange="post.on_file_upload(this);">
                            <i class="icon fa fa-camera"></i>
                        </div>
                    </td>
                    <td width="50%" align="right">
                        <div class="buttons">
                            <button type="button" class="post-write-submit">Submit</button>
                            <button type="button" class="post-write-cancel btn btn-secondary btn-sm">CANCEL</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</script>

<script type="text/template" id="comment-write-template">
    <?php get_comment_form() ?>
</script>