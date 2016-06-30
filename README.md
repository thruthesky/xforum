# xforum
New forum for withcenter company.



# documents

https://docs.google.com/document/d/1wuYQzA0qZlviz9vxW7bM2Zvbj5YpL5ubNLSxZte5kEo/edit#


# How to test

access index.php with "test=testAll" or "test=testForum", etc...

* the value of 'test' key is the name of the class file.



$ curl "http://work.org/wordpress-4.5.3/?class=all"
$ curl "http://work.org/wordpress-4.5.3/?class=testFunction"
$ curl "http://work.org/wordpress-4.5.3/?class=testForum&method=crud"



# TODO

* Code inconsistency
    * it is confusing with $_REQUEST['do'] and $_REQUEST['forum'].
    * Make it $_REQUEST['forum']. Do not use $_REQUEST['do']
    
    
    



# How to run script / How to generate fake data.


Run script like below;

$ curl "http://work.org/wordpress-4.5.3/?script=post-generate"




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

## Life Cycle of XForum List Page
