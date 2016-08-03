<script type="text/template" id="post-write-template">
    <div class="form post-write">
        <form enctype="multipart/form-data" action="" method="POST">
            <input type="hidden" name="do" value="post_edit_submit">
            <input type="hidden" name="session_id" value="<?php echo in('session_id')?>">
            <input type="hidden" name="content_type" value="text/plain">
            <input type="hidden" name="response" value="template/api/view">
            <input type="hidden" name="slug" value="<?php echo in('slug')?>">
            <input type="hidden" name="post_ID" value="">
            <input type="text" name="title">
            <textarea name="content"></textarea>
            <div class="message loader"></div>
            <button type="button" class="post-write-submit">Submit</button>
            <button type="button" class="post-write-cancel btn btn-secondary btn-sm">CANCEL</button>
        </form>
    </div>
</script>

<script type="text/template" id="comment-write-template">
    <?php get_comment_form() ?>
</script>