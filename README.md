# xforum
New forum for withcenter company.



# documents

https://docs.google.com/document/d/1wuYQzA0qZlviz9vxW7bM2Zvbj5YpL5ubNLSxZte5kEo/edit#


# How to test

access index.php with "test=testAll" or "test=testForum", etc...

* the value of 'test' key is the name of the test class file.
    - names of test file is like test[XXXXX].php
    - Just put [XXXXX] part of the class name.



* If you are going to test on OSX or Linux, you need to have proper file permission since mkdir(), touch() will fail on test code of template hierarchy.
    * chmod 777 wp-content/plugins/xforum/template/
    * chmod 777 wp-content/themes/twentysixteen/

$ curl "http://work.org/wordpress-4.5.3/?test=all"
$ curl "http://work.org/wordpress-4.5.3/?test=Function"
$ curl "http://work.org/wordpress-4.5.3/?test=Forum&method=crud"
$ curl "http://work.org/wordpress-4.5.3/?test=Post"



# Configuration in php.ini format

All the configuration (settings) format are in php INI format.


    http://php.net/manual/en/function.parse-ini-string.php



# TODO


* comment CRUD
* file CRUD
* ITS template development.
* @done : post CRUD

* add forum admins ( who owns the forum. separated by comma )
* add forum members ( who can 'cud' for the forum. )


    => forum category is the project.
    => forum members are the members of its.
    => forum admins are the project managers.


* Remove un-necessary query - there are some SQL query doen by WP  but it is not used in the theme.

    SELECT SQL_CALC_FOUND_ROWS wp_posts.ID
    FROM wp_posts 
    WHERE 1=1 
    AND wp_posts.post_type = 'post'
    AND (wp_posts.post_status = 'publish'
    OR wp_posts.post_status = 'private') 
    ORDER BY wp_posts.post_date DESC
    LIMIT 0, 10
    
    SELECT FOUND_ROWS()
    
    
    SELECT wp_posts.*
    FROM wp_posts
    WHERE ID IN (103,102,101,100,99,98,97,96,95,94)
    
    
    
    SELECT t.*, tt.*, tr.object_id
    FROM wp_terms AS t
    INNER JOIN wp_term_taxonomy AS tt
    ON tt.term_id = t.term_id
    INNER JOIN wp_term_relationships AS tr
    ON tr.term_taxonomy_id = tt.term_taxonomy_id 
    WHERE tt.taxonomy IN ('category', 'post_tag', 'post_format')
    AND tr.object_id IN (94, 95, 96, 97, 98, 99, 100, 101, 102, 103)
    ORDER BY t.name ASC
    
    SELECT post_id, meta_key, meta_value
    FROM wp_postmeta
    WHERE post_id IN (94,95,96,97,98,99,100,101,102,103)
    ORDER BY meta_id ASC
    


* Code inconsistency
    * it is confusing with $_REQUEST['do'] and $_REQUEST['forum'].
    * Make it $_REQUEST['forum']. Do not use $_REQUEST['do']
    
    
    
* Check: When the postmeta() data is loaded? If it is loaded automatically on wordpress boot like get_option(), it becomes a serious problem.



# How to run script / How to generate fake data.


Run script like below;

$ curl "http://work.org/wordpress-4.5.3/?script=post-generate"


# How to use/manage
* when a forum loads category, it loads its meta data and put it all together in $category
* category must be set in php.ini format.
    * category can have
        * name
        * admins ( each category can have admin. It's different from forum admin )
        * members ( each category can have members. It's different from forum members )


# Code Tech

## No routing

* Routing is needed for Nice URL.

* Nice URL has no problem with article ( or individual post ) because you can just enable nice url option on admin page.

* But what about forum listing page?

    * No need. Because there is rear case that Search Engine will list forum list page as a result of a search.

    * And post write page, post edit page, post delete page, comment edit page ... and those likes are definitely not needed for nice url.
     
    * So, as long as individual posts have nice URL, we don't need Nice URL any more.





# Submission - How submission works.

* When you submit a form of xforum, you can use the form
    * in ordinary form ( a form submit and redirect into another page )
    * or as in ajax call ( a forum submit with ajax and get return thru ajax )

    * you can do this with wordpress wp_redirect since it only prints out the redirect information in the header,
        * you can put 'return_url' parameter thru the form if you want to redirect a page
        * or you can echo json data from the php script if you want to use it as ajax.
        * see forum()->url_redirect()
        * you can use it both at the same time since wordpress wp_redirect() does not print out any data in the body.
        



# Template hierarchy

## WordPress default template.

if the template file does not exists, WordPress may use default template.

Like comments.php, if the xforum has no comment.php template in anywhere, then the WordPress uses theme ( or system ) template file.






# Life Cycle of XForum

## XForum Main Script Life Cycle.


- etc/function.php
- class/library.php
- class/forum.php
- class/post.php
- class/user.php
- etc/action.php
    - on 'init' action
        - enqueue wp-util, font-awesome.css, bootstrap.css, tether.js, bootstrap.js
    - on 'wp_before_admin_bar_render', remove toolbar on top.
    - on 'admin_menu', add admin menu.
- etc/filter.php
    - on 'template_include' filter
        - relate( locate ) template files of forum depending on $_REQUEST['forum'] and $_REQUEST['id'], etc.
            - $_REQUEST['forum'] is in 'list, new, edit, view, etc...'
- etc/init.php

## Life Cycle of XForum List Page


-> "?forum=list"
    -> add_filter( 'template_include', ... )
        -> locateTemplate()
            -> list.php ( template hierarchy )



# Coding Guide

## forum()->getCategory()->xxxx

Use this as much as you can.
