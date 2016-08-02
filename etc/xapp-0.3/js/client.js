var x = {};
var db = Lockr;
var session_id = db.get('session_id');
var server_url = document.location.origin + '?session_id=' + session_id + '&';
$(function(){
    loadPage(function() {
        /// test
        $('[panel="login"]').click();
    });
    var $body = $('body');
    $body.on('click', '[rel="ajax"]', ajaxLoad);
    $body.on('click', '[panel]', panel.on_click);
    $body.on('click', '.panel .close', panel.on_close);
    $body.on('click', '.form.login .submit', user.login_submit);
});
x.holder = function() {
    return $('.x-holder');
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

}(jQuery));

function loadPage(callback) {
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
}


function ajaxLoad(e) {
    e.preventDefault();
    var href = $(this).attr('url');
    console.log(href);
    $.get( href, function(re) {
        x.holder().html( re );
    });
}

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
        console.log('shown');
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
                location.reload();
            }
            else {
                if ( re.data && re.data.message ) $form.setMessage( re.data.message );
            }
        }
    );
};
