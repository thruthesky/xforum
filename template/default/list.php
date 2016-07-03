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
        $posts = get_posts(
            [
                'category' => $category->term_id,
            ]
        );

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
        <?php } ?>
    </div>

<?php get_footer(); ?>