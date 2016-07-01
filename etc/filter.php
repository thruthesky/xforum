<?php

/**
 * It switches template files based on xforum's configuration.
 *
 *
 * @note template files that need to be switched - list/comment-list/view/edit/write/comment
 *
 *
 *
 *
 */
add_filter( 'template_include', function ( $template ) {

    $forum = in('forum');

    if ( $forum == 'list' ) {
        $category_slug = in('id');
        forum()->setCategory( $category_slug );
        return forum()->locateTemplate( $category_slug, 'list');
    }
    else if ( $forum == 'edit' ) {
        forum()->setCategoryByPostID( in('post_ID') );
        return forum()->locateTemplate( forum()->getCategory()->slug, 'edit');
    }
    else if ( is_single() ) {
        xlog("add_filter() : is_single()");
        $id = get_the_ID();
        if ( forum()->isPost($id) ) {
            forum()->setCategoryByPostID( $id );
            return forum()->locateTemplate( forum()->getCategory()->slug, 'view'); //
        }
        /*
        if ( $id ) {
            $categories = get_the_category( $id );
            if ( $categories ) {
                $category = current( $categories ); // @todo Warning: what if the post has more than 1 categories?
                $category_id = $category->term_id; // get the slug of the post
                xlog("category_id: $category_id");
                $ex = explode('/', get_category_parents($category_id, false, '/', true)); // get the root slug of the post
                //di($ex);
                xlog("category slug of the category id: $ex[0]");
                if ( $ex[0] == FORUM_CATEGORY_SLUG ) { // is it a post under XForum?
                    forum()->setCategory( $category->slug );
                    return forum()->locateTemplate( $category->slug, 'view'); //
                }
            }
        }
        */
    }
    return $template;
}, 0.01 );




/**
 * Hooks to use xforum template.
 */

add_filter( 'comments_template', function( $comment_template ) {
    //$post = get_post();
    if ( forum()->isPost( get_the_ID() ) ) {
        return forum()->locateTemplate( forum()->getCategory()->slug, 'comment'); //
    }
    /*
    $categories = get_the_category( $post->ID );
    if ( forum()->getCategory() ) {
        $slug = current( $categories )->slug;
        if ( in_array( $slug, forum()->slugs() ) ) {

            $template = $this->locateTemplate($slug, 'comments');
            $comment_template = locate_template( $template );
            //$comment_template = locate_template('forum-comments-basic.php');

            if ( empty($comment_template) ) {
                $comment_template = FORUM_PATH . "template/forum-comments-basic.php";
            }
        }
    }
    */
    return $comment_template;
});

