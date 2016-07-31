if ( typeof xapp == 'undefined' ) var xapp = {};




/**
 *
 * @param o
 *
 * @code  Example of getting
 *
 xapp.wp_get_categories({
        'expire' : 86400 * 7,
        'success' : function( re ) {
            console.log(re);
        },
        'failure' : function( re ) {
            alert('ERROR on getting categories');
        }
    });
 *
 * @endcode
 */
xapp.wp_get_categories = function ( o ) {
    var defaults = {
        id : 'wp_get_category',
        url : this.server_url + '?forum=api&action=get_categories',
        expire : 86400 // cache for a day.
    };
    o  = $.extend( defaults, o );

    xapp.cache( o );
};

/**
 * This method does
 *      1. WP_Query to the server
 *      2. Gets all result of data ( post, comments, meta information, author information, and every ting that related with the post )
 *      3. Pass the data to success callback.
 * @param o
 */
/**
 *
 *
xapp.wp_query = function (o) {
    var defaults = {
        url : this.server_url,
        forum : 'api'
    };
    o = $.extend( defaults, o );
//    console.log(o);
    xapp.cache( o );
};

*/
