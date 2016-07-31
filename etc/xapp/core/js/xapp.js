if ( typeof xapp == 'undefined' ) var xapp = {};
/**
 *
 * XAPP Mini Framework
 *
 *      This mini framework has only a few functions. DON'T DO TOO MUCH THINGS.
 *
 * @note  What it does
 *
 *  - Ajax Query to Wordpress through XForum API.
 *      => list, view, posting, editing, EndLess listing.
 *
 *
 *
 *
 *
 * @note  External Libraries
 *
 *      - jQuery
 *      - underscore
 *      - underscore.string
 *      - bootstrap v4
 *      - font-awesome
 *
 *
 *
 *
 * @file xapp.js
 *
 * @type {{}}
 *
 */


var db = Lockr;
xapp.bootstrap = {};
xapp.bs = xapp.bootstrap;
var x = xapp.x = function( obj ) { x.obj = obj; return x; };
xapp.local_url = 'index.html?';
xapp.query = '';
xapp.qv = {};
xapp.debug = true;          // true 이면 디버깅 모드를 실행한다. ajax 등을 할 때, timestamp dummy 를 추가한다.

xapp.deleted = 'deleted, ...';
xapp.option = {};
xapp.option.alert = {};
xapp.option.cache = {};
xapp.option.alert.after_post = false;        // if true, it shows alert box before refresh (after post). if not, just refresh. (글 등록 후 알림창을 표시하고 페이지 reload 할 지, 그냥 reload 할지 결정.)
xapp.option.alert.after_edit = false;
xapp.option.alert.after_comment = true;     //
xapp.option.cache.front_page_expire = 1200;
xapp.option.cache.post_list_expire = 1200;
xapp.option.cache.post_list = true;         // if true, it caches post-list page.




xapp.start = function () {
    if ( this.isFront() ) {
        xapp.callback_front_page();
    }
    else if ( this.isPostList() ) {
        var query = xapp.post_list_query_args( 0 );
        //xapp.wp_query( query );
        endless.load_next_page();
    }
    else {
        alert("No route to go");
    }

};
xapp.init = function() {
    xapp.parse_query_string();
    xapp.bind_api();
};
$(function() {
    xapp.init();        // call xapp.init when DOM is ready.
});

/**
 * Returns true if current page is 'front' page.
 *
 */
xapp.isFront = function() {
    var action = xapp.in('action');
    return _.isEmpty( action );
};

/**
 * Returns true if current page is 'post_list' page.
 * @returns {boolean}
 */
xapp.isPostList = function() {
    return xapp.in('action') == 'post_list';
};

xapp.move = function( api ) {
    location.href = xapp.local_url + api;
};
xapp.reload = xapp.refresh = function () {
    location.reload( true );
};


/**
 *
 * @note Current page url is always like "?action=post_list&slug=its".
 *
 *  ( 현재 접속 주소는 항상 "?action=post_list&slug=its" 와 같다. 왜냐하면 웹 페이지에서 링크가 그냥 이렇게 걸리기 때문이다. )
 *
 * @note Set this.query with current url and put query key/values into this.qv
 *
 *  ( 현재 접속 주소 전체를 this.query 에 집어 넣고, 그것을 파싱하여 키/값 형태로 된 것을 this.qv 에 집어 넣는다. )
 *
 *
 *
 *
 *
 */
xapp.parse_query_string = function () {
    this.qv = {};
    this.query = '';
    var splits = location.href.split('?');
    if ( splits.length > 1 ) {
        this.query = splits[1];
        parse_str( this.query, this.qv);
    }
    //console.log(this.qv);
};


/**
 * Returns the $_GET variable.
 * @param name
 */
xapp.in = function( name ) {
    if (_.isEmpty( this.qv ) ) return null;
    if ( typeof this.qv[ name ] == 'undefined' ) return null;
    return this.qv[ name ];
};


xapp.process_api = function ( api_query ) {

    var qs = {};
    parse_str( api_query, qs );

    if ( qs.action == 'post_list' ) {
        xapp.move( api_query );
    }
    else if ( qs.action == 'post_view' ) {

    }
    else {
        xapp.move( api_query );
    }

};

xapp.bind_api = function () {

    $('body').on('click', '[api]', function(e) {
        e.preventDefault();
        var $this = $(this);
        xapp.process_api( $this.attr('api') );

    } );
};

xapp.get = function ( url, success, error ) {
    //console.log('xapp.get() : ' + url);

    var o = {
        url: url,
        success: success,
        error: error
    };

    if ( xapp.debug ) o.cache = false;

    $.ajax( o );
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
xapp.alert = function( title, content, callback ) {

    var m = '' +
        '<div class="xapp-alert modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalLabeled" aria-hidden="true">' +
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
    $('.xapp-alert').modal('show');
    function handler_xapp_alert_close(e) {
        $('.xapp-alert').remove();
        if ( typeof callback == 'function' ) callback();
                //$('.xapp-alert').off('hidden.bs.modal', handler_xapp_alert_close);
        // console.log("un-bind for close() .... " + (new Date).toString() );
    }
    $('.xapp-alert').on('hidden.bs.modal', handler_xapp_alert_close);

};




xapp.get_error_message = function (data) {
    var code = data.code;
    var message = data.message;
    return "ERROR(" + code + ") " + message;
};


/**
 *
 * Returns true if the WP_Comment has parent.
 *
 * @note This is only for comment.
 * @note x.obj must be WP_Comment object.
 * @returns {boolean}
 */
x.hasParent = function () {
    return !(typeof x.obj.comment_parent == 'undefined' || isEmpty(x.obj.comment_parent));
};


x.isWPPost = function() {
    return !!x.obj.post_date;
};
x.isWPComment = function() {
    return !!x.obj.comment_ID;
};

/**
 * Inserts a post or a comment in the post-list/comment-list
 *
 */
x.insert = function() {
    var o = x.obj;
    if ( o.ID ) {

    }
    else if ( o.comment_ID ) { // comment
        var comment = o;
        var $m = $( markup.comment( comment ) );
        if ( x.hasParent() ) {
            var $p = x.findParent();
            var depth = parseInt($p.attr('depth')) + 1;
            $m.attr('depth', depth);
            $p.after( $m );
        }
        else {
            post_comment_list( comment.post_ID ).prepend( $m );
        }
    }
};

/**
 * 'x.obj' must be WP_Comment
 *
 */
x.increaseNoOfComments = function() {
    x.getPost().find('.comments .meta .count').html( markup.get_comments_meta_count ( get_comments_meta_count(x.obj.comment_post_ID) + 1 ));
};


/**
 *
 * Replaces a comment with WP_Comment 'x.obj'
 *
 * @note 'x.obj' must be WP_Comment.
 *
 */
x.replace = function() {
    if ( x.isWPPost() ) {
        // x.getPost().replaceWith( x.markup() );
        alert('x.replace for WP_Post is not supported.')
    }
    else if ( x.isWPComment() ) {
        var $m = $( x.markup() );
        var $c = x.getComment();
        $m.attr('depth', $c.attr('depth'));
        $c.replaceWith( $m );
    }
};


/**
 *
 * Returns jQuery object of the comment Node for x.obj
 *
 * @note x.obj could be many things.
 *
 *      - comment ID
 *      - any Number
 *      - jQuery object under a comment
 *      - Node under a comment.
 *
 *
 * @return $
 */
x.getComment = x.findComment = function() {
    var obj = x.obj;
    if ( obj ) {
        if ( obj.comment_ID ) return $('.comment[comment-id="'+ obj.comment_ID +'"]');   // WP_Object
        else if ( isNumber( obj ) ) return $('.comment[comment-id="'+ obj +'"]');   // comment_ID
        else if ( isjQuery( obj ) ) return obj.closest( '.comment' );                  // jQuery Object
        else if ( isNode( obj ) ) return $(obj).closest( '.comment' );                 // Node
    }
    return null;
};


/**
 * Returns a comment ID.
 *
 * @note it uses x.findComment() internally, so 'x.obj' must be a proper data for x.findComment()
 *
 * @returns {*}
 */
x.getCommentID = function () {

    var o = x.findComment();
    if ( o ) return o.attr('comment-id');
    else return null;
};



/**
 *
 * Returns jQuery object of the parent comment Node for x.obj
 *
 * @note x.obj must be WP_Comment object.
 *
 * return $
 */
x.findParent = function() {
    return find_comment( x.obj.comment_parent );
};



/**
 * Returns post ID.
 * It uses x.getPost() internally. So, x.obj must be a proper data for x.getPost()
 *
 * @returns {*}
 *
 * @code
 *   // When a button clicked on ".post", use can use like below

     post_list.post_like_button_clicked = function () {
        var post_ID = x(this).getPostID();
     }

 * @endcode
 */
x.getPostID = function () {
    return x.getPost().attr('post-id');
};

/**
 * Returns null or jQuery object based on x.obj
 *
 *      - x.obj can be
 *
 *          - Node
 *
 *              - Any HTML node under post or even comment will find a post.
 *
 *          - jQuery object.
 *
 *              - Just like 'node', it will search for the nearest '.post'
 *
 *              - it works on any object of post or comment.
 *
 *          - Number
 *
 *              - it find the '.post' which has the post id.
 *
 *          - WP_Post
 *
 *          - WP_Comment
 *
 * @returns {*}
 */
x.getPost = function() {
    var obj = x.obj;
    if ( isNumber(obj) ) {
        return $('.post[post-id="'+obj+'"]');
    }
    else if ( isjQuery(obj) ) {
        return obj.closest( '.post' );
    }
    else if ( isNode( obj ) ) {
        return $( obj ).closest( '.post' );
    }
    else if  ( x.isWPPost() ) {
        return $('.post[post-id="'+obj.ID+'"]');
    }
        else if (x.isWPComment() ) {
        return $('.post[post-id="'+obj.comment_post_ID+'"]');
    }
    else return null;
};

/**
 * Return the parent wrapper of the form.
 *
 * It returns the FORM wrapper, NOT the FORM itself.
 *
 * x.obj can be any form. comment form, post form, login form .. etc.
 *
 * @see readme for DOM.
 *
 * x.obj can be Node or jQuery under a form.
 *
 * @since 2016-07-30
 * @returns {*}
 */
x.getForm = function() {
    var obj = x.obj;
    if ( isjQuery(obj) ) {
        return obj.closest('form').parent();
    }
    else if ( isNode( obj ) ) {
        return $( obj ).closest('form').parent();
    }
    else alert("assert 123: no form element.");
};





/**
 *
 * if x.obj is WP_Comment object, then it return HTML Markup for comment rendering.
 *
 * ( 현재 x.obj 가 WP_Post instance 이면 '.post' 에 대한 HTML 문장을 리턴한다. )
 *
 * @attention It DOES NOT SUPPORT for '.post' because a post has a lot of things to do. a post is not only for a post itself. It redraws all the comments and other things.
 *
 * @code
 *
    x.replace = function() {
        if ( x.isWPComment() ) {
            var $m = $( x.markup() );

        ... } }

 * @endcode
 *
 *
 */
x.markup = function() {
    if ( x.isWPPost() ) {
        // return markup.post( x.obj );
    }
    else if ( x.isWPComment() ) {
        return markup.comment( x.obj );
    }
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
        xapp.alert('Server failed...', 'Malformed server response. Server script printed error.');
        return false;
    }
    else if ( re.success ) return true;
    else {
        xapp.alert("Error ...", re['data']['message']);
    }
};