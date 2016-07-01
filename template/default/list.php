<?php
$category = forum()->getCategory();
get_header();
?>


    <h1><?php echo in('id') ?> LIST PAGE</h1>

    <a class="btn btn-primary" href="?forum=edit&id=<?php echo $category->slug?>">Write</a>



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