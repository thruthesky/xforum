<?php

class testUser extends user
{

    public function __construct()
    {

        parent::__construct();

    }

    public function runTest()
    {
        $this->remoteCRUD();
        $this->testInstance();
        $this->crud();
        $this->setGet();
        $this->forceLogin();
        $this->permission();
        $this->test_session_id();
        $this->test_login();
        $this->test_remote_login();
    }


    private function testInstance()
    {
        $user = user();
        check( $user instanceof user, "User instance okay.", "should be user instance");
    }

    public function crud() {

        $user_id_count = date("his");
        user()
            ->set('a', 'b');
        check( user::$user_data['a'] == 'b' , "a=b", "user()->set() problem" );
        $id = @user()->create();
        check( is_wp_error( $id ), "User create shouldn't be done yet because no user_login provied.: " . $id->get_error_message(), "user()->set()->create() should be error this time.");


        $id = @user()
            ->set('user_login', "id_$user_id_count")
            ->create();

        check( is_integer( $id ), "User created", "user()->set()->create() failed." . (is_wp_error($id) ? $id->get_error_message() : null));

        $user_id_count .= "_2";
        $user_login = "user_login_$user_id_count";
        $nickname = "nickname_$user_id_count";
        $email = "email_$user_id_count@gmail.com";

        $id = user()
            ->set('user_login', $user_login)
            ->set('user_pass', "pass_$user_id_count")
            ->set('user_email', $email)
            ->set('nickname', $nickname)
            ->set('name', "name_$user_id_count")
            ->set('meta_key', 'meta_value')
            ->create();

        check( is_integer( $id ), "User created", "user()->set()->create() failed." . (is_wp_error($id) ? $id->get_error_message() : null));


        $user = user( $id );
        check( $user instanceof user, "User instance okay.", "should be user instance");
        check( $user->user_login == $user_login, 'User login match', "User login does not mach. $id");
        check( $user->nickname == $nickname, 'nickname match', 'nickname does not match');
        check( $user->user_email == $email, 'email match', 'email does not match');
        check( $user->meta_key == 'meta_value', 'meta key match', 'meta key/value does not match');

        /// delete user
        $user->delete();


        /// reload the user and check if match.
        $user = user( $id );
        check( ! $user->exists(), "User deleted", "User exists after delete");
        check( $user->user_login != $user_login, 'User login does not match ( deleted )', "User login match. $id");
        check( $user->user_email != $email, 'email does not match ( deleted )', 'email match');
        check( $user->meta_key != 'meta_value', 'meta key does not match ( delete )', 'meta key/value match');



        $user_id = user()
            ->set('user_login', $user_login)
            ->set('user_pass', '1111')
            ->set('nickname', $nickname)
            ->set('k', 'v')
            ->create();

        $user_A = user( $user_id );
        $user_B = user( 1 );
        $user_C = user( $user_A->ID );

        check( $user_B->ID != $user_C->ID, 'B & C are not equal', 'B & C are equal?');
        check( $user_B->ID == 1, 'B is admin', 'B is not admin?');
        check( $user_A->ID == $user_C->ID, "user_A's ID is $user_A->ID", "A & B are not equal?");

        check( $user_A->k == 'v', "A's meta is okay.", "A's meta is wrong");


        $admin = $user_B;
        $admin->k = 'k-admin';


        check( $user_A->k != $admin->k, "meta key 'k' are not the same.", "meta key 'k' are the same");
        check( $admin->k == 'k-admin', "admin's meta is okay.", "admin's meta is wrong");

        check( $user_A->nickname == $nickname, "nickname ok: $nickname", "Nickname is wrong");

        $user_A
            ->set('nickname', 'nick_a')
            ->set('k', 'value_a')
            ->update();

        check( $user_A->nickname == "nick_a", "nickname changed", "Nickname is wrong");
        check( $user_A->k == "value_a", "meta k is ok: $user_A->k", "meta k is wrong");

        $user_A->delete();

    }



    public function remoteCRUD() {

        $user_login = 'user_' . date('his');



        $p = [];
        $p['do'] = 'user_register';

        $p['user_pass'] = $user_login;
        $p['user_email'] = $user_login . '@gmail.com';
        $p['first_name'] = 'First Name';
        $p['last_name'] = 'Last Name';
        $p['nickname'] = 'Nickname';
        $p['mobile'] = '09170001234';
        $p['landline'] = '0450004322';
        $p['address'] = '경남 김해시 대성동 부림아파트 나동 201 호';

        $p['response'] = 'ajax';


        // User create FAIL test.
        $re = forum()->http_query( $p );
        check( ! $re['success'], "User create failed because no 'user_login' provided.", "User created?");



        // User create SUCCESS test.
        $p['user_login'] = $user_login;
        $re = forum()->http_query( $p );
        check( $re['success'], "remoteCRUD() : do=register OK. User created.", "Remote user create failed: $re[data]");

        $session_id = $re['data'];

        $s = [];
        $s['do'] = 'user_check_session_id';
        $s['session_id'] = $session_id;
        forum()->http_query( $s );
        check( $re['success'], "remote sessison_id check ok", "remote session_id check error.");




        $A = user( user()->get_user_id_from_session_id( $session_id ) );




        // User Update Test
        $p['do'] = 'user_update';
        $p['ID'] = $A->ID;
        $p['session_id'] = $session_id;
        //$p['user_login'] = $user_login . '_changed';
        $p['user_pass'] = $p['user_pass'] . '_changed';
        $p['first_name'] = $p['first_name'] . '_changed';
        $p['user_email'] = $p['user_email'] . '_changed';
        $p['user_nicename'] = $user_login . '_nice';

        $re = forum()->http_query( $p, true );


        check( $re['success'], "remoteCRUD() : do=user_update OK. User created.", "Remote user update failed: {$re['data']['message']}");

        clean_user_cache( $A );
        $B = user( $p['ID'] );


        check( $A->user_nicename != $B->user_nicename, "Nicename updated", "Nice name was not updated.");


        $d = [];
        $d['do'] = 'user_delete';
        $d['ID'] = $B->ID;
        $re = forum()->http_query( $d, true );
        check( ! $re['success'], "User is not deleted.", "User deleted. " . ( isset( $re['data'] ) ? $re['data'] : null ));

        $d['session_id'] = $B->get_session_id();
        $re = forum()->http_query( $d, true );
        check( $re['success'], "User deleted.", "User delete failed. " . ( isset( $re['data'] ) ? $re['data'] : null ));

        clean_user_cache( $B );
        $user = user( $d['ID'] );
        check( !$user->exists(), "User has been deleted.", "User is not deleted?");


    }



    private function userLogin()
    {

        user()->logout();

        $login = "my_login_" . date('his');
        $pass = "my_pass_" . date('his');
        $email = "my_email_" . date('his') . '@gmail.com';
        $nick = "my_nickname_" . date('his');
        $ID = user()
            ->set('user_login', $login)
            ->set('user_pass', $pass)
            ->set('user_email', $email)
            ->set('nickname', $nick)
            ->create();

        check( is_integer( $ID ), "User created for login test", "user()->set()->create() failed." . (is_wp_error($ID) ? $ID->get_error_message() : null));

        $user = user( $ID );
        check( ! user()->login(), 'User not logged in', 'User logged in? should not logged in after right after user create.' );

        $user->forceLogin();
        check( user()->login(), 'Yes, User has logged in after forceLogin()', 'User not logged in? after forceLogin()?' );

        $user->logout();
        check( ! user()->login(), 'User not logged in', 'User logged in?' );


        $re = user()->login( 'my_login', 'my_pass');
        check( $re, 'Yes, User has logged in with user()->login()', 'User not logged in with user()->login()' );

        user()->logout();



        $user->delete();
    }

    private function permission()
    {
        $login = 'login_' . date('his');
        $ID = user()
            ->set('user_login', $login)
            ->set('user_pass', '1111')
            ->create();

        check( ! user( $login )->admin(), "No, $login is NOT admin", "$login is admin?");

        check(user(1)->admin(), "Yes, user no. 1 is admin", "User no. 1 is not admin?");


    }

    private function setGet()
    {
        $user = user(1);
        $user->nickname = "admin_nickname";
        check( $user->nickname == user(1)->nickname, "nickname has changed", "nickname has not changed.");

        $user->meta_key = 'meta_value';
        check( $user->meta_key == user( $user->user_login )->meta_key, "meta key is okay: $user->meta_key", "Meta key is not okay" );
    }

    /**
     *
     *
     *
     *
     *
     *
     */
    public function test_session_id()
    {

        $user = user(1);
        $sid = $user->get_session_id();

        check( $sid == user( 1 )->get_session_id(), "Admin session id ok", "session error" );
        check( user( 1 )->check_session_id( $sid ), "Admin check session id ok", "session error" );
        check( user()->check_session_id( $sid ), "Self session check ok", "self session error" );

        $user_login = 'abc_' . date('his');
        $user_email = $user_login . '@gmail.com';
        $user_id = user()
            ->set('user_login', $user_login)
            ->set('user_pass', '1111')
            ->set('user_email', $user_email)
            ->create();

        $user = user( $user_id );

        check( $user_session_id = $user->get_session_id(), "Got user session", "No session ID?" );
        check( $user_session_id == user( $user_id )->get_session_id(), "user session ok", "User session not ok");
        check( user( $user_id )->check_session_id( $user_session_id ), "User session check ok.", "User session check error");
        check( $uid = user()->check_session_id( $user_session_id ), "Self User session check ok.", "Self User session check error");
        check( $uid == $user_id, "User session ID ok.", "User session ID error");


        check( user(1)->get_session_id() != user( $user_id )->get_session_id(), "User session check for admin and user is ok", "User session check error. admin and user have same session id?");
        check( $admin_id = user()->check_session_id( $sid ), "Proper session ID", "Wrong session ID" );
        check( $admin_id == 1, "Admin session ID ok.", "Admin session ID wrong");



    }

    private function test_login()
    {
        $user_login = '';
        $user_pass = '';
        $remember_me = '';
        $re = user()->login( $user_login, $user_pass, $remember_me );
        check( !$re, "test_login(): User login failed because no info provied.", "User logged in with no user login info?");

        $re = user()->doLogin( $user_login, $user_pass, $remember_me );
        check( !$re, "test_login(): User login failed.", "User logged in with no login info?");

        $user_login = 'user' . date('his');
        $user_pass = $user_login;
        $remmeber_me = true;

        $user_id = user()
            ->set('user_login', $user_login)
            ->set('user_pass', $user_pass)
            ->create();

        $re = user()->login( $user_login, $user_pass, $remember_me );
        check( $re, "User login OK.", "User login after user()->set()->create()");

        $re = user()->login( $user_login, $user_pass . 'wrong password', $remember_me );
        check( ! $re, "User fail OK with wrong password.", "User login ok with wrong password?");

        $re = user()->doLogin( $user_login, $user_pass, $remember_me );
        check( $re, "User doLogin OK.", "User doLogin failed after user()->set()->create()");


        $re = user()->doLogin();
        check( ! $re, "User doLogin failed without params and query vars.", "User doLogin OK without info?");

        add_query_var('user_login', $user_login);
        $re = user()->doLogin();
        check( ! $re, "User doLogin failed with _GET[user_login]=$user_login because password was not set.", "User doLogin OK without password?");

        add_query_var('user_pass', $user_pass);
        $re = user()->doLogin();
        check( $re, "User doLogin OK with query vars.", "User doLogin failed with query vars?");

    }

    public function test_remote_login()
    {

        $user_login = 'remote_login_test_' . date('his');



        $p = [];
        $p['do'] = 'user_register';

        $p['user_login'] = $user_login;
        $p['user_pass'] = '1234abcd';
        $p['user_email'] = $user_login . '@gmail.com';
        $p['first_name'] = 'First Name';
        $p['last_name'] = 'Last Name';
        $p['nickname'] = 'Nickname';
        $p['mobile'] = '09170001234';
        $p['landline'] = '0450004322';
        $p['address'] = '경남 김해시 대성동 부림아파트 나동 201 호';

        $p['response'] = 'ajax';

        $re = forum()->http_query( $p );
        check( $re['success'], "test_remote_login() : do=user_register OK. User created.", "test_remote_login(): user create failed: $re[data]");

        $session_id = $re['data'];

        $r = [];
        $r['do'] = 'user_login_check';
        $r['user_login'] = $user_login . '_fail_test';
        $r['user_pass'] = '1234abcd';
        $re = forum()->http_query( $r );
        check( ! $re['success'], "Fail test: wrong ID: $re[data]", "test_remote_login: fail test failed.");

        $r['user_login'] = $user_login;
        $r['user_pass'] = '1234abcd' . '_fail_test';
        $re = forum()->http_query( $r );
        check( ! $re['success'], "Fail test: wrong password: $re[data]", "test_remote_login: fail test failed: $re[data]");

        $r['user_login'] = $user_login;
        $r['user_pass'] = '1234abcd';
        $re = forum()->http_query( $r );
        check( $re['success'], "Login success: session_id: $re[data]", "test_remote_login: Login failed: $re[data]");


        check( $session_id == $re['data'], "Session ID of register and Session ID of login are the same.", "Session ID does not match.");


    }


}