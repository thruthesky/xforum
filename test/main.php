<?php
xlog("test runs : main.php");
foreach( glob(DIR_XFORUM . 'test/*.php') as $php ) {
    if ( strpos($php, 'main.php') === false ) {
        include $php;
        if ( strpos( $php, 'test') !== false ) {
            $tests[] = pathinfo( $php, PATHINFO_FILENAME);
        }
    }
}

$class = 'test' . in('test');
msg("main.php : going to test : " . $class);


if ( $class == 'testall' ) {
    foreach( $tests as $class ) testClass( $class );
}
else testClass( $class );


xlog("xforum.php ends -----------------------");
exit;



/** function */

function testClass( $class ) {
    $obj = new $class();
    if ( $method = in('method') ) {
        $obj->$method();
    }
    else $obj->runTest();
}


/**
 * @param $re
 * @param $msg
 */
function isTrue( $re, $msg = null ) {
    static $__count_isTrue = 0;
    $__count_isTrue ++;
    if ( $re ) {
        echo "$__count_isTrue ";
    }
    else {
        msg( "TEST ERROR: $msg");
    }
}

function msg( $str ) {
    echo "$str\n";
}

