<?php

class testFunction {

    public function __construct()
    {

    }


    public function runTest() {
        $this->testInput();
    }



    private function testInput()
    {
        $uid = uniqid();
        check( in( $uid ) === null, "in( $uid ) is empty.", "Error in in()");

        $_GET[ $uid ] = true;
        reset_http_query();

        check( in( $uid ) !== null, "in( $uid ) is not empty. okay.", "NULL Test : Shouldn't be null");
        check( in( $uid ) === true, "in( $uid ) is true. okay.", "Should be true.");

        $_GET[ $uid ] = null;
        reset_http_query();
        
        check( in( uniqid(), true) === true, 'in() default is okay.', 'Should be true');
        check( in( uniqid(), false) === false, 'in() default for false is okay.', 'Fase Test: Should be false');
        check( in( uniqid(), 'okay') === 'okay', 'in() for default value is okay.', 'Should be okay');

        add_query_var('a', 'b');
        check( in( 'a' ) === 'b', 'in(a) is b. okay.', 'Should be b');

        add_query_var('c', 'd');
        check( in( 'a' ) === 'b', 'in(a) is still b. okay.', 'Should be still b');
        check( in( 'c' ) === 'd', 'in(c) is d. okay.', 'Should be d');
    }

}








