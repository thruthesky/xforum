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

    public function __construct()
    {

    }


    /**
     * Returns forum category.
     *
     *
     *
     * @todo unit test
     */
    public function getForumCategory()
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
     *      <form action="<?php echo forum()->doURL('forum_create')?>" method="post">
     * @encode
     */
    public function doURL($method)
    {
        return home_url("?do=$method");
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
     */
    public function submit()
    {
        $what = in('do');
        if ( empty($what) ) {
            $error = "<h2>method name is empty</h2>";
            wp_send_json_error([ 'code' => -4443, 'message' => $error ]);
        }
        else {
            $do_list = [
                'forum_create',
                'forum_edit',
                'forum_delete',
                'post_create',
                'post_delete',
                'comment_create',
                'comment_delete',
                'file_upload',
                'file_delete',
                'blogger_getUsersBlogs',
                'login',
            ];
            if ( in_array( $what, $do_list ) ) {
                $this->$what();     /// @Attention all the function here must end with wp_send_json_success/error()
            }
            else {
                $error = "You cannot call the method - '$what' because the method is not listed on 'do-list'.";
                ferror( -4444, $error );
            }
        }
        exit; // no effect...
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

        if ( ! in('category_nicename') ) ferror(-50020, 'category_nicename(Forum ID) is not provided');
        if ( ! in('category_description') ) wp_send_json_error(['code'=>-50021,'message'=>'category_description is not provided']);
        if ( ! in('cat_name') ) wp_send_json_error(['code'=>-50022,'message'=>'cat_name(Forum Name) is not provided']);
        $catarr = array(
            'cat_name' =>in('cat_name'),
            'category_description' => in('category_description'),
            'category_nicename' => in('category_nicename'),
            'category_parent' => get_category_by_slug( FORUM_CATEGORY_SLUG )->term_id
        );

        $term_ID = $this->createOrUpdate( $catarr );

        if ( is_wp_error( $term_ID ) ) {
            wp_send_json_error( ['code'=>-4100, 'message'=>$term_ID->get_error_message()] );
        }

        $this->meta( $term_ID, 'template', in('template') );
        $this->url_redirect();

        wp_send_json_success();
    }

    public function forum_edit() {
        if ( ! in('cat_ID') ) ferror(-50014, 'cat_ID is not provided');
        if ( ! in('category_parent') ) ferror(-50015, 'category_parent is not provided');
        if ( ! in('category_nicename') ) ferror(-50016,'category_nicename is not provided');
        if ( ! in('category_description') ) ferror(-50017, 'category_description is not provided');
        if ( ! in('cat_name') ) ferror(-50018, 'cat_name(Forum Name) is not provided');
        $catarr = array(
            'cat_ID' => in('cat_ID'),
            'cat_name' =>in('cat_name'),
            'category_description' => in('category_description'),
            'category_nicename' => in('category_nicename'),
            'category_parent' => in('category_parent')
        );

        $term_ID = $this->createOrUpdate( $catarr );
        if ( is_wp_error( $term_ID ) ) {
            wp_send_json_error( ['code'=>-4101, 'message'=>$term_ID->get_error_message()] );
        }
        $this->meta($term_ID, 'template', in('template'));
        $this->url_redirect();
        wp_send_json_success();
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
     * @return int|object - returns INT on success.
     */
    public function save() {
        $term_ID = $this->createOrUpdate( self::$entity );
        if ( is_wp_error( $term_ID ) ) return $term_ID->get_error_message();
        return $term_ID;
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
     *      <a href="<?php echo forum()->doURL('forum_delete')?>&cat_ID=<?php echo $category->term_id?>&return_url=<?php echo urlencode(forum()->adminURL())?>">Delete</a>
     * @endcode
     */
    public function forum_delete() {
        if ( ! function_exists('wp_insert_category') ) require_once (ABSPATH . "/wp-admin/includes/taxonomy.php");
        //wp_delete_category();
        if ( $cat_ID = in('cat_ID') ) {
            $category = get_category( $cat_ID );
            if ( $category ) {
                wp_insert_category([
                    'cat_ID' => $category->term_id,
                    'category_nicename' => 'deleted-from-xforum-' . $category->slug,
                    'cat_name' => "Deleted : " . $category->name,
                    'category_parent' => 0,
                ]);
                if ( ! function_exists('wp_redirect') ) require_once (ABSPATH . "/wp-includes/pluggable.php");
                wp_redirect( in('return_url') );
                wp_send_json_success();
            }
            else {
                wp_send_json_error(['code'=>-50011, 'message'=>'cat_ID is ok. But category does not exists.']);
            }
        }
        else {
            wp_send_json_error(['code'=>-50010, 'message'=>'cat_ID is empty']);
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
     * @param $param
     * @return array|mixed|object
     *
     */
    public function http_query($param)
    {
        $url = home_url( '?' . http_build_query( $param ) );
        $re = json_decode(wp_remote_retrieve_body(wp_remote_get( $url )), true);
        return $re;
    }

    /**
     *
     * @attention if $value is empty, then it will only delete the meta.
     *
     * @param $term_ID
     * @param $key
     * @param $value
     *
     * @todo unit test
     */
    public function meta($term_ID, $key, $value = null)
    {
        delete_term_meta( $term_ID, $key );
        if ( $value ) add_term_meta( $term_ID, 'template', $value, true );

    }

    /**
     * It redirect current page to the page of $_REQUEST['url_redirect'].
     *
     * @note it does not do anything if $_REQUEST['url_redirect'] is empty.
     *
     */
    private function url_redirect()
    {
        if ( ! function_exists('wp_redirect') ) require_once (ABSPATH . "/wp-includes/pluggable.php");
        wp_redirect( in('return_url') );
    }


    /**
     * @deprecated - use urlAdminPage()
     *
     */
    public function adminURL()
    {
        return $this->urlAdminPage();
    }

    public function urlAdminPage() {
        return home_url('wp-admin/admin.php?page=xforum%2Ftemplate%2Fadmin.php');
    }


    public function urlForumCreate()
    {
        return home_url('wp-admin/admin.php?page=xforum%2Ftemplate%2Fadmin.php&amp;template=adminForumCreate');
    }


    /**
     * @deprecated - use urlForumList
     */
    public function listURL($slug)
    {
        return $this->urlForumList($slug);
    }

    public function urlForumList($slug)
    {
        return home_url("?forum=list&id=$slug");
    }

    public function urlAdminForumEdit($term_id)
    {
        return $this->urlAdminPage() . "&template=adminForumEdit&cat_ID=$term_id";
    }

    public function categories()
    {
        $forum_category = forum()->getForumCategory();
        return lib()->get_categories_with_depth( $forum_category->term_id );
    }


}


$__forum = null;
/**
 *
 *
 * @note This function caches on memory. so no matter how many times you call this function, it does not produce burden on Process.
 * @return forum
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
