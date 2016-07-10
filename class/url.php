<?php

trait Url {

    use UrlForum, UrlPost, UrlComment {
        UrlForum::urlVersion insteadof UrlPost, UrlComment;
        UrlForum::urlVersion as urlForumVersion;
        UrlPost::urlVersion as urlPostVersion;
        UrlComment::urlVersion as urlCommentVersion;
    }

}

