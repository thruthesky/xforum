<?php
$category = forum()->getCategory();
get_header();
?>


    <h1><?php echo in('slug') ?> LIST PAGE</h1>

    <a class="btn btn-primary" href="<?php forum()->urlWrite()?>">Write</a>

<?php


$posts = get_posts(
    [
        'category' => $category->term_id,
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

<div>
    End of list
</div>

<?php get_footer(); ?>