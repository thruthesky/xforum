if ( typeof xapp == 'undefined' ) var xapp = {};
/**
 *
 *
 *
 * @file theme.js
 * @desc Edit this script for the needs of each site.
 * @note This is not part of 'xapp'.
 *
 *
 *
 *
 */
window.xappReady = function () {
    xapp.server_url = "http://work.org/wordpress/";
    // xapp.server_url = "http://wordpress46b1.org/";
    //xapp.server_url = "http://dev.withcenter.com/wordpress/";


    ///
    /// xapp.file_server_url = "http://www.file-server.com/file-upload/index.php";

    xapp.option.cache.post_list_expire = 0;
    xapp.option.cache.post_list = false;
    xapp.start();
    console.log('index.js $(function() { ... }); finished.');
    /// TEST ...
//    auto_show_write_forum();
    //auto_show_login_form();
    //auto_show_register_form();

    //alert( db.get('session_id') );
};
function auto_show_login_form() {
    setTimeout(function(){
        $('.user-login-button').click();
    }, 500);
}
function auto_show_register_form() {
    setTimeout(function(){
        $('.user-register-button').click();
    }, 500);
}
function auto_show_write_forum() {
    setTimeout(function(){
        $('.post-list-page[page="1"] ' + sel(post_write_button)).click();
    }, 1000);
}
