var x = jQuery;
var server_url = document.location.origin;
$(function(){
    loadPage();
    var $body = $('body');
    $body.on('click', '[rel="ajax"]', ajaxLoad);
});
x.holder = function() {
    return x('.x-holder');
};
(function($){
    $.fn.getName = function() {
        if ( this.hasClass('x-page') ) return this.attr('name');
        else return 'undefined';
    };
    $.fn.getPage = function() {
        return x('.x-page');
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
    }
}(jQuery));

function loadPage() {
    console.log('page:  ' + x().isFrontPage());
    if ( x().isFrontPage() ) {
        $.get( server_url + '?forum=api&action=page&name=index', function( re ){
            // console.log(re);
            x.holder().html( re );
        });
    }
    else {
    }
}

function ajaxLoad(e) {
    e.preventDefault();
    var href = $(this).attr('url');
    console.log(href);
    $.get( href, function(re) {
        x.holder().html( re );
    });
}
