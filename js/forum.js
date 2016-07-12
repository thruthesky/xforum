( function ( $ ) {

    var bEditClicked = false;
    var xforum_write_href = '';


    var e = {
        'loginModal': function () {
            return $('#loginModal');
        },
        'loginButton' : function () {
            return $('.xforum-login-button');
        }
    };
    var $body = $('body');
    $body.on('click', '.xforum-login-button', xForumLogin);
    $body.on('click', '.xforum-login-submit-button', xForumLoginSubmit);
    $body.on('click', '.xforum-logout-button', xForumLogoutSubmit);
    $body.on('click', '.xforum-edit-button', xForumEditButton);


    function xForumEditButton() {
        var $button = $(this);
        xforum_write_href = $button.attr('href');
        if ( e.loginButton().length ) {
            bEditClicked = true;
            openLoginBox();
        }
        else moveToWritePage();
    }

    /**
     *
     * Moves to the write page.
     *
     * @note It is called when write button is clicked.
     *      - if the user logged in, it is called directly.
     *      - if the user is not logged in,
     *          - it first opens login box
     *              - if login success it moves to write page.
     *              - if login fail, don't move.
     *
     *
     */
    function moveToWritePage() {
        location.href = xforum_write_href;
    }


    /**
     *
     */
    function xForumLogin() {
        openLoginBox();
    }

    /**
     *
     * Opens user login box
     *
     * @note
     */
    function openLoginBox() {
        var $old = e.loginModal();
        if ( $old ) $old.remove();
        var m = html_bootstrap_login_popup();
        $body.append( m );
        var $loginModal = e.loginModal();


        $loginModal.modal();

        /**
         * @todo you need to do 'off' on each 'on'.
         */
        $loginModal.on('hide.bs.modal', function(e) {
            console.log('close');
            $loginModal.remove();
        });
    }

    /**
     *
     *
     *
     *
     */
    function xForumLoginSubmit() {
        var $form = $(this).parents('.modal').find('form');
        var data = $form.serialize();
        var url = home_url + '?forum=login&'+data;
        $.post( url, function( response ) {
            if ( typeof response.success == 'undefined' ) return alert('Wrong response.');
            console.log( response );
            if ( response.success ) {
                callback_xforum_user_login( response.data );
                e.loginModal().modal('hide');
                e.loginModal().remove();
            }
            else {
                var data = response.data;
                alert( "ERROR (" + data.code + ") : " + data.message );
            }
        });
    }

    /**
     *
     *
     */
    function xForumLogoutSubmit() {
        var url = home_url + '?forum=logout';
        console.log(url);
        $.get( url, function( response ) {
            if ( typeof response.success == 'undefined' ) return alert('Wrong response.');
            console.log( response );
            if ( response.success ) {
                callback_xforum_user_logout( response.data );
            }
            else {
                var data = response.data;
                alert( "ERROR (" + data.code + ") : " + data.message );
            }
        });
    }

    /**
     *
     *
     * @param html
     */
    function callback_xforum_user_login(html) {
        $('.xforum-login-button').replaceWith( html );
        if ( bEditClicked ) {
            moveToWritePage();
            bEditClicked = false;
        }
    }

    /**
     *
     *
     * @param html
     */
    function callback_xforum_user_logout(html) {
        $('.xforum-profile').replaceWith( html );
    }

} ) ( jQuery );


function html_bootstrap_login_popup() {
    var m = '';
    m += '' +
        '<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">' +
        '   <div class="modal-dialog" role="document">' +
        '       <div class="modal-content">' +
        '           <div class="modal-header">' +
        '               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        '               <h4 class="modal-title" id="loginModalLabel">User Login</h4>' +
        '           </div>' +
        '           <div class="modal-body">' +
        '               <form>' +
        '                   <div class="form-group">' +
        '                       <label for="user-login" class="form-control-label">User ID:</label>' +
        '                       <input type="text" class="form-control" id="user-login" name="id" placeholder="Input user id.">' +
        '                   </div>' +
        '                   <div class="form-group">' +
        '                       <label for="user-password" class="form-control-label">Message:</label>' +
        '                       <input type="password" class="form-control" id="user-password" name="password" placeholder="Input user password.">' +
        '                   </div>' +
        '               </form>' +
        '           </div>' +
        '           <div class="modal-footer">' +
        '               <button type="button" class="btn btn-primary xforum-login-submit-button">User Login</button>' +
        '               <button type="button" class="btn btn-secondary">Register</button>' +
        '               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>' +
        '           </div>' +
        '       </div>' +
        '   </div>' +
        '</div>' +
        '';
    return m;
}
