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
        check( in( $uid ) === null, "Function okay.", "Error in in()");

        $_GET[ $uid ] = true;
        reset_http_query();

        check( in( $uid ) !== null, "Function okay.", "NULL Test : Shouldn't be null");
        check( in( $uid ) === true, "Function okay.", "Should be true.");

        $_GET[ $uid ] = null;
        reset_http_query();
        
        check( in( uniqid(), true) === true, 'Function okay.', 'Should be true');
        check( in( uniqid(), false) === false, 'Function okay.', 'Fase Test: Should be false');
        check( in( uniqid(), 'okay') === 'okay', 'Function okay.', 'Should be okay');
    }

}








