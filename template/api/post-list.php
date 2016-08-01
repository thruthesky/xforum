<?php
forum()->setCategory( in('slug') );
$args = [
    'cat' => forum()->term_id,
    'posts_per_page' => in('posts_per_page', 10),
    'paged' => in('page'),
];
$in = in();
$category = forum()->getCategory();
$posts = get_posts($args);
foreach( $posts as $post ) {
    if ( $post->post_author ) {
        $user = get_user_by( 'id', $post->post_author );
        $post->author_name = $user->user_nicename;
        $meta = get_post_meta( $post->ID );
        foreach( $meta as $k => $arr ) {
            $post->$k = $arr[0];
        }
        $post->comments = comment()->get_nested_comments_with_meta( $post->ID );
    }
}
?>
<div class="x-page post-list">
    <?php
    include DIR_XFORUM . 'template/api/header.php';
    ?>
    <div class="x-content">

        <h2><?php _text('POST List') ?></h2>

        <div class="posts">
        <?php foreach( $posts as $post ) {
            post( $post );
            ?>
            <div class="post"><?php echo post()->title()?></div>
        <?php } ?>
        </div>

    </div>
    <?php
    include DIR_XFORUM . 'template/api/footer.php';
    ?>
</div>
