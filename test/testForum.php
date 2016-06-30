<?php

class testForum extends forum {

    public function __construct()
    {
        parent::__construct();
    }


    public function runTest() {
        $this->testInstance();
        $this->crud();
        $this->testForumCRUDRemote();
        $this->testForumCount();
        $this->testTemplate();
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

    private function testForumCRUDRemote()
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
        $param['do'] = 'forum_edit';
        $param['cat_ID'] = $category->term_id;
        $param['cat_name'] = 'Test Forum Name Has Changed';
        $re = forum()->http_query( $param );
        isTrue( $re['success'], $re['success'] ? null : "failed on do=forum_edit : code=>{$re['data']['code']}, message=>{$re['data']['message']}");


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

    final public function crud()
    {

        $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);

        $test_slug = "test-slug" . uniqid();
        // create the forum of the slug
        $cat_ID = forum()->create()
            ->set('cat_name', 'test-name')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description')
            ->save();
        isTrue( is_integer($cat_ID), "failed on forum()->create()->save() : $cat_ID");


        // delete the forum
        $re = forum()->delete($cat_ID);
        isTrue( !$re,  "failed on forum()->delete($cat_ID) : $re");

    }

    private function deleteTemplates() {
        $template_name = 'flower';
        $theme_default_list = get_stylesheet_directory() . "/template-forum/default/list.php";
        $plugin_default_list = DIR_XFORUM . "template/default/list.php";
        $theme_flower_list = get_stylesheet_directory() . "/template-forum/$template_name/list.php";
        $plugin_flower_list = DIR_XFORUM . "template/$template_name/list.php";

        @unlink($theme_default_list);
        @unlink($theme_flower_list);
        @unlink($plugin_default_list);
        @unlink($plugin_flower_list);
    }

    public function testTemplate()
    {

        // test with a forum. the template name is 'flower'
        $template_name = 'flower';
        $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $test_slug = "test-slug" . uniqid();


        // create 'flower' plugin folders.

        @mkdir( DIR_XFORUM . "template/$template_name");
        @mkdir( get_stylesheet_directory() . "/template-forum/default", 0777, true);
        @mkdir( get_stylesheet_directory() . "/template-forum/$template_name", 0777, true);



        // delete all the template files of 'flower' before you begin test.

        $this->deleteTemplates();

        $theme_default_temp = get_stylesheet_directory() . "/template-forum/default/temp.php";
        $plugin_default_temp = DIR_XFORUM . "template/default/temp.php";
        $theme_flower_temp = get_stylesheet_directory() . "/template-forum/$template_name/temp.php";
        $plugin_flower_temp = DIR_XFORUM . "template/$template_name/temp.php";



        // test on non existing forum.
        // must be plugin/default/temp.php since no template exists.
        $this->deleteTemplates();
        $path = forum()->locateTemplate( 0, 'temp' );
        isTrue( $path == $plugin_default_temp, "2: path: $path vs expectation: $plugin_default_temp");





        // create the forum of the slug
        $cat_ID = forum()->create()
            ->set('cat_name', 'test-name')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description')
            ->save();
        isTrue( is_integer($cat_ID), "failed on forum()->create()->save() : $cat_ID");



        // put the template sa $template_name
        forum()->meta($cat_ID, 'template', $template_name);


        // check if the template name set properly.
        $category = get_category_by_slug( $test_slug );
        isTrue( forum()->meta( $category->cat_ID, 'template') == $template_name, "Template name was not set properly.");




        // get the location of the template. it should not exist since you didn't create it.

        // must be default since the plugin/flower/temp does not exists.
        // plugin/template/flower/temp.php does not exist. it falls back to default.
        $path = forum()->locateTemplate( $cat_ID, 'temp' );
        isTrue( $path == $plugin_default_temp, "3: path: $path vs expectation: $plugin_default_temp");


        // touch the template under plugin template.
        // so plugin/flower/temp should exist.
        touch( $plugin_flower_temp );
        $path = forum()->locateTemplate( $cat_ID, 'temp' );
        isTrue( $path == $plugin_flower_temp, "4: path: $path, expectation: $plugin_flower_temp");


        // touch default template on theme.
        touch ( $theme_flower_temp );
        $path = forum()->locateTemplate( $cat_ID, 'temp' );
        isTrue( $path == $theme_flower_temp, "5: path: $path, expectation: $theme_flower_temp");


        // remove all templates. & create theme/template/flower/temp.php
        $this->deleteTemplates();
        touch ( $theme_flower_temp );
        $path = forum()->locateTemplate( $cat_ID, 'temp' );
        isTrue( $path == $theme_flower_temp, "6: path: $path, expectation: $theme_flower_temp");


        // delete the forum
        $re = forum()->delete($cat_ID);
        isTrue( !$re,  "failed on forum()->delete($cat_ID) : $re");

    }
}








