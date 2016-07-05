<div class="file-upload-form">
    <form action="<?php echo get_option('xforum_url_file_server')?>" target="hidden_iframe_file_upload" method="post" enctype="multipart/form-data">
        <input type="hidden" name="domain" value="philgo">
        <input type="hidden" name="uid" value="<?php user()->uniqid()?>">
        <input type="hidden" name="return" value="">
        <input type="file" name="userfile" placeholder="Choose file" onchange="submit();">
    </form>
</div>
<script>
    var content_type = '<?php echo $type?>';
    window.addEventListener("message", receiveMessage, false);
    function receiveMessage( re ) {
        var url = re.data.url;
        var filename = re.data.filename;
        var m = '<img class="file-upload" alt="'+filename+'" src="'+url+'"/>';
        if ( content_type == 'comment' ) {
            // 여기서부터...
            // 업로드된 파일의 data/upload/... 이후의 값만 코멘트 양식의 hidden 으로 넣는다.
            // 보기/수정을 할 때 표시를 하고, 삭제를 할 수 있도록 한다. ( 파일서버에서 삭제하지 않음 )
            //

            $(".file-upload-form").append( m );
        }
        else {
            tinymce.activeEditor.insertContent(m);
        }
    }
</script>
<iframe name="hidden_iframe_file_upload" src="javascript:;" width="0" height="0" style="width:0; height:0; display: none;"></iframe>
