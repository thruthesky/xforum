<?php
$category = forum()->getCategory();
get_header();
?>


    <h1><?php echo in('slug') ?> LIST PAGE</h1>

    <a class="btn btn-primary" href="<?php forum()->urlWrite()?>">Write</a>

<?php

//di(forum()->getMeta('category', 'ini'));
//di ( forum()->getCategory()->config );



?>

<pre>
admin ( manage member, issue, everything ),
member,
    => level 0~9.
project,
    => forum category is the project.
    => forum category admin is the managers of the project.

    => forum members are the members of its.
    => forum admins are the its admin who can do anything to category.

    => project statistics. ( who have many works. who have done many works )
    =>
    => issues,
        => Issue information:
            => worker ( who is working on this issue? )
            => in-charge ( who will be responsible for this work? )
            => dead-line
        => issue status:
            => work priority 1~9( is it urgent? ) never mind, low, medium, high, immediate, critical(don't do anything before this).
            => work difficulty : very low, medium, high, very high.
            => work process ( where is the work or when it will be finished? ). proposal, work started, in-process(how many percent it's done), alpha, beta(test), finished(wait for approval), done(approved), rejected( not-approved. do it again )
            => work evaluation ( how is the work? ) : buggy, good, bad(work is not good. Maybe the source code is too complicated. ),

How it work:
=> if admin want to something that project manager can, then he must become project manager also.
=> There may be more than 1 manager.
=> close ( is the work finished and finalized? ). Only project manager can close the issue.
=> open : anyone can open.
=> Only worker, in-charge and manager can change the work status.
=> Only manager can change issue info.

Search & Sort :
    => Search all project or some / one project.
    => Memo upto 10 search option. like; over due ( by workers/in-charge ), list critical, list dead-line within a week
    => member by workers, in-charge,
    => deadline ( today, within a week, within a month, over due, over one week, over one month )
    => issue information( worker, in-charge, dead-line)
    => issue status( priority, difficulty, process, evaluation )
    => date of issue ( recently updated, recently opened/created, oldest opened, new comment )

</pre>








<?php




$posts = get_posts(
    [
        'category' => $category->cat_ID,
    ]
);


foreach ( $posts as $post ) {
    setup_postdata( $post );
    ?>

    <div>
        <a href="<?php the_permalink()?>"><?php the_title()?></a>
    </div>

    <?php
}

?>



<?php get_footer(); ?>