var x = {};
x.slug = ''; /// forum slug.
x.posts_per_page = 4;

x.debug = true;
var post = {};
var db = Lockr;
var session_id = db.get('session_id');
var user_login = db.get('user_login');
var user_nicename = db.get('user_nicename');
var server_url = document.location.origin + '?session_id=' + session_id + '&';


$(function(){
    x.init();
    if ( x.indexPage() ) {
        console.log('index page');
        x.loadPage(function() {

            $('.list-group a:eq(0)').click();

            /// test
            //$('[panel="menu"]').click();
            ///$('[panel="login"]').click();
            ///
            //$('[panel="user"]').click();
        });
    }
    else {
        console.log("error on routing");
    }
    var $body = $('body');
    $body.on('click', '[forum]', x.loadForum);
    $body.on('click', '[panel]', panel.on_click);
    $body.on('click', '.panel-close', panel.on_close);
    $body.on('click', '.login-submit', user.login_submit);
    $body.on('click', '.logout', user.logout);



    $body.on('submit', '.post-page form', function() { return false; } ); // block submit by entering on input.

    // post
    $body.on('click', '.post-write-button', post.on_post_write_button_click);
    $body.on('click', '.post-edit-button', post.on_post_edit_button_click);
    $body.on('click', '.post-write-submit', post.on_post_write_submit);
    $body.on('click', '.post-write-cancel', post.on_post_write_cancel);
    $body.on('click', '.post-delete-button', post.on_post_delete_button_click);

    $body.on('click', '.post-edit-cancel', post.on_post_edit_cancel);


    /// comment write/edit
    $body.on('click', '.form.comment-write .file-upload, .form.comment-write textarea', post.comment_form_clicked);
    $body.on('click', '.comment-edit-button', post.on_comment_edit_button_clicked);
    $body.on('click', '.comment-edit-cancel', post.on_comment_edit_cancel);
    $body.on('click', '.comment-edit-submit', post.on_comment_edit_submit);
    $body.on('click', '.comment-delete-button', post.on_comment_delete_button_clicked);


    /// like button for post & comment
    post.like_button_clicked = function () {
        console.log("like button clicked");

        var $like_button = $(this);
        var url = $like_button.getLikeURL();
        console.log(url);
        $.get(url, function(re) {
            if ( x.success( re ) ) {
                $like_button.find('.no').remove();
                $like_button.append('<span class="no">'+re.data.like+'</span>');
            }
        } );

    };
    $body.on('click', '.post-like-button, .comment-like-button', post.like_button_clicked);

});
x.loadForumEnd = function() {
    // test
    //
    //
    // $('.post-page[no="1"]').find('.post-write-button').click(); // open new post button

    // $('.post-edit-button:eq(0)').click(); /// test. open post edit form.

    //$('.post-page[no="1"]').find('.post:first-child').find('.post-edit-button').click(); //

    /// $('.form.comment-write:eq(0)').find('[name="comment_content"]').click(); // open comment write form

    // $('.comment-edit-button:eq(0)').click(); // open comment edit form
};
x.init = function() {
    var parse_query_string = function () {
        x.in = {};
        x.query = '';
        var splits = location.href.split('?');
        if ( splits.length > 1 ) {
            x.query = splits[1];
            parse_str( x.query, x.in );
        }
        console.log('query: ', x.query);
        console.log('x.in: ', x.in);
    };
    parse_query_string();
};
x.holder = function() {
    return $('.x-holder');
};
x.content = function() {
    return $('.x-content');
};
x.indexPage = function() {
    return isEmpty(x.in);
};
x.forumPage = function() {
    return x.in && x.in.forum;
};
x.loader = function(o) {
    var defaults = {
        'icon' : 'fa-pulse fa-3x fa-fw',
        'text' : 'Loading ...'
    };
    o = $.extend( defaults, o );
    return '<i class="loader fa fa-spinner '+ o.icon +'"></i>' + o.text;
};
x.removeLoader = function() {
    $('.loader').remove();
};

/**
 *
 * Returns true if the result of 'xforum api' is success.
 * Otherwise, it alerts and returns false.
 *
 * @param re - the returned result from 'xforum api' ( xforum api 의 결과를 그대로 이 함수로 전달하면 된다. )
 *
 * @returns {boolean}
 */
x.success = function ( re, title ) {
    if ( typeof re.success == 'undefined' ) {
        title = title ? title : 'Server internal error';
        x.alert(title, 'Malformed server response. Server script printed error.');
        return false;
    }
    else if ( re.success ) return true;
    else {
        title = title ? title : 'Error';
        x.alert(title, x.get_error_message( re['data'] ));
    }
};

x.get_error_message = function (data) {
    var code = data.code;
    var message = data.message;
    return "Error " + code + " : " + message;
};


/**
 *
 *
 * @code
 *
 *      xapp.alert("POST Success", "You just have posted...", xapp.reload); // with a callback
 *
 *      xapp.alert("EDIT Success", "You just have edited a psot.");     // without callback
 *
 * @endcode
 *
 */
x.alert = function( title, content, callback ) {
    var m = '' +
        '<div class="x-alert modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalLabeled" aria-hidden="true">' +
        '   <div class="modal-dialog modal-sm">' +
        '       <div class="modal-content">' +
        '           <div class="modal-header">' +
        '               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        '               <h4 class="modal-title" id="gridModalLabel">'+title+'</h4>' +
        '           </div>' +
        '           <div class="modal-body">' +
        content +
        '           </div>' +
        '           <div class="modal-footer">' +
        '               <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>' +
        '           </div>' +
        '       </div>' +
        '   </div>' +
        '</div>' +
        '';
    $('body').append( m );
    var $x_alert = $('.x-alert');
    $x_alert.modal('show');
    function handler_xapp_alert_close(e) {
        $('.x-alert').remove();
        if ( typeof callback == 'function' ) callback();
    }
    $x_alert.on('hidden.bs.modal', handler_xapp_alert_close);
};


x.loadForum = function (e) {
    e.preventDefault();
    x.slug = $(this).attr('forum');
    console.log('loading forum');
    // var url = server_url + "forum=api&action=page&name=post-list&slug=" + x.slug;
    var url = server_url + 'forum=api&action=post_list&slug=' + x.slug;
    console.log(url);
    $.get( url, function(re) {
        x.content().html( markup.postList(re) );
        x.loadForumEnd();
    });
};



(function($){
    $.fn.getName = function() {
        if ( this.hasClass('x-page') ) return this.attr('name');
        else return 'undefined';
    };
    $.fn.getPage = function() {
        return $('.x-page');
    };
    /**
     * @code
     *
     console.log( $('body').isIndexPage() );
     * @endcodce
     * @returns {boolean}
     */
    $.fn.isFrontPage = function() {
        return ! this.getPage().length;
    };


    /**
     *
     *
     * Returns true if the form is for a top comment right under a post.
     *
     * 'this' must be a comment form object.
     *
     * @returns {*}
     */
    $.fn.isFirstDepthComment = function() {
        return this.attr('first') == 'yes';
    };

    /**
     * Returns true if the form is for creating a comment ( not for editing )
     * @note 'this' must be a comment form object.
     * @returns {boolean}
     */
    $.fn.isNewComment = function() {
        return isEmpty(this.find('[name="comment_ID"]').val());
    };



    /**
     * Returns true if this form object is comment form object.
     *
     * @note 'this' must be a form object.
     */
    $.fn.isComment = function()  {
        return isEmpty(this.value('slug'));
    };

    /**
     * Returns the value of attr, prop or input.
     * @param name
     * @returns {string}
     */
    $.fn.value = function( name ) {
        var v = '';
        v = this.attr(name); if ( ! isEmpty(v) ) return v;
        v = this.prop(name); if ( ! isEmpty(v) ) return v;
        var obj = this.find('[name="'+name+'"]');
        if ( obj.length ) {
            v = obj.val();
            if ( ! isEmpty(v) ) return v;
        }
        obj = this.find('['+name+']');
        if ( obj.length ) {
            v = obj.val();
            if ( ! isEmpty(v) ) return v;
        }
        return v;
    };



    $.fn.getForm = function() {
        return this.closest( '.form' );
    };
    $.fn.getPost = function(post_ID) {
        if ( post_ID ) return $('.post[no="'+post_ID+'"]');
        else return this.closest( '.post' );
    };
    $.fn.getComment = function(comment_ID) {
        if ( comment_ID ) return $('.comment[no="'+comment_ID+'"]');
        else {
            if ( this.hasClass('comment-write') ) { // if it's a comment edit form.
                comment_ID = this.value('comment_ID');
                if ( comment_ID ) return this.getComment(comment_ID);
            }
            return this.closest( '.comment' );
        }
    };
    $.fn.disableButtons = function() {
        this.find('button').prop('disabled', true);
        return this;
    };
    $.fn.enableButtons = function() {
        this.find('button').prop('disabled', false);
        return this;
    };
    $.fn.findPost = function( post_ID ) {
        return $('.post[no="'+post_ID+'"]');
    };
    $.fn.showLoader = function( o ) {
        this.find('.loader').html( getLoader( o ) );
        return this;
    };
    $.fn.hideLoader = function() {
        this.find('.loader').html( '' );
        return this;
    };
    /**
     * 'this' must be '.form'
     * @returns {string}
     */
    $.fn.getURL = function() {
        return server_url + this.find('form').serialize();
    };

    /**
     * 'this' must be either '.post' or '.comment'
     */
    $.fn.getDeleteURL = function() {
        var url = server_url + '?session_id='+session_id+'&response=ajax&forum=';
        if ( this.hasClass('post') ) {
            url += 'post_delete_submit&post_ID=' + this.attr('no');
        }
        else {
            url += 'comment_delete_submit&comment_ID=' + this.attr('no');
        }
        return url;
    };

    /**
     * 'this' must be '.post-like-button' or '.comment-like-button'
     * @returns {string}
     */
    $.fn.getLikeURL = function() {
        var no;
        if ( this.hasClass('post-like-button') ) no = this.getPost().attr('no');
        else no = this.getComment().attr('no');
        if ( isEmpty(no) ) return alert("Error no No. has chosen.");
        return server_url +
            '&forum=post_like' +
            '&response=ajax' +
            '&session_id=' + session_id +
            '&post_ID=' + no;
    };

    /**
     * 'this' must be either '.post' or '.comment'
     */
    $.fn.delete = function() {
        if ( this.hasClass('post') ) this
            .setTitle( post_title_deleted )
            .setContent( post_content_deleted )
            .addClass('deleted');
        else this
            .setContent( comment_deleted )
            .addClass('deleted');
        return this;
    };
    /**
     * Returns <textarea name='content'> or <textarea name='comment_content'>
     *
     * @note
     *
     * @returns {*}
     */
    $.fn.getContent = function() {
        if ( this.isComment() ) return this.find('[name="comment_content"]');
        else return this.find('[name="content"]');
    };
    /**
     * Add a string value into a form input/textarea.
     * @note this must be input tag or textarea tag
     */
    $.fn.addVal = function( str ) {
        this.val( this.val() + str );
        return this;
    };
    /**
     * Returns '.files' HTML.
     *
     * @note 'this' must a '.form'.
     *
     */
    $.fn.getFiles = function() {
        var $files = this.find('.files');
        if ( $files.length ) return $files[0].outerHTML;
        else return '';
    };
    $.fn.getQuery = function() {
        var $clone = this.find('form').clone();
        $clone.getContent().addVal( this.getFiles() );
        /*
        if ( files = this.getFiles() ) {

        }
        var $obj = this.find('.files');
        if ( $obj.length ) {
            var files = $obj[0].outerHTML;
            var $content;
            if ( this.isComment() ) $content = $clone.find('[name="comment_content"]');
            else $content = $clone.find('[name="content"]');
            $content.val( $content.val() + files );
        }
        */

        /*
        if ( $content.length ) {
            $content.val( $content.val() + files );
        }
        else {
            $content = $clone.find('[name="comment_content"]');
            $content.val( $content.val() + files );
        }
        */
        var re;
        if ( x.debug ) {
            re = {
                'url' : server_url + $clone.serialize(),
                'data' : {}
            }
        }
        else {
            re = {
                'url' : server_url,
                'data' : $clone.serializeArray()
            }
        }
        return re;
    };
    $.fn.postURL = function() {
        return server_url;
    };
    $.fn.setMessage = function( m ) {
        this.find('.message').html( m );
    };

    /**
     * @todo x-holder-post-write-form 을 사용 하지 말고, 그냥 맨 위에 표시 할 것.
     */
    $.fn.addPostForm = function () {
        var m = $('#post-write-template').html();
        this
            .closest('.post-page')
            .find('.x-holder-post-write-form')
            .html( m );
    };
    $.fn.showPostEditForm = function( )  {
        if ( $('.form.post-write').length ) return x.alert('Notice', 'You have opened a post write form already. Please submit/remove the other form.');

        var $post = this.getPost(); // this.closest('.post');

        var $content = $( '<div>' + $post.find('.content').html() + '</div>' );
        var $files = $content.find('.files').html();


        //var post_ID = $post.attr('no');
        var title = trim($post.find('.title').text());
        var content = trim( $post.find('.content').text());

        var $form = $( $('#post-write-template').html() );

        // buttons
        $form.find('.post-write-cancel')
            .addClass('post-edit-cancel');
        $form.find('.post-edit-cancel').removeClass('post-write-cancel');


        //$m.find('[name="post_ID"]').val( post_ID );
        $form.set('post_ID', $post.value('no'));
        $form.find('[name="title"]').val( title );
        $form.find('[name="content"]').val( content );
        $form.find('.files').html( $files );

        //
        $post.find('.data').hide();
        $post.find('.data').after( $form );
    };
    $.fn.removePostForm = function () {
        this
            .closest('.x-holder-post-write-form')
            .html('');
    };
    $.fn.removePostEditForm = function () {
        var $form = this.closest('.post-write');
        var post_ID = $form.find('[name="post_ID"]').val();
        $form.remove();
        $('.post[no="'+post_ID+'"]').show();
    };
    $.fn.addPost = function(re) {
        this.closest('.post-page').find('.posts').prepend( re.data.markup );
        this.remove();
        console.log("A post added");
    };
    $.fn.updatePost = function(re) {
        var post_ID = this.find('[name="post_ID"]').val();
        var $post = this.findPost( post_ID );
        $post.next().remove();
        $post.replaceWith( re.data.markup );
        console.log("A post updated");
    };
    $.fn.getData = function() {
        return this.find('form').serialize();
    };
    $.fn.postData = function() {
        return this.find('form').serializeArray();
    };

    $.fn.set = function( name, value ) {

        var obj = this.find('[name="'+name+'"]');  // if there is any attribute with name
        if ( obj.length ) obj.val( value );


        obj = this.find('['+name+']'); // if there is any attributes
        if ( obj.length ) {
            obj.attr( name, value );
        }

        obj = this.find('.' + name ); // if there is any classes
        if ( obj.length ) obj.html( value );

        return this;
    };
    /**
     * 'this' must be '.post'
     */
    $.fn.setTitle = function(str) {
        this.find('.title').text( str );
        return this;
    };
    /**
     * 'this' must be '.post' or '.content'
     */
    $.fn.setContent = function(str) {
        if ( this.hasClass('post') ) this.find('.content').html( str );
        else this.find('.comment-content').html( str );
        return this;
    };



    /**
     * 'this' is .form
     *
     * @param re - return value of forum()->comment_edit_submit()
     *
     */
    $.fn.addComment = function ( re ) {
        console.log('addComment');
        if ( this.value('comment_parent') ) { // add a comment under another comment.
            var $comment = this
                .close()
                .getComment();

            var depth = parseInt($comment.attr('depth')) + 1;
            var $m = $( re.data.markup )
                .attr('depth', depth);
            $comment.after( $m );
        }
        else { // add a comment right under a post.
            this
                .close()
                .getPost()
                .find('.comment-list')
                .prepend( re.data.markup );
        }
    };
    $.fn.updateComment = function ( re ) {
        console.log('updateComment');
        this
            .remove()
            .getComment()
            .replaceWith( $( re.data.markup ).attr('depth', this.getComment().attr('depth')) );
    };

    /**
     * Close the form
     * 'this' must be .form
     *
     */
    $.fn.close = function() {
        if ( this.isComment() ) {
            this
                .removeClass('selected')
                .set('comment_content', '')
                .find('.message')
                .html('');
            this.find('.files').html('');
            return this;
        }
    };


}(jQuery));

x.loadPage = function(callback) {
    console.log('page:  ' + $().isFrontPage());
    if ( $().isFrontPage() ) {
        var url = server_url + 'forum=api&action=page&name=index';
        console.log(url);
        $.get( url, function( re ){
            // console.log(re);
            x.holder().html( re );
            callback();
        });
    }
    else {
    }
};



///
///  Widgets ---------------------------------------------------------
///

/**
 *
 * @param name
 */
var panel = {};
panel.hasOpen = function() {
    return $('.panel:visible').length;
};
panel.slideUp = function() {
    $('.panel').slideUp();
};
panel.getActiveName = function() {
    return $('.panel:visible').attr('name');
};
panel.show = function( $this, callback ) {
    var name = $this.attr('panel');
    if ( panel.hasOpen() ) {
        var active = panel.getActiveName();
        if ( active != name ) {
            $('.panel[name="'+active+'"]').slideUp('fast', function() {
                $('.panel[name="'+name+'"]').slideToggle(function(){
                    callback();
                });
            });
        }
        else {
            $('.panel[name="'+name+'"]').slideToggle(function(){
                callback();
            });
        }
    }
    else {
        $('.panel[name="'+name+'"]').slideToggle(function(){
            callback();
        });
    }
};
panel.on_click = function ( ) {
    var $this = $(this);
    panel.show($this, function(){
        // console.log('shown');
    });
};
panel.on_close = function () {
    $(this).closest('.panel').slideUp();
};






///
/// User ------------------------------------------------------------
///
var user = {};
user.login_submit = function () {
    var $form = $(this).getForm().showLoader({text:'Please, wait while connecting to server ...'});
    var url = $form.getURL();
    console.log(url);
    $.get(
        url,
        function(re) {
            console.log(re);
            if ( re['success'] ) {
                db.set('session_id', re.data.session_id);
                db.set('user_login', re.data.user_login);
                db.set('user_nicename', re.data.user_nicename);
                location.reload();
            }
            else {
                if ( re.data && re.data.message ) $form.setMessage( re.data.message );
            }
        }
    );
};
user.logout = function () {
    //var url = server_url + 'forum=api&action=logout';
    //console.log(url);
    session_id = null;
    db.set('session_id', '');
    db.set('user_login', '');
    db.set('user_nicename', '');
    location.reload();
};




///
/// P o s t ----------------------------------------------------------
///

post.on_post_write_button_click = function () {
    console.log('post write button clicked');
    $(this).addPostForm();
};


post.on_post_edit_button_click = function () {
    console.log('post edit button clicked');
    $(this).showPostEditForm();
};


post.on_post_write_submit = function () {
    console.log('post write submit button clicked');
    var $form = $(this)
        .getForm()
        .disableButtons()
        .showLoader({text:'Please, wait while posting to server ...'});

    var post_ID = $form.find('[name="post_ID"]').val();
    var q = $form.getQuery();
    console.log(q);
    $.post({
        'url' : q.url,
        'data' : q.data,
        'success' : function(re) {
            console.log(re);
            if ( x.success( re, 'Post failure' ) ) {
                if ( post_ID )  $form.updatePost( re );
                else $form.addPost( re );
            }
            $form.enableButtons();
        },
        'error' : function () {
            $form.enableButtons();
            x.alert("Post query error", "Error occurs on post query.");
        }
    });
};




post.on_post_write_cancel = function () {
    $(this).removePostForm();
};


post.on_post_edit_cancel = function () {
    $(this).removePostEditForm();
};

post.on_post_delete_button_click = function () {
    console.log('post delete button clicked');
    var $post = $(this).getPost();
    var url = $post.getDeleteURL();
    console.log(url);
    $.get(url, function(re) {
        if ( x.success(re, 'Failed on delete')  ) {
            $post.delete();
            x.alert("Success", "You have deleted a post.");
        }
    } );
};
post.on_comment_delete_button_clicked = function () {
    console.log('comment delete button clicked');
    var $comment = $(this).getComment();
    var url = $comment.getDeleteURL();
    console.log(url);
    $.get(url, function(re) {
        console.log(re);
        if ( x.success(re, 'Comment delete failed')  ) {
            $comment.delete();
            x.alert("Comment deleted", "You have deleted a comment.");
        }
    } );
};


/**
 * This expands/shows comment textarea & buttons
 *      when user clicks on camera button or textarea on comment box.
 */
post.comment_form_clicked = function() {
    var $form = $(this).getForm();
    console.log('comment form clicked');
    if  ( ! $form.hasClass('selected') ) $form.addClass('selected');
    if ( $form.isFirstDepthComment() ) {
        console.log("Commenting right under a post");
        var post_ID = $form.getPost().attr('no');
        $form.set('post_ID', post_ID);
    }
    else {
        console.log("Commenting under another comment");
        var comment_ID = $form.parent().attr('no');
        $form.set('comment_parent', comment_ID);
    }
};

post.on_comment_edit_button_clicked = function () {
    console.log('post.on_comment_edit_button_clicked');
    var $comment = $(this).getComment();
    $comment.hide();
    var $files = $comment.find('.files').html();
    var $form = $($('#comment-write-template').html())
        .addClass('selected')
        .set('comment_content', $comment.find('.comment-content').text())
        .set('comment_ID', $comment.attr('no'))
        .set('files', $files);
    $comment.after( $form );
};


post.on_comment_edit_cancel = function () {

    var $form = $(this).getForm();
    if ( $form.isNewComment() ) $form.close();
    else {
        $form
            .remove()
            .getComment()
            .show();
    }
};

post.on_comment_edit_submit = function () {
    var $form = $(this)
        .getForm()
        .disableButtons()
        .showLoader({text:'Please, wait while commenting...'});
    var q = $form.getQuery();
    console.log(q.url);
    $.post({
        'url' : q.url,
        'data' : q.data,
        'success' : function (re) {
            console.log(re);
            if ( x.success( re, 'Comment failure' ) ) {
                if ( $form.isNewComment() )  $form.addComment( re );
                else $form.updateComment( re );
            }
            $form.enableButtons();
        },
        'error': function () {
            $form.enableButtons();
            alert('error on comment write');
        }
    });
};




/// file upload
post.on_file_upload = function (input) {

    var $form = $(input)
        .getForm()
        .set('action', file_server_url)
        .disableButtons()
        .showLoader({text:'uploading... <progress value="0" max="100"></progress>'});

    $form.find('form')
        .ajaxSubmit( {
        beforeSend: function() {
            console.log('beforeSend:');
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            $form.find('progress').val( percentComplete );
        },
        success: function() {
            console.log('success:');
        },
        complete: function(xhr) {
            $form
                .enableButtons()
                .hideLoader();
            console.log('complete:');
            var re = xhr.responseText;
            console.log(re);
            var data = JSON.parse( re );
            if ( isEmpty(data.error) ) {
                var m = '<img src="'+data.url+'">';
                $form.find('.files').append( m );
            }
            else {
                alert( data.error );
            }
        }
    } );


};


post.getClass = function(post) {
    var cls = 'post';
    if ( post.deleted ) cls += ' deleted';
    return cls;
};




//////////////////////////////////////////////////////////////////////
//
// EO Post
//
//////////////////////////////////////////////////////////////////////










//////////////////////////////////////////////////////////////////////
//
//
// Endless Page Loading
//
//
//////////////////////////////////////////////////////////////////////


var LoadMore = function( o ) {
    var defaults = {
        page: 1,
        in_loading: false,
        no_more_data : false,
        distance_from_bottom: 300
    };
    o = $.extend( defaults, o );


    this.start = function() {
        var $window = $( window );
        var $document = $( document );
        $document.scroll( function() {
            if ( o.no_more_data ) return o.callback_no_more_data();
            if ( o.in_loading ) return o.callback_in_loading();
            var top = $document.height() - $window.height() - o.distance_from_bottom; // compute page position
            if ($window.scrollTop() >= top) { // page reached at the bottom?
                o.page ++;
                o.callback_begin_loading();
                //var url = server_url + 'forum=api&action=page&name=post-list&posts_per_page=' + x.posts_per_page + '&slug=' + x.slug + '&page=' + o.page;
                var url = server_url + 'forum=api&action=post_list&slug=' + x.slug + '&page=' + o.page;

                console.log(url);
                o.in_loading = true;

                $.get( url, function(re) {
                    if ( isEmpty(re) ) {
                        o.no_more_data = true;
                        return o.callback_no_more_data();
                    }
                    else {
                        o.in_loading = false;
                        o.callback_data( re );
                    }
                });
            }
        });
    };
};

var loadmore = new LoadMore({
    callback_data : function( re ) {
        console.log('data');
        x.content().append(markup.postList(re));
        x.removeLoader();
        /// 여기서 부터... 글 내용, 코멘트 내용이 너무 길면, 4줄만 보여주고 감춘다...
        /// 중요: 6줄 이상이면, 4줄만 감춘다. 내용이 5줄 이면, 다 보여준다. 즉, 여유를 준다.
        //xapp.callback_post_add_show_more(re.data);
    },
    callback_no_more_data : function() { console.log('no more data')},
    callback_in_loading : function() {},
    callback_begin_loading: function() {
        x.content().append(x.loader({'text': 'Loading more posts ...'}));
    }
});
loadmore.start();


/*
endless.in_loading = false,
    endless.no_more_posts = false,
    endless.distance_from_bottom = 300,
    endless.page = 1;

    var $window = $( window );
    var $document = $( document );
    $document.scroll( function() {
        if ( endless.no_more_posts || endless.in_loading ) return;
        var top = $document.height() - $window.height() - endless.distance_from_bottom; // compute page position
        if ($window.scrollTop() >= top) endless.load_next_page(); // page reached at the bottom?
    });
endless.load_next_page = function() {
    endless.page ++;
    console.log("xapp.endless.js count:" + endless.page + ", : " + '');
    var url = server_url + 'forum=api&action=page&name=post-list&posts_per_page=' + x.posts_per_page + '&slug=' + x.slug + '&page=' + endless.page;

    x.content().append(x.loader({'text': 'Loading more posts ...'}));
    console.log(url);
    endless.in_loading = true;

    $.get( url, function(re) {
        if ( empty(re) ) {
            endless.no_more_posts = true;
            return;
        }
        x.content().append( re );
        endless.in_loading = false;
        x.removeLoader();

        /// 여기서 부터... 글 내용, 코멘트 내용이 너무 길면, 4줄만 보여주고 감춘다...
        /// 중요: 6줄 이상이면, 4줄만 감춘다. 내용이 5줄 이면, 다 보여준다. 즉, 여유를 준다.
        xapp.callback_post_add_show_more(re.data);

    });
};
*/




//////////////////////////////////////////////////////////////////////
//
// EO Endless Page Loading
//
//////////////////////////////////////////////////////////////////////





//////////////////////////////////////////////////////////////////////
//
//
// Markup
//
//
//////////////////////////////////////////////////////////////////////



///
/// Markup
///
var markup = {};
var getLoader = markup.getLoader = function( o ) {
    var defaults = {
        icon: 'fa-spinner',
        text: 'Loading ...'
    };
    o = $.extend( defaults, o );
    return '' +
        '<div>' +
        '   <i class="fa fa-spin '+o.icon+'"></i>' +
        o.text +
        '</div>' +
        '';
};

markup.postList = function( re ) {
    console.log(re);
    var q = re.data['in'];
    var category = re.data.category;
    var m = '';
    m += '<section class="page-list" no="'+q.page+'">';
    m += '  <h2>Post List '+ q.page+'</h2>';
    m += '  <div class="desc">' + category.description + '</div>';
    m +=    markup.postListButtons();
    m += '  <div class="posts">';
    m +=    markup.posts(re.data.posts);
    m += '  </div>';
    m += '</section>';

    return m;
};

markup.postListButtons = function() {
    return '<div class="buttons post-list-buttons">' +
        '       <button type="button" class="post-write-button btn btn-secondary">POST</button>' +
        '   </div>';
};

markup.posts = function( posts ) {
    var m = '',
        p = null;
    for( var i in posts ) {
        if ( ! posts.hasOwnProperty(i) ) continue;
        p = posts[i];
        m += '<div class="'+post.getClass(post)+'" no="'+p.ID+'">';

        m += '  <div class="data">';
        m +=        markup.postMeta(p);
        m +=        markup.postButtons();
        m +=        markup.postTitle(p);
        m +=        markup.postContent( p );
        m +=        markup.commentForm();
        m +=        markup.comments( p );
        m += '  </div>';

        m += '</div>';
    }
    return m;
};

markup.postMeta = function( post ) {
    var m = '<div class="meta">';

    m += "No. " + post.ID;
    m += "Date. " + post.date;
    m += "Author. " + post.author;

    m += "</div>";
    return m;
};


markup.postButtons = function() {
    var m = '<div class="buttons">' +
    '<ul>' +
    '   <li class="post-edit-button">edit</li>' +
    '   <li class="post-delete-button">delete</li>' +
    '   <li class="post-like-button">like</li>' +
    '   <li class="post-spam-button">spam</li>' +
    '   <li class="post-move-button">move</li>' +
    '   <li class="post-copy-button">copy</li>' +
    '   <li class="post-block-button">block</li>' +
    '   <li class="post-blind-button">blind</li>' +
    '</ul>' +
    '</div>';
    return m;
};




markup.postTitle = function(post) {
    return '<div class="title">' +
        post.post_title +
        '</div>';
};

markup.postContent = function( post ) {
    return '<div class="content">' +
        post.post_content +
        '</div>';
};

markup.commentForm = function() {
    var comment = $('#comment-write-template').html();
    return comment;
};

markup.comments = function( p) {
    var _comments = p.comments;
    if ( isEmpty(_comments) || _comments.length == 0 ) return '';

    var count = _comments.length;
    console.log(_comments);

    var m = '';
    m += '<div class="comments">';
    m += '  <div class="comments-meta">';
    m += '      <div class="count" count="'+count+'"></div>';
    m += '  </div>';
    m += '  <div class="comment-list">';

    if ( count ) {
        for ( var i in _comments ) {
            if ( ! _comments.hasOwnProperty(i) ) continue;
            var comment = _comments[i];
            m += '<div class="comment" no="'+comment.comment_ID+'" depth="'+comment.depth+'">';
            m +=    markup.commentMeta( comment );
            m +=    markup.commentContent( comment );
            m +=    markup.commentForm();
            m += '</div>';

        }
    }

    m += '  </div>';
    m += '</div>';

    return m;
};


markup.commentMeta = function( comment ) {
    var m = '' +
        '<div class="comment-meta">' +
        '   <span class="no">' +
        '      <span class="caption">No.</span>' +
        '       <span class="text">'+comment.comment_ID+'</span>' +
        '   </span>' +
        '   <span class="author">' +
        '       <span class="caption">Author</span>' +
        '       <span class="text">'+comment.author+'</span>' +
        '   </span>' +
        '   <div class="buttons">' +
        '       <span class="comment-edit-button">edit</span>' +
        '       <span class="comment-delete-button">delete</span>' +
        '       <span class="comment-like-button">like<span class="no"></span></span>' +
        '       <span class="comment-report-button">report</span>' +
        '       <span class="comment-copy-button">copy</span>' +
        '       <span class="comment-move-button">move</span>' +
        '       <span class="comment-blind-button">blind</span>' +
        '       <span class="comment-block-button">block</span>' +
        '   </div>' +
        '</div>';
    return m;

};


markup.commentContent = function( comment ) {

    var m = '' +
        '<div class="comment-content">' +
        sanitize_content( comment.comment_content ) +
        '</div>' +
        '';
    return m;
};


//////////////////////////////////////////////////////////////////////
//
// EO Markup
//
//////////////////////////////////////////////////////////////////////




//////////////////////////////////////////////////////////////////////
//
//
// Functions
//
//
//////////////////////////////////////////////////////////////////////



function trim(text) {
    return s(text).trim().value();
}

function sanitize_content ( content ) {
    return nl2br( s.stripTags( trim( content ) ) );
}


/**
 *
 * @param obj
 *      - jQuery object
 *      - Node
 *
 * @return jQuery object ( of the node )
 *
 * @code
 *      var $form = find_comment_edit_form( disable_button( this ) );
 * @endcode
 */
function disable_button( obj ) {
    if ( isNode( obj ) ) obj = $( obj );
    obj.prop('disabled', true);
    return obj;
}

/**
 *
 * @param obj
 *      - jQuery object
 *      - Node
 *
 * @return jQuery object ( of the node )
 */
function enable_button(obj) {
    if ( isNode( obj ) ) obj = $( obj );
    obj.prop('disabled', false);
    return obj;
}



/**
 * Returns true if the obj has empty value like - undefined, null, 'null', 'false', '', 0, '0', falsy value like {}, []
 * @param obj
 * @returns {boolean}
 */
function isEmpty( obj ) {
    if ( _.isEmpty( obj ) ) return true;
    else return !!(typeof obj == 'undefined' || typeof obj == null || obj == 'null' || obj == 'false' || obj == '' || obj == 0 || obj == '0' || !obj || obj == {} || obj == []);
}


//////////////////////////////////////////////////////////////////////
//
// EO Functions
//
//////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////////
//
//
// PHP JS Functions
//
//
//////////////////////////////////////////////////////////////////////

function nl2br (str, isXhtml) {
    var breakTag = (isXhtml || typeof isXhtml === 'undefined') ? '<br ' + '/>' : '<br>'

    return (str + '')
        .replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2')
}

