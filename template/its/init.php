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
}

