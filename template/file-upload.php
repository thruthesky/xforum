<?php
$action = get_option('xforum_url_file_server');
if ( empty($action) ) {
    echo "File Server URL is empty. Update it in admin page.";
    return;
}
/**
 * @note for post edit(write) page, parent_ID, comment_ID are not in use. just ignore them.
 * @note post_ID is 0 when post editing(writing), post_ID is NOT 0 when commenting.
 */
?>
<div class="file-upload-form" post_ID="<?php the_ID()?>" parent_ID="<%=parent_ID%>" comment_ID="<%=comment_ID%>">
    <form action="<?php echo $action?>" target="xforum_hidden_iframe" method="post" enctype="multipart/form-data">
        <input type="hidden" name="domain" value="philgo">
        <input type="hidden" name="uid" value="<?php echo user()->get_session_id()?>">
        <input type="file" name="userfile" placeholder="Choose file" onchange="on_file_upload_submit(this);">
    </form>
</div>
