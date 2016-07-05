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
        isTrue( in( $uid ) === null, "Error in in()");

        $_GET[ $uid ] = true;
        reset_http_query();

        isTrue( in( $uid ) !== null, "NULL Test : Shouldn't be null");
        isTrue( in( $uid ) === true, "Should be true.");

        $_GET[ $uid ] = null;
        reset_http_query();
        
        isTrue( in( uniqid(), true) === true, 'Should be true');
        isTrue( in( uniqid(), false) === false, 'Fase Test: Should be false');
        isTrue( in( uniqid(), 'okay') === 'okay', 'Should be okay');
    }

}








