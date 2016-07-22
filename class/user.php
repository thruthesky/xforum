<?php
/**
 * @file class.php
 */
/**
 * Includes.
 */
require_once ABSPATH . '/wp-includes/pluggable.php';
require_once ABSPATH . '/wp-includes/user.php';
require_once ABSPATH . '/wp-admin/includes/user.php';

/**
 * Class WP_INCLUDE_USER
 */
class user extends WP_User {



public static $user_data = [];
    /**
     *
     * properties of WP_User
     *
     */
    static $properties = [
        'nickname',
        'description',
        'user_description',
        'first_name',
        'user_firstname',
        'last_name',
        'user_lastname',
        'user_login',
        'user_pass',
        'user_nicename',
        'user_email',
        'user_url',
        'user_registered',
        'user_activation_key',
        'user_status',
        'display_name',
        'spam',
        'deleted',
    ];


    /**
     * WP_LIBRARY_USER constructor.
     * @param null $uid - it can be one of the following value.
     *      - ID
     *      - user_email
     *      - user_login
     *      - empty - the current logged in user.
     */
    public function __construct( $uid = null )
    {
        if ( $uid ) {
            if ( is_numeric($uid) ) parent::__construct( $uid );
            else if ( is_email( $uid ) ) parent::__construct( get_user_by( 'email', $uid ) );
            else parent::__construct( 0, $uid);
        }
        else {
            parent::__construct( $this->currentUser()->ID );
        }
    }


    public function currentUser() {
        return wp_get_current_user();
    }


    /**
     *
     * Check if the user has logged in.
     *
     * @update 2016-07-22. If $user_login is not null, then it tries to login the user.
     *
     * @note (Ko) WP 의 is_user_logged_in() 을 그대로 리턴한다.
     *
     *  이 함수는 wp_get_current_user()->exists() 와 같은 것이다.
     *
     *  따라서, 로그인을 했는지 안했는지는 wp_get_current_user() 로 사용자의 값을 추출 할 수 있는지 없는지
     *      또는 wp_get_current_user() 에 사용자가 설정되어져 있는지 없는지에 달려져 있다.
     *
     *
     * @param null $user_login
     * @param null $user_pass
     * @param null $remember_me
     *
     * @return bool - same as is_user_logged_in();
     * @code
     *      user()->login();
     * @endcode
     *
     * @see test/testUser::userLogin() for sample code.
     *
     *
     */

    public function login($user_login=null, $user_pass=null, $remember_me=null) {
        if ( $user_login ) {
            return $this->doLogin($user_login, $user_pass, $remember_me);
        }
        return is_user_logged_in();
    }

    /**
     * Set the logged in user to 'logged out'.
     *
     * @attention it is different from wp_logout() since it clears global $current_user.
     * @see test/testUser::userLogin() for sample code.
     *
     * @code
     *          user()->logout();
     * @endcode
     *
     *
     */
    public function logout() {


        wp_destroy_current_session();
        wp_clear_auth_cookie();

        global $current_user;
        $current_user = null;

    }


    /**
     *
     * Set the user logged in without password check.
     *
     * @note This is stateless which means, on next access, the user is no longer logged in.
     * @param $user_login
     * @return false|WP_User
     *
     *
     * @see test/testUser::userLogin() for sample code
     *
     */
    public function forceLogin($user_login=null) {
        // echo "user_login: $user_login\n";
        if ( empty($user_login) ) $user_login = $this->user_login;
        $user = get_user_by( 'login', $user_login );
        if ( $user === false ) {
            xlog("error on forceLogin");
        }
        wp_set_current_user( $user->ID );
        return $user;
    }


    /**
     * @deprecated use __get()
     * Returns a field value.
     * @param $field
     * @return bool|mixed
     */
    public function field($field) {
        if ( ! $this->login() ) return false;
        return $this->currentUser()->$field;
    }



    /**
     * Returns true if the user is admin ( can manage options )
     *
     * @return bool
     *
     * @code
     *      user(1)->admin()
     * @endcode
     *
     * @see test/testUser::permission for example.
     */
    public function admin()
    {
        return user_can( $this->ID, 'manage_options' );
    }


    /**
     *
     * It only sets key/value on self::$userdata for the use of user()->create & user()->update
     *
     * @param $key
     * @param $value
     * @return $this
     *
     * @see test/testUser.php for sample codes.
     */
    public function set( $key, $value ) {
        self::$user_data[$key] = $value;
        return $this;
    }


    /**
     *
     * Creates a user
     *
     *
     *
     * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not
     *                      be created.
     *
     *
     * @desc (Korean) 사용자 추가를 한다.
     *
     * 워드프레스의 기본 필드를 입력 받고, 추가 필드가 입력되면, 사용자 메타 정보로 저장한다.
     *
     * @note This clears self::$user_data only if user has been created.
     *
     *
     * user_login, user_pass
     *
     * @see test/testUser.php for sample codes.
     *
     */
    public function create() {

        $id = wp_insert_user(self::$user_data);
        if ( is_wp_error( $id ) ) return $id;
        $user = user( $id );
        $keys = array_keys( self::$user_data );
        $keys_diff = array_diff( $keys, self::$properties );
        foreach ( $keys_diff as $key ) {
            $user->$key = self::$user_data[$key];
        }
        self::$user_data = [];
        return $id;
    }


    /**
     *
     *
     * @return bool|int|WP_Error
     *
     */
    public function update() {
        if ( ! $this->ID ) {
            return false;
        }
        self::$user_data['ID'] = $this->ID;
        $user_id = wp_update_user(self::$user_data);

        $keys = array_keys( self::$user_data );
        $keys_diff = array_diff( $keys, self::$properties );
        foreach ( $keys_diff as $key ) {
            $user->$key = self::$user_data[$key];
        }


        self::$user_data = [];
        return $user_id;
    }




    /**
     *
     * Saves user field or meta into database.
     *
     * ( 워드프레스 사용자 기본 필드와 추가 메타 정보를 저장한다. )
     *
     * @attention WP_User()->__set() does not save value into database. It only saves into memory.
     *
     *
     * @note
     *      - It uses wp_update_user() for updating the WP_User properties.
     *      - It uses update_user_meta() for updating non WP_User properties.
     *
     *      - 이 메쏘드로 워드프레스 사용자 기본 필드가 meta key 로 들어오면, 워드프레스의 기본 정보를 업데이트하고,
     *      - 워드프레스의 기본 사용자 필드가 아니면, user-meta 에 저장을 한다.
     *
     *
     * @note update_user_meta() 는 usermeta 테이블 정보를 업데이트한다. 하지만 user 테이블은 업데이트를 하지 않는다.
     * 따라서 WP_User property 의 경우에는 wp_update_user() 를 통해서 업데이트를 한다.
     *
     * @note wp_update_user() 는 데이터베이스를 업데이트한다. __set() 은 메모리만 업데이트한다. 따라서 이 둘을 동기화 시켜야 한다.
     *
     *
     * @param string $key
     * @param mixed $value
     * @return bool|int
     */

    public function __set( $key, $value )
    {
        if ( !isset( $this->ID ) || empty($this->ID) ) return false;
        if ( in_array( $key, self::$properties ) ) {
            $ID = wp_update_user( [
                'ID' => $this->ID,
                $key => $value
            ] );
            if ( is_wp_error( $ID ) ) return false;
        }
        else {
            $re = update_user_meta( $this->ID, $key, $value );
            if ( $re === false ) return false;
        }

        /**
         * @todo Is it necessary to do parent::__set() for meta?
         */
        parent::__set($key, $value);

        return true;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get( $key ) {
        return self::get( $key );
    }
    public function get( $key ) {
        if ( in_array( $key, self::$properties ) ) return parent::__get($key);
        else return get_user_meta( $this->ID, $key, true);
    }



    /*
        public function get( $key ) {

            return $this->__get( $key );
        }
    */

    /**
     * wrapper of wp_delete_user()
     *
     *
     * @param null $reassign
     *
     * @return bool
     */
    public function delete( $reassign = null ) {
        if ( $this->ID ) {
            return wp_delete_user( $this->ID, $reassign );
        }
        return false;
    }


    /**
     *
     * @todo UNIT-TEST
     */
    public function count() {
        wp_send_json( count_users() );
    }


    /**
     * Register a user with $_GET, $POST input.
     *
     * @todo use $this->create()
     * @todo theme developer may use different registratoin form. Need to provide a way to adapt form variation.
     */
    public function registerSubmit() {
        do_action('begin_registerSubmit');
        if ( ! in('user_login') ) wp_send_json(json_error(-5, 'Input username') );
        if ( ! in('user_pass') ) wp_send_json(json_error(-6, 'Input password') );
        if ( user( in('user_login') )->exists() ) wp_send_json(json_error(-10, 'Username already exists.'));

        if ( ! in('user_email') ) wp_send_json(json_error(-7, 'Input email') );
        if ( user( in('user_email') )->exists() ) wp_send_json(json_error(-20, 'email already exists.'));

        if ( ! in('name') ) wp_send_json(json_error(-8, 'Input name') );
        if ( ! in('mobile') ) wp_send_json(json_error(-9, 'Input mobile number') );

        $id = user()
            ->set('user_login', in('user_login'))
            ->set('user_pass', in('user_pass'))
            ->create();
        /*

        $id = user()->create(
            array(
                'user_login' => in('user_login'),
                'user_pass' => in('user_pass'),
                'user_email' => in('user_email'),
                'nickname' => in('nickname'),
                'name' => in('name'),
                'mobile' => in('mobile'),
                'landline' => in('landline'),
                'address' => in('address'),
                'skype' => in('skype'),
                'kakao' => in('kakao'),
            )
        );
        */
        if ( is_wp_error( $id ) ) wp_send_json( json_error( -10140, 'failed on registratoin' ) ); // $id ;
        else { // Registration is OK



            do_action('end_registerSubmit', $id);

            if ( in('login') == '1' ) { // Set user logged-in
                $credits = array(
                    'user_login'    => in('user_login'),
                    'user_password' => in('user_pass'),
                    'rememberme'    => true
                );
                wp_signon( $credits, false );
            }
            wp_send_json( json_success( $id ) );
        }

    }

    /**
     *
     *
     * @todo use $this->update()
     */
    public function updateSubmit() {
        do_action('begin_updateSubmit');
        if ( ! user()->login() ) wp_send_json(json_error(-4077, 'Login first') );
        if ( ! in('user_email') ) wp_send_json(json_error(-4078, 'Input email') );
        // @note
        $user = user( in('user_email') );
        if ( $user->exists() ) {
            if ( $user->ID != my()->ID ) {
                wp_send_json(json_error(-4079, 'email already exists.'));
            }
        }
        if ( ! in('name') ) wp_send_json(json_error(4070, 'Input name') );
        if ( ! in('mobile') ) wp_send_json(json_error(-4071, 'Input mobile number') );

        my()->user_email = in('user_email');
        my()->nickname = in('nickname');
        my()->name = in('name');
        my()->mobile = in('mobile');
        my()->landline = in('landline');
        my()->address = in('address');
        my()->skype = in('skype');
        my()->kakao = in('kakao');

        do_action( 'end_updateSubmit', my()->ID );

        wp_send_json( json_success() );
    }


    /**
     *
     *
     * @todo unit test
     */
    public function loginSubmit( ) {
        $this->doLogin();
    }

    /**
     * It does user login.
     *
     * It returns boolean if in('response') is empty.
     * It returns json if in('response') is not empty.
     *
     * @attention @update
     *
     *  - in('response') 에 값이 없으면 true 또는 false 만 리턴한다.
     *
     * @param null $user_login
     * @param null $user_pass
     * @param null $remember_me
     * @return bool
     */
    public function doLogin( $user_login=null, $user_pass=null, $remember_me=null) {
        if ( empty($user_login) ) {
            $user_login = in('user_login');
            $user_pass = in('user_pass');
            $remember_me = in('remember_me');
        }
        $credits = array(
            'user_login'    => $user_login,
            'user_password' => $user_pass,
            'rememberme'    => $remember_me
        );
        $re = wp_signon( $credits, false );
        if ( is_wp_error($re) ) {
            if ( in('response') ) {
                $user = user( in('user_login') );
                if ( $user->exists() ) ferror( -40132, "Wrong password" );
                else ferror( -40131, "Wrong username" );
            }
            else
                return false;
        }
        else {
            if ( in('response') == 'ajax' ) wp_send_json_success();
            return true;
        }
        return false;
    }





    public function passwordLostSubmit() {
        include ABSPATH . '/wp-login.php';
        retrieve_password();
    }

    /**
     * Returns user's Unique ID. It is composed with ID and registered date.
     * @return null
     */
    public function uniqid()
    {
        echo $this->getUniqID();
    }
    public function getUniqID() {
        $uid = null;
        if ( $this->login() ) {
            $uid = my()->user_registered;
            //$uid = strtotime( $uid );
            $uid = str_replace(' ', '', $uid);
            $uid = str_replace('-', '', $uid);
            $uid = str_replace(':', '', $uid);

            $uid = my()->ID . my()->user_login . $uid;
        }
        return $uid;
    }


}

/**
 *
 * Returns user object(inherited by WP_User).
 *
 * @see test/userTest.php for more sample codes.
 *
 * @param null $uid
 * @return user
 *
 * @code basic usage
    $user = user( "id_$user_id_count" );
    print_r( $user );
    print_r( $user->user_login );
 * @endcode
 *
 * @code Can create more than 1 user objects and use it at the sametime.
        $user_A = user( $user_id );
        $user_B = user( 1 );
 * @endcode
 *
 * @attention Do not use like below.
 * @code WRONG CODE. ( 아래와 같이 하면 안된다 )
 *      user('abc');
 *      user()->get('nickname');
 * @endcode
 * @code RIGHT WAY TO USE ( 아래와 같이 객체를 사용 해야 한다. )
 *      $user = user('abc');
 *      $user->nickname
 * @endcode
 *
 */
function user( $uid = null ) {
    $user = new user($uid);
    return $user;
}

/**
 *
 *
 * @param null $uid
 * @return user
 * @code
 *
    $user = user( in('user_email') );
    if ( $user->exists() ) {
        if ( $user->ID != my()->ID ) {
            wp_send_json(json_error(-20, 'email already exists.'));
        }
    }
 * @endcode
 */
function my( $uid = null ) {
    return user( $uid );
}

/**
 * @param $attr
 * @return bool|mixed
 *
 *  - false if the user is not logged.
 *  - if the user logged in, WP_User()->__get($field) will be returned.
 */
function login( $attr ) {
    return my()->field($attr);
}



/**
 *
 * @param $code
 * @param string|WP_Error $message - it may be a string or WP_Error.
 *  If WP_Error is passed, then it gets the key and message of the error.
 * @return array
 */
function json_error( $code, $message ) {
    if ( is_wp_error( $message ) ) {
        list ( $k, $v ) = each ($message->errors);
        $message = "$k : $v[0]";
    }
    return array(
        'code' => $code,
        'message' => $message
    );
}

function json_success( $data = array() ) {
    return array(
        'code' => 0,
        'data' => $data
    );
}
