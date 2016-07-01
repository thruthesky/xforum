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
    }
    /*

    // forum list.
    // http://abc.com/forum/qna
    if ( seg(0) == 'forum' && seg(1) != null && seg(2) == null  ) {
        return $this->loadTemplate( $this->locateTemplate(seg(1), 'list') );
    }

    // forum pagination
    // http://domain.com/forum/qna/5 ==> 5 page.
    else if ( seg(0) == 'forum' && seg(1) != null && seg(2) == 'page' ) {
        return $this->loadTemplate( $this->locateTemplate(seg(1), 'list') );
    }

    // post edit
    // http://abc.com/forum/(xxxx)/edit
    // if (xxxx) is numeric, then it's 'post edit'
    // else it's 'new post'.
    else if ( seg(0) == 'forum' && seg(1) != null && seg(2) == 'edit'  ) {
        $s = seg(1);
        if ( is_numeric($s) ) $this->checkOwnership( $s ); // post edit
        else $this->checkLogin(); // post write

        return $this->loadTemplate( $this->locateTemplate(seg(1), 'edit') );
        //return $this->loadTemplate( 'forum-edit-basic.php' );
    }


    // view
    // https://abc.com/forum/forum-name/[0-9]+
    else if ( seg(0) == 'forum' && seg(1) != null && is_numeric(seg(2))  ) {
        return $this->loadTemplate( $this->locateTemplate(seg(1), 'view') );
    }
    // Matches if the post is under forum category.
    else if ( is_single() ) {
        klog("add_filter() : is_single()");
        $id = get_the_ID();
        if ( $id ) {
            $categories = get_the_category( $id );
            if ( $categories ) {
                $category = current( $categories );
                $category_id = $category->term_id;
                klog("category_id: $category_id");
                $ex = explode('/', get_category_parents($category_id, false, '/', true));
                klog("category slug of the category id: $ex[0]");
                if ( $ex[0] == FORUM_CATEGORY_SLUG ) {
                    //return $this->loadTemplate('forum-view-basic.php');
                    return $this->loadTemplate( $this->locateTemplate( $category->slug, 'view' ) );
                }
            }
        }
    }
    */
    return $template;
}, 0.01 );





