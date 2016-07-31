/**
 *
 * @file xapp.callback.js
 * @desc This script holds callback functions that does not belong to any other scripts.
 *
 */

///
if ( typeof xapp == 'undefined' ) var xapp = {};

/**
 *
 * @file xapp.callback.js
 * @desc This script holds default callback routines. You can overwrite this as your need.
 */


/**
 *
 * 게시판의 게시물이 로드된 경우 이 함수가 호출된다.
 *
 * - Endless 호출은 오직 게시물 뿐이다. 다른 어떤 정보도 endless 방식으로 데이터를 로드하지 않는다.
 * - Endless 로 데이터가 서버로 부터 전달되면, 이 함수가 호출되고 화면에 맨 아래에 추가하면 된다.
 *
 * @note This is a callback function. This callback is called when there is new post list data from server.
 *
 * @param re
 */
xapp.callback_endless_post_list = function( re ) { // Callback for display post data on device.
    // console.log( re );
    if (x.success( re ) ) {
        xapp.file_server_url = re.data.file_server_url;
        // alert(xapp.file_server_url);
        var m = markup.post_list_page( re.data );
        layout.main().append( m );
        setTimeout(function() {
            xapp.callback_endless_finish_loading();
            xapp.callback_post_add_show_more(re.data);
        }, 20);
    }
};





xapp.callback_endless_finish_loading = function() {
    layout.main().find('.post-list-loader').remove();
    endless.in_loading = false;
};


/**
 * (해당 게시판에서) Endless 로 데이터를 전송 받았는데, 게시물이 더 이상 존재 하지 않을 때 호출 된다.
 */
xapp.callback_endless_no_more_posts = function () {
    layout.main().append( "<h2>No more posts</h2>");
};


xapp.callback_endless_in_loading = function () {
    // console.log("callback_endless_in_loading");
};




/**
 *
 * Must return the parameta of xapp.cache() to get data from server.
 *
 *
 *
 */
xapp.callback_endless_cache_args = function ( page ) {
    if ( this.isFront() ) return xapp.post_list_query_args_for_front( page );
    else if ( this.isPostList() ) return xapp.post_list_query_args( page );
    else return null;
};



/**
 *
 * 첫번째 페이지를 endless 방식으로 표시하고자 할 경우 이 함수를 사용 할 수 있다.
 *
 * - 이 함수가 null 을 리턴하면, xapp.endless.js 에서 서버로 request 를 하지 않는다.
 */
xapp.post_list_query_args_for_front = function ( page ) {

};



/**
 *
 * 게시판의 글을 서버로 부터 추출한다.
 *
 * - 이 함수는 정형화(활용도가 고정)되어져 있어서 따로 커스터마이징을 할 필요가 없다.
 *
 *
 * @param page - page no. what page of content ( post ) should be displayed?
 *
 * @attention 2016-07-24. caching for post list is disabled.
 */
xapp.post_list_query_args = function ( page ) {
    var id = xapp.query + '_' + page;
    //page = parseInt( page ) + 1;
    var query = {
        url: xapp.server_url + '?forum=api&' + xapp.query + '&page=' + page + '&posts_per_page=4',
        expire : xapp.option.cache.post_list_expire,
        success : this.callback_endless_post_list,
        'failure' : function ( re ) {
            alert('ERROR on xforum api query. Please check if the server url correctly set.');
        }
    };
    if ( xapp.option.cache.post_list ) {
        query.id = id;
    }
    // console.log(query);
    return query;
};




/**
 * 첫번째 페이지를 표시한다.
 *
 * - xapp.start() 에 의해서 첫번째 페이지로 인식되면 이 메소드가 호출된다.
 * - 첫번째 페이지 표시에 대한 모든 것을 출력 하면된다.
 * - 추가적으로 endless 에 대한 내용을 출력하고자 한다면, post_list_query_args_for_front() 를 참고한다.
 *
 * @note It displays front page.
 *
 */
xapp.callback_front_page = function ( ) {
    xapp.wp_get_categories({
        'expire' : xapp.option.cache.front_page_expire,
        'success' : function( re ) {
            // console.log(re);
            var m = xapp.bootstrap.list_group_linked_items( {
                'title' : 'Forum list',
                'lists' : xapp.convert_categories_into_list_group_item( re.data )
            } );
            // console.log(m);
            layout.main().prepend(m);
        },
        'failure' : function( re ) {
            alert('ERROR on getting categories');
        }
    });
};
