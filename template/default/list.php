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
        $query = new WP_Query(
            [
                'cat' => $category->term_id,
                'posts_per_page' => 3,

            ]
        );

        di($query->have_posts());





        if ( $posts ) { ?>
            <table class="table">

                <?php
                foreach ( $posts as $post ) {
                    setup_postdata( $post );
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
                            <?php the_date()?>
                        </td>
                    </tr>
                <?php } ?>
            </table>


<?php
            global $wpdb;
            echo "<pre>";
            print_r($wpdb->queries);
            echo "</pre>";

            di( $wp_query );


            ?>

            <?php the_posts_pagination( array(
                'mid_size' => 2,
                'prev_text' => __( 'Back', 'textdomain' ),
                'next_text' => __( 'Onward', 'textdomain' ),
            ) ); ?>

        <?php } ?>
    </div>

<?php get_footer(); ?>