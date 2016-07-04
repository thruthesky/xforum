<?php

class testForumByViel extends forum
{

    public function __construct()
    {
        parent::__construct();
    }

    public function runTest()
    {
        $this->crud();
        $this->testCount();
        $this->testTemplate();
        $this->testInstance();
    }

    private function testInstance()
    {
        $forum1 = forum();
        $forum2 = forum();

        isTrue( $forum1 instanceof forum, "forum instance" );
        isTrue( $forum2 instanceof forum, "forum instance" );
        isTrue( forum() instanceof forum, "forum instance" );

        isTrue( $forum1 instanceof post == false, "forum instance" );
        isTrue( $forum2 instanceof post == false, "forum instance" );
        isTrue( forum() instanceof post == false, "forum instance" );
    }

    public function crud(){
        $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);

        // forum slug
        $test_slug = "test-slug" . uniqid();

        // create the forum of the slug
        $cat_ID = forum()->create()
            ->set('cat_name', 'cat-name-test')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description')
            ->save();
        isTrue( is_integer($cat_ID), "failed on forum()->create()->save() : $cat_ID");

        // delete the forum
        $re = forum()->delete($cat_ID);
        isTrue( !$re,  "failed on forum()->delete($cat_ID) : $re");

        // create again
        $cat_ID = forum()->create()
            ->set('cat_name', 'cat-name-test-second')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description-second create')
            ->save();
        isTrue( is_integer($cat_ID), "failed on forum()->create()->save() second create : $cat_ID");

         // update/edit the forum
        $cat_ID_update = forum()->create()
            ->set('cat_ID', $cat_ID)
            ->set('cat_name', 'cat-name-test-edited')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description - edited')
            ->save();
        isTrue( is_integer($cat_ID_update), "failed on forum()->create()->save() edit : $cat_ID_update");

        // delete the forum
        $re = forum()->delete($cat_ID_update);
        isTrue( !$re,  "failed on forum()->delete($cat_ID_update) : $re");

        // check if forum has been deleted (does not belong to XForum anymore)
        $category = get_category($cat_ID_update);
        isTrue( $category->parent == 0 , "forum not deleted : $cat_ID_update");

    }
    public function testCount(){

        // initial count of forums
        $cat = forum()->getXForumCategory();
        $categories = lib()->get_categories_with_depth( $cat->term_id );
        $no_of_categories = count($categories);

        // create the forum
        $parent = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $param = [];
        $param['do'] = 'forum_create';
        $param['cat_name'] = 'Test Forum on count()';
        $param['slug'] = 'cat-count-test' . uniqid(); // change from category_nicename to slug 7/4/2016
        $param['category_parent'] = $parent->term_id;
        $param['category_description'] = "This is a category created by unit test";
        $re = forum()->http_query( $param );
        isTrue( $re['success'], $re['success'] ? null : "failed on do=forum_create. {$re['data']['code']} : {$re['data']['message']}");

        // count the forums again
        $cat = forum()->getXForumCategory();
        $categories2 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $no_of_categories_create = count($categories2);

        // compare initial count to new count
        isTrue( ($no_of_categories + 1) == $no_of_categories_create, "No of categories is wrong. prev: $no_of_categories, new: $no_of_categories_create");

       // edit forum
        $category = get_category_by_slug( $param['slug'] );
        $param_edit = [];
        $param_edit['do'] = 'forum_edit';
        $param_edit['term_id'] = $category->term_id; // changed cat_ID to term_id 7/4/2016
        $param_edit['cat_name'] = 'Test Forum on count() - Edited';
        $param_edit['slug'] = $param['slug'];  //change from category_nicename to slug 7/4/2016
        $param_edit['category_parent'] = $param['category_parent'];
        $param_edit['category_description'] = "This is a category created by unit test - Edited";
        $re = forum()->http_query( $param_edit );
        isTrue( $re['success'], $re['success'] ? null : "failed on do=forum_edit. {$re['data']['code']} : {$re['data']['message']}");

        // count again should be the same - because it is not inserted
        $cat = forum()->getXForumCategory();
        $categories3 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $no_of_categories_edit = count($categories3);

        // compare the initial count to count when edit a post; should only add one because the other was only edited not inserted
        isTrue( ($no_of_categories + 1) == $no_of_categories_edit, "No of categories is wrong. prev: $no_of_categories, new: $no_of_categories_edit");

        // delete forum (Not really deleted, we just remove it from xforum parent category)
        $category = get_category_by_slug( $param_edit['slug'] );
        $param_delete = [];
        $param_delete['do'] = 'forum_delete';
        $param_delete['cat_ID'] =  $category->term_id; // changed $category->cat_ID to $category->term_id 7/4/2016
        $param_delete['cat_name'] = 'Deleted: ' .$param_edit['cat_name'];
        $param_delete['slug'] = 'deleted-' . $param_edit['slug']; // change from category_nicename to slug 7/4/2016
        $param_delete['category_parent'] = 0;
        $re = forum()->http_query( $param_delete );
        isTrue( $re['success'], $re['success'] ? null : "failed on do=forum_delete. {$re['data']['code']} : {$re['data']['message']}");

        // count again
        $cat = forum()->getXForumCategory();
        $categories4 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $no_of_categories_delete = count($categories4);

        // compare initial count to the count after delete;
        isTrue( $no_of_categories == ($no_of_categories_delete - 1), "No of categories is wrong. prev: $no_of_categories, new: $no_of_categories_delete");


    }

    public function testTemplate(){

        // test forum with default template name
        $template_name = 'withcenter';
        $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $test_slug = "test-slug" . uniqid();

        // create the folder
        @mkdir( DIR_XFORUM . "template/$template_name");
        @mkdir( get_stylesheet_directory() . "/template-forum/default", 0777, true);
        @mkdir( get_stylesheet_directory() . "/template-forum/$template_name", 0777, true);


        // delete all the template files of folder before you begin test.
        $this->deleteAllTemplates($template_name);

        $theme_default_temp = get_stylesheet_directory() . "/template-forum/default/temp.php";
        $plugin_default_temp = DIR_XFORUM . "template/default/temp.php";
        $theme_custom_temp = get_stylesheet_directory() . "/template-forum/$template_name/temp.php";
        $plugin_custom_temp = DIR_XFORUM . "template/$template_name/temp.php";

        // locate the file: test on non existing forum - must be plugin/default/temp.php since no template exists.
        $path = forum()->locateTemplate( 0, 'temp' );
        isTrue( $path == $plugin_default_temp, "1: path: $path vs expectation: $plugin_default_temp");

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
        isTrue( $category->cat_ID == $cat_ID, "cat_ID are not equal.");

        // plugin/template/custom/temp.php does not exist. it falls back to default.
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        isTrue( $path == $plugin_default_temp, "3a: path: $path vs expectation: $plugin_default_temp");

        // touch the template under plugin template: plugin/custom/temp should exist.
        touch( $plugin_custom_temp );
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        isTrue( $path == $plugin_custom_temp, "4a: path: $path, expectation: $plugin_custom_temp");

        // check the custom template file in theme: touch custom template on theme.
        touch ( $theme_custom_temp );
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        isTrue( $path == $theme_custom_temp, "5a: path: $path, expectation: $theme_custom_temp");

        $this->deleteAllTemplates($template_name);
        touch ( $theme_custom_temp );
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        isTrue( $path == $theme_custom_temp, "6: path: $path, expectation: $theme_custom_temp");


        $template_name = 'withcenter-edited';

        // update/edit the forum
        $category = get_category($cat_ID);
        $cat_ID_update = forum()->create()
            ->set('cat_ID', $cat_ID)
            ->set('cat_name', 'test-name-edited')
            ->set('category_nicename',$category->slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description - edited')
            ->save();
        isTrue( is_integer($cat_ID_update), "failed on forum()->create()->save() edit : $cat_ID_update");
        isTrue( $cat_ID_update == true, "failed on forum()->create()->save() edit: $cat_ID_update");

        // put the template sa $template_name
        forum()->meta($cat_ID_update, 'template', $template_name);

        // check if the template name set properly.
        $category = get_category_by_slug( $category->slug );
        isTrue( forum()->meta( $category->cat_ID, 'template') == $template_name, "Template name was not set properly.");
        isTrue( $category->cat_ID == $cat_ID_update, "cat_ID are not equal.");


        // plugin/template/custom/temp.php does not exist. it falls back to default.
        $path = forum()->locateTemplate( $category->slug, 'temp' );
        isTrue( $path == $plugin_default_temp, "2b: path: $path vs expectation: $plugin_default_temp");

        // touch the template under plugin template: should be plugin/default/temp since we didn't create a new template folder
        touch( $plugin_custom_temp );
        $path = forum()->locateTemplate( $category->slug, 'temp' );
        isTrue( $path == $plugin_default_temp, "3b: path: $path, expectation: $plugin_default_temp");

        /* touch custom template on theme: should be plugin/default/temp since don't have custom and default file on theme
         - falls back to default of plugin */
        touch ( $theme_custom_temp );
        $path = forum()->locateTemplate( $category->slug, 'temp' );
        isTrue( $path == $plugin_default_temp, "4b: path: $path, expectation: $plugin_default_temp");


        // remove all templates. & create theme/template/custom/temp.php
        $this->deleteAllTemplates($template_name);

        // delete the forum
        $re = forum()->delete($cat_ID);
        isTrue( !$re,  "failed on forum()->delete($cat_ID) : $re");


    }

    private function deleteAllTemplates($template_name = null) {
        if (empty($template_name)) $template_name = 'withcenter';

        $theme_default_temp = get_stylesheet_directory() . "/template-forum/default/temp.php";
        $plugin_default_temp = DIR_XFORUM . "template/default/temp.php";
        $theme_custom_temp = get_stylesheet_directory() . "/template-forum/$template_name/temp.php";
        $plugin_custom_temp = DIR_XFORUM . "template/$template_name/temp.php";

        @unlink($theme_default_temp);
        @unlink($plugin_default_temp);
        @unlink($theme_custom_temp);
        @unlink($plugin_custom_temp);
    }

}

?>