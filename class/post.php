<?php
class post extends forum {
    public function __construct()
    {

        parent::__construct();

    }
}




function post() {
    return new post();
}
