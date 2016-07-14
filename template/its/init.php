<?php
$old_timezone = date_default_timezone_get();
date_default_timezone_set( 'Asia/Manila' );


add_action( 'wp_insert_comment', function( $comment_ID, $comment ) {
    //di( in() );
    //di( $comment_ID );
    //di( $comment );

    $post_ID = in('post_ID');
    post()->meta( $post_ID, 'process', in('process') );
    post()->meta( $post_ID, 'percentage', in('percentage') );

}, 10, 2);



add_action('content_save_pre', function ( $where ) {
    $title = in('title');
    $content = in('content');
    $reg_exUrl = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/";
    if ( preg_match_all($reg_exUrl, $content, $matches) ) {
        foreach ( $matches[0] as $i => $match ) {
            $content = str_replace( $match, '<a href="'.$matches[0][$i].'">'.$match.'</a>', $content );
        }
     }

    return $content;

} );



class its {
    static $priority = [
        0 => 'None',
        10 => 'Never Mind',
        20 => 'Low',
        30 => 'Medium',
        40 => 'High',
        50 => 'Immediate',
        60 => 'Critical',
    ];
    static $process = [
        'N' => 'Not started',
        'P' => 'Progress (Started)',
        'F' => 'Finished',
        'A' => 'Approved',
        'R' => 'Rejected',
        'V' => 'Failed',
    ];

    /**
     *
     * Returns true if the Deadline is set and it is past.
     *
     * @return bool
     */
    public static function isOverdue()
    {
        if ( post()->process == 'A' || post()->process == 'R' ) return false;

        $d = post()->deadline;
        if ( empty($d) ) return false;
        else if ( strtotime($d) < strtotime(date('Y-m-d')) ) return true;
        return false;
    }

//    public function autoLink($text){
//        // force http: on www.
//        $text = ereg_replace( "www\.", "http://www.", $text );
//        // eliminate duplicates after force
//        $text = ereg_replace( "http://http://www\.", "http://www.", $text );
//        $text = ereg_replace( "https://http://www\.", "https://www.", $text );
//
//        // The Regular Expression filter
//        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
//        // Check if there is a url in the text
//        if(preg_match($reg_exUrl, $text, $url)) {
//            // make the urls hyper links
//            $text = preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow">'.$url[0].'</a>', $text);
//        }    // if no urls in the text just return the text
//
//        return ($text);
//    }
//    public static function get_link_url() {
//        $content = get_the_content();
//        $has_url = get_url_in_content( $content );
//
//        return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
//    }




}


