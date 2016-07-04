<div class="file-upload">
    <form action="<?php echo get_option('xforum_url_file_server')?>" target="hidden_iframe_file_upload" method="post" enctype="multipart/form-data">
        <input type="hidden" name="domain" value="philgo">
        <input type="hidden" name="uid" value="<?php user()->uniqid()?>">
        <input type="file" name="userfile" placeholder="Choose file" onchange="submit();">
    </form>
</div>
<script>
    window.addEventListener("message", receiveMessage, false);
    function receiveMessage( re ) {
        var url = re.data.url;
        var filename = re.data.filename;
        var m = '<img class="file-upload" alt="'+filename+'" src="'+url+'"/>';
        tinymce.activeEditor.insertContent(m);
    }
</script>
<iframe name="hidden_iframe_file_upload" src="javascript:;" width="0" height="0" style="width:0; height:0; display: none;"></iframe>
