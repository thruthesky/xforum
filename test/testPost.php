<?php

class testPost extends post {

    public function __construct()
    {
        parent::__construct();
    }


    public function runTest() {
        $this->testInstance();
    }



    private function testInstance()
    {
        $post1 = post();
        $post2 = post();

        isTrue( $post1 instanceof post, "post instance 1" );
        isTrue( $post2 instanceof post, "post instance 2" );
        isTrue( post() instanceof post, "post instance 3" );

        isTrue( $post1 instanceof forum, "post instance 1" );
        isTrue( $post2 instanceof forum, "post instance 2" );
        isTrue( post() instanceof forum, "post instance 3" );

    }

}








