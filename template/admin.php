<?php
include DIR_XFORUM . 'etc/admin-function.php';
if ( in('template') ) $template = in('template').'.php';
else $template = 'adminForumList.php';

include $template;

