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
    $body.on('click', '.post-write-button', post.on_post_write_button_click);
    $body.on('click', '.post-edit-button', post.on_post_edit_button_click);
    $body.on('click', '.post-write-submit', post.on_post_write_submit);
    $body.on('click', '.post-write-cancel', post.on_post_write_cancel);

    $body.on('click', '.post-edit-cancel', post.on_post_edit_cancel);

});
x.loadForumEnd = function() {
    // test
    // $('.post-page[no="1"]').find('.post-write-button').click();

    //$('.post-page[no="1"]').find('.post:first-child').find('.post-edit-button').click();

    $('.post-edit-button:eq(0)').click();
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
x.success = function ( re ) {
    if ( typeof re.success == 'undefined' ) {
        x.alert('Server failed...', 'Malformed server response. Server script printed error.');
        return false;
    }
    else if ( re.success ) return true;
    else {
        x.alert("Error ...", x.get_error_message( re['data'] ));
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
    $.fn.getForm = function() {
        return this.closest( '.form' );
    };
    $.fn.showLoader = function( o ) {
        this.find('.loader').html( getLoader( o ) );
        return this;
    };
    $.fn.getURL = function() {
        return server_url + this.find('form').serialize();
    };
    $.fn.postURL = function() {
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
    $.fn.addPostEditForm = function( )  {
        if ( $('.form.post-write').length ) return x.alert('Notice', 'You have opened a post write form already. Please submit/remove the other form.');
        var $post = this.closest('.post');
        var post_ID = $post.attr('no');
        var $m = $( $('#post-write-template').html() );
        $m.find('[name="post_ID"]').val( post_ID );
        $m.find('.post-write-cancel')
            .addClass('post-edit-cancel');
        $m.find('.post-edit-cancel').removeClass('post-write-cancel');
        $post.hide();
        $post.after( $m );
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
    $.fn.getData = function() {
        return this.find('form').serialize();
    };
    $.fn.postData = function() {
        return this.find('form').serializeArray();
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
    $(this).addPostEditForm();
};


post.on_post_write_submit = function () {
    console.log('post write submit button clicked');
    var $this = $(this);
    $this.prop('disabled', true);
    var $form = $this.getForm().showLoader({text:'Please, wait while posting to server ...'});

    var url = server_url;
    var data = {};
    if ( x.debug ) {
        url += $form.getData();
    }
    else {
        data = $form.postData();
    }
    console.log(url);
    $.post({
        'url' : url,
        'data' : data,
        'success' : function(re) {
            console.log(re);
            if ( x.success( re ) ) {
                // location.reload();
                $form.closest('.post-page').find('.posts').prepend( re.data.markup );
                $form.remove();
            }
            $this.prop('disabled', false);
        },
        'error' : function () {
            $this.prop('disabled', false);
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
