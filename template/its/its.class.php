<?php

class its {
    static $priority = [
        false => '',
        '' => '',
        0 => '',
        10 => 'Never Mind',
        20 => 'Low',
        30 => 'Medium',
        40 => 'High',
        50 => 'Immediate',
        60 => 'Critical',
    ];
    static $process = [
        '' => '',
        'A' => 'ALL',
        'N' => 'Not started',
        'P' => 'Progress (Started)',
        'F' => 'Finished',
    ];
}

