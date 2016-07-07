<?php
$category = forum()->getCategory();
get_header();
?>
    <style>
        .post-list {
            margin: 1em 0;
        }
    </style>
    <h1><?php echo in('slug') ?> LIST PAGE</h1>

<?php forum()->list_menu_write()?>
<?php forum()->list_menu_user()?>


combination of search items:


worker,
incharge,
date of a work begin ( begin/end date option. so you know works began in the last month )
deadline ( work of deadline in a week, in a day, ... give begin/end date of deadline. so you know deadline work of today, next week, last month )
newly comments( day option. how old days of the comment is considers as new. )
priority,
process,
percentage of work,
title search,
title + content search,







    <div class="post-list">
        <?php
        $posts = get_posts(
            [
                'category' => $category->term_id,
            ]
        );

        if ( $posts ) { ?>
            <table class="table">

                <?php
                foreach ( $posts as $post ) {
                    post()->setup( $post );
                    ?>
                    <tr>
                        <td>
                            <a href="<?php the_permalink()?>">
                                <?php the_title()?>
                                <?php forum()->count_comments( get_the_ID() ) ?>
                            </a>
                        </td>
                        <td>
                            <?php the_author()?>
                        </td>
                        <td><?php echo post()->getNoOfView( get_the_ID() )?></td>

                        <td>
                            <?php e( post()->meta('deadline') ) ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

<?php get_footer(); ?>