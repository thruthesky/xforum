/**
 *
 * @file xapp.endless.js
 * @desc This scripts hold methods for endless related codes.
 *
 */

/// variables
if ( typeof xapp == 'undefined' ) var xapp = {};
var endless = xapp.endless = {};

/**
 *
 * @note variables
 *
 * @type {number}
 */
endless.in_loading = false;                // if true, it is in loading from server. whether it is in loading.
endless.no_more_posts = false;                     // if true, there is no more post from that forum.
endless.distance_from_bottom = 300;
endless.page = 0;
(function() {
    var $window = $( window );
    var $document = $( document );
    $document.scroll( function() {
        if ( endless.get_no_more_posts() ) return; // no more posts?
        if ( endless.is_in_loading() ) return xapp.callback_endless_in_loading(); // in loading?
        var top = $document.height() - $window.height() - endless.distance_from_bottom; // compute page position
        if ($window.scrollTop() >= top) endless.load_next_page(); // page reached at the bottom?
    });
}());


/**
 * Returns endless.in_loading
 *
 * @returns {number|boolean}
 */
endless.is_in_loading = function() {
    return endless.in_loading;
};

/**
 * Returns endless.no_more_posts;
 * @returns {boolean}
 */
endless.get_no_more_posts = function () {
    return endless.no_more_posts;
};
/**
 * Sets there is no more posts from server.
 * @returns {boolean}
 */
endless.set_no_more_posts = function() {
    return endless.no_more_posts = true;
};


endless.load_next_page = function() {

    endless.page ++;

    console.log("xapp.endless.js count:" + endless.page + ", : " + '');
    var o = xapp.callback_endless_cache_args( endless.page );


    if ( o ) {
        //console.log( o );

        /**
         *
         * Load more data.
         *
         *      - show loader icon
         *      - get data from server
         *      - display it.
         *      - hide loader icon.
         */

        endless.loading_begin(); // until cache finishes.
        xapp.cache( o );


    }
    else {

    }
};

endless.loading_begin = function() {
    layout.main().append('<i class="post-list-loader fa fa-spinner fa-pulse fa-3x fa-fw"></i>');
    endless.in_loading = true;
};
