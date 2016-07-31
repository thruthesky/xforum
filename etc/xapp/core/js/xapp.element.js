if ( typeof xapp == 'undefined' ) var xapp = {};
xapp.layout = {};
var layout = xapp.layout;

layout.main = function() {
    return $(".page-content-main");
};


var sl = sel = function ( cls ) {
    return '.' + cls;
};
var el = ele = function ( cls ) {
    return $('.' + cls);
};


var post_write_form = 'post-write-form';
var post_write_button = 'post-write-button';

// var post_edit_form = 'post-edit-form'; // @deprecated


var post_edit_button = 'post-edit-button';
var post_delete_button = 'post-delete-button';
var post_like_button = 'post-like-button';

var comment_write_form = 'comment-write-form';
var comment_write_button = 'comment-write-button';
var comment_cancel_button = 'comment-cancel-button';
var comment_edit_form = 'comment-edit-form';

var comment_edit_button = 'comment-edit-button';
var comment_delete_button = 'comment-delete-button';
var comment_like_button = 'comment-like-button';
var comment_report_button = 'comment-report-button';
var comment_copy_button = 'comment-copy-button';
var comment_move_button = 'comment-move-button';
var comment_blind_button = 'comment-blind-button';
var comment_block_button = 'comment-block-button';


var file_upload_button = 'file-upload-button';





var register_form_message = function() {
    return $( '.user-register-form-message' );
};


var register_form = function() {
    return $('.user-register-form');
};



var login_form_message = function() {
    return $( '.user-login-form-message' );
};


var user_login_form = function () {
    return $('.user-login-form');
};


/**
 * Returns jQuery object of 'comment count'. It is the '.count' node.
 * @param post_ID
 * @returns {*|{}}
 */
var comments_meta_count = function( post_ID ) {
    return post( post_ID ).find('.comments .meta .count');
};

/**
 * Returns the no of count.
 *
 * @param post_ID
 * @returns int
 */
var get_comments_meta_count = function ( post_ID ) {
    var $m = comments_meta_count( post_ID );
    if ( $m.find('.no').length ) return parseInt( $m.find('.no').text() );
    else return 0;
};







/**
 *
 * Returns comment ID
 *
 * @note it searches comment ID from a form or a node.
 *
 * @param obj
 *      - jQuery object of a comment edit form or a comment.
 *      - Node of a comment edit form or a comment.
 *
 * @returns int
 *
 *
 * @code
 post_list.comment_edit_form_cancel = function () {
            var $form = get_comment_edit_form( $(this) );
            get_comment( get_comment_ID( $form ) ).show();
            $form.remove();
        };
 * @endcode
 *
 *
 */
var get_comment_id = get_comment_ID = function( obj ) {
    if ( isjQuery(obj ) ) {
        if ( obj.hasClass( comment_edit_form ) ) {                    // is comment edit form jQuery object ?
            return obj.find('[name="comment_ID"]').val();
        }
        else if ( obj.hasClass( 'comment' ) ) {                         // is comment jQuery object ?
            return obj.find('comment-id');
        }
    }
    else if ( isNode( obj ) ) {                                     // is it HTML node? then it assumes it is a node under a comment.
        var id = comment( obj ).attr('comment-id');
        if ( isNumber(id ) ) return id;
    }
    else return null;                                           // return null otherwise
};



var get_post_id = get_pst_ID = function ( $form ) {
    return $form.find('[name="post_ID"]').val();
};




/**
 *
 * if obj is not number or string, then it assumes 'obj' as post_ID
 * or, it assumes 'jQuery Object'
 *
 * ( 입력된 글 번호 또는 객체의 가장 가까운 글을 찾는다. )
 *
 *
 * @code
 *      var $post = get_post( $this );
 *      @endcode
 *
 * @deprecated use x.getPost()
 *
 */
var post = get_post = function ( obj ) {
    if ( typeof obj == 'string' || typeof obj == 'number' ) {
        return $('.post[post-id="'+obj+'"]');
    }
    else {
        return obj.closest( '.post' );
    }
};


/**
 * Returns comment list ( dom ) of comment list
 * 
 * @param post_ID
 * @returns {*|{}}
 */
var post_comment_list = function(post_ID) {
    return post( post_ID ).find('.comment-list');
};



/**
 *
 * Returns the comment object
 *
 * @param obj
 *          - comment_ID
 *          - jQuery object on any comment
 *          - HTML Node ( element ) on any comment.
 *
 *
 *
 * @code when a user clicks on 'edit button' on a comment.
        find_comment( this ).hide();
 * @endcode
 *
 *
 * @return null|jQuery Object
 *
 */
var comment = find_comment = /* get_comment = */ function ( obj ) {
    if ( isNumber( obj ) ) return $('.comment[comment-id="'+ obj +'"]');
    else if ( isjQuery( obj ) ) return obj.closest( '.comment' );
    else if ( isNode( obj ) ) return $(obj).closest( '.comment' );
    else return null;
};

/**
 * Returns the nearest comment edit form.
 *
 * @return {*} jQuery object
 *
 *
 * @code
 post_list.comment_edit_form_cancel = function () {
            var $form = get_comment_edit_form( $(this) );
            get_comment( get_comment_ID( $form ) ).show();
            $form.remove();
        };
 * @endcode
 *
 */
/*
var find_comment_edit_form = function( $obj ) {
    return $obj.closest( sl(comment_edit_form) );
};
*/


/**
 *
 * @param obj
 *      - jQuery object - any child object under a comment edit for.
 *      - Node ( HTML Element )
 *
 * @returns {*}
 */
var find_comment_edit_form = function ( obj ) {
    if ( isNode(obj) ) obj = $( obj );
    return obj.closest( sl(comment_edit_form) );
};
