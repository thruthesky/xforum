# XAPP

A framework which has some fundamental functions to serve for app(web-app) with the backend of xforum. 




# IMPORTANT THING TO KNOW / TO CONSIDER

* It only works with xforum.

* see "### ONE PAGE APP" in xforum README.md

* Try to put 'a' tag as much possible. Even though search bots cannot understand javascript and they are not able to load more on endless page, it is worth to give a link.


* Why does it not open a new page when a user wants to edit his post/comment?

    * Because of the speed.
    
    * When a user uses same wireless internet (3G, 4G), mobile looks more slow than other devices.
    
        And we focus on mobile.
        


# ITS

* see 'housemaid' its on http://dev.withcenter.com



# TODO

* see - https://docs.google.com/document/d/1xObimH0kQsx1ixUGRT7HTVKIL27l0bEjIKpNa16_mFc/edit
* xapp.markup_xxxx 을 markup = xapp.markup 으로 하고, 함수를 분리한다.
* Facebook Login API is for web for now. It uses cookie. it wouldn't work on app since it is web app and using cookie.
    * you need to get it for app version.
* 글 쓸 때, $.post() 를 사용하는데, 글 데이터를 data 속성에 넣어야 한다.



# INSTALLATION

* Add cors for 'font-awesome font' in nginx.conf ( nginx 에 아래를 추가해서 cors 를 모두 무시하도록 한다. )


    	location / {
    		add_header 'Access-Control-Allow-Origin' '*';
    		add_header 'Access-Control-Allow-Credentials' 'true';
    		add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS';
    		add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
    	}


* Check if css.php and js.php works with gzip or deflate.




# HOW TO

* access the index.html with "file://" scheme like "file:///C:/work/www/wordpress46b1/wp-content/plugins/xforum/tmp/xapp/index.html"
 


# DOM Structure of xapp

* All element must have only wrapper element.

For instance,

    * FORM must have a wrapper like "<div><form></form></form>"
    
    * Title, content, button and all other element must have a wrapper element.
    
        * For title,
        
            <div class=wrapper><div class=title>...</div></div>
    
        * Buttons 
            <div class=wrapper>
                <div class=buttons>
                    <div class=wrapper>
                        <button>

* Functions of x works on the wrapper element.

    * for instance, when it gets an element for a FORM, it gets the wrapper which includes the FORM tag.
    
    * so, when you submit the form, you need to
    
        x.getForm().find('form').submit()
        
        or
        
        x.submit();
        



# Coding Guide


## x Class

* x class is only for manipulating DOM of xapp.

    * it shall not include function, variables, option values, etc...
    
    * it shall not contain methods like 'alert', 'parsing query string', 'move', 'reload', 'refresh', 'ajax', etc...
    
    * it really ONLY have manipulating methods only.
    

## Element class, selector, element object

for element class name holder, just use variable name - 'comment_write_button'
for element selector, use sel('comment_write_button')
for element object, use ele('comment_write_button')


## Getting Object and HTML of a node.

if a function name begins with 'get_' then, it returns the HTML of the node or value of the node.
if a function name begins without 'get_', then it returns the jQuery object of the node.

if a function name conflicts with 'element class name', then use 'find_' for singular, 'find______s' for plural


example:
 
    * markup.get_comments_meta_count( post.comment_count ) return 'HTML Markup'
    * get_comments_meta_count( post_ID ) returns the 'no of comments'.
    * comments_meta_count( post_ID ) return 'jQuery object of the node'

example of real code;

    * comments_meta_count( post_ID ).html( markup.get_comments_meta_count( get_comments_meta_count(post_ID) + 1 ) );




## CSS & Javascript loading

* xapp loads CSS & JS thru PHP and gets the benefits of mixing CSS & JS & PHP. ( php 로 CSS 와 JS 를 로딩해서 compile 이나 304 not modified 등에 활용한다. )

    * the purpose of using this is to manage css & js files easy to use.


### How to use it.

* try css.php and js.php in web browser with the options.
* when "?debug=true", it does not do '304 test'. ( debug=true 이면, "304 not modified" 를 사용하지 않는다. 즉, 수정하는 즉시 바로 웹브라우저에 반영된다.)
* when "?compile=true", it compiles all the (css/js) files into one. ( compile=true 이면, 하나의 파일로 묶어서 리턴한다. 이 값을 지정하지 않으면 각각의 자바스크립트가 로드된다.)
* when "?version=xxxxxx", it delivers the version tag to its (css/js files). By using tag version, it will update the browser cache and server cache.
    * try something like "http://work.org/xapp/core/js.php?version=2016072102&debug=true&compile=true" and change option values and see what is happening.

* IMPORTANT : One thing you have to remember is that, when you don't compile all css into one file, it is JAVASCRIPT which will load all the css files using add_css() function.
    * So, USE when '<link ...>' WHEN 'compile=true'
    * USE '<script src="...."></script>' tag WHEN 'compile' is not 'true' !!

* IMPORTANT : Since javascript css/js loading needs 'body' tag loaded first, IF you are going to load css/js with add_css()/add_javascript() function, you must put it inside 'body' tags.

    * which means, css.php with 'compile=true' can be put inside '<head>' tag.
    * css.php without 'compile=true' must be put inside '<body>' since it uses 'add_css()'.
    * 'js.php' should be put at the bottom of '<body>' tag. ( right before '</body>' )
    

i.e.) for development mode, <script src="http://work.org/xapp/core/css.php?version=201607215&debug=true"></script>
i.e.) for production mode, <link rel="stylesheet" href="http://work.org/xapp/core/css.php?version=201607210001&debug=true&compile=true" />

        


* NOTE: For development, the best use is "?version=xxx&debug=true&compile=false", For production "?version=xxx&compile=true".

* WARNING : Javascript lading with 'compile=false' ( or without compile=true ) is not working at the time.

    * There is dependency problem. it looks like all javascript files are really loaded in async.
    
        * one solution may be compile all xapp  js files into one.

    * Use individual js link for development.
    
        * Use 'compile=true' for production mode.
        
    



## CALLBACKS AND Overriding


* By using callbacks, you can separate and capsulise code blocks.

    * For instance, you can put all the layout works in layout.js using callbacks.
    

* You can also override the default callbacks.

    * theme.js is the best place you can override the defaults( functions, variables, etc )


## Overriding

* You can overrides not only callbacks but also all the classes and functions.

    * try to override xapp.get_post_list_header() on theme.js
    


## Template & Layout

* There is only one (1) HTML file which is 'index.html'

* There is no ajax content load which MEANS 'view page' of a post or content MUST BE redirected to (or reload) index.html

    with different query vars.
    
    * see


* The PROTOCOL is exactly the same as "## PROTOCOLS" in "xforum README.md"

    Just redirect to (or reload) index.html with the PROTOCOL



## Cache

If you have no 'id' in parameter, then it is just the same as xapp.get()

( xapp.cache() 에 id 값을 주지 않으면 그냥 xapp.get() 과 동일하다. 특히, 게시판 목록을 할 때에는 캐시를 하면 안된다. 따라서 id 값을 주지 않는다. )

    xapp.cache( { 'url' : '...' } );

It just does the same as $.get()



## User Login & Logout & User information display

* 'xapp.login.js' & 'xapp.markup.js' are the scripts that will do all the work for user relation process.


### User login & logout display

When 'xapp.login.js' boots, it checks 'session_id'.

만약, session_id 에 값이 있으면 사용자가 로그인을 한 것으로 한다.

xapp.login() 은 사용자가 로그인을 했으면 사용자 아이디(사용자 아이디가 없으면 session_id)를 리턴하고, 아니면 false 를 false 한다.

element-user-login 클래스는 사용자 로그인을 했으면 보여지고,

element-user-logout 클래스는 사용자 로그아웃을 했으면 보여진다.


.user-login-value 에는 사용자의 user login 값이 들어간다.
.session-id-value 에는 사용자의 sesion_id 값이 들어간다.

.user-login-button 을 클릭하면 로그인 창을 layout.main() 상단에 보여준다.
.user-register-button 을 클릭하면 회원 가입 창을 layout.main() 상단에 보여준다.
.user-account-button 을 클릭하면 사용자 정보(수정 정보 포함)를 layout.main() 상당에 보여주어야 한다.

.user-logout-button 을 클릭하면 사용자 로그아웃을 한다. ( 단순히 session_id 를 db 에서 지우고, 리프레시를 하면 된다. )



## POST, POST Management


Post related actions will be triggered when one of the css class will be clicked.


post-edit-button for Edit
post-delete-button for Delete
post-vote-button for Vote
post-report-button for Report
post-copy-button for Copy
post-move-button for Move
post-blind-button for Blind
post-block-button for Block



## Permission


* even though you are admin, you cannot edit other's post if you use ssesion_id.





# Cycle

xapp.start()
 
    ==> if is front page()
 
            ===> display fornt page.
    
    ==> if post_list_page
    
            ===> xapp.wp_query() ==> xapp.cache()
    
    