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

    static $entity;
    public static $query_vars = ['forum', 'response', 'slug', 'on_error', 'return_url', 'title', 'content'];


    private $category = null;


    public function __construct()
    {

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
            'forum_create',
            'forum_edit',
            'forum_delete',
            'setting_submit',
            'edit_submit',
            'delete_submit', // @todo implement ajax call.
            'comment_edit_submit', // @todo implement ajax call.
            'comment_delete_submit', // @todo implement ajax call.
            'file_upload', // @todo implement ajax call.
            'file_delete', // @todo implement ajax call.
            'blogger_getUsersBlogs',
            'login',
            'logout',
            'export',
            'import_submit',
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
        if ( in('forum') ) {
            if ( empty($slug) ) {
                if ( forum()->getCategory() ) {
                    $slug = forum()->getCategory()->slug;
                    return "?forum=edit&slug=$slug";
                }
            }
        }
        return null;
    }




    /**
     *
     * Creates / Updates a post
     *
     * @todo add test code. This assigned to viel.
     */
    public function edit_submit() {
        $slug = in('slug'); // forum id ( slug ). It is only available on creating a new post.
        $post_ID = in('post_ID'); // post id. it is only available on updating a post.
        $title = in('title');
        $content = in('content');

        if ( empty( $slug ) && empty( $post_ID ) ) ferror(-50044, 'slug ( category_slug ) or post_ID are not provided');
        if ( ! $title ) ferror(-50045, 'title is not provided');
        if ( ! $content ) ferror(-50046,'content is not provided');

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
            ->set('post_status', 'publish')
            ->set('post_author', wp_get_current_user()->ID);
        if ( $slug ) $post_ID = $post->create();
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




        $this->response( [ 'post_ID' => $post_ID  ] );
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
     * @param array $o
     *      $slug - in('response') == 'list' 인 경우, 게시판 목록을 할 때 사용된다.
     *      $post_ID - in('response') == 'view' 인 경우,글쓰기에서 글을 보여준다.
     *      $comment_ID - in('response') == 'view' 인 경우,코멘트 쓰기에서 코멘트를 보여준다.
     *      $data - 만약 위 3개의 값이 null 이고, data 에 값이 있으면 그 값을 wp_send_json_success() 로 출력한다.
     *
     * @todo add unit test code
     */
    private function response( $o = array() ) {

        $slug = isset($o['slug']) ? $o['slug'] : null;
        $post_ID = isset($o['post_ID']) ? $o['post_ID'] : null;
        $comment_ID = isset($o['comment_ID']) ? $o['comment_ID'] : null;
        $data = isset($o['data']) ? $o['data'] : null;

        $url = in('return_url');
        if ( $url ) {
            wp_redirect( $url );
            exit;
        }
        $res = in('response');
        if ( $res == 'list' ) {
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
            $json = [
                'slug' => $slug,
                'post_ID' => $post_ID,
                'comment_ID' => $comment_ID,
                'html' => $data
            ];
            wp_send_json_success( $json );
        }
        else if ( $data ) {
            wp_send_json_success( $data );
        }
        else if ( empty($slug) && empty($post_ID) && empty($comment_ID) && empty($data) ) {
            echo ('No response coe. [response] must be one of list, view, ajax');
        }
        die();
    }

    public function errorResponse($code, $message) {
        $url = in('return_url_on_error');
        $message = urlencode($message);
        $url .= "&error_code=$code&error_message=$message";
        $this->url_redirect( $url );
        //echo $url;
        die();
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
     * @param $id
     * @param string $type
     *
     * @return bool
     */
    private function checkOwnership( $id, $type='post' )
    {
        if ( ! is_user_logged_in() ) wp_die("Please login");

        if ( current_user_can( 'manage_options' ) ) return true;

        $user = wp_get_current_user();
        $user_id = 0;
        if ( $user->exists() ) {
            if ( $type == 'post' ) {
                $post = get_post( $id );
                if ( empty($post) ) { // if post does not exists, it is a new post writing.
                    wp_die("Post does not exists");
                }
                $user_id = $post->post_author;
            }
            else if ( $type == 'comment' ) {
                $comment = get_comment( $id );
                if ( empty( $comment ) ) wp_die("Comment does not exists");
                $user_id = $comment->user_id;
            }
            else wp_die( 'Wrong Post Type Check');

            if ( $user->ID == $user_id ) {
                // ok
            }
            else {
                wp_die("You are not the owner of the $type");
            }
        }
        else {
            wp_die("User does not exists.");
        }
        return true;
    }




    /**
     * Creates / Updates a comment.
     *
     *
     *
     */
    private function comment_edit_submit( ) {

        //
        if ( $comment_ID = in( 'comment_ID' ) ) { // update
            $this->endIfNotMyComment( $comment_ID );
            $comment = get_comment( $comment_ID );
            $post_ID = $comment->comment_post_ID;
            $re = wp_update_comment([
                'comment_ID' => $comment_ID,
                'comment_content' => in('comment_content')
            ]);

            if ( ! $re ) {
                // error or content has not changed.
            }
        }
        else { // new
            $post_ID = in('post_ID');
            $comment_ID = wp_insert_comment([
                'comment_post_ID' => $post_ID,
                'comment_parent' => in('comment_parent'),
                'user_id' => wp_get_current_user()->ID,
                'comment_content' => in('comment_content'),
                'comment_approved' => 1,
            ]);
            if ( ! $comment_ID ) {
                wp_die("Comment was not created");
            }
        }

        //$this->updateFileWithPost( FORUM_COMMENT_POST_NUMBER  + $comment_ID );

        $url = get_permalink( $post_ID ) . '#comment-' . $comment_ID ;


        //
        $files = in('files');
        if ( $files ) {
            $arr = explode('| |', $files);
            $files = array_filter( $arr );
            comment()->meta( $comment_ID, 'files', $files );
        }

        $this->response( [ 'post_ID' => $post_ID, 'comment_ID' => $comment_ID ] );

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
     * @return $this
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
    public function http_query($param)
    {
        $url = home_url( '?' . http_build_query( $param ) );
        xlog( $url );
        //echo "url: $url<hr>";
        $res = wp_remote_get( $url, ['timeout'=>20,  'httpversion'=>'1.1'] );
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
        echo home_url("?forum=edit&post_ID=$post_ID");
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
     * @param $page
     * @return string
     *
     * @warning before
     */
    public function locateTemplate( $slug, $page )
    {
        if ( empty($page) ) ferror(-50051, "page shouldn't be empty on locateTemplate()");

        $page = "{$page}.php";
        $template_name = 'default';
        if ( $slug ) {
            $category = get_category_by_slug( $slug );
            $term_id = $category->term_id;
            $template_name = $this->meta( $term_id, 'template');
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
     */
    public function setCategory($category_slug)
    {
        $this->category = get_category_by_slug( $category_slug );
        if ( empty($this->category) ) {
            wp_die("Error: Category(slug) - $category_slug - does not exists.");
        }
        $this->loadConfig();
    }

    public function setCategoryByPostID($id)
    {
        $categories = get_the_category( $id );
        $this->category = current( $categories ); // @todo Warning: what if the post has more than 1 categories?
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
        $this->loadMetaCategory();
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
        else {
            $this->response( ['data' => $this->get_list_menu_user( $user_login ) ] );
        }
    }

    public function logout() {
        wp_logout();
        $this->response( ['data'=>$this->get_list_menu_user()] );
    }



    public function list_menu_user() {
        $id = null;
        if ( user()->login() ) $id = my()->user_login;
        echo $this->get_list_menu_user($id);
    }
    public function get_list_menu_user($id = null) {

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
    <button type="button" class="btn btn-primary xforum-login-button">Login</button>
EOH;
        }
    }

    public function list_menu_write() {
        echo <<<EOH
        <button class="btn btn-primary xforum-edit-button">Write</button>
EOH;
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
