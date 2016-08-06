var x = {};
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
        /*
    else if ( x.forumPage() ) {
        console.log("forum page");
        x.loadForum( function(slug){
            console.log('clicking .post-write');
            $('.post-page[no="1"]').find('.post-write-button').click();
        });
    }
    */
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
    var slug = $(this).attr('forum');
    console.log('loading forum');
    var url = server_url + "forum=api&action=page&name=post-list&slug=" + slug;
    console.log(url);
    $.get( url, function(re) {
        x.content().html( re );
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




/// EO Post




//////////////////////////////////////////////////////////////////////
//
// Functions
//
//////////////////////////////////////////////////////////////////////
function trim(text) {
    return s(text).trim().value();
}

function sanitize_content ( content ) {
    return nl2br(s.stripTags(content));
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
 * Returns true if obj is jQuery object.
 *
 * @param obj
 * @returns {*|boolean}
 */
function isjQuery(obj) {
    return !! (obj && (obj instanceof jQuery || obj.constructor.prototype.jquery));
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
/**
 * Returns true if obj is int or NUMERIC STRING
 * @param obj
 * @returns {*}
 */
function isNumber( obj ) {
    if ( typeof obj == 'object' || typeof obj == 'undefined' || typeof obj == null || obj == '' ) return false;
    return _.isNumber( parseInt(obj) );
}

function isBoolean( obj ) {
    return _.isBoolean( obj );
}
/**
 *
 alert( isNode(document.getElementsByTagName('a')[0]) ); // true
 alert( isNode('<a>..</a>') );
 alert( isNode('string') );
 alert( isNode(1234) );
 alert( isNode([]) );
 alert( isNode({}) );

 * @type {isNode}
 */
var isElement = isNode = function( obj ) {
    /*
     if ( isEmpty(obj) ) return false;
     else if ( isjQuery( obj ) ) return false;
     else  return true;
     */
    /// return obj.ownerDocument.documentElement.tagName.toLowerCase() == "html";
    return ! ( typeof obj == 'undefined' || typeof obj.nodeName == 'undefined' );
};



//////////////////////////////////////////////////////////////////////
//
// EO Functions
//
//////////////////////////////////////////////////////////////////////
