<?php

$user = user()->forceLogin('admin');

$forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);
$test_slug = "test-gen-3";


// create a forum
$category = get_category_by_slug($test_slug);
if ( $category ) {
    $cat_ID = $category->term_id;
}
else {

    $cat_ID = forum()->create()
        ->set('cat_name', 'Test Forum')
        ->set('category_nicename', $test_slug)
        ->set('category_parent', $forum_category->term_id)
        ->set('category_description', 'This is the description of test forum.')
        ->save();

    if ( is_integer($cat_ID) ) {

    }
    else {
        die("Failed to create the forum");
    }

}



// create a post under the forum.
for ( $i = 1; $i <= 200; $i ++ ) {
    $post_ID = post()
        ->set('post_category', [$cat_ID])
        ->set('post_title', "$i - $test_slug")
        ->set('post_content', "This is post content")
        ->set('post_status', 'publish')
        ->set('post_author', $user->ID)
        ->create();
    if ( is_integer($post_ID) ) echo "Post created count. $i, post.ID = $post_ID<br>";
}

