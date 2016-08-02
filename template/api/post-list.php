<?php
forum()->setCategory( in('slug') );
if ( in('page') < 1 ) $page_no = 1;
else $page_no = in('page');
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
    <script type="text/template" id="post-write-template">
        <div class="form post-write">
            <form>
                <input type="hidden" name="do" value="post_edit_submit">
                <input type="hidden" name="session_id" value="<?php echo in('session_id')?>">
                <input type="hidden" name="content_type" value="text/plain">
                <input type="hidden" name="response" value="template/api/view">
                <input type="hidden" name="slug" value="<?php echo in('slug')?>">
                <input type="hidden" name="post_ID" value="">
                <input type="text" name="title">
                <textarea name="content"></textarea>
                <div class="message loader"></div>
                <button type="button" class="post-write-submit">Submit</button>
                <button type="button" class="post-write-cancel btn btn-secondary btn-sm">CANCEL</button>
            </form>
        </div>
    </script>


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
            include DIR_XFORUM . 'template/api/view.php';
        }
        ?>
    </div>
</section>