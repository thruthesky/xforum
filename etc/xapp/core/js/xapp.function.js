if ( typeof xapp == 'undefined' ) var xapp = {};
function get_page_no( no ) {
    if (_.isEmpty( no )) no = 1;
    return no;
}

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
    if ( typeof obj == 'undefined' || typeof obj == null || obj == 'null' || obj == 'false' || obj == '' || obj == 0 || obj == '0' || ! obj || obj == {} || obj == [] ) return true;
    // else if ( _.isEmpty( obj ) ) return true;
    else return false;
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

