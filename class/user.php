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
     *
     * @note 자동으로 현재 사용자의 정보를 설정한다.
     *
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
        if ( $user_login === null ) return is_user_logged_in();
        else return $this->doLogin($user_login, $user_pass, $remember_me);
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
     * @return user
     *
     * @see test/testUser.php for sample codes.
     */
    public function set( $key, $value ) {
        self::$user_data[$key] = $value;
        return $this;
    }

    public function sets( $arr ) {
        self::$user_data = array_merge( self::$user_data, $arr );
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
     * @code
     *         $in = in();
    unset( $in['do'], $in['response'] );
    $user_id = user()->sets( $in )->create();
    $this->response( $user_id );

     * @endcode
     */
    public function create() {

        $user_id = wp_insert_user(self::$user_data);
        if ( is_wp_error( $user_id ) ) return $user_id;
        $user = user( $user_id );
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
     * @attention It only updates with user object that has proper user data.
     *
     *      which means, you must first create user instance with ID or user_login.
     *
     *
     * @attention It does not update user_login. and it does not update email immediately depending on the settings.
     *
     * @see https://docs.google.com/document/d/1hTnA99kcDY13tzxK1lg2khZG83SJUnQzGiNzQ87c1c8/edit#heading=h.so8sn8ytxpzl
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
        if ( is_wp_error( $user_id ) ) return $user_id;
        $user = user( $user_id );
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


    /**
     * Returns if user login exists.
     * @note WP_User()->exists() is expansive.
     *
     *
     */
    public function user_login_exists( $user_login ) {
        global $wpdb;
        $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users WHERE user_login = %s", $user_login ) );
        return $count === 1;
    }



    /**
     * wrapper of wp_delete_user()
     *
     *
     * @param null $reassign
     *
     * @return bool - true if user deleted.
     *
     */
    public function delete( $reassign = null ) {
        if ( $this->ID ) {
            return wp_delete_user( $this->ID, $reassign );
        }
        return false;
    }


    /**
     *
     * @todo Is this method for what?
     */
    public function passwordLostSubmit() {
        include ABSPATH . '/wp-login.php';
        retrieve_password();
    }


    /**
     *
     * Returns a session id of a user.
     *
     * @use when you need a session id.
     *
     *
     * 리턴되는 값은 사용자 정보를 바탕으로 한 md5 를 리턴하는 것으로 해당 사용자의 고유 값이라고 보면 된다.
     *
     *
     *
     * @see test/testUser::test_session_id() for more session id.
     *
     * @note 입력값 $userdata 가 있으면, 해당 사용자의 정보를 바탕으로 session_id 를 만든다.
     *
     *      만약 $userdata 가 지정되지 않았으면, 현재 객체의 값을 바탕으로 session_id 를 만든다.
     *
     * @param array $userdata
     * @return mixed|null|string
     */
    public function get_session_id( $userdata = array() ) {
        $uid = null;
        if ( empty( $userdata ) ) $userdata = $this->to_array();

        if ( isset($userdata['ID']) && $userdata['ID'] ) {
            $uid = $userdata['user_registered'];
            //$uid = strtotime( $uid );
            $uid = str_replace(' ', '', $uid);
            $uid = str_replace('-', '', $uid);
            $uid = str_replace(':', '', $uid);
            $uid = $userdata['ID'] . $userdata['user_login'] . $userdata['user_pass'] . $userdata['user_email'] . $uid;
            $uid = $userdata['ID'] . '_' . md5( $uid );
        }
        return $uid;
    }


    /**
     *
     * @attention this method does not need any user object instance on $this.
     *
     *      - but still you can use the user instance of $this.
     *
     *      ( session_id 가 유효한지 비교하는데, session_id 에는 사용자 번호가 포함이 되어져 있다. 그래서 session_id 만으로 그 값이 유효한지 알 수 있다. )
     *
     * @code both will work.
     *      user()->check_session_id( in('session_id') );
     *      user(1234)->check_session_id( in('session_id') );
     * @endcode
     *
     *
     * @param $session_id - If it is null, then it uses current object's userdata.
     * @return bool|int - false on error. user's ID on success.
     *
     *
     */
    public function check_session_id( $session_id ) {
        if ( $session_id === null ) {
            if ( $this->exists() && $this->ID ) {
                if ($this->get_session_id() == $session_id) return $this->get_user_id_from_session_id($session_id);
                else return false;
            }
            else return false;
        }
        else {
            $user_id = $this->get_user_id_from_session_id( $session_id );
            if ( $user_id ) {
                $user = get_user_by('id', $user_id);
                if ( $user ) {
                    if ( user()->get_session_id( $user->to_array() ) == $session_id ) return $this->get_user_id_from_session_id($session_id);
                }
            }
            return false;
        }
    }

    /**
     * Returns user ID from session_id.
     * @param $session_id
     * @return mixed
     */
    public function get_user_id_from_session_id( $session_id ) {
        $arr = explode( '_', $session_id );
        return $arr[0];
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



    /**
     *
     * Logs in with the HTTP Query Variables.
     *
     * @todo unit test
     */
    public function user_login( ) {
        $this->doLogin();
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
 * @attention it reload user object, use clean_user_cache()
 * @see https://docs.google.com/document/d/1hTnA99kcDY13tzxK1lg2khZG83SJUnQzGiNzQ87c1c8/edit#heading=h.d1ob0kkr5vl0
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
        $message = $message->get_error_message();
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
