<?php

class testForumByViel extends forum
{

    public function __construct()
    {
        parent::__construct();
    }

    public function runTest()
    {

        $this->testInstance();
        $this->crud();
        $this->testCount();
        $this->testTemplate();

    }

    private function testInstance()
    {
        $forum1 = forum();
        $forum2 = forum();

        check( $forum1 instanceof forum, "forum instance okay.", "should be forum instance" );
        check( $forum2 instanceof forum, "forum instance okay.", "should be forum instance" );
        check( forum() instanceof forum, "forum instance okay.", "should be forum instance" );

        check( $forum1 instanceof post == false, "forum instance okay.", "should be forum instance" );
        check( $forum2 instanceof post == false, "forum instance okay.", "should be forum instance" );
        check( forum() instanceof post == false, "forum instance okay.", "should be forum instance" );
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
        check( is_integer($cat_ID), "$test_slug forum has been created.", "failed on forum()->create()->save() : $cat_ID");
        check( $cat_ID == true, "creating $test_slug forum succeed: returns true." ,"failed on forum()->create()->save() : $cat_ID");

        // delete the forum
        $re = forum()->delete($cat_ID);
        check( !$re, "$cat_ID has been deleted.", "failed on forum()->delete($cat_ID) : $re");

        // create again
        $cat_ID = forum()->create()
            ->set('cat_name', 'cat-name-test-second')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description-second create')
            ->save();
        check( is_integer($cat_ID), "$test_slug forum has been created.", "failed on forum()->create()->save() second create : $cat_ID");
        check( $cat_ID == true, "creating $test_slug forum succeed: returns true." ,"failed on forum()->create()->save() second create : $cat_ID");


        // update/edit the forum
        $cat_ID_update = forum()->create()
            ->set('cat_ID', $cat_ID)
            ->set('cat_name', 'cat-name-test-edited')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description - edited')
            ->save();
        check( is_integer($cat_ID_update), "$test_slug forum has been edited.", "failed on forum()->create()->save() edit: $cat_ID_update");
        check( $cat_ID_update == true, "editing $test_slug forum succeed: returns true." ,"failed on forum()->create()->save() edit: $cat_ID_update");


        // delete the forum
        $re = forum()->delete($cat_ID_update);
        check( !$re, "$cat_ID_update has been deleted", "failed on forum()->delete($cat_ID_update) : $re");

        // check if forum has been deleted (does not belong to XForum anymore)
        $category = get_category($cat_ID_update);
        check( $category->parent == 0 , "$cat_ID_update forum has been deleted.", "forum not deleted : $cat_ID_update");

    }
    public function testCount(){

        // initial count of forums
        $cat = forum()->getXForumCategory();
        $categories = lib()->get_categories_with_depth( $cat->term_id );
        $no_of_categories = count($categories);

        // create the forum
        $slug = 'cat-count-test' . uniqid();
        $parent = get_category_by_slug(FORUM_CATEGORY_SLUG);
        $param = [];
        $param['do'] = 'forum_create';
        $param['cat_name'] = 'Test Forum on count()';
        $param['slug'] = $slug;
        $param['category_parent'] = $parent->term_id;
        $param['category_description'] = "This is a category created by unit test";
        $re = forum()->http_query( $param );
        success( $re, "$slug has been created", "failed on do=forum_create.", true);

        // count the forums again
        $cat = forum()->getXForumCategory();
        $categories2 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $no_of_categories_create = count($categories2);

        // compare initial count to new count
        check( ($no_of_categories + 1) == $no_of_categories_create, "No of categories match.",
            "No of categories is wrong. prev: $no_of_categories, new: $no_of_categories_create", true);

       // edit forum
        $category = get_category_by_slug( $param['slug'] );
        $param_edit = [];
        $param_edit['do'] = 'forum_edit';
        $param_edit['term_id'] = $category->term_id;
        $param_edit['cat_name'] = 'Test Forum on count() - Edited';
        $param_edit['slug'] = $param['slug'];
        $param_edit['category_parent'] = $param['category_parent'];
        $param_edit['category_description'] = "This is a category created by unit test - Edited";
        $re = forum()->http_query( $param_edit );
        success( $re, "forum has been edited", "failed on do=forum_edit.", true);

        // count again should be the same - because it is not inserted
        $cat = forum()->getXForumCategory();
        $categories3 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $no_of_categories_edit = count($categories3);

        // compare the initial count to count when edit a post; should only add one because the other was only edited not inserted
        check( ($no_of_categories + 1) == $no_of_categories_edit, "No of categories match.",
            "No of categories is wrong. prev: $no_of_categories, new: $no_of_categories_edit", true);

        // delete forum (Not really deleted, we just remove it from xforum parent category)
        $slug = 'deleted-' . $param_edit['slug'];

        $category = get_category_by_slug( $param_edit['slug'] );
        success(
            forum()->http_query( ["do"=>"forum_delete", "term_id"=>$category->term_id] ),
            "Forum - $slug - has been deleted.",
            "failed on forum_delete ", true
        );

        // count again
        $cat = forum()->getXForumCategory();
        $categories4 = lib()->get_categories_with_depth( $cat->term_id, 0, 'no-cache' );
        $no_of_categories_delete = count($categories4);

        // compare initial count to the count after delete;
        check( $no_of_categories == ($no_of_categories_delete - 1), "No of categories match.",
            "No of categories is wrong. prev: $no_of_categories, new: $no_of_categories_delete", true);


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
        check( $path == $plugin_default_temp, "template match.", "1: path: $path vs expectation: $plugin_default_temp");

        // create the forum of the slug
        $cat_ID = forum()->create()
            ->set('cat_name', 'test-name')
            ->set('category_nicename', $test_slug)
            ->set('category_parent', $forum_category->term_id)
            ->set('category_description', 'test-description')
            ->save();
        check( is_integer($cat_ID), "$test_slug has been created.", "failed on forum()->create()->save() : $cat_ID");
        check( $cat_ID == true, "creating $test_slug succeed: returns true.","failed on forum()->create()->save(): $cat_ID");

        // put the template sa $template_name
        forum()->meta($cat_ID, 'template', $template_name);

        // check if the template name set properly.
        $category = get_category_by_slug( $test_slug );
        check( forum()->meta( $category->cat_ID, 'template') == $template_name, "Template name was set properly.", "Template name was not set properly.");
        check( $category->cat_ID == $cat_ID, "cat_ID matched: forum has been created.", "cat_ID are not equal.");

        // plugin/template/custom/temp.php does not exist. it falls back to default.
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        check( $path == $plugin_default_temp, "templates matched.", "3a: path: $path vs expectation: $plugin_default_temp");

        // touch the template under plugin template: plugin/custom/temp should exist.
        touch( $plugin_custom_temp );
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        check( $path == $plugin_custom_temp, "templates matched for $plugin_custom_temp", "4a: path: $path, expectation: $plugin_custom_temp");

        // check the custom template file in theme: touch custom template on theme.
        touch ( $theme_custom_temp );
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        check( $path == $theme_custom_temp, "templates matched for $theme_custom_temp", "5a: path: $path, expectation: $theme_custom_temp");

        $this->deleteAllTemplates($template_name);
        touch ( $theme_custom_temp );
        $path = forum()->locateTemplate( $test_slug, 'temp' );
        check( $path == $theme_custom_temp, "templates matched for $theme_custom_temp", "6: path: $path, expectation: $theme_custom_temp");


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
        check( is_integer($cat_ID_update), "$category->slug has been edited.", "failed on forum()->create()->save() edit : $cat_ID_update");
        check( $cat_ID_update == true, "editing $category->slug succeed: returns true.", "failed on forum()->create()->save() edit: $cat_ID_update");

        // put the template sa $template_name
        forum()->meta($cat_ID_update, 'template', $template_name);

        // check if the template name set properly.
        $category = get_category_by_slug( $category->slug );
        check( forum()->meta( $category->cat_ID, 'template') == $template_name, "Template name was set properly.", "Template name was not set properly.");
        check( $category->cat_ID == $cat_ID_update, "cat_ID matched: forum has been edited.", "cat_ID are not equal.");


        // plugin/template/custom/temp.php does not exist. it falls back to default.
        $path = forum()->locateTemplate( $category->slug, 'temp' );
        check( $path == $plugin_default_temp, "Template falls back to default. (1)", "2b: path: $path vs expectation: $plugin_default_temp");

        // touch the template under plugin template: should be plugin/default/temp since we didn't create a new template folder
        touch( $plugin_custom_temp );
        $path = forum()->locateTemplate( $category->slug, 'temp' );
        check( $path == $plugin_default_temp, "Template falls back to default. (2)", "3b: path: $path, expectation: $plugin_default_temp");

        /* touch custom template on theme: should be plugin/default/temp since don't have custom and default file on theme
         - falls back to default of plugin */
        touch ( $theme_custom_temp );
        $path = forum()->locateTemplate( $category->slug, 'temp' );
        check( $path == $plugin_default_temp, "Template falls back to default. (3)", "4b: path: $path, expectation: $plugin_default_temp");


        // remove all templates.
        $this->deleteAllTemplates();

        // delete the forum
        $re = forum()->delete($cat_ID);
        check( !$re,  "$cat_ID forum has been deleted.", "failed on forum delete($cat_ID) : $re");


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