<?php
$category = forum()->getCategory();
get_header();
?>
<style>
    .post-list {
        margin: 1em 0;
    }
</style>
    <h1>
        <?php _text("Forum Name")?>: <?php echo forum()->getCategory()->name?>
    </h1>



<?php forum()->button_write()?>
<?php forum()->button_list(['text'=>'TOP'])?>
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

            <?php include forum()->locateTemplate( forum()->slug, 'list-meta-top') ?>


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