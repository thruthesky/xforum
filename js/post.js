
var remember_comment_parent = 0;
function on_file_upload_submit(box) {
    //console.log('on_file_upload_submit');
    var $this = $(box);
    var $form = $this.parent( 'form' );
    var $file_upload_form = $form.parent();
    //var action = $form.prop('action');
    //var comment_ID = $file_upload_form.attr('comment_ID');
    remember_comment_parent = $file_upload_form.attr('parent_ID');
    //console.log('remember_comment_parent:' + remember_comment_parent);
    $form.submit(); // form is submitted by default.
}
window.addEventListener("message", receiveMessage, false);
function receiveMessage( re ) {
    var $upload_form = find_active_comment_file_upload_form();
    var post_ID = $upload_form.attr('post_ID');
    var url = re.data.url;
    var filename = re.data.filename;
    var m = '<img class="file-upload" alt="'+filename+'" src="'+url+'"/>';
    if ( post_ID > 0 ) {
        //console.log(re);
        $upload_form.append( m );
        update_uploaded_files( $upload_form );
    }
    else {
        tinymce.activeEditor.insertContent(m);
    }
}
function find_active_comment_file_upload_form() {
    var $form = $('.file-upload-form[parent_ID="'+remember_comment_parent+'"]');
    //console.log( 'parent_ID:' + $form.attr('parent_ID'));
    return $form;
}
function find_active_comment_form() {
    return $('.comment-form[parent_ID="'+remember_comment_parent+'"] form');
}
function update_uploaded_files( $upload_form ) {
    var $form = find_active_comment_form();
    //console.log($form);
    var files = '';
    _.each( $upload_form.find('img'), function(img) {
        var $img = $(img);
        files += '| |' + $img.prop('src');
    });
    $form.find('[name="files"]').val(files);
}