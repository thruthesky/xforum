<?php
$old_timezone = date_default_timezone_get();
date_default_timezone_set( 'Asia/Manila' );

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
        '' => '',
        'N' => 'Not started',
        'P' => 'Progress (Started)',
        'F' => 'Finished',
        'A' => 'Approved',
        'R' => 'Rejected',
    ];

    public static function isOverdue()
    {
        if ( post()->process == 'A' || post()->process == 'R' ) return false;
        if ( post()->deadline > date('Y-m-d') ) return false;
        return true;
    }
}

