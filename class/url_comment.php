<?php

trait UrlComment {





    public function urlVersion() {
        echo "comment version url";
    }


    /**
     * Echoes url of delete.
     */
    public function urlDelete() {
        $comment_ID = comment()->comment_ID;
        echo home_url("?forum=comment_delete_submit&comment_ID=$comment_ID");
    }



}