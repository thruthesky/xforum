if ( typeof xapp == 'undefined' ) var xapp = {};



/**
 *
 * Saves cache data and stamp with the input 'id'.
 *
 *
 * @param id
 * @param data
 * @code
 *      db.setCache( o.id, re );
 * @endcode
 */
db.setCache = function (id, data) {

    db.set( id, data );
    var stamp = Math.floor(Date.now() / 1000);
    db.set( id + '_stamp', stamp );

};


/**
 *
 *
 * Returns the cache data.
 *
 * @note it returns the cached stamp & data of the 'id'.
 *
 * @param id
 * @returns {{stamp, data}}
 *
 * @code
 *      console.log( db.getCache( 'test4' ) );
 * @endcode
 *
 */
db.getCache = function ( id ) {
    var data = db.get( id );
    var stamp = db.get( id + '_stamp' );
    return {
        stamp: stamp,
        data: data
    };
};


/**
 *
 * @param id
 * @param seconds
 * @returns {boolean}
 *
 *      - returns false when there is no id + '_stamp' key exists. ( which means the data was never saved ).
 */
db.expired = function( id, seconds ) {
    var old_stamp = db.get( id + '_stamp', 0 );
    var new_stamp = Math.floor(Date.now() / 1000);
    old_stamp = parseInt( old_stamp ) + seconds;



    if ( isNaN(old_stamp) || old_stamp < new_stamp ) {
        //console.log( id + ' has expired' );
        return true;
    }
    else {
        // console.log( id + ' has cached until: ' + new Date( old_stamp * 1000 ).toString());
        return false;
    }
};



xapp.doCache = function (o) {
    //console.log( 'xapp.doCache() : Going to CACHE for ' + o.id + ' URL: ' + o.url  );
    //console.log( o );
    // console.log( 'xapp.doCache() : ', o);
    this.get( o.url, function(re) {
        db.set ( o.id, re );
        db.setCache( o.id, re );
        o.success( re );
    }, o.failure );
};

/**
 * IF id is NOT given,
 *
 *      It is the same as xapp.get() without id.
 *
 *      - it does not use cache. ( because there is no id. how can it do cache without id )
 *
 *      - get the data from server and call success callback.
 *
 *      - it does not save the data into db. ( because there is no id, how can it save data ).
 *
 *
 * if id is GIVEN
 *
 *      - if expires == 0, then
 *
 *          - it does not use cache. ( but save the result data into db. No need to delete the cache. )
 *
 *          - just get data from server
 *
 *              - save the data.
 *
 *              - and call success callback.
 *
 *      - if expires > 0, then
 *
 *          - Cache Expired ??
 *
 *              - then, it gets data from server and save it. ( it does not fire success callback again. No need to delete the cache. )
 *
 *              - else, just return ( don't do anything else )
 *
 *
 * @param o
 *
 *
 * @code cache without id
 *
 xapp.cache( {
        'url' : url,
        'success' : function( re ) { console.log('success', re); },
        'failure' : function( re ) { console.log('failure', re); }
    } );
 *
 * @endcode
 *
 *
 *
 * @code example 'caching with default expire seconds'
 *
 xapp.cache( {
        'url' : url,
        'id' : 'test4', // <<===================
        'success' : function( re ) {
            console.log(re);
            $('#main').prepend( re );
        },
        'failure' : function( re ) { console.log('failure', re); }
    } );
 *
 * @endcode
 *
 * @code example of '1 hour caching'.
 xapp.cache( {
        'url' : url,
        'id' : 'test4', // <<==========================
        'expire' : 60 * 60, // <<==========================
        'success' : function( re ) {
            console.log(re);
            $('#main').prepend( re );
        },
        'failure' : function( re ) { console.log('failure', re); }
    } );

 * @endcode
 *
 *
 * @attention 2016-07-15, 'expire' is set to 0.
 *
 *
 */
xapp.cache = function ( o ) {

    var defaults = {
        'expire': 0,
        'success' : function() {},
        'failure' : function() {}
    };


    o  = $.extend( defaults, o );

    if ( typeof o.id == 'undefined' || o.id == '' ) {
        this.get( o.url, o.success, o.failure );
    }
    else {
        if ( o.expire == 0 ) {
            xapp.doCache( o );
        }
        else {

            // cache expired or not?
            if ( db.expired( o.id, o.expire ) ) {


                xapp.doCache( o );
            }
            else o.success ( db.get( o.id ) );
        }
    }
};
