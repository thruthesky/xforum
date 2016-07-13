# xforum
New forum for withcenter company.

# documents

https://docs.google.com/document/d/1wuYQzA0qZlviz9vxW7bM2Zvbj5YpL5ubNLSxZte5kEo/edit#

# ITS

* http://dev.withcenter.com/ 에서 모든 작업을 관리한다.

* 모든 TODO 나 모든 작업을 위 ITS 옮긴다.

    * 모든 문서를 다 찾아서 하나 하나 재 검토하고 옮긴다.

  

  
## CHANGES
  
* 2016-07-13 Now, all pages ( list, view, edit, edit_submit, comment_edit_submit, post_delete_submit, comment_delete_submit and all)

        are loading 'init.php' of the template.
        
        
  
# Work Environment

* DB server & File server are on http://dev.withcenter.com
    ( 소스는 현재 컴퓨터에 있고, DB 서버와 파일 서버를 실제서버에서 하는 경우 )
    ( 중요 : DB 서버와 ping 에서 time 이 100 이하로 나와야 작업을 할 수 있다. 아니면 시간이 너무 오래 걸림 )
    * Needs to edit DB info and Site address info.
    * see https://docs.google.com/document/d/1_eq21Xj2uBApeu9P-FmYQtLGcHL92oR8h_Impg02IBs/edit#heading=h.si5iimehk6gw
    * How to setup DB information and Site information.
* ITS is also in http://dev.withcenter.com



# TODO

* MOVE ALL Work DATA to ITS

* ITS 서버 백업.

* put OG_ImAGE tag. on ... wp_head. get the first image or featured image for the content.
    * put default image when there is no image to show.


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

* @warning. somehow, when i work WordPress on my computer but the real site and db server is on different server, a post had disappeared.

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
    필리핀 대사관 정보를 자동 등록한다.
    


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





# How to run script / How to generate fake data.


Run script like below;

$ curl "http://work.org/wordpress-4.5.3/?script=post-generate"


# How to use/manage on admin
* when a forum loads category, it loads its meta data and put it all together in $category



## category configuration

* input category on admin configuration page with comma separated.

    forum()->getCategory()->config['category']

## depcreated of category ini format.

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





# Submission & Response - How submission works.

* When you submit a form of xforum, you do the form submission as

    * ordinary form submit ( a form submit and redirect into another page )
    
    * ajax call ( a forum submit with ajax and get return through JSON string )


    * form options of return urls.
    
        * return_url for success. ( You can add query var as "&success=true"
        * return_url_on_error for error.

        * examples

            <input type="hidden" name="return_url" value="<?php forum()->urlAdminImport()?>&amp;success=true">
            <input type="hidden" name="return_url_on_error" value="<?php forum()->urlAdminImport()?>">

    * if there is no put return urls in the form

        then, 'response' will be used secondly.

        'response' has three values.

            'list' - will list the posts of the fourm after submission.

            'view' - will view the post after submission.

            'ajax' - will echo JSON string with post information after submission.

    * if there is no 'return urls', nor 'response',

        it will just echo some HTML contents in json.

    * 2016-07-10 'on_error' option will not be used any more.


If you need to get return data in Ajax or you need to get error in JSON String, use 'reponse' with 'ajax'.


    * When form is submitted with 'response'='list',
    
        if there is error, it will be displayed on the browser with 'wp_die()'

    * Logially, 'reponse' and return_url_on_error can be used at the same time.
    


## Form Submission and Rewrite

WordPress uses rewrite for nice url.

if rewrite is like "/wordpress" and a form's action is "/wordpress", it may redirect to a wrong page.

So, the best way to submit to is to send to index.php like below.

    <form action="<?php echo home_url('index.php')?>" method="post">






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


## How to use forum configuration

Use forum config like below.

    forum()->getCategory()->config['members']

* comma(,) separated values will be automatically parsed into array.


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



## xforum_admins

xforum_admins in setting(option) tells who can manage all the forum.





# FILE SERVER

See README.md of file-upload GIT project for more detail.

        https://github.com/thruthesky/file-upload


## Installation of File Server

See README.md of file-upload GIT project for more detail.

        https://github.com/thruthesky/file-upload



The setting var - xforum_url_file_server will have the url of the file server.

## File upload

Developers must fully understand about file upload to apply t in his need.


## To Upload

* create a form ( independent form from editing form )
    * set the domain, uid and action of the form.
    * submit through a hidden iframe.

* when submit
    * listen 'message' event since the hidden iframe will send back a message with 'postMessage'
    * save the url of the uploaded file into post_meta or comment_meta.



## description

* We save files into a different server. There are many reasons for this especially the site gets bigger, ... and on distributed web server.
*
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




# IMPORT / EXPORT

URL : http://work.org/?forum=export&slug=its

Use export & import when you need to copy posts of a category into another category or even another site.

You can use this as a backup. If you need to backup, simply save the JSON string into a file.

When you export, you will get JSON string containg all the posts of the category and its meta datas.

    - copy the JSON string and paste it into admin page's IMPORT menu.

* This was made for test purpose.

    * you need posts of production server on your local machine to test.

* This was not tested and not recommended for a big category of manay posts.


* WARNING : You need to copy the JSON string from the source view page. or it may causes propblems.




# OG Tags


* xforum does OG Tags only for forum list and is_single().

    * which means, you need to do it manually for page specific content.
    
    * and Don't let other function (like plugins, themes, etc) do the OG tags for is_single().
    




# AJAX SEARCH


## Request Parameters


forum = ajax_search

@todo change method of ajax_search to proper REST API. 


slug = forum slug. if it is empty, then all forum will be searched.
title_only = 1 or empty. by default, title+content are searched. but title_only=1, then, it will only search title.

return: html.





# REST API - Ajax with JSON data.

* refer for idea : http://v2.wp-api.org/

## IMPORTANT THINGS TO CONSIDER

### ONE PAGE APP

* If you are going to build one page app

    * Be sure you add all <a> tags on link to do SEO work.
    
    * Be sure when a new content is loaded, change the link of the browser with hash tag (#)
    
        * So, people can copy the URL and paste it in their website.
    
    * OR BE sure you provide proper sitemap.xml and submit it to search engine.
    



## PROTOCOLS

* ?forum=api&action=user_login&id=....&password=....&rememberme=.....
* ?forum=api&action=post_list&...[search options]...
* ?forum=api&action=post_get&post_ID=....
* ?forum=api&action=post_write&slug=....
* ?forum=api&action=post_edit&post_ID=....
* ?forum=api&action=post_delete&post_ID=....
* ?forum=api&action=post_vote&post_ID=....&mode=[good|bad]
* ?forum=api&action=post_report&post_ID=....&reason=....
* ?forum=api&action=post_comment_write&parent_ID=....
* ?forum=api&action=post_comment_edit&comment_ID=....
* ?forum=api&action=post_comment_delete&comment_ID=....
* ?forum=api&action=post_comment_vote&post_ID=....&mode=[good|bad]
* ?forum=api&action=post_comment_report&post_ID=....&reason=....
* FILE UPLOAD PROTOCOL : must follow the way how file server works.


### TEST FOR PROTOCOLS

* Crate test codes




### Search Options

You can input all the search options of WP_Query.

By query directly to WP_Query with full support WP_Query arguments, you can get what ever posts. like posts which have file uploads by searching meta 'files'



* slug - is the slug of the forum
* posts_per_page - is the number of posts
* s - is the search keyword






