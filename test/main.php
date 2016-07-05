<?php
set_time_limit(0);
$__count_isTrue = 0;

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


xlog("xforum.php TEST ends -----------------------");
exit;



/** function */

function testClass( $class ) {
    $obj = new $class();
    if ( $method = in('method') ) {
        xlog("$class ->$method ()");
        $obj->$method();
    }
    else {
        xlog("$class ->runTest()");
        $obj->runTest();
    }
}



/**
 * @deprecated use check
 * @param $re
 * @param null $msg
 * @param bool $end
 */
function isTrue( $re, $msg = null, $end = false ) {
    global $__count_isTrue;
    die("DO NOT USE isTrue()");
    $__count_isTrue ++;
    if ( $re ) {
        echo "$__count_isTrue ";
    }
    else {
        msg( "TEST ERROR: $msg");
        if ( $end ) die();
    }
}

function check( $re, $ok='', $bad='', $end = false ) {
    global $__count_isTrue;
    $__count_isTrue ++;

    if ( $re ) {
        echo "OK: $__count_isTrue : $ok<br>\n";
    }
    else {
        msg( "<b style='color:red;'>ERROR:</b> $bad<br>\n");
        if ( $end ) die();
    }
}

/**
 *
 * Use this function with the return value of fourm()->http_query();
 * @param $re
 * @param $ok
 * @param $bad
 * @param bool $end
 *
 * @code how to use success()
 * success(
        forum()->http_query( ["do"=>"forum_delete", "term_id"=>$category->term_id] ),
        "Grabage froum - $slug - delted !!",
        "failed on forum_delete (7) "
        );
 * @endcode
 */
function success( $re, $ok, $bad, $end = false ) {
    if ( ! isset( $re['success'] ) ) {
        echo "WRONG JOSN FORMAT When it should be : ";
        echo($re);
        die();
    }
    if ( $re['success'] ) {
        $re = true;
    }
    else {
        $data = $re['data'];
        $bad .= " :: JSON ERROR : CODE($data[code]) : $data[message]";
        $re = false;
    }
    check ($re, $ok, $bad, $end);
}

function msg( $str ) {
    echo "$str\n";
}

