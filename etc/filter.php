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


    if ( $forum == 'all' ) {
        return forum()->locateTemplate( '', 'all');
    }
    else if ( $forum == 'list' ) {
        $category_slug = in('slug');
        forum()->setCategory( $category_slug );
        
        return forum()->locateTemplate( $category_slug, 'list');
    }
    else if ( $forum == 'edit' ) {
        if ( in('slug') ) { // new post
            forum()->setCategory(in('slug'));
        }
        else { // edit post
            // check if ownership.
            forum()->endIfNotMyPost( in('post_ID') );
            forum()->setCategoryByPostID( in('post_ID') );
        }

        return forum()->locateTemplate( forum()->getCategory()->slug, 'edit');
    }
    else if ( is_single() ) {
        xlog("add_filter() : is_single()");
        $id = get_the_ID();
        if ( forum()->isPost($id) ) {
            the_post();
            $GLOBALS['post_view_count'] = post()->increaseNoOfView( $id );
            forum()->setCategoryByPostID( $id );
            return forum()->locateTemplate( forum()->getCategory()->slug, 'view'); //
        }

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
    return $comment_template;
});



add_filter( 'the_content', function( $content ) {
    //$content = preg_replace("/([^\"='])(https?:\/\/[^ <>'\"\\n]*)/", "$1<a href='$2' target='_blank'>$2</a>", $content);
    //$content = make_clickable( $content );
    $url_pattern = "@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@";

    /*
    $url_pattern = "/([^\"='])(https?:\/\/[^ <>'\"\\n]*)(&#[0-9]+;)/";
    $replace = '$1<a href="$2" target="_blank">$2</a>$3';
    $content = preg_replace( $url_pattern, $replace, $content);
    */

    /**
     * @todo @warning enabling entity code may cause secrious security problems.
     */
    $content = html_entity_decode( $content ); // to remove things like  "&#1234;"

    $url_pattern = "/([^\"='])(https?:\/\/[a-zA-Z0-9\~\!@#\$%^&\*\(\)\-\+\\_=\|\[\]\{\};:,\.\?\/]+)/";
    $replace = '$1<a href="$2" target="_blank">$2</a>';
    $content = preg_replace( $url_pattern, $replace, $content);


    return $content;
});