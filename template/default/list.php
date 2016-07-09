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

    <div class="post-list">
        <?php
        $page = in('page');
        $query = new WP_Query(
            [
                'cat' => $category->term_id,
                'posts_per_page' => forum()->meta( 'posts_per_page' ),
                'paged' => $page,

            ]
        );

        if ( $query->have_posts() ) { ?>
            <table class="table">

                <?php
                while ( $query->have_posts() ) {
                    post()->setup( $query );
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
                            <?php echo get_the_date()?>
                        </td>
                    </tr>
                <?php } ?>
            </table>


            <?php include forum()->locateTemplate( forum()->slug, 'pagination') ?>


<?php

/*
            global $wpdb;
            echo "<pre>";
            print_r($wpdb->queries);
            echo "</pre>";

            di( $query );
*/

            ?>

        <?php } ?>
    </div>

<?php get_footer(); ?>