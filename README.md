# xforum
New forum for withcenter company.



# documents

https://docs.google.com/document/d/1wuYQzA0qZlviz9vxW7bM2Zvbj5YpL5ubNLSxZte5kEo/edit#


# How to test

access index.php with "test=testAll" or "test=testForum", etc...

* the value of 'test' key is the name of the class file.


ie) curl http://work.org/wordpress-4.5.3/?test=testAll






# Code Tech

## No routing

* Routing is needed for Nice URL.

* Nice URL has no problem with article ( or individual post ) because you can just enable nice url option on admin page.

* But what about forum listing page?

    * No need. Because there is rear case that Search Engine will list forum list page as a result of a search.

    * And post write page, post edit page, post delete page, comment edit page ... and those likes are definitely not needed for nice url.
     
    * So, as long as individual posts have nice URL, we don't need Nice URL any more.






