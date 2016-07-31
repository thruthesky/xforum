$(function(){
    var $body = $('body');
    $body.on('click', '.user-login-button', xapp.callback_login_button_clicked );
    $body.on('click', '.user-login-form .submit', xapp.callback_login_form_submit);
    $body.on('click', '.user-login-form .cancel', xapp.callback_login_form_cancel);

    $body.on('click', '.user-register-button', xapp.callback_register_button_clicked);
    $body.on('click', '.user-register-form .submit', xapp.callback_register_form_submit);
    $body.on('click', '.user-register-form .cancel', xapp.callback_register_form_cancel);



    $body.on('click', '.user-account-button', xapp.callback_account_button_clicked);


    $body.on('click', '.user-logout-button', xapp.callback_logout_button_clicked);

    /**
     * @note xapp.session_id & xapp.user_login are set here.
     */
    xapp.session_id = db.get('session_id');
    xapp.user_login = db.get('user_login');
    if (_.isEmpty( xapp.session_id ) ) xapp.callback_on_user_logged_out();
    else xapp.callback_on_user_logged_in();

});
xapp.login = function() {
    if (_.isEmpty( xapp.session_id ) ) return false;
    else {
        if (_.isEmpty( xapp.user_login)) {
            return xapp.session_id;
        }
        else {
            return xapp.user_login;
        }
    }
};
xapp.callback_register_form_submit = function () {

    var $form = $(this).closest( 'form' );
    var url = xapp.server_url + '?' + $form.serialize();
    register_form_message().html( get_loader() );
    xapp.get(
        url,
        function(re) {
            console.log(re);
            if ( re['success'] ) {
                xapp.callback_register_form_submit_success(re);
            }
            else {
                xapp.callback_register_form_submit_error(re);
            }
        },
        function(re) {
            alert(re);
        }
    );

};

xapp.callback_login_button_clicked = function () {
    user_login_form().remove();
    var m = markup.user_login_form();
    layout.main().prepend( m );
};
xapp.callback_login_form_submit = function () {
    var $form = $(this).closest( 'form' );
    var url = xapp.server_url + '?' + $form.serialize();
    console.log(url);


    login_form_message().html( get_loader() );
    xapp.get(
        url,
        function(re) {
            console.log(re);
            if ( re['success'] ) {
                xapp.callback_login_form_submit_success(re);
            }
            else {
                xapp.callback_login_form_submit_error(re);
            }
        },
        function(re) {
            alert(re);
        }
    );

};

xapp.callback_login_form_cancel = function () {
    user_login_form().remove();
};


xapp.callback_register_button_clicked = function () {
    register_form.remove();
    var m = markup.user_register_form();
    layout.main().prepend( m );

};


xapp.callback_register_form_submit_error = function (re) {
    var m = '<div class="alert alert-danger" role="alert">' + re['data'] + "</div>";
    register_form_message().html( m );
};

xapp.callback_register_form_cancel = function () {
    register_form().remove();
};


xapp.user_login_save = function ( re ) {
    db.set( 'session_id', re['data']['session_id'] );
    db.set( 'user_login', re['data']['user_login'] );
};

xapp.callback_register_form_submit_success = function (re) {
    //console.log(re);
    xapp.user_login_save(re);
    xapp.reload();
};

xapp.callback_logout_button_clicked = function () {
    db.set( 'session_id', '' );
    db.set( 'user_login', '' );
    xapp.reload();
};


xapp.callback_login_form_submit_success = function (re) {
    xapp.user_login_save(re);
    xapp.reload();
};
xapp.callback_login_form_submit_error = function (re) {
    var m = '<div class="alert alert-danger" role="alert">' + re['data'] + "</div>";
    login_form_message().html( m );
};




xapp.callback_on_user_logged_out = function () {
    $('.element-user-logout').show();
};
xapp.callback_on_user_logged_in = function () {

    $('.user-login-value').text( xapp.login() );
    $('.element-user-login').show();
};


xapp.callback_account_button_clicked = function () {
    layout.main().prepend( markup.user_account_form() );
};


