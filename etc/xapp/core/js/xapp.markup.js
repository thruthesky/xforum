if ( typeof xapp == 'undefined' ) var xapp = {};
/**
 *
 * @file xapp.markup.js
 * @type {{}}
 */

var markup = xapp.markup = {};

/**
 *
 * Returns 'Bootstrap' list group markup.
 *
 * @use when you want to display list-group data.
 * @param data - boostrap list group data.
 *      - data.title is the title of the list-group
 *      - data.lists is the list of the list-group.
 *          Each of lists must have text and href.
 */
xapp.bs.list_group_linked_items = function ( data ) {

//    console.log( data );

    var m = '' +
        '<div class="list-group">' +
        '   <a class="list-group-item active" href="javascript:;">' + data.title + '</a>' +
        '';

    if ( typeof data.lists != 'undefined' && data.lists.length ) {
        for ( var i in data.lists ) {
            var item = data.lists[i];
            m += '<a class="list-group-item" api="'+item.api+'" href="'+item.href+'">'+item.text+'</a>';
        }
    }

    m += '</div>';

    return m;
};

/**
 *
 * Gets categories data and convert it into 'list-group-item'.
 *
 * @use when you want to display xforum into a list group.
 *
 * @param data
 * @returns {Array}
 */
xapp.convert_categories_into_list_group_item = function ( data ) {
    var lists = [];

    for ( var i in data ) {
        var item = {};
        item.text = data[i].cat_name;
        item.href = this.server_url + '?forum=list&slug=' + data[i].slug;
        item.api = "action=post_list&slug=" + data[i].slug;
        lists.push( item );
    }

    return lists;
};


/**
 *
 *
 * Returns HTML (markup) for post list page.
 *
 * @note post wrapper must be a tag for seo purpose.
 *
 * @param data
 */
markup.post_list_page = function ( data ) {
    if ( isEmpty(data) || isEmpty(data.posts) ) return alert("No post data. check if 'xapp.server_url' is correct.");
    var posts = data.posts;
    var page = get_page_no(data.in['page']);
    if ( _.isEmpty(posts) ) {
        endless.set_no_more_posts();
        return xapp.callback_endless_no_more_posts();
    }
    var slug = data.in['slug'];
    var m = '';
    m += '<div class="post-list-page" slug="'+slug+'" page="'+page+'">';

    // template
    m += markup.get_post_list_page_header(data);


    m += '  <div class="posts">';

    for ( var i in posts ) {
        var post = posts[i];
        //console.log(post);
        var url = post.guid;
        var post_content = get_content(post);
        var like_number = '';
        if ( post.like ) {
            like_number = '<span class="no">'+post.like+'</span>';
        }


        var cls = 'post';
        if ( post.post_title == xapp.deleted && post.post_content == xapp.deleted ) cls += ' deleted';
        var item = '<div class="'+cls+'" post-id="'+post.ID+'">';
        item += '   <section class="display">';
        item += '       <a href="'+url+'" api="action=post_view" class="title">';
        item += '          ' + post.post_title + '';
        item += '       </a>';
        item += '       <div class="meta">' +

            '               <div class="info">' +
            '               <span class="no-caption">No.</span> <span class="no">'+post.ID+'</span>' +
            '               <span class="author-caption">author:</span><span class="author">'+post.author_name+'</span>' +
                '           </div>'+

            '               <div class="buttons">' +
            '                   <span class="post-edit-button">Edit</span>' +
            '                   <span class="'+post_delete_button+'">Delete</span>' +
            '                   <span class="'+post_like_button+'">like'+like_number+'</span>' +
            '                   <span class="post-report-button">Report</span>' +
            '                   <span class="post-copy-button">Copy</span>' +
            '                   <span class="post-move-button">Move</span>' +
            '                   <span class="post-blind-button">Blind</span>' +
            '                   <span class="post-block-button">Block</span>' +
            '               </div>' +
            '           </div>';
        item += '       <section class="content">' + post_content + '</section>';
        item += markup.comment_write_form( post.ID );
        item += '</section>';
        item += markup.comments( post );

        item += '</div>';

        m += item;
    }
    m += '  </div>';


    m += '</div>';
    return m;

};

/**
 *
 * Returns HTML markup of comment list.
 *
 * @param post
 * @returns {string}
 */

markup.comments = function( post ) {
    var m = '';
    m += '<div class="comments">';
    m += '<div class="meta">';
    m += '  <div class="count">';
    m += markup.get_comments_meta_count( post.comment_count );
    m += '  </div>';
    m += '</div>';

    m += '  <div class="comment-list">';

    if ( post['comments'] ) {
        for( var i in post['comments'] ) {
            var comment = post['comments'][i];
            m += markup.comment( comment );
        }
    }

    m += '   </div>';
    m += '</div>';

    return m;
};


markup.comment = function( comment ) {
    if ( isEmpty(comment.depth) ) comment.depth = 0;
    var cls = 'comment';
    if ( comment.comment_content  == xapp.deleted ) cls += ' deleted';
    var like_number = '';
    if ( comment.like ) {
        like_number = '<span class="no">'+comment.like+'</span>';
    }
    return '' +
        '<div class="'+cls+'" comment-ID="'+comment.comment_ID+'" depth="'+comment.depth+'">' +
        '   <div class="comment-meta">' +
        '       <div>' +
        '           No.: ' + comment.comment_ID +
        '           Author: ' + comment.comment_author + "" +
        "           " +
        "       </div>" +

        '               <div class="buttons">' +
        '                   <span class="'+comment_edit_button+'">edit</span>' +
        '                   <span class="'+comment_delete_button+'">delete</span>' +
        '                   <span class="'+comment_like_button+'">like'+like_number+'</span>' +
        '                   <span class="'+comment_report_button+'">report</span>' +
        '                   <span class="'+comment_copy_button+'">copy</span>' +
        '                   <span class="'+comment_move_button+'">move</span>' +
        '                   <span class="'+comment_blind_button+'">blind</span>' +
        '                   <span class="'+comment_block_button+'">block</span>' +
        '               </div>' +
        '   </div>' +
        '<div class="comment-content">' +
            // sanitize_content( comment.comment_content ) +
            get_content( comment ) +
        '</div>' +

        markup.comment_write_form( comment.comment_post_ID, comment.comment_ID ) +

        '' +
        '</div>';
};



markup.get_comments_meta_count = function ( count ) {
    var m = '';
    if ( parseInt( count  ) == 0 ) {
        m = '<div class="no-comment">No comments under this post...</div>';
    }
    else {
        m = '<div class="has-count">Comments: <span class="no">' + count + "</span></div>";
    }
    return m;
};


markup.get_post_list_page_header = function ( data ) {
    var category = data.category;
    var page = get_page_no(data.in['page']);
    var m = '';
    m += '  <div class="header">';
    m += '      <h4 class="list-group-item-heading">' + category['cat_name'] + '</h4>';
    m += '      <p class="list-group-item-text">' +
        '           <div class="meta">Page: '+page+', No. of Posts : '+ category['count'] +'</div>' +
        '           <div class="description">'+category['category_description']+'</div>' +
        '           <div class="buttons">' +
        '               <button class="'+post_write_button+' btn btn-secondary">POST</button>' +
        '               <button class="top btn btn-secondary">TOP</button>' +
        '           </div>' +
        '       </p>';
    m += '  </div>';
    return m;
};


markup.post_write_form = function ( $this ) {
    var $header = $this.closest( '.header' );
    var $page = $header.parent();
    var page_no = $page.attr('page');
    var slug = $page.attr('slug');
    var m = '' +
        '<div class="'+post_write_form+'" page-no="'+page_no+'">' +
        '   <form>' +
        '       <input type="hidden" name="do" value="post_edit_submit">' +
        '       <input type="hidden" name="domain" value="housemaid.philgo.com">' +
        '       <input type="hidden" name="content_type" value="text/plain">' +
        '       <input type="hidden" name="response" value="ajax">' +
        '       <input type="hidden" name="slug" value="'+slug+'">' +
        '       <input type="hidden" name="post_ID" value="">' +
        '       <input type="hidden" name="session_id" value="'+xapp.session_id+'">' +
            markup.post_form_title() +
            markup.post_form_content() +
        '   <div class="buttons">' +
        '       <span class="left file-upload">' +
        '               <input type="file" name="userfile" onchange="comment_file_upload(this);">' +
        '               <i class="fa fa-camera '+file_upload_button+'"></i>' +
        '       </span>' +
        '       <span class="right">' +
        '           <button type="button" class="submit btn btn-secondary btn-sm">SUBMIT</button>' +
        '           <button type="button" class="cancel btn btn-secondary btn-sm">CANCEL</button>' +
        '       </span>' +
        '   </div>' +
        '   </form>' +
        '</div>';
    return m;
};

markup.post_form_title = function () {
    return '' +
        '       <fieldset class="form-group">' +
        '           <label class="sr-only">Title</label>' +
        '           <input class="form-control" type="text" name="title" value="" placeholder="Input title">' +
        '       </fieldset>';
};
markup.post_form_content = function() {
    return '' +
        '       <fieldset class="form-group">' +
        '           <label class="sr-only">Content</label>' +
        '           <textarea class="form-control" name="content"></textarea>' +
        '       </fieldset>';
};

/***
 * @since 2016-08-29. post_edit_form has merged into post_write_form.
 *
markup.post_edit_form = function ( $post ) {
    var m = '' +
        '<div class="'+post_edit_form+'">' +
        '   <form>' +
        '       <input type="hidden" name="do" value="post_edit_submit">' +
        '       <input type="hidden" name="domain" value="housemaid.philgo.com">' +
        '       <input type="hidden" name="content_type" value="text/plain">' +
        '       <input type="hidden" name="response" value="ajax">' +
        '       <input type="hidden" name="post_ID" value="">' +
        '       <input type="hidden" name="session_id" value="'+xapp.session_id+'">' +

        markup.post_form_title() +
        markup.post_form_content() +

        '       <button type="button" class="submit btn btn-secondary btn-sm">SUBMIT</button>' +
        '       <button type="button" class="cancel btn btn-secondary btn-sm">CANCEL</button>' +
        '   </form>' +
        '</div>';

    return m;

};
 */







markup.user_login_form = function( ) {
    var m = '' +
    '<div class="user-login-form">' +
    '   <form>' +
    '       <input type="hidden" name="forum" value="user_login_check">' +
        '       <input type="text" name="user_login" value="" placeholder="Input User ID">' +
        '       <input type="password" name="user_pass" value="" placeholder="Input Password">' +

        '       <div class="user-login-form-message"></div>' +

        '   <div class="button">' +
    '          <button type="button" class="submit btn btn-secondary btn-sm">SUBMIT</button>' +
    '          <button type="button" class="cancel btn btn-secondary btn-sm">CANCEL</button>' +
        '   </div>' +
    '   </form>' +
    '</div>';

    return m;
};



markup.user_register_form = function( ) {
    var m = '' +
        '<div class="user-register-form">' +
        '   <form>' +
        '       <input type="hidden" name="forum" value="user_register">' +
        '       <input type="text" name="user_login" value="" placeholder="Input User Login ID">' +
        '       <input type="password" name="user_pass" value="" placeholder="Input Password">' +
        '       <input type="text" name="user_email" value="" placeholder="Input Email">' +
        '       <input type="date" name="birthday" value="">' +
        '       <input type="text" name="gender" value="">' +
        '       <div class="user-register-form-message"></div>' +
        '       <div class="buttons">' +
        '          <button type="button" class="submit btn btn-secondary btn-sm">REGISTER</button>' +
        '          <button type="button" class="cancel btn btn-secondary btn-sm">CANCEL</button>' +
        '       </div>' +
        '   </form>' +
        '</div>';

    return m;
};




var get_loader = markup.get_loader = function () {
    return '' +
        '<div>' +
        '   <i class="fa fa-spin fa-spinner"></i>' +
        '   Loading ...' +
        '</div>' +
        '';
};




markup.user_account_form = function() {
    return '' +
        '<div class="user-account-form">' +
        '   <div class="user-logout-button btn btn-primary">Logout</div>' +
        '' +
        '' +
        '' +
        '' +
        '</div>';
};


/**
 *
 * post_ID 가 '.comment' 객체이면 코멘트 수정이다.
 *
 * @param post_ID
 * @param comment_parent
 * @returns {string}
 */
markup.comment_write_form = function( post_ID, comment_parent ) {

    var cls = comment_write_form;
    if ( isjQuery(post_ID) ) {
        var $comment = post_ID;
        var comment_ID = $comment.attr('comment-ID');
        var content = $comment.find('.comment-content').text();
        post_ID = '';
        comment_parent = '';
        cls += ' selected';
    }
    else {
        if ( typeof comment_parent == 'undefined' ) comment_parent = 0;
        comment_ID = '';
        content = '';
    }

    var m = '<div class="'+cls+'">' +
        '<form enctype="multipart/form-data" action="" method="POST">' +
        '   <input type="hidden" name="forum" value="comment_edit_submit">' +
    '       <input type="hidden" name="content_type" value="text/plain">' +
    '       <input type="hidden" name="domain" value="housemaid.philgo.com">' +
    '   <input type="hidden" name="session_id" value="'+xapp.session_id+'">' +
        '   <input type="hidden" name="response" value="ajax">' +
        '   <input type="hidden" name="post_ID" value="'+ post_ID +'">' +
        '   <input type="hidden" name="comment_parent" value="'+ comment_parent +'">' +
        '   <input type="hidden" name="comment_ID" value="'+ comment_ID +'">' +
        '   <table>' +
        '       <tr valign="top">' +
        '           <td>' +
        '               <div class="file-upload">' +
            '               <input type="file" name="userfile" onchange="comment_file_upload(this);">' +
            '               <i class="fa fa-camera '+file_upload_button+'"></i>' +
        '               </div>' +
        '           </td>' +
        '           <td width="99%">' +
        '               <textarea name="comment_content">'+content+'</textarea>' +
        '           </td>' +
        '       </tr>' +
        '       <tr>' +
        '           <td></td>' +
        '           <td class="buttons">' +
        '               <button class="'+comment_write_button+'" type="button">Submit</button>' +
        '               <button class="'+comment_cancel_button+'" type="button">Cancel</button>' +
        '           </td>' +
        '       </tr>' +
        '   </table>' +
        '</form>' +
        '</div>';
    return m;
};

/**
 *
 * @since 2016-07-29 comment edit merged into comment write.

markup.comment_edit_form = function ( $comment ) {
    var comment_ID = $comment.attr('comment-ID');
    var content = $comment.find('.comment-content').text();
    var m = '' +
        '<div class="'+comment_edit_form+'">' +
        '   <form>' +
        '       <input type="hidden" name="forum" value="comment_edit_submit">' +
        '       <input type="hidden" name="domain" value="housemaid.philgo.com">' +
        '       <input type="hidden" name="content_type" value="text/plain">' +
        '       <input type="hidden" name="session_id" value="'+xapp.session_id+'">' +
        '       <input type="hidden" name="response" value="ajax">' +
        '       <input type="hidden" name="comment_ID" value="'+ comment_ID +'">' +
        '   <table>' +
        '       <tr valign="top">' +
        '           <td>' +
        '               <i class="fa fa-camera fa-2x"></i>' +
        '           </td>' +
        '           <td width="99%">' +
        '               <textarea name="comment_content">'+content+'</textarea>' +
        '           </td>' +
        '       </tr>' +
        '       <tr>' +
        '           <td></td>' +

        '           <td class="buttons">' +
        '               <button class="submit" type="button">Submit</button>' +
        '               <button class="cancel" type="button">Cancel</button>' +
        '           </td>' +
        '       </tr>' +
        '   </table>' +

        '   </form>' +
        '</div>';
    return m;
};
*/

