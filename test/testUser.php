<?php

class testUser extends user
{

    public function __construct()
    {
        error_reporting( E_ALL ^ E_NOTICE );
        parent::__construct();
    }

    public function runTest()
    {
        $this->testInstance();
        $this->crud();
        $this->userLogin();
        $this->permission();
        $this->remoteCRUD();
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
        $id = user()->create();
        check( is_wp_error( $id ), "User create shouldn't be done yet because no user_login provied.: " . $id->get_error_message(), "user()->set()->create() should be error this time.");


        $id = user()
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

    }



    public function remoteCRUD() {

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
            ->create();

        check( ! user( $login )->admin(), "No, $login is NOT admin", "$login is admin?");

        check(user(1)->admin(), "Yes, user no. 1 is admin", "User no. 1 is not admin?");


    }


}