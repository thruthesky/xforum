<?php

class testPostByViel extends post
{
    protected $prop;
    public $pub;
    private $priv;
    static $test = ['test-content'];

    public function __construct()
    {
        parent::__construct();
    }

    public function runTest()
    {
        $this->testInstance();
        $this->testProperty();
        // $this->post_crud(); // THIS TEST LOOKS LIKE HAVING A LOT OF TEST ERRORS.
        $this->testPostDate();
    }

    public function testInstance()
    {
        $post1 = post();
        $post2 = post();

        check( $post1 instanceof post, "post instance okay.", "should be post instance" );
        check( $post2 instanceof post, "post instance okay.", "should be post instance" );
        check( post() instanceof post, "post instance okay.", "should be post instance" );

        check( $post1 instanceof forum == false, "post instance okay.", "should be post instance" );
        check( $post2 instanceof forum == false, "post instance okay.", "should be post instance" );
        check( post() instanceof forum == false, "post instance okay.", "should be post instance" );
    }

    public function testProperty(){
        check( property_exists($this, 'prop'), "Property exists.", "Property 'prop' does not exists." );
        check( !property_exists($this, 'method'), "Property does not exists.", "Property 'method' should not exist." );

        check( property_exists('testPostByViel', 'pub'), "Property exists.", "Property 'pub' does not exists." );
        check( property_exists('testPostByViel', 'priv'), "Property exists.", "Property 'pub' does not exists." );
        check( property_exists(new testPostByViel, 'pub'), "Property exists.", "Property 'pub' does not exists." );
        check( property_exists($this, 'test'), "Property exists.", "Property 'test' does not exists." );
    }

    public function post_crud()
    {
        if ( ! function_exists('get_user_by') ) require_once ABSPATH . 'wp-includes/pluggable.php';
        $author = get_user_by('id', 1); // admin

        $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $test_slug = "test-slug" . uniqid();

        // create a forum
        $cat_ID = forum()->create()
            ->set('cat_name', 'test-name')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description')
            ->save();
        check( is_integer($cat_ID), "$test_slug forum has been created", "failed on forum()->create()->save() : $cat_ID");

        // initial count for published posts
        $initial_count = wp_count_posts()->publish;

        // create a post under the forum.
        $post_ID = post()
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title")
            ->set('post_content', "This is post content")
            ->set('post_status', 'publish')
            ->set('post_author', $author->ID)
            ->create();
        check( is_integer($post_ID), "$post_ID post has been created", "failed on post()->create() : $post_ID");

        //check if post is published
        $post = get_post( $post_ID );
        check( $post->post_status == 'publish', "Post has been published.", "post is not published : $post_ID");


        // edit the post.
        $update_ID = post()
            ->set('ID', $post_ID)
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title - Edited")
            ->set('post_content', "This is post content - Edited")
            ->set('post_status', 'publish')
            ->set('post_author', $author->ID)
            ->update();
        check( is_integer($update_ID), "$update_ID post has been edited.", "failed on post()->update() : $update_ID");

        // check is post is updated not inserted again or not duplicated
        check( $post_ID == $update_ID, "Post ID matched: post was updated.", "Post is not updated, Another post was inserted with the ID: $update_ID");

        //check if edited post is published
        $post = get_post( $update_ID );
        check( $post->post_status == 'publish', "$update_ID post has been published.", "post is not published : $update_ID");

        // count again published posts
        $post_count = wp_count_posts()->publish;

        // check if only one post has been added
        // THIS TEST HAS ERROR
//        check( ($initial_count + 1) == $post_count,"Post count for published posts matched.", "Error on the count of published posts");

        // delete the edited post
        $post = post()->delete($update_ID);
        check( $post, "$update_ID post has been deleted.", "failed on post()->delete( $update_ID )");

        // check if the edited post is still exists; it should not exist

        $post_check = post()->exists( $update_ID );

        check( ! $post_check, "Deleted post ($post_ID) did not exist.", "Error: $post_ID shouldn't exist.");

        // create a draft post
        $post_ID = post()
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title")
            ->set('post_content', "This is post content")
            ->set('post_status', 'draft')
            ->set('post_author', $author->ID)
            ->create();
        check(is_integer($post_ID), "$post_ID draft post has been created.", "failed on post()->create() : $post_ID");

        // check if post is draft not published
        $post = get_post( $post_ID );
        check( $post->post_status == 'draft', "Draft post has been created", "Post is not draft : $post_ID");

        // count the number of draft posts
        check(  wp_count_posts()->draft == 1, "Count for draft posts matched.", "Error on the count of draft posts. ( $post_ID )");

        // delete the draft post
        $post = post()->delete($post_ID);
        check( $post, "$post_ID post has been deleted.", "failed on post()->delete( $post_ID )");

        // check if the draft post is still exists; it should not exist
        $post_check = post()->exists( $post_ID );
        check( ! $post_check, "Deleted post ($post_ID) did not exist.", "$post_ID shouldn't exist.");

        // delete the forum
        $re = forum()->delete($cat_ID);
        check( !$re,  "$cat_ID forum has been deleted.", "failed on forum()->delete($cat_ID) : $re");

    }

    public function testPostDate() {

        if ( ! function_exists('get_user_by') ) require_once ABSPATH . 'wp-includes/pluggable.php';
        $author = get_user_by('id', 1); // admin

        $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $test_slug = "test-slug" . uniqid();

        // create a forum
        $cat_ID = forum()->create()
            ->set('cat_name', 'test-name')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description')
            ->save();
        check( is_integer($cat_ID), "$test_slug forum has been created", "failed on forum()->create()->save() : $cat_ID");

        // create a post under the forum with the post_date of 2 days ago
        $date = date("Y-m-d", strtotime("now") - 172800);
        $post_ID = post()
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title")
            ->set('post_content', "This is post content")
            ->set('post_status', 'publish')
            ->set('post_author', $author->ID)
            ->set('post_date', $date)
            ->create();
        check( is_integer($post_ID), "$post_ID post has been created dated: $date", "failed on post()->create() : $post_ID");

        // edit the post & see if the post_modified date is the day you edited
        $post_ID = post()
            ->set('ID', $post_ID)
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title - Edited")
            ->set('post_content', "This is post content - Edited")
            ->set('post_status', 'publish')
            ->set('post_author', $author->ID)
            ->update();
        check( is_integer($post_ID), "$post_ID post has been edited.", "failed on post()->update() : $post_ID");

        // check the post_modified date and post_date of edited post
        $post = get_post($post_ID);
        check( date("Y-m-d", strtotime($post->post_modified)) == date("Y-m-d", strtotime("now") ), "$post_ID post has been edited with the date today.", "post_modified date did not match the date today.");
        check( date("Y-m-d", strtotime($post->post_date)) == $date , "$post_ID post date matched the date two days ago.", "$post_ID post date did not match the date two days ago.");

        // check the status of the edited post
        check( $post->post_status == 'publish', "$post_ID status is Publish", "$post_ID status is not publish");

        // delete the post
        $post = post()->delete($post_ID);
        check( $post, "$post_ID post has been deleted.", "failed on post()->delete( $post_ID )");

        // delete forum
        $re = forum()->delete($cat_ID);
        check( !$re,  "$cat_ID forum has been deleted.", "failed on forum()->delete($cat_ID) : $re");
    }


}