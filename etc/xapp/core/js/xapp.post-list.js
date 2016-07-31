/**
 *
 * @file xapp.post-list.js
 *
 */
/**
 *
 */
if ( typeof xapp == 'undefined' ) var xapp = {};
var post_list = xapp.post_list = {};

$(function() {
    var $body = $('body');

    $body.on('click', sl(post_like_button), post_list.post_like_button_clicked);
    $body.on('click', ".post-report-button", post_list.post_report_button_clicked);
    $body.on('click', ".post-copy-button", post_list.post_copy_button_clicked);
    $body.on('click', ".post-move-button", post_list.post_move_button_clicked);
    $body.on('click', ".post-blind-button", post_list.post_blind_button_clicked);
    $body.on('click', ".post-block-button", post_list.post_block_button_clicked);
    $body.on('click', '.show-more', post_list.show_more_clicked);



    // post write / edit
    $body.on('click', sel(post_edit_button), post_list.post_edit_button_clicked);
    $body.on('click', sel(post_write_button), post_list.post_write_button_clicked);
    $body.on('click', sel(post_write_form) + ' .cancel', post_list.post_write_form_cancel);
    $body.on('click', sel(post_write_form) + ' .submit', post_list.post_write_form_submit);



    //$body.on('click', sel(post_edit_form) + ' .cancel', post_list.post_edit_form_cancel);
    //$body.on('click', sel(post_edit_form) + ' .submit', post_list.post_edit_form_submit);

    /// post delete
    $body.on('click', sel(post_delete_button), post_list.post_delete_button_clicked);


    /// comment write / edit
    $body.on('click',
        sel(comment_write_form) + ' .file-upload, ' + sel(comment_write_form) + ' textarea',
        post_list.comment_form_clicked);
    $body.on('click', '.' + comment_edit_button, post_list.comment_edit_button_clicked);
    $body.on('click', sel(comment_write_button), post_list.comment_write_form_submit);
    $body.on('click', sel(comment_cancel_button), post_list.comment_cancel_form_submit);


    /// comment edit
    /**
     * @since 2016-07-29 comment edit merged into comment write.
    $body.on('click', '.' + comment_edit_button, post_list.comment_edit_button_clicked);
    $body.on('click', sl(comment_edit_form) + ' .submit', post_list.comment_edit_form_submit);
    $body.on('click', sl(comment_edit_form) + ' .cancel', post_list.comment_edit_form_cancel);
    */
    /// comment delete
    $body.on('click', '.'+ comment_delete_button, post_list.comment_delete_button_clicked);

    /// comment like
    $body.on('click', '.'+ comment_like_button, post_list.comment_like_button_clicked);


    

    // $body.on('click', sl( file_upload_button ), post_list.file_upload_button_cliecked );
    


});



post_list.show_more_clicked = function() {
    var $this = $(this);
    var $post = $this.parent();
    post_list.show_content($post.find('.content'));

};

post_list.show_content = function ( $content ) {
    $content.css( {
        'overflow': 'visible',
        'max-height' : 'none'
    } );
    x($content).getPost().find('.show-more').remove();
};





/**
 *
 * Returns content of an post/content object.
 *
 * @code
 *      get_content( post )
 *      get_content( comment )
 * @endcode
 *
 */
var get_content = function ( obj ) {
    var content = '';
    if ( obj.content_type == 'undefined' ) {
        content = obj;
    }
    else if ( obj.content_type == 'text/plain'  ) {
        if ( typeof obj.post_content != 'undefined' ) content = sanitize_content( obj.post_content );
        else if ( typeof obj.comment_content != 'undefined' ) content = sanitize_content( obj.comment_content );
        else content = obj;
    }
    else {
        if ( typeof obj.post_content != 'undefined' ) content =  obj.post_content;
        else if ( typeof obj.comment_content != 'undefined' ) content = obj.comment_content;
        else content = obj.post_content;
    }
    return content;
};





post_list.post_edit_button_clicked = function() {
    // console.log('post edit button clicked');
    if ( ele(post_write_form).length ) {
        xapp.alert('Cannot edit', 'You are on editing another post now. Please submit or cancel the post edit before you are going to edit this post.');
        return;
    }
    var $this = $(this);
    var $post = x(this).getPost();
    //console.log($post);

    $post.hide();
    var $m = $(markup.post_write_form( $this ));
    var post_ID = $post.attr('post-id');
    var title = trim($post.find('.title').text());
    var content = trim( $post.find('.content').text());
    $m.find('[name="post_ID"]').val( post_ID );
    $m.find('[name="title"]').val( title );
    $m.find('[name="content"]').val( content );
    $post.after( $m );



    /*
    $post.hide();
    var $m = $(markup.post_edit_form( $this ));
    var post_ID = $post.attr('post-id');
    var title = trim($post.find('.title').text());
    var content = trim( $post.find('.content').text());
    $m.find('[name="post_ID"]').val( post_ID );
    $m.find('[name="title"]').val( title );
    $m.find('[name="content"]').val( content );
    $post.after( $m );
    */
};


post_list.post_delete_button_clicked = function () {
    console.log('post delete button clicked');
    var $this = $(this);
    var $post = get_post( $this );
    var post_ID = $post.attr('post-id');
    var url = xapp.server_url + '?forum=post_delete_submit&response=ajax' +
        '&session_id=' + xapp.session_id +
        '&post_ID=' + post_ID;
    console.log(url);
    $.get(url, function(re) {
        x.deletePost = function () {
            var $post = x.getPost();
            $post.find('.title').text( x.obj.post_title );
            $post.find('.content').text( x.obj.post_content );
            $post.addClass('deleted');
        };
        if ( re.success  ) {
            xapp.alert("Success", "You have deleted a post.");
            // $post.remove();
            //var $post = x( re.data.post ).getPost();
            x( re.data.post ).deletePost();
        }
        else {
            xapp.alert("Failed ...", re['data']['message']);
        }
    } );
};



post_list.post_write_button_clicked =  function() {


    if ( ele(post_write_form).length ) {

        xapp.alert('POST Writing', 'Post write form is already opened. You must close it first before you post another.');
        return;
    }

    var $this = $(this);
    var $header = $this.closest( '.header' );
    $header.after( markup.post_write_form( $this ) );
};



xapp.callback_post_add_show_more = function (data) {

    ///;
    $(".post-list-page[page='"+data.in['page']+"']").find('.post').each(function(index, element){
        //console.log(index);
        //console.log(element);

        var $post = $(element);
        var $content = $post.find('.content');

        // console.log($content);


        //console.log($content[0].scrollHeight);
        //console.log($content.innerHeight());
        /**
         * if the height of content is over-wrapped.
         */
        /**
         * @note innerHeight() 에 +1 을 해야지, 더 잘되는 것 같다.
         *
         */
        //if ( typeof $content[0] != 'undefined' && typeof $content[0].scrollHeight != 'undefined' ) {
            if ( $content[0].scrollHeight > $content.innerHeight() + 1) {
                $content.css('background-color', '#efe9e9');
                $content.after("<div class='show-more'>show more ..." +
                         //"scrollHeight:" + $content[0].scrollHeight +
                         //"innerHeight:" + $content.innerHeight() +
                    "</div>");
            }
        //}
    });
    ///;
};



post_list.post_like_button_clicked = function () {
    var post_ID = x(this).getPostID();
    var $like_button = $(this);
    var url = xapp.server_url +
        '?forum=post_like' +
        '&response=ajax' +
        '&session_id=' + xapp.session_id +
        '&post_ID=' + post_ID;
    console.log(url);
    $.get(url, function(re) {
        if ( x.success( re ) ) {
            $like_button.find('.no').remove();
            $like_button.append('<span class="no">'+re.data.like+'</span>');
        }
    } );
};
post_list.comment_like_button_clicked = function () {
    var $like_button = $(this);
    var url = xapp.server_url +
        '?forum=comment_like' +
        '&response=ajax' +
        '&session_id=' + xapp.session_id +
        '&comment_ID=' + x(this).getCommentID();
    console.log(url);
    $.get(url, function(re) {
        if ( x.success( re ) ) {
            $like_button.find('.no').remove();
            $like_button.append('<span class="no">'+re.data.like+'</span>');
        }
    } );


};


post_list.post_report_button_clicked = function () {

};
post_list.post_copy_button_clicked = function () {

};
post_list.post_move_button_clicked = function () {

};
post_list.post_blind_button_clicked = function () {

};
post_list.post_block_button_clicked = function () {

};
post_list.post_write_form_cancel = function() {
    var $form = x(this).getForm();
    var post_ID = $form.find('[name="post_ID"]').val();
    if ( post_ID ) { // update
        var $post = x(post_ID).getPost();
        $post.show();
        post_list.show_content($post.find('.content'));
    }
    el(post_write_form).remove();
};


post_list.post_write_form_submit = function() {
    var $this = $(this);
    disable_button($this);
    var $form = $this.closest('form');
    var url = xapp.server_url + '?' + $form.serialize();
    console.log(url);
    $.post({
        'url' : url,
        'success' : function(re) {
            console.log(re);
            if ( typeof re.success ) {
                if ( re.success ) {
                    if ( xapp.option.alert.after_post ) xapp.alert("POST Success", "You just have posted...", xapp.reload);
                    else {
                        var post_ID = $form.find('[name="post_ID"]').val();
                        if ( post_ID ) { // update
                            var $post = x.getPost( post_ID);
                            var title = $form.find('[name="title"]').val();
                            var content = $form.find('[name="content"]').val();
                            $post.find('.title').text( title );
                            $post.find('.content').html( sanitize_content(content) );
                            el(post_write_form).remove();
                            $post.show();
                        }
                        else xapp.reload(); // for new post, just reload.
                    }
                }
                else {
                    xapp.alert( "Error on posting", re.data.message );
                }
            }
            else {
                xapp.alert("Server error", "Cannot parse response. There might be an error inside the server. ( Maybe it's a script error. )");
            }
            enable_button( $this );
        },
        'error' : function () {
            enable_button( $this );
            xapp.alert("Post query error", "Error occurs on post query.");
        }
    });
};



/**
post_list.post_edit_form_cancel = function () {

    var $cancel = $(this);
    var $edit = $cancel.closest( sel(post_edit_form) );

    $edit.remove();
    var post_ID = $edit.find('[name="post_ID"]').val();
    $('.post[post-id="'+post_ID+'"').show();

};
post_list.post_edit_form_submit = function () {
    var $submit = $(this);
    $submit.prop('disabled', true);
    var $edit = $submit.closest( sel(post_edit_form) );
    var post_ID = $edit.find('[name="post_ID"]').val();
    var $post = $('.post[post-id="'+post_ID+'"');
    var url = xapp.server_url + '?' + $edit.find('form').serialize();
    console.log(url);
    $.post({
        'url' : url,
        'success' : function(re) {
            console.log(re);
            if ( re.success ) {
                var title = $edit.find('[name="title"]').val();
                var content = $edit.find('[name="content"]').val();
                $post.find('.title').text( title );
                $post.find('.content').html( sanitize_content(content) );
                $edit.remove();
                $post.show();
                if ( xapp.option.alert.after_edit ) xapp.alert("EDIT Success", "You just have edited a post.");

            }
            else {
                xapp.alert( 'Post edit failed', xapp.get_error_message(re.data) );
            }
            $submit.prop('disabled', false);
        },
        'error' : function () {
            $submit.prop('disabled', false);
            alert('error on edit write form');
        }
    });

};
 */


/**
 * This expands comment textarea when user clicks on camera button or textarea on comment box.
 */
post_list.comment_form_clicked = function() {
    var $form = $(this).closest( sel(comment_write_form) );
    console.log('comment form clicked');
    if  ( $form.hasClass('selected') ) {
        // console.log('and it has .selected already.');
    }
    else {
        $form.addClass('selected');
        // console.log('adding .selected');
    }
};

post_list.comment_edit_button_clicked = function () {
    //console.log('comment edit button clicked');

    if ( el(comment_edit_form).length ) return xapp.alert('Cannot edit', 'You are on another comment editing form. Please submit or cancel the comment editing form before you are going to edit another.');

    //var $button = $(this);
    //var $comment = $button.closest( '.comment' );

    var $comment = find_comment( this );
    var m = markup.comment_write_form( $comment );
    $comment.hide();
    $comment.after( m );

};


post_list.comment_write_form_submit = function () {

    var $submit = $(this);
    //$submit.prop('disabled', true);
    disable_button( $submit );
    var $comment_form = $submit.closest( sel(comment_write_form) );
    var post_ID = $comment_form.find('[name="post_ID"]').val();
    var $post = $('.post[post-id="'+post_ID+'"');
    var url = xapp.server_url + '?' + $comment_form.find('form').serialize();
    console.log( url );

    $.post({
        'url' : url,
        'success' : function(re) {
            console.log(re);

            if (x.success(re)) {
                var comment_ID = $comment_form.find('[name="comment_ID"]').val();
                if ( isEmpty( comment_ID ) ) { // new
                    post_list.close_comment_write_form( $comment_form );
                    x( re.data.comment ).insert();
                    x.increaseNoOfComments();
                }
                else { // update ...

                    $comment_form.remove();
                    x(re.data.comment).replace();

                    /// post_list.close_comment_write_form($comment_form);
                    ///var comment = markup.comment(re.data.comment);
                    ///var post_ID = re.data.comment.comment_post_ID;

                    //find_comment( re.data.comment.comment_ID ).remove();
                    //post_comment_list(post_ID).prepend(comment);
                    //$comment_form.remove();
                }

            }
            //$submit.prop('disabled', false);
            enable_button($submit);
        },
        'error' : function () {
            //$submit.prop('disabled', false);
            enable_button($submit);
            alert('error on comment write');
        }
    });


};

x.isCommentEditForm = function () {
    var comment_ID = x.getForm().find('[name="comment_ID"]').val();
    return ! isEmpty( comment_ID );
};

x.cancelCommentEdit = function () {
    if ( x.isCommentEditForm() ) {
        var comment_ID = x.getForm().find('[name="comment_ID"]').val();
        x.getForm().remove();
        x(comment_ID).findComment().show();
    }
};

post_list.comment_cancel_form_submit = function () {
    x(this).cancelCommentEdit();
    var $cancel = $(this);
    var $form = $cancel.closest( 'form' ).parent();
    post_list.close_comment_write_form( $form );
};

post_list.close_comment_write_form = function ( $form ) {
    $form.find('textarea').val('');
    $form.removeClass('selected');
};


/**
 *
 * @since comment edit merged into comment write.
 *
 *
post_list.comment_edit_form_submit = function () {

    var $submit = $(this);

    ///disable_button($submit);
    ///var $form = find_comment_edit_form($submit);
    ///
    var $form = find_comment_edit_form( disable_button( this ) );

    //var post_ID = get_post_id($comment_form);
    //var $post = get_post(post_ID);
    var url = xapp.server_url + '?' + $form.find('form').serialize();
    console.log(url);

    $.post({
        'url': url,
        'success': function (re) {
            console.log(re);

            if (re.success) {
                if (xapp.option.alert.after_edit) xapp.alert("Comment edit success", "You just have just edited a comment.");
                post_list.close_comment_write_form($form);
                var comment = markup.comment(re.data.comment);
                var post_ID = re.data.comment.comment_post_ID;
                find_comment( re.data.comment.comment_ID ).remove();
                post_comment_list(post_ID).prepend(comment);
                $form.remove();
            }
            else {
                xapp.alert('Comment edit failed', xapp.get_error_message(re.data));
            }
            enable_button($submit);
        },
        'error': function () {
            enable_button($submit);
            alert('error on comment write');
        }
    });
};


post_list.comment_edit_form_cancel = function () {
    var $form = find_comment_edit_form( this );
    find_comment( get_comment_ID( $form ) ).show();
    $form.remove();
};
*/



post_list.comment_delete_button_clicked = function () {
    // console.log('comment delete');


    x( this );
    var $comment = x.getComment();
    var comment_ID = x.getCommentID();
    var $post = x.getPost();
    var post_ID = $post.attr('post-id');

    var url = xapp.server_url +
        '?forum=comment_delete_submit&response=ajax' +
        '&session_id=' + xapp.session_id +
        '&comment_ID=' + comment_ID;
    console.log(url);
    $.get(url, function(re) {
        if ( re.success  ) {
            xapp.alert("Comment deleted", "You have deleted a comment.");

            //
            x( re.data.comment ).replace();
        }
        else {
            xapp.alert("Failed ...", re['data']['message']);
        }
    } );
};



/// file upload
function comment_file_upload(input) {

    var $button = $(input);
    var $form = $button.closest( 'form' );
    $form.prop('action', xapp.file_server_url);
    $form.prepend('<input type="hidden" name="uid" value="'+xapp.session_id+'">');
    var $progress = $('<progress value="0" max="100"></progress>');
    $form.ajaxSubmit( {
        beforeSend: function() {
            $form.append($progress);
            console.log('beforeSend:');
        },

        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            $progress.val( percentComplete );
        },
        success: function() {
            console.log('success:');
        },
        complete: function(xhr) {
            console.log('complete:');
            var re = xhr.responseText;
            console.log(re);
            var data = JSON.parse( re );
            if ( isEmpty(data.error) ) {
                var $img = '<img src="'+data.url+'">';
                $progress.replaceWith( $img );
            }
            else {
                alert( data.error );
            }
            $form.find('[name="uid"]').remove();
        }
    } );


}
