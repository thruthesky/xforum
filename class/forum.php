<?php
/**
 * Class forum
 *
 * @file forum.php
 *
 *
 *
 */
class forum {

    use Url;


    const deleted = 'deleted, ...';
    static $entity;
    public static $query_vars = ['forum', 'do', 'session_id', 'response', 'slug', 'on_error', 'return_url', 'title', 'content'];


    private $category = null;


    public function __construct()
    {

        header('Access-Control-Allow-Origin: *');

    }

    public function __get( $field ) {
        if ( $this->category && isset( $this->category->$field ) ) {
            return $this->category->$field;
        }
        else return false;
    }





    /**
     *
     * This method is called by 'http://abc.com/forum/submit' with $_REQUEST['do']
     *
     * Use this function to do action like below that does not display data to web browser.
     *
     *  - ajax call
     *  - submission without display data and redirect to another page.
     *
     *
     * @Note This method can only call a method in 'forum' class.
     *
     * @note it exits. This functions exists the script.
     *
     *  But echoes json string to indicates the result.
     *
     * @Attention All the functions inside this function must echo & exit with wp_send_json_error()/wp_send_json_success()
     *
     * @todo change 'do' to 'forum'.
     * @todo put enough of test code here.
     *
     * @change 2016-06-30 the value of $_REQUEST['forum'] can be anything now. If it is not listed under $forum_do_list, then it just don't do anything. the script does not exit.
     */
    public function submit()
    {
        // @todo remove $_REQUEST['do']
        $what = in('do');
        if ( empty($what) ) $what = in('forum');

        $forum_do_list = [
            'ping',
            'api',
            'forum_create',
            'forum_edit',
            'forum_delete',
            'setting_submit',
            'edit_submit',
            'post_edit_submit',
            'post_delete_submit', // @todo implement ajax call.
            'post_like',
            'comment_like',
            'comment_edit_submit', // @todo implement ajax call.
            'comment_delete_submit', // @todo implement ajax call.
            'file_upload', // @todo implement ajax call.
            'file_delete', // @todo implement ajax call.
            'blogger_getUsersBlogs',
            'user_register',
            'user_update',
            'user_delete',
            'user_check_session_id',
            'user_login_check',
            'login',
            'logout',
            'export',
            'import_submit',
            'ajax_search',
        ];

        if ( in_array( $what, $forum_do_list ) ) {
            $this->$what();     /// @Attention all the function here must end with wp_send_json_success/error()
            exit; // no effect...
        }
        /**
        else {
        $error = "You cannot call the method - '$what' because the method is not listed on 'forum(do) list'.";
        ferror( -4444, $error );
        }
         */


    }


    public function ping() {
        $this->response( ['data' => ['pong'=>time()] ] );
    }
    public function api() {
        $action = in('action');
        if ( empty( $action ) ) ferror(-500500, 'Please, input action. No action provided.');
        api()->$action();
        exit;
    }




    /**
     * Returns very Top (root) XForum category.
     *
     *
     *
     * @todo unit test
     */
    public function getXForumCategory()
    {
        $cat = get_category_by_slug(FORUM_CATEGORY_SLUG);
        /**
         * If there is no 'k-forum' post PostType category, then it do the defaults.
         */
        if ( empty($cat) ) {
            forum()->doDefaults();
            $cat = get_category_by_slug(FORUM_CATEGORY_SLUG);
        }
        return $cat;
    }


    /**
     *
     * Create the default forum.
     *
     * Does the default works ( creating FORUM_CATEGORY_SLUG, etc ) for the forum activation.
     *
     * @Attention This method is only called when the admin or 'multisite admin' accesses 'admin page'.
     *
     * @note it adds routes here. This registers the 'routes' like "/forum/qna"
     *
     *
     * @return $this
     *
     * @todo add unit test.
     */
    public function doDefaults() {

        xlog("doDefaults()");

        $category = get_category_by_slug(FORUM_CATEGORY_SLUG);

        if ( ! $category ) { // category of xforum exists?

            if ( ! function_exists('wp_insert_category') ) require_once(ABSPATH . "/wp-admin/includes/taxonomy.php");

            $catarr = array(
                'cat_name' => __('XForum', 'xforum'),
                'category_description' => __("This is XForum.", 'xforum'),
                'category_nicename' => FORUM_CATEGORY_SLUG,
            );
            $ID = wp_insert_category($catarr, true);
            if ( is_wp_error($ID) ) wp_die( $ID->get_error_message() );

            $catarr = array(
                'cat_name' => __('Welcome', 'xforum'),
                'category_description' => __("This is Welcome forum. It is created on the first run of xforum.", 'xforum'),
                'category_nicename' => 'welcome-' . date('his'), // @note When or for some reason, when k-forum and its category was deleted, it must create a new slug. ( guess this is because the permalink or route is already registered. )
                'category_parent' => $ID,
            );
            $ID = wp_insert_category($catarr, true);
            if (is_wp_error($ID)) wp_die($ID->get_error_message());
        }

        return $this;
    }


    /**
     * Returns the URL of forum submit with the $method.
     *
     * The returned URL will will call the method.
     *
     * @param $method
     * @return string|void
     * @code
     *      <form action="<?php echo forum()->urlForumDo('forum_create')?>" method="post">
     * @encode
     */
    public function urlForumDo($method)
    {
        return home_url("?do=$method");
    }

    /**
     * @param null $slug
     */
    public function urlWrite( $slug = null ) {
        echo $this->getUrlWrite( $slug );
    }


    /**
     * Returns URL of post write.
     *
     * @note 만약 현제 페이지에 해당하는 게시판이 없다면, null 을 리턴한다.
     *
     * @param null $slug
     * @return null|string - if it's not forum page, then it returns.
     */
    public function getUrlWrite( $slug = null )
    {

        if ( $slug ) {
            return home_url("?forum=edit&slug=$slug");
        }
        else if ( in('forum') ) {
            if ( empty($slug) ) {
                if ( forum()->getCategory() ) {
                    $slug = forum()->getCategory()->slug;
                    return home_url("?forum=edit&slug=$slug");
                }
            }
        }
        return null;

    }

    /**
     *
     * Returns the user ID of poster.
     *
     * @note if there is in('session_id'), then it uses that user of the session id.
     *      or else, it uses current login user's ID.
     *
     *
     * @return mixed
     *
     *      - if in('session_id') has wrong value, then it will return false.
     *      - INT will be returned when it has poster's ID.
     *      - 0 will be return otherwise.
     *      - ( Ko ) session_id 가 없거나, 로그인을 하지 않았으면 0 을 리턴한다. ( 이것은 anomymous 로 글을 쓸 수 있게 해 준다. )
     *
     */
    public function get_post_author() {
        if ( in('session_id') ) {
            $ID = user()->check_session_id( in('session_id') );
        }
        else {
            $ID = wp_get_current_user()->ID;
            if ( empty($ID) ) $ID = 0;
        }
        return $ID;
    }

    /**
     *
     * This method posts or edits a post.
     *
     * @note It can redirect to another page based on the 'response'.
     *
     *      And it can display JSON data with the result.
     *
     * @note since this method can response in JSON, it can be used for API.
     *
     *
     *
     *
     * @todo add test code with session_id.
     *
     */
    public function post_edit_submit() {

        $slug = in('slug'); // forum id ( slug ). It is only available on creating a new post.
        $post_ID = in('post_ID'); // post id. it is only available on updating a post.
        $title = in('title');
        $content = in('content');

        if ( empty( $slug ) && empty( $post_ID ) ) ferror(-50044, 'slug ( category_slug ) or post_ID are not provided');
        if ( ! $title ) ferror(-50045, 'title is not provided');
        if ( ! $content ) ferror(-50046,'content is not provided');
        $user_ID = $this->get_post_author();
        if ( empty($user_ID) ) ferror( -50047, "login  first");

        if ( $slug ) { // new post
            //$category = get_category_by_slug( $id );
            $this->setCategory( $slug );
        }
        else { // update post
            forum()->endIfNotMyPost( $post_ID );
            $this->setCategoryByPostID( $post_ID );
        }
        $post = post()
            ->set('post_category', [ forum()->getCategory()->term_id ])
            ->set('post_title', $title)
            ->set('post_content', $content)
            ->set('post_status', 'publish');

        if ( $slug ) { // new post
            $post
                ->set('post_author', $user_ID);
            $post_ID = $post->create();
        }
        else {
            $post_ID = $post
                ->set('ID', $post_ID)
                ->update();
        }
        if ( ! is_integer($post_ID) ) ferror( -50048, "Failed on post_create() : $post_ID");


        // file upload
        preg_match_all("/http.*\/data\/upload\/[^\/]*\/[^\/]\/[^'\"]*/", $content, $ms);
        $files = $ms[0];
        // save uploaded files
        post()->meta( $post_ID, 'files', $files );


        // Save All extra input into post meta.
        post()->saveAllMeta( $post_ID );

        $this->response( [ 'slug' => forum()->getCategory()->slug, 'post_ID' => $post_ID  ] );
    }



    /**
     *
     * @deprecated
     * Creates / Updates a post
     *
     */
    public function edit_submit() {
        $this->post_edit_submit();
    }


    /**
     *
     * Deletes a post.
     *
     * @note if $_REQUEST['return_url'] and $_REQUEST['response'] are empty,
     *
     *      The default, on success, is 'response'='list'
     *
     */
    public function post_delete_submit() {
        $post_ID = in('post_ID');
        forum()->endIfNotMyPost( $post_ID );
        $this->setCategoryByPostID( $post_ID );

        // $re = wp_delete_post( $post_ID );
        $re = post()->delete( $post_ID );
        $data = ['post_ID' => $post_ID];
        if ( $re ) {
            if ( in('response') == 'ajax' ) {
                $data['post'] = get_post( $post_ID );
            }
            else if ( ! in( 'response') ) {
                add_query_var('response', 'list'); // if there in no 'response', then it goes to 'post list' page.
            }
            $this->response( $data );
        }
        else {
            $this->errorResponse( -50314, "Failed to delete post");
        }
    }

    /**
     *
     * @note since it response in JSON, comment delete api does not needed.
     */
    public function comment_delete_submit() {

        $comment_ID = in('comment_ID');
        $comment = get_comment( $comment_ID );
        $post_ID = $comment->comment_post_ID;

        $this->setCategoryByPostID( $post_ID );


        $this->endIfNotMyComment( $comment_ID );


        // $re = wp_delete_comment( $comment_ID );
        $re = comment()->delete( $comment_ID );

        if ( $re ) {
            if ( in('response') == 'ajax' ) {
                $this->response( ['post_ID' => $post_ID, 'comment_ID', 'comment' => comment()->get_comment_with_meta( $comment_ID ) ] );
            }
            else {
                add_query_var('response', 'view');
                $this->response( ['post_ID' => $post_ID ] );
            }
        }
        else {
            $this->errorResponse(-50310, "Failed to delete comment");
        }
    }


    public function setting_submit() {
        $acceptable_setting_vars = ['xforum_url_file_server', 'xforum_admins', 'xforum_file_server_domain'];

        foreach ( $acceptable_setting_vars as $e ) {
            update_option($e, in($e));
        }

        $this->response();
    }


    /**
     * Exports posts and its related data as JSON string.
     *
     * @see README
     */
    public function export( ) {
        $slug = in( 'slug' );
        $category = get_category_by_slug( $slug );
        $term_id = $category->term_id;
        $posts = get_posts([
                'cat' => $term_id,
                'posts_per_page' => -1,
        ]
        );
        $_posts = [];
        foreach ( $posts as $post ) {
            $post->meta = get_post_meta( $post->ID );
            $_posts[] = $post;
        }
        echo json_encode( $_posts );
        exit;
    }


    public function import_submit() {
        $data = in('data');
        $slug = in('slug');
        if ( empty($data) ) $this->errorResponse(-50060, 'No data');
        if ( empty($slug) ) $this->errorResponse(-50061, 'No slug');

        $category = get_category_by_slug($slug);
        $term_id = $category->term_id;
        $posts = json_decode($data, true);
        if ( $posts ) {
            foreach ( $posts as $post ) {
                $post_ID = post()
                    ->set('post_category', [$term_id])
                    ->set('post_title', $post['post_title'])
                    ->set('post_content', $post['post_content'])
                    ->set('post_status', 'publish')
                    ->set('post_author', 1)
                    ->create();
                if ( ! is_integer($post_ID) ) $this->errorResponse(-50062, $post_ID);

            }
        }
        $this->response();
    }

    /**
     *
     * 글을 작성 또는 XForum 에 쿼리를 하고 처리 결과를 받거나 처리 후 이동을 한다면 이 함수를 사용한다.
     *
     * @Attention in('return_url') 이 있으면 해당 페이지로 먼저 이동을 한다.
     * @Attention
     *
     * @Warning Use this only when you need to do something after post write/edit/delete, comment write/edit/dele.
     *          In other case, just use, wp_send_json_success() or wp_send_json_error().
     *
     *
     *
     * @attention 입력 값에 따라서 여러가지 동작이 가능하다.
     *
     * @param mixed $o
     *      $slug - in('response') == 'list' 인 경우, 게시판 목록을 할 때 사용된다.
     *      $post_ID - in('response') == 'view' 인 경우,글쓰기에서 글을 보여준다.
     *      $comment_ID - in('response') == 'view' 인 경우,코멘트 쓰기에서 코멘트를 보여준다.
     *      $data - 만약 위 3개의 값이 null 이고, data 에 값이 있으면 그 값을 wp_send_json_success() 로 출력한다.
     *
     *      If $o is not an array, then it is considered as markup.
     *
     * @todo add unit test code
     */
    private function response( $o = array() ) {

        $slug = isset($o['slug']) ? $o['slug'] : null;
        $post_ID = isset($o['post_ID']) ? $o['post_ID'] : null;
        $comment_ID = isset($o['comment_ID']) ? $o['comment_ID'] : null;
        $data = isset($o['data']) ? $o['data'] : null;
        if ( ! is_array( $o ) ) $data = $o;

        $url = in('return_url');
        if ( $url ) {
            wp_redirect( $url );
            exit;
        }
        $res = in('response');
        if ( $res == 'list' ) {
            if ( empty($slug) ) $slug = $this->getCategory()->slug; // $_REQUEST['slug'] 에 값이 없으면 현재 카테고리로 이동한다.
            $this->url_redirect( $this->getUrlList( $slug ) );
        }
        else if ( $res == 'view' ) {
            if ( $comment_ID ) {
                $this->url_redirect( $this->getUrlViewComment( $comment_ID ) );
            }
            else if ( $post_ID ) {
                $this->url_redirect( $this->getUrlView( $post_ID ) );
            }
            else wp_die("forum()->response() : no post_ID or comment_ID provided.");
        }
        else if ( $res == 'ajax' ) {
            /**
            $json = [];
            if ( $slug ) $json['slug'] = $slug;
            if ( $post_ID ) $json['post_ID'] = $post_ID;
            if ( $comment_ID ) $json['comment_ID'] = $comment_ID;
            if ( $data ) $json['html'] = $data;
            */
            /*
            $json = [
                'slug' => $slug,
                'post_ID' => $post_ID,
                'comment_ID' => $comment_ID,
                'html' => $data
            ];
            */
            //wp_send_json_success( $json );
            wp_send_json_success( $o );
        }
        else if ( $o ) {
            wp_send_json_success( $o );
        }
        else if ( empty($slug) && empty($post_ID) && empty($comment_ID) && empty($data) ) {
            echo ('No response code. [response] must be one of list, view, ajax');
        }
        die();
    }


    /**
     *
     * Exists with Error message / JSON / Redirect.
     *
     * @use when there is error and need to terminate.
     *
     * @param $code
     * @param $message
     */
    public function errorResponse($code, $message) {
        if ( is_wp_error($message) ) $message = $message->get_error_message();
        if ( $url = in('return_url_on_error') ) {
            $message = urlencode($message);
            $url .= "&error_code=$code&error_message=$message";
            $this->url_redirect( $url );
        }
        else if ( in('response') == 'ajax' ) {
            wp_send_json_error( ['code'=>$code, 'message'=>$message]);
        }
        else {
            // wp_send_json_error( $message );
            wp_die($message, "XForum Error");
        }
        exit;
    }




    public function endIfNotLogin() {
        if ( ! is_user_logged_in() ) wp_die("Please login");
    }

    /**
     * @param $post_ID
     */
    public function endIfNotMyPost($post_ID)
    {
        $this->checkOwnership( $post_ID, 'post' );       // check post owner.
    }

    public function isEdit()
    {
        return in('forum') == 'edit' && in('post_ID');
    }

    public function isNew()
    {
        return in('forum') == 'edit' && in('post_ID') == null;
    }

    public function endIfNotMyComment($comment_ID)
    {
        $this->checkOwnership( $comment_ID, 'comment' );       // check comment owner.
    }

    /**
     *
     * Exits if the user has no right to edit/delete on the $post_id
     *
     * @Attention if the logged-in user is admin, then he can do 'edit/delete'
     *
     * @Warning it ends if the user has no ownership of the post/comment.
     *
     *          - It uses $this->errorResponse();
     *
     *            so, follow the rules.
     *
     * @note by calling this method, it is ensured that the post/comment exists.
     *
     * @see README.md for error response.
     *
     *
     * @param $id
     * @param string $type
     *
     * @return bool
     *              - Returns true if admin
     *              - returns true if the user has permission.
     *
     *
     * @todo add test code.
     * $todo add test code with session_id
     *
     */
    private function checkOwnership( $id, $type='post' )
    {


        if ( current_user_can( 'manage_options' ) ) return true;



        /**
         * @since 2016-07-24
         */

        $current_user_id = 0;
        if ( $session_id = in('session_id') ) {
            $current_user_id = user()->check_session_id( $session_id );
            if ( ! $current_user_id ) $this->errorResponse(-50510, "session_id is wrong.");
        }
        else if ( $login = is_user_logged_in() ) {
            $user = wp_get_current_user();
            $current_user_id = $user->ID;
        }
        else {
            $this->errorResponse( -50400, "Please, Login first...");
        }

        /// if ( ! is_user_logged_in() ) $this->errorResponse(-50400, "Please login");





        // Get post/comment user info.
        $user_id = 0;
        if ( $type == 'post' ) {
            $post = get_post( $id );
            if ( empty($post) ) { // if post does not exists, it may be a new post writing.
                $this->errorResponse( -50402, "Post does not exists");
            }
            $user_id = $post->post_author;
        }
        else if ( $type == 'comment' ) {
            $comment = get_comment( $id );
            if ( empty( $comment ) ) $this->errorResponse(-50408, "Comment does not exists");
            $user_id = $comment->user_id;
        }
        else $this->errorResponse(-50409, 'Wrong Post Type Check');




        // compare.
        if ( $current_user_id == $user_id ) {
            // ok
            return true;
        }
        else {
            $this->errorResponse(-50405, "You are not the owner of the $type no. $id");
        }

        return true;
    }

    /**
     * Returns true if the logged in user is admin of the forum.
     *
     * @return bool
     */
    public function admin()
    {
        if ( forum()->getCategory() ) {
            $admins = forum()->getCategory()->config['admins'];
            if ( is_array( $admins ) ) return in_array( user()->user_login, $admins );
        }
        return false;
    }



    /**
     * Creates / Updates a comment.
     *
     * @note since it can retrun the result in JSON, it does not need to be in api class.
     *
     *
     * @note it loads 'init.php' of the template.
     *
     */
    private function comment_edit_submit( ) {

        $comment_ID = in( 'comment_ID' );
        $update = $comment_ID ? true : false;
        
        
        if ( $update ) {
            $comment = get_comment( $comment_ID );
            $post_ID = $comment->comment_post_ID;
        }
        else {
            $post_ID = in('post_ID');
        }
        forum()->setCategoryByPostID($post_ID);





        //
        if ( $update ) { // update
            $this->endIfNotMyComment( $comment_ID );
            $re = wp_update_comment([
                'comment_ID' => $comment_ID,
                'comment_content' => in('comment_content')
            ]);

            if ( ! $re ) {
                // error or content has not changed.
            }
        }
        else { // new
            $user_ID = $this->get_post_author();
            $user = get_user_by( 'id', $user_ID );
            $comment_ID = wp_insert_comment([
                'comment_post_ID' => $post_ID,
                'comment_parent' => in('comment_parent'),
                'comment_author' => $user->user_login,
                'user_id' => $user_ID,
                'comment_content' => in('comment_content'),
                'comment_approved' => 1,
            ]);
            if ( ! $comment_ID ) {
                $this->errorResponse(-50302, "Comment was not created");
            }
        }

        //$this->updateFileWithPost( FORUM_COMMENT_POST_NUMBER  + $comment_ID );

        // $url = get_permalink( $post_ID ) . '#comment-' . $comment_ID ; // this is not used.

        //
        $files = in('files');
        if ( $files ) {
            $arr = explode('| |', $files);
            $files = array_filter( $arr );
            comment()->meta( $comment_ID, 'files', $files );
        }



        // Save All extra input into post meta.
        comment()->saveAllMeta( $comment_ID );


        $o = [ 'post_ID' => $post_ID, 'comment_ID' => $comment_ID ];
        if ( in('response') == 'ajax' ) $o['comment'] = comment()->get_comment_with_meta( $comment_ID );
        $this->response( $o );
    }
    /**
     *
     *
     * Creates / Updates a forum.
     *
     *
     * @input HTTP variables.
     *      if $_REQUEST['category_id'] has value, it is update.
     * @return false on success. otherwise error code.
     *
     *
     * @code An example code how to create a forum ( category )
     *
    $_REQUEST['cat_name'] = 'Test Forum';
    $_REQUEST['category_nicename'] = $slug;
    $_REQUEST['category_parent'] = $parent->term_id;
    $_REQUEST['category_description'] = "This is a category created by unit test";
    $re = $this->forum_create();

     * @endcode
     */
    public function forum_create() {

        if ( ! in('slug') ) ferror(-50020, 'category_nicename(Forum ID) is not provided');
        if ( ! in('category_description') ) wp_send_json_error(['code'=>-50021,'message'=>'category_description is not provided']);
        if ( ! in('cat_name') ) wp_send_json_error(['code'=>-50022,'message'=>'cat_name(Forum Name) is not provided']);

        /**
         * Put category parent ID
         */
        if ( ! $category_parent = in('category_parent') ) {
            $category_parent = get_category_by_slug( FORUM_CATEGORY_SLUG )->term_id;
        }
        $catarr = array(
            'cat_name' =>in('cat_name'),
            'category_description' => in('category_description'),
            'category_nicename' => in('slug'),
            'category_parent' => $category_parent
        );

        $term_ID = $this->createOrUpdate( $catarr );

        if ( is_wp_error( $term_ID ) ) {
            wp_send_json_error( ['code'=>-4100, 'message'=>$term_ID->get_error_message()] );
        }

        $this->updateMeta( $term_ID );
        $this->response();
    }

    public function forum_edit() {
        if ( ! in('term_id') ) ferror(-50014, 'term_id is not provided');
        if ( ! in('category_parent') ) ferror(-50015, 'category_parent is not provided');
        if ( ! in('slug') ) ferror(-50016,'slug is not provided');
        if ( ! in('category_description') ) ferror(-50017, 'category_description is not provided');
        if ( ! in('cat_name') ) ferror(-50018, 'cat_name(Forum Name) is not provided');
        $catarr = array(
            'cat_ID' => in('term_id'),
            'cat_name' =>in('cat_name'),
            'category_description' => in('category_description'),
            'category_nicename' => in('slug'),
            'category_parent' => in('category_parent')
        );

        $term_ID = $this->createOrUpdate( $catarr );
        if ( is_wp_error( $term_ID ) ) {
            wp_send_json_error( ['code'=>-4101, 'message'=>$term_ID->get_error_message()] );
        }
        $this->updateMeta( $term_ID );
        $this->response();
    }




    /**
     *
     * @param $catarr
     *
     * array(
     *      'cate_ID'       => category id. if it exists, it is going to update the forum.
     *      'cat_name' => $data['name'],
     * 'category_description' => $data['desc'],
     *      'category_nicename' => $data['id'], // it is slug.
     * 'category_parent' => $data['parent'],
     * );
     *
     *
     * @return int|object The ID number of the new or updated Category on success. Zero or a WP_Error on failure,
     *                    depending on param $wp_error.

     *
     *
     */
    private function createOrUpdate( $catarr ) {
        if ( ! function_exists('wp_insert_category') ) require_once (ABSPATH . "/wp-admin/includes/taxonomy.php");
        return wp_insert_category( $catarr, true );
    }



    public function create() {
        self::$entity = [];
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return forum
     */
    public function set( $key, $value ) {
        self::$entity[ $key ] = $value;
        return $this;
    }


    /**
     * @return int|object - returns term_id (INT) on success.
     *          return string on error.
     *
     *
     * @todo remove save(). do something like "forum()->set(...)->create()"
     *
     */
    public function save() {
        $term_ID = $this->createOrUpdate( self::$entity );
        if ( is_wp_error( $term_ID ) ) return $term_ID->get_error_message();
        return $term_ID;
    }


    /**
     * @deprecated
     * @param $post_ID
     */
    public function count_comments( $post_ID ) {
        post()->count_comments( $post_ID );
    }

    /**
     * @deprecated
     * @param $post_ID
     * @return int
     */
    public function get_count_comments( $post_ID ) {
        return post()->get_count_comments( $post_ID );
    }




    /**
     *
     *
     *
     * @Attention When a category is deleted, it is not actually deleted from database.
     *              Its 'category_parent' is set to 0, which means it does not belongs to the xforum any more.
     *              Since this, admin can manually remove the category from post category.
     *              This is just for a safety of prevention that one may accidentally delete a forum.
     *
     * @code Examples
     *      http://work.org/wordpress-4.5.3/?do=forum_delete&cat_ID=86&return_url=http%3A%2F%2Fwork.org%2Fwordpress-4.5.3%2Fwp-admin%2Fadmin.php%3Fpage%3Dxforum%252Ftemplate%252Fadmin.php
     *      <a href="<?php echo forum()->urlForumDo('forum_delete')?>&cat_ID=<?php echo $category->term_id?>&return_url=<?php echo urlencode(forum()->adminURL())?>">Delete</a>
     * @endcode
     */
    public function forum_delete() {

        $term_id = in('term_id');
        if ( empty($term_id) ) ferror( -50043, 'term_id is not provided.');

        if ( ! function_exists('wp_insert_category') ) require_once (ABSPATH . "/wp-admin/includes/taxonomy.php");
        //wp_delete_category();
        if ( $term_id = in('term_id') ) {
            $category = get_category( $term_id );
            if ( $category ) {
                wp_insert_category([
                    'cat_ID' => $category->term_id,
                    'category_nicename' => 'deleted-from-xforum-' . $category->slug,
                    'cat_name' => "Deleted : " . $category->name,
                    'category_parent' => 0,
                ]);
                if ( ! function_exists('wp_redirect') ) require_once (ABSPATH . "/wp-includes/pluggable.php");
                $this->response();
            }
            else {
                wp_send_json_error(['code'=>-50011, 'message'=>'term_id is ok. But category does not exists.']);
            }
        }
        else {
            wp_send_json_error(['code'=>-50010, 'message'=>'term_id is empty']);
        }
    }

    /**
     *
     * Deletes a category.
     *
     * @param $cat_ID
     * @return bool|string - false on success, otherwise error message.
     */
    public function delete($cat_ID)
    {
        if ( empty($cat_ID) ) return 'cat_ID is empty';
        if ( ! is_numeric($cat_ID) ) return 'cat_ID is not a numeric';
        $category = get_category( $cat_ID );
        if ( empty($category) ) return 'category does not exists by that cat_ID';
        $term_ID = wp_insert_category([
            'cat_ID' => $category->term_id,
            'category_nicename' => 'deleted-from-xforum-' . $category->slug,
            'cat_name' => "Deleted : " . $category->name,
            'category_parent' => 0,
        ]);
        if ( is_wp_error( $term_ID ) ) return $term_ID->get_error_message();
        return false;
    }




    /**
     *
     * It returns a string or an array of json object.
     *
     * @note if the web server returns JSON code, then it returns in an array or it returns in a string.
     *
     * @param $param
     * @return array|mixed|object
     *
     */
    public function http_query($param, $echo_url=false)
    {
        $url = home_url( '?' . http_build_query( $param ) );
        xlog( $url );
        if ( $echo_url ) {
            echo "<div>url: <a href='$url' target='_blank'>$url</a></div>";
        }
        $res = wp_remote_get( $url, ['timeout'=>5] );
        $body = wp_remote_retrieve_body( $res );
        $re = json_decode( $body, true);
        if ( empty($re) && !empty($body) ) return $body;
        return $re;
    }


    /**
     *
     * Sets / Updates / Gets the value of meta tag of the category ( forum )
     *
     * @attention if $value is empty, then it return the value. If you want to delete the meta data, just call delete_term_meta().
     *
     * @update 2016-07-07 If $key is null, then term_ID is the key and the term_ID is comes from the current forum config.
     *
     * @param $term_ID - term_ID or key
     * @param $key - if it's null, then $term_ID is $key, and $term_ID is given from current fourm config.
     * @param $value
     *
     *
     * @return mixed|null - return null on setting/updating. return value of string on getting.
     *
     * @code to get meta data
     *      forum()->meta('template');
     *      forum()->meta($cat_ID, 'template');
     * @endcode
     * @code to save meta data
     *         forum()->meta($cat_ID, 'template', $template_name);
     * @endcode
     *
     * @todo add test on meta('name');
     */
    public function meta($term_ID, $key=null, $value = null)
    {
        if ( $key === null ) {
            if ( $this->getCategory() ) {
                $key = $term_ID;
                $term_ID = $this->getCategory()->term_id;
            }
            else return null;
        }
        if ( $value !== null ) {
            delete_term_meta( $term_ID, $key );
            add_term_meta( $term_ID, $key, $value, true );
            return null;
        }
        else {
            return get_term_meta($term_ID, $key, true);
        }
    }


    /**
     *
     *
     *
     * It redirect current page to the page of $_REQUEST['url_redirect'].
     *
     * @note it does not do anything if $_REQUEST['url_redirect'] is empty.
     *
     * @param $url
     * @return bool - true if it redirected.
     *
     */
    private function url_redirect($url)
    {
        if ( empty( $url ) ) return false;
        else {
            if ( ! function_exists('wp_redirect') ) require_once (ABSPATH . "/wp-includes/pluggable.php");
            wp_redirect( $url );
            return true;
        }
    }


    /**
     * @deprecated - use urlAdminPage()
     *
     */
    public function adminURL()
    {
        return $this->urlAdminPage();
    }

    /**
     * @deprecated use getUrlAdmin()
     * @return string|void
     */
    public function urlAdminPage() {
        return $this->getUrlAdmin();
    }
    public function getUrlAdmin() {
        return home_url('wp-admin/admin.php?page=xforum%2Ftemplate%2Fadmin.php');
    }
    public function urlAdmin() {
        echo $this->getUrlAdmin();
    }

    public function urlAdminImport() {
        echo home_url("wp-admin/admin.php?page=xforum%2Ftemplate%2Fimport.php");
    }

    public function urlAdminSetting() {
        echo home_url('/wp-admin/admin.php?page=xforum%2Ftemplate%2Fsetting.php');
    }

    public function urlForumCreate()
    {
        return home_url('wp-admin/admin.php?page=xforum%2Ftemplate%2Fadmin.php&amp;template=adminForumCreate');
    }



    /**
     * @deprecated - use urlList
     */
    public function listURL($slug)
    {
        $this->urlForumList($slug);
    }

    /**
     *
     * @deprecated use urlList()
     * Returns the URL of the forum list page.
     *
     *
     *
     * @param null $slug - if it's  null, it uses $this->category information.
     * @return string|void
     */
    public function urlForumList($slug = null)
    {
        $this->urlList( $slug );
    }

    public function urlList( $slug = null )
    {
        echo $this->getUrlList( $slug );
    }
    public function getUrlList( $slug = null ) {
        if ( empty($slug) ) $slug = $this->getSlug();
        return home_url("?forum=list&slug=$slug");
    }

    public function urlView( $post_ID ) {
        echo $this->getUrlView( $post_ID );
    }
    public function getUrlView( $post_ID )
    {
        return get_permalink( $post_ID );
    }

    public function urlViewComment( $comment_ID ) {
        echo $this->getUrlViewComment( $comment_ID );
    }
    public function getUrlViewComment($comment_ID)
    {
        return get_comment_link( $comment_ID );
    }



    public function urlExport( $slug = null ) {
        if ( empty($slug) ) $slug = $this->getSlug();
        echo home_url("?forum=export&slug=$slug");
    }


    /**
     * @deprecated use forum()->slug or $this->slug
     * Returns the slug of the current forum.
     * @return null
     */
    public function getSlug() {
        if ( ! $slug = in('slug') ) {
            if ( $this->getCategory() ) $slug = $this->getCategory()->slug;
        }
        return $slug;
    }
    /**
     * @deprecated use urlEdit
     * @param $ID
     * @return string|void
     *
     */
    public function urlPostEdit( $ID )
    {
        echo $this->urlEdit( $ID );
    }

    /**
     *
     * Echoes URL of post edit.
     *
     * @param $post_ID
     * @return string|void
     */
    public function urlEdit( $post_ID )
    {
        echo $this->getUrlEdit( $post_ID );
    }
    public function getUrlEdit( $post_ID ) {
        return home_url("?forum=edit&post_ID=$post_ID");
    }

    /**
     * @param $post_ID
     */
    public function urlDelete( $post_ID ) {
        echo $this->getUrlDelete( $post_ID );
    }

    public function getUrlDelete( $post_ID ) {
        return home_url("?forum=post_delete_submit&post_ID=$post_ID");
    }


    public function urlAdminForumEdit($term_id)
    {
        return $this->urlAdminPage() . "&template=adminForumEdit&term_id=$term_id";
    }

    public function categories()
    {
        $forum_category = forum()->getXForumCategory();
        return lib()->get_categories_with_depth( $forum_category->term_id );
    }


    /**
     * @param $slug
     * @param $page - template name.
     *      - If $page is empty, then $slug is used for page and current forum's slug will be used as slug.
     * @return string
     *
     * @warning before
     *
     * @code
     *      return forum()->locateTemplate( forum()->getCategory()->slug, 'comment');
     *      <?php include forum()->locateTemplate( forum()->slug, 'pagination') ?>
     * @endcode
     *
     * @desc (Ko) 적당한 $slug 값이 없으면, 그냥 아무 값이나 지정하면 default 폴더에서 찾는다.
     *
     *          - 그런데, 현재 forum()->getCategory() 에 값이 없으면서, this->locateTemplate( 'abc' ) 와 같이 하면, -50052 에러가 난다.
     *
     */
    public function locateTemplate( $slug, $page = null )
    {
        if ( empty($slug) ) ferror(-50051, "slug or page shouldn't be empty on locateTemplate()");

        if ( empty( $page ) ) {
            $page = $slug;
            $cat = $this->getCategory();
            if ( empty( $cat) ) ferror( -50052, "Current forum is not found. Slug is not found. If slug is not passed to xforum()->locatTemplate(), then current forum must be set.");
            $slug = $cat->slug;
        }

        $page = "{$page}.php";
        $template_name = 'default';
        if ( $slug ) {
            $category = get_category_by_slug( $slug );
            if ( $category ) {
                $term_id = $category->term_id;
                $template_name = $this->meta( $term_id, 'template');
            }
        }
        if ( empty( $template_name ) ) $template_name = 'default';

        if ($template_name) {
            $template_in_theme = get_stylesheet_directory() ."/template-forum/$template_name/$page";
            if ( file_exists($template_in_theme) ) return $template_in_theme;
            else {
                $template_in_plugin = DIR_XFORUM . "template/$template_name/$page";
                if ( file_exists($template_in_plugin) ) return $template_in_plugin;
                else {
                    $template_in_theme = get_stylesheet_directory() ."/template-forum/default/$page";
                    if ( file_exists($template_in_theme) ) return $template_in_theme;
                    else {
                        return DIR_XFORUM . "template/default/$page";
                    }
                }
            }
        }
        return null;
    }

    /**
     *
     * Sets category of the forum.
     *
     * The forum(category) information of the list/edit/view page.
     *
     * @attention Since each time when you call get_category_by_slug(), it access database. WordPress does not cache it in memory.
     *      This is called on add_filter('template_include', ...);
     *
     * @param $category_slug - category slug.
     *
     *
     * @Attention 2016-07-13 It just returns if $this->category is not empty. which MEANS, you can call this function( or $this->setCategory() ) only once.
     *
     */
    public function setCategory($category_slug)
    {
        if ( $this->category ) return;
        $this->category = get_category_by_slug( $category_slug );
        if ( empty($this->category) ) {
            wp_die("Error: Category(slug) - $category_slug - does not exists.");
        }
        $this->loadConfig();
    }


    /**
     *
     * Sets forum category information to $this->category.
     *
     * @Attention 2016-07-13 It just returns if $this->category is not empty. which MEANS, you can call this function( or $this->setCategory() ) only once.
     *
     * @note it does what setCategory() does.
     *
     * @param $id - POST ID
     *
     *
     *
     */
    public function setCategoryByPostID($id)
    {
        if ( $this->category ) return;
        $categories = get_the_category( $id );
        $this->category = current( $categories ); // @todo Warning: what if the post has more than 1 categories?
        if ( empty($this->category) ) {
            wp_die("Error: Category(slug) - does not exists by that post id: $id");
        }
        $this->loadConfig();
    }



    /**
     * Returns the forum (category) info of the forum page.
     * @note Use this method to get forum category information on list/edit/view page.
     *      - This method is only available on a forum( when forum id - $_REQUEST['i'd] is given )
     *
     * @return null
     */
    public function getCategory()
    {
        return $this->category;
    }


    private function updateMeta( $term_ID  )
    {
        $this->meta( $term_ID, 'admins', in('admins') );
        $this->meta( $term_ID, 'members', in('members') );
        $this->meta( $term_ID, 'template', in('template') );
        $this->meta( $term_ID, 'category', in('category') );
        $this->meta( $term_ID, 'posts_per_page', in('posts_per_page') );
    }

    /**
     *
     * Returns the configuration of the forum.
     *
     * @attention This uses $this->getCategory() which means, you can only use this method when $this->getCategory() is available.
     *
     * @param $key
     * @return bool|mixed|null
     * @code
     *      di ( forum()->getMeta('category') );
     *      forum()->getMeta('category', 'ini');
     * @endcode
     */
    public function getMeta( $key )
    {
        return $this->getCategory()->config[ $key ];
    }

    /**
     *
     * @deprecated
     *
     * Returns the category information of the forum configuration.
     * @see README.md for detail
     * @return array
     *
     */
    public function getMetaCategory()
    {
        return forum()->getMeta('category', 'ini');
    }


    /**
     * Loads all meta configuration of the forum into $this->category->config
     * @note it is called on setCategory()
     */
    private function loadConfig()
    {
        $this->category->config = [];
        $this->loadMeta('admins', 'array');
        $this->loadMeta('members', 'array');
        $this->loadMeta('template');
        $this->loadMeta('category', 'array');
        //$this->loadMetaCategory();
        $this->loadInitScript();
    }

    /**
     *
     * It loads 'init.php' on the template ( as in the template hierarchy )
     *
     * @Warning be careful that is called(loaded) only once.
     */
    public function loadInitScript() {
        //di(forum()->getCategory());
        include_once forum()->locateTemplate('init');
        //exit;
    }


    /**
     * It saves the meta value of the forum config and returns it.
     * @param $key
     * @param string $format
     * @return array|mixed|null
     */
    private function loadMeta( $key, $format='string' ) {
        $c = $this->getCategory();
        if ( $c ) $value = $this->meta( $c->term_id, $key );
        else $value = null;
        if ( $value ) {
            if ( $format == 'array' ) {
                $value = explode(',', $value);
            }
        }
        $this->category->config[$key] = $value;
        return $value;
    }

    /**
     *
     * @deprecated
     *
     */
    private function loadMetaCategory()
    {
        $value = [];
        $category = $this->loadMeta( 'category' );
        $ini = parse_ini_string( $category, true );
        if ( $ini ) {
            foreach ( $ini as $k => $v ) {
                $v['admins'] = explode( ',', $v['admins']);
                $v['members'] = explode( ',', $v['members']);
                $value[$k] = $v;
            }
        }
        $this->category->config['category'] = $value;
    }

    /**
     *
     * Returns true if the post of post_ID belongs to XForum.
     * If the post is a post of PostType but it is not belongs to xforum, then it returns false.
     *
     * @param $post_ID
     * @return bool
     */
    public function isPost( $post_ID )
    {
        $categories = get_the_category( $post_ID );

        if ( $categories ) {
            $category = current( $categories ); // @todo Warning: what if the post has more than 1 categories?
            $category_id = $category->term_id; // get the slug of the post
            xlog("category_id: $category_id");
            $ex = explode('/', get_category_parents($category_id, false, '/', true)); // get the root slug of the post
            xlog("category slug of the category id: $ex[0]");
            if ( $ex[0] == FORUM_CATEGORY_SLUG ) { // is it a post under XForum?
                return true;
            }
        }
        return false;
    }


    /**
     * Create a user.
     *
     * @see test/testUser::remoteCRUD() for more example.
     *
     * @attention it returns user session id on json.
     */
    public function user_register() {


        if ( ! in('user_login') ) ferror( -500250, "Input user login ID");
        if ( user()->user_login_exists( in('user_login') ) ) ferror( -500251, "User login ID exists.");
        if ( ! in('user_pass') ) ferror( -500252, "Input password");
        if ( strlen(in('user_login')) < 3 ) ferror( -500253, "User login ID is too short.");
        if ( strlen(in('user_pass')) < 4 ) ferror( -500254, "Password is too short.");


        $in = in();
        unset( $in['do'], $in['response'] );



        $user_id = @user()->sets( $in )->create();
        if ( is_wp_error( $user_id ) ) {
            wp_send_json_error( $user_id->get_error_message() );
        }
        else {
            $this->response( [
                'session_id' => user( $user_id )->get_session_id(),
                'user_login' => in('user_login'),
            ] );
        }

    }

    public function user_update() {

        if ( ! in('ID') ) ferror( -500329, "User ID is empty");
        $user = user( in('ID') );
        if ( $user->get_session_id() != in('session_id') ) ferror(-500330, "Session ID does not match.");


        $in = in();
        unset( $in['do'], $in['response'], $in['session_id'] );
        $user_id = @$user->sets( $in )->update();
        if ( is_wp_error( $user_id ) ) {
            wp_send_json( $user_id->get_error_message() );
        }
        else $this->response( $user_id );
    }

    /**
     *
     * @see test/testUser::remoteCRUD for more sample codes.
     *
     */
    public function user_delete() {

        if ( ! in('ID') ) ferror( -50049, "User ID is empty");
        $user = user( in('ID') );


        //di( $user->get_session_id() );
        //print_r($user);
        //di( in('session_id') );
        //print_r( in() );

        if ( $user->get_session_id() != in('session_id') ) ferror(-500348, "Session ID does not match for user delete");

        if ( $user->delete() ) {
            wp_send_json_success();
        }
        else {
            wp_send_json_error();
        }

    }


    /**
     * Check user session id and response through json string.
     */
    public function user_check_session_id() {
        if ( user()->check_session_id( in('session_id') ) ) wp_send_json_success();
        else wp_send_json_error();
    }

    /**
     * @attention if the user has logged in, it only returns session_id through JSON.
     *
     *          - it does not actually login
     *
     *
     *
     * @see test/testUser::test_remote_login() for more sample code for remote login & session_id test.
     *
     */
    public function user_login_check() {
        if ( ! in('user_login') ) ferror( -500201, "User login is empty");
        if ( ! in('user_pass') ) ferror( -500202, "User pass is empty");
        $user_login = in('user_login');
        $user = user( $user_login );
        if ( ! $user->exists() ) ferror( -500503, "Invalid username. User - $user_login - does not exist");
        if ( ! wp_check_password( in('user_pass'), $user->user_pass, $user->ID ) ) {
            ferror( -500504, "The password you entered for the username - $user_login is incorrect.");
        }
        wp_send_json_success(
            [
                'session_id' => $user->get_session_id(),
                'user_login' => $user->user_login,
            ]
        );

    }

    /**
     *
     *
     * @attention this is only for web-login. Use user_login_check() for mobile login check.
     *
     *
     *
     * if in('response') == 'ajax', then it returns session_id and session_password.
     *
     * @todo customize the return value.
     *
     *
     */
    public function login() {

        $user_login = trim(in('id'));
        $user_pass = in('password');
        $remember_me = 1;

        $credits = array(
            'user_login'    => $user_login,
            'user_password' => $user_pass,
            'rememberme'    => $remember_me
        );

        $re = wp_signon( $credits, false );

        if ( is_wp_error($re) ) {
            $user = user( $user_login );
            if ( $user->exists() ) ferror( -40132, "Wrong password" );
            else ferror( -40131, "Wrong username" );
        }
        else if ( in('response') == 'ajax' ) {
            // $this->response( user($user_login)->session() ); // 여기서 부터..
        }
        else {
            $this->response( ['data' => $this->get_button_user( $user_login ) ] );
        }

    }


    public function logout() {
        wp_logout();
        $this->response( ['data'=>$this->get_button_user()] );
    }


    /**
     * @deprecated use button_list_menu_user()
     */
    public function list_menu_user() {
        $this->button_user();
    }

    public function button_user() {
        $id = null;
        if ( user()->login() ) $id = my()->user_login;
        echo $this->get_button_user($id);
    }


    /**
     * @deprecated Don't do front-end coding here.
     *
     * @param null $id
     * @return string
     *
     */
    public function get_button_user($id = null) {

        if ( $id ) {
            return <<<EOH
<div class="btn-group xforum-profile">
  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    $id Profile
  </button>
  <div class="dropdown-menu">
    <h6 class="dropdown-header">$id Profile menu</h6>
    <a class="dropdown-item" href="#">Update profile</a>
    <a class="dropdown-item" href="#">List my posts</a>
    <a class="dropdown-item" href="#">View comments of my posts</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item xforum-logout-button" href="#">Logout</a>
  </div>
</div>
EOH;
        }
        else {
            return <<<EOH
    <button type="button" class="btn btn-secondary xforum-login-button">Login</button>
EOH;
        }
    }


    /**
     *
     * @param array $o
     *
     * @code
     *      forum()->button_edit(['text'=>'Create Dependent', 'query'=>"parent=".get_the_ID()])
     * @endcode
     *
     */
    public function button_edit( array $o = [] ) {
        $defaults = [
            'text' => 'EDIT',
            'query' => '',
        ];
        $o = array_merge( $defaults, $o );

        $url = forum()->getUrlEdit( get_the_ID() );
        echo <<<EOH
<a class="btn btn-secondary" href="$url&$o[query]">$o[text]</a>
EOH;
    }

    public function button_like( array $o = [] ) {
        $defaults = [
            'text' => 'LIKE',
            'no' => '',
        ];
        $o = array_merge( $defaults, $o );
        echo <<<EOH
<button class="like btn btn-secondary" type="button">$o[text]<span class="no">$o[no]</span></button>
EOH;
    }



    public function button_write() {
        $this->button_new();
    }

    /**
     * @param array $o
     *
     * @attention it has write link on BUTTON attribute 'href'.
     *      It does not move (redirect) the page to write page since it does not have A tag.
     *      So you need to provide javascript to move it.
     *
     * @see forum.js for a clear example.
     *
     * @code
     *      $this->button_new();
     *      <?php forum()->button_new(['text'=>'Create Dependent', 'query'=>"parent=".get_the_ID()])?>
     * @endcode
     *
     *
     */
    public function button_new( array $o = [] )
    {

        $defaults = [
            'text' => 'Write',
            'query' => '',
            'slug' => forum()->getCategory()->slug,
        ];
        $o = array_merge( $defaults, $o );
        $href = forum()->getUrlWrite( $o['slug'] );
        echo <<<EOH
        <button class="btn btn-secondary xforum-edit-button" href="$href&$o[query]">$o[text]</button>
EOH;
    }

    public function button_delete() {
        $url = forum()->getUrlDelete( get_the_ID() );
        echo <<<EOH
<a class="btn btn-secondary" href="$url" onclick="return confirm('Are you sure you want to Delete this post?');">DELETE</a>
EOH;

    }

    public function button_list( array $o = [] ) {
        $defaults = [
            'text' => 'LIST',
        ];
        $o = array_merge( $defaults, $o );
        $url = forum()->getUrlList();
        echo <<<EOH
        <a class="btn btn-secondary" href="$url">$o[text]</a>
EOH;
    }




    function ajax_search() {

        $q = new WP_Query(
            [
                's' => in('keyword'),
                'posts_per_page' => 15,
            ]
        );

        $html = null;
        if ( $q->have_posts() ) {
            $html .= "<div class='no-of-posts'>No. of search result : " . $q->found_posts . "</div>";
            while ( $q->have_posts() ) {
                post()->setup( $q );
                $m = get_the_title();
                $url = get_the_permalink();
                $html .= <<<EOH
<div class="post"><a href="$url">$m</a></div>
EOH;
            }
        }
        $html = "<div class='ajax-search'>$html</div>";
        $this->response( $html );
    }




    public function post_like() {
        if ( $this->is_user_logged_in() ) {
            $like = get_post_meta( in('post_ID'), 'like', true);
            $like ++;
            update_post_meta( in('post_ID'), 'like', $like);
            wp_send_json_success( ['post_ID' => in('post_ID'), 'like' => $like ] );
        }
        else {
            wp_send_json_error( json_error( -100400, 'Please, login first') );
        }
    }

    public function comment_like() {
        if ( $this->is_user_logged_in() ) {
            $like = get_comment_meta( in('comment_ID'), 'like', true);
            $like ++;
            update_comment_meta( in('comment_ID'), 'like', $like);
            wp_send_json_success( ['comment_ID' => in('comment_ID'), 'like' => $like ] );
        }
        else {
            wp_send_json_error( json_error( -100400, 'Please, login first') );
        }
    }


    /**
     *
     * Returns true if the has logged in.
     *
     * @return bool
     *
     */
    public function is_user_logged_in() {
        if ( $session_id = in('session_id') ) {
            $current_user_id = user()->check_session_id( $session_id );
            if ( $current_user_id ) return true;
            else return false;
        }
        else if ( is_user_logged_in() ) return true;
        else return false;
    }


}


$__forum = null;
/**
 *
 *
 * @note This function caches on memory. so no matter how many times you call this function, it does not produce burden on Process.
 * @return forum
 *
 */
function forum() {
    global $__forum;
    static $__count_forum = 0;
    $__count_forum ++;
    if ( isset($__forum) ) return $__forum;
    else {
        if ( $__count_forum > 1 ) {
            xlog('Fatal error: forum object instanticated more than twice.');
            return null;
        }
        else {
            xlog('Creates forum instance. This should be done only once.');
            $__forum = new forum();
            return $__forum;
        }
    }
}
