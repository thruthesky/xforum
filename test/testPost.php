<?php

class testPost extends post {

    public function __construct()
    {
        parent::__construct();
    }


    public function runTest() {
        $this->testInstance();
        $this->crud();
    }



    private function testInstance()
    {
        $post1 = post();
        $post2 = post();

        isTrue( $post1 instanceof post, "post instance 1" );
        isTrue( $post2 instanceof post, "post instance 2" );
        isTrue( post() instanceof post, "post instance 3" );

        isTrue( $post1 instanceof forum, "post instance 1" );
        isTrue( $post2 instanceof forum, "post instance 2" );
        isTrue( post() instanceof forum, "post instance 3" );

    }

    public function crud()
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
        isTrue( is_integer($cat_ID), "failed on forum()->create()->save() : $cat_ID");


        // create a post under the forum.
        $post_ID = post()
            ->create()
            ->set('post_category', [$cat_ID])
            ->set('post_title', "This is the title - This should not be in trash")
            ->set('post_content', "This is post content")
            ->set('post_status', 'publish')
            ->set('post_author', $author->ID)
            ->save();
        isTrue( is_integer($post_ID), "failed on post()->create()->save() : $post_ID");

        $post = post()->delete($post_ID);
        isTrue( $post,"failed on post()->delete( $post_ID )");


        // delete the forum
        $re = forum()->delete($cat_ID);
        isTrue( !$re,  "failed on forum()->delete($cat_ID) : $re");


    }


}








