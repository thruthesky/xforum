<?php
include_once 'function.php';
forum()->setCategory( in('slug') );
if ( is_numeric(in('page')) && in('page') > 1 ) $page_no = in('page');
else $page_no = 1;
if ( $page_no == 1 ) include 'post-list-top.php';
$args = [
    'cat' => forum()->term_id,
    'posts_per_page' => in('posts_per_page', 10),
    'paged' => $page_no,
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
<section class="post-page" no="<?php echo $page_no?>">
    <h2><?php _text('POST List') ?></h2>
    <div class="desc">
        Page No. <?php echo $page_no?>
    </div>
    <div class="buttons">
        <button type="button" class="post-write-button btn btn-secondary">POST</button>
    </div>
    <div class="x-holder-post-write-form"></div>
    <div class="posts">
        <?php
        foreach( $posts as $post ) {
            post( $post );
            include 'view.php';
        }
        ?>
    </div>
</section>