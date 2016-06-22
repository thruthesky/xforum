<?php

class testForum extends forum {

    public function __construct()
    {
        parent::__construct();
    }


    public function runTest() {
        $this->testInstance();
        $this->testCreateForum();
        $this->testForumCount();
    }

    private function testInstance()
    {
        $forum1 = forum();
        $forum2 = forum();

        isTrue( $forum1 instanceof forum, "forum instance" );
        isTrue( $forum2 instanceof forum, "forum instance" );
        isTrue( forum() instanceof forum, "forum instance" );

        isTrue( forum() instanceof post == false, "forum instance" );
    }

    private function testCreateForum()
    {
        $parent = get_category_by_slug(FORUM_CATEGORY_SLUG);
        isTrue( $parent, "Forum category does not exists");


        // forum slug
        $slug = "new_test_category_slug";
        $category = get_category_by_slug($slug); // load forum

        if ( $category ) { // delete if exists.
            $re = forum()->http_query( ["do"=>"forum_delete", "cat_ID"=>$category->term_id] );
            isTrue($re['success'], "failed on do=forum_delete");
            $category = get_category_by_slug($slug);
        }
        isTrue( ! $category, "$slug shouldn't exist.");


        // create the forum of the slug
        $param = [];
        $param['do'] = 'forum_create';
        $param['cat_name'] = 'Test Forum';
        $param['category_nicename'] = $slug;
        $param['category_parent'] = $parent->term_id;
        $param['category_description'] = "This is a category created by unit test";
        $re = forum()->http_query( $param );
        isTrue( $re['success'], "failed on do=forum_create");

        // delete it
        $category = get_category_by_slug($slug);
        isTrue( $category, "$slug should exist.");      // this test may not be needed.
        $re = forum()->http_query( ["do"=>"forum_delete", "cat_ID"=>$category->term_id] );
        isTrue($re['success'], "failed on do=forum_delete");

        // check if it is deleted.
        $category = get_category_by_slug($slug);
        isTrue( ! $category, "$slug shouldn't exist.");



        // create the forum again
        $param = [];
        $param['do'] = 'forum_create';
        $param['cat_name'] = 'Test Forum';
        $param['category_nicename'] = $slug;
        $param['category_parent'] = $parent->term_id;
        $param['category_description'] = "This is a category created by unit test";
        $re = forum()->http_query( $param );
        isTrue( $re['success'], "failed on do=forum_create");

        // update. change the category name.
        $category = get_category_by_slug($slug);
        $param['do'] = 'forum_update';
        $param['cat_ID'] = $category->term_id;
        $param['cat_name'] = 'Test Forum Name Has Changed';
        $re = forum()->http_query( $param );
        isTrue( $re['success'], $re['success'] ? null : "failed on do=forum_update : code=>{$re['data']['code']}, message=>{$re['data']['message']}");


        // and check if the category name has changed.
        $category = get_category_by_slug($slug);
        isTrue( $category->cat_name == $param['cat_name'], "Category name should be $param[cat_name]");

        // delete the garbage.
        $re = forum()->http_query( ["do"=>"forum_delete", "cat_ID"=>$category->term_id] );
        isTrue($re['success'], "failed on do=forum_delete");
    }

    private function testForumCount()
    {
        // count forums
        $cat = forum()->getForumCategory();
        $categories = lib()->get_categories_with_depth( $cat->term_id );
        $no_of_categories = count($categories);

        // create the forum again
        $parent = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $param = [];
        $param['do'] = 'forum_create';
        $param['cat_name'] = 'Test Forum';
        $param['category_nicename'] = 'cat-count-test' . uniqid();
        $param['category_parent'] = $parent->term_id;
        $param['category_description'] = "This is a category created by unit test";
        $re = forum()->http_query( $param );
        isTrue( $re['success'], $re['success'] ? null : "failed on do=forum_create. {$re['data']['code']} : {$re['data']['message']}");

        // count the forums again
        $cat = forum()->getForumCategory();
        $categories2 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $new_no_of_categories = count($categories2);

        isTrue( ($no_of_categories + 1) == $new_no_of_categories, "No of categories is wrong. prev: $no_of_categories, new: $new_no_of_categories");
    }
}








