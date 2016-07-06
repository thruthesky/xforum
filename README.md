# xforum
New forum for withcenter company.


# documents

https://docs.google.com/document/d/1wuYQzA0qZlviz9vxW7bM2Zvbj5YpL5ubNLSxZte5kEo/edit#


# TODO
* create ITS and put all the works on it.
* @done comment CRUD
* @done file update
* Do file deletion from command line.

* ITS template development.
* @done : post CRUD

* Make response() & redirect() simple.
    it is confusing.
    remove redirect().
    make response with options


* add forum admins ( who owns the forum. separated by comma )
* add forum members ( who can 'cud' for the forum. )
* 게시판과 socket.io chat 을 연결하여 실시간 질문 답을 만든다. 내공을 집어 넣는다.
* 하우스메이드 로그인 페이스북 또는 카카오로만 로그인 가능.
* 사용자가 직접 게시판을 만들고, 글과 조회수가 많으면 맨 위로 나오게 한다.
    * 단, 홈페이지 게시 조건을 지켜야 한다.
    

    => forum category is the project.
    => forum members are the members of its.
    => forum admins are the project managers.

* Professional writing for SEO

* 내공제를 하지 않는다. 사이트가 복잡해 진다.
    * 기존 포인트 제도를 활용한다.
* 20 레벨 부터는 자신의 글에 댓글을 삭제 할 수 있다.
* 10 레벨 부터는 자신을 글에 코멘트를 달 수 없게 할 수 있다.
* 30 레벨 부터는 광고를 잘 보이는 곳에 등록 할 수 있다.
* 40 레벨 부터는 자기 게시판을 생성 하고 관리 할 수 있다.
    * 게시판이 활성화되면 상단에 노출.
* 50 레벨 부터는 자신의 게시판에 글 쓴 회원들에게 SMS 문자 메세지를 보낼 수 있다.
* 총 3명 이상. 총 합 20 레벨 이상. 글 임시 조치되며 자동으로 운영자에게 신고.
* 
* 스크롤을 맨 밑에 까지 하거나 글을 다 읽었다는 표시를 하면,
    이 글이 도움이 되었는지를 1 ~ 5 까지 하고
    부족한 부분이 있으면 코멘트로 보충 요구를 할 수 있도록 한다.


* cron 을 이용한 xforum 에 글 자동 등록.


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



# How to test / UnitTest

access index.php with "test=testAll" or "test=testForum", etc...

* the value of 'test' key is the name of the test class file.
    - names of test file is like test[XXXXX].php
    - Just put [XXXXX] part of the class name.


* Windows 8 + Nginx + PHP-FPM ( PHP 5.4 ) + cURL has a problem. it does not see hosts file.
    * cURL works file with actual Domain but not the domains in hosts file in windows.
    * OSX + Nginx + PHP-FPM + cURL work fine.
    

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


# URLs

## list all forums

    /?forum=all




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
        

* 폼 전송 후, 페이지 이동 또는 json 데이터 출력

    * 글 쓰기/코멘트 쓰기의 경우
        * response = list 이면 게시판 목록 페이지로 간다.
        * response = view 이면 글 읽기 페이지로 간다.
        * response = ajax 이면, 글/코멘트 쓰기 성공 여부를 json 으로 출력한다.
        



# Template hierarchy

## WordPress default template.

if the template file does not exists, WordPress may use default template.

Like comments.php, if the xforum has no comment.php template in anywhere, then the WordPress uses theme ( or system ) template file.


## all.php template

* it does not use a category template which means, only theme/.../template-forum/all.php or template/default/all.php will be used.





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


## Javascripts

* forum.js is the JS file for whole xforum
* post.js is only for post view/edit/delete
* comment.js is only for comment list/view/edit/delete


## Hidden iframe

xforum_hidden_iframe is added at the end of html through whole xforum page.
This is for form submit which should be hidden like file-upload.

 

    <iframe name="xforum_hidden_iframe" src="javascript:;" width="0" height="0" style="width:0; height:0; display: none;"></iframe>

## Add xforum plugin in git addon

* create a project pointing its workspace as xforum plugin path

    * and then, it will ask to add whole project.
    
    * click OK and then it will detect the xforum git.
    
    

## forum()->getCategory()->xxxx

Use this as much as you can.


## File Server

see README.md of file-upload
the setting var - xforum_url_file_server will have the url of the file server.




## xforum_admins

xforum_admins in setting(option) tells who can manage all the forum.



## File upload

Developers must fully understand about file upload to apply t in his need.

### To upload

* create a form ( independent form from editing form )
    * set the domain, uid and action of the form.
    * submit through a hidden iframe.
* when submit
    * listen 'message' event since the hidden iframe will send back a message with 'postMessage'
    * save the url of the uploaded file into post_meta or comment_meta.


###

### description



* We save files into a different server. There are many reasons for this especially the site gets bigger, ... and on distributed web server.
* To install a file server "git clone https://github.com/thruthesky/file-upload" and follow README.md instruction.
* User's secret key is my()->uniqid()

* 글을 쓸 때, 첨부 파일을 아래와 같이 저장한다.

    preg_match_all("/\/data\/upload\/[^\/]*\/[^\/]\/[^'\"]*/", $content, $ms);
    $files = $ms[0];
    post()->meta( $post_ID, 'files', $files );

    이는 아래와 같이 필요 할 때 읽을 수 있다.

    post()->meta( get_the_ID(), 'files')

    이렇게 하므로서 어떤 파일이 업로드되었는지 확인을 할 수 있다.
    
    

    * HOW-TO clean garbage files.

        * delete files on file server that are not used in the post/comment content.
        * to do this, first get all the list of files in file server and get all the list of file that are used in post content, comment content.
            * compare the list and find files of file server that are not exists in post/comment content and delete it.



# Installation

* git clone xforum

* activate it from admin page.

* test it.





# Tuning

* Do not consider image CDN. it's useless.
* Care about PING route trip time to fast connect to web server.
    * If user can connect nearest web server among distributed ones, that's better.



# Capsulate Code and make it in-dependency

* you make a button with some css and js code.
* make it as much in-dependency as it could be so it can be easily copied and pasted.
* sample code is "<?php forum()->list_menu_user()?>". it can be copied and pasted in anywhere without editing.







# Know Problems

bootstrap v4 and twenty sixteen theme conflicts.

Bootstrap is a well-known framework and it is not boot strap's problem.


