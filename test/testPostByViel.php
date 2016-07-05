<?php

class testPostByViel extends post
{

    public function __construct()
    {
        parent::__construct();
    }

    public function runTest()
    {
        $this->post_crud();
        $this->testInstance();
    }

    private function testInstance()
    {
        $post1 = post();
        $post2 = post();

        isTrue( $post1 instanceof post, "post instance" );
        isTrue( $post2 instanceof post, "post instance" );
        isTrue( post() instanceof post, "post instance" );

        isTrue( $post1 instanceof forum == false, "post instance" );
        isTrue( $post2 instanceof forum == false, "post instance" );
        isTrue( post() instanceof forum == false, "post instance" );
    }

    private function post_crud()
    {
        // code
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
        isTrue( is_integer($cat_ID), "failed on forum()->create()->save() : $cat_ID");

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
        isTrue( is_integer($post_ID), "failed on post()->create() : $post_ID");
        isTrue( $post_ID == true, "failed on post()->update(): $post_ID");

        //check if post is published
        $post = get_post( $post_ID );
        isTrue( $post->post_status == 'publish', "post is not published : $post_ID");


        // edit the post.
        $update_ID = post()
            ->set('ID', $post_ID)
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title - Edited")
            ->set('post_content', "This is post content - Edited")
            ->set('post_status', 'publish')
            ->set('post_author', $author->ID)
            ->update();
        isTrue( is_integer($update_ID), "failed on post()->update() : $update_ID");

        // check is post is updated not inserted again or not duplicated
        isTrue( $post_ID == $update_ID, "Post is not updated, Another post was inserted with the ID: $update_ID");

        //check if edited post is published
        $post = get_post( $update_ID );
        isTrue( $post->post_status == 'publish', "post is not published : $update_ID");

        // count again published posts
        $post_count = wp_count_posts()->publish;

        // check if only one post has been added
        isTrue( ($initial_count + 1) == $post_count, "Error on the count of published posts");

        // delete the post
        $post = post()->delete($post_ID);
        isTrue( $post,"failed on post()->delete( $post_ID )");

        // check if post is still exists; it should not exists
        $args = array(
            'ID' => $post_ID,
            'post_status' => 'publish'
        );
        $post_check  = get_post($args);
        isTrue( ! $post_check, "$post_ID shouldn't exist.");

        // delete the forum
        $re = forum()->delete($cat_ID);
        isTrue( !$re,  "failed on forum()->delete($cat_ID) : $re");

    }


}