<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 *
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
    return;
}

wp_enqueue_script('comment', URL_XFORUM . 'js/comment.js');
//wp_enqueue_style( 'forum-comments-basic', FORUM_URL . 'css/forum-comments-basic.css' );




?>
<style>
    .file-upload-form .file-upload {
        max-width: 160px;
    }
</style>
<script>
    ///
    var post_process = "<?php echo post()->process?>";
    var post_percentage = "<?php echo post()->percentage?>";
</script>




<?php
function comments_basic($comment, $args, $depth) {
comment()->set($comment);
$parent_comment = null;
if ( $comment->comment_parent ) $parent_comment = get_comment($comment->comment_parent);
?>
<li <?php comment_class( 'comment ' . empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>" comment_ID="<?php comment_ID() ?>">
    <div class="content">
        <div class="meta">
            <?php if ( $parent_comment ) : ?>
                <?php echo get_comment_author(); ?> commented on <?php echo get_comment_author($parent_comment)?> at
            <?php else : ?>
                <?php echo get_comment_author(); ?> wrote at
            <?php endif; ?>
            <?php printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time() ); ?>
            <div>
                <?php _text('Comment No.') ?>: <?php echo $comment->comment_ID?>
            </div>
        </div>

        <?php
        $files = comment()->meta('files');
        if ( $files ) {
            foreach ( $files as $file ) {
                echo "<img src='$file'>";
            }
        }
        ?>

        <div class="text">
            <?php comment_text(); ?>
        </div>

        <div class="buttons">
            <span class="reply"><?php _text('Reply') ?></span>
            <span class="edit"><?php _text('Edit') ?></span>
            <span class="delete"><a href="<?php comment()->urlDelete()?>"><?php _text('Delete') ?></a></span>
            <span class="report" title="This function is not working, yet."><?php _text('Report') ?></span>
            <span class="like" title="This function is not working, yet."><?php _text('Like') ?></span>
        </div>
    </div>
    <hr>
    <?php
    } /** EO comments_basic callback */
    ?>



    <script>
        window.addEventListener('load', function(){
            ( function( $ ) {


                $("body").on('change', "input[name='process']", function () {
                    $("#percent").hide();
                    if ($(this).val() == "P") {
                        $("#percent").show();
                    }
                });

            }) ( jQuery );
        });
    </script>


    <script type="text/template" id="comment-form-template">
        <%
        if ( typeof comment_ID == 'undefined' ) comment_ID = 0;
        if ( typeof text == 'undefined' ) text = '';
        else text = s(text).trim().value();
        %>
        <section class="comment-form" parent_ID="<%=parent_ID%>" comment_ID="<%=comment_ID%>">

            <form action="<?php echo home_url('index.php')?>" method="post" name="comment" id="comment">
                <input type="hidden" name="forum" value="comment_edit_submit">
                <input type="hidden" name="post_ID" value="<?php the_ID()?>">
                <input type="hidden" name="comment_ID" value="<%=comment_ID%>">
                <input type="hidden" name="comment_parent" value="<%=parent_ID%>">
                <input type="hidden" name="response" value="view">
                <input type="hidden" name="files" value="">




                <fieldset class="form-group">



                    <% if ( typeof process != 'undefined' ) { %>
                    <div class="caption">Process</div>
                    <label class="radio-inline">
                        <input type="radio" name="process" value="N"<%= process == 'N' ? ' checked=1' : '' %>> <?php _text('Not started.') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="process" value="P"<%= process == 'P' ? ' checked=1' : '' %>> <?php _text('Progress (Started).') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="process" value="F"<%= process == 'F' ? ' checked=1' : '' %>> <?php _text('Finished.') ?>
                    </label>


                    <% if ( process == 'P' ) { %>
                    <fieldset id="percent">
                        <% } else { %>
                    <fieldset id="percent" style="display:none;">

                        <% } %>
                        <%
                        if ( typeof percentage == 'undefined' || percentage == '' ) percentage = 0;
                        %>
                        <label class="caption" for="percentage"><?php _text('Percentage') ?></label>
                        <input id="percentage" name="percentage" type="range" min="0" max="100" step="1" value="<%=percentage%>" oninput="percentage_value.value=percentage.value"/>
                        <output name="percentage_value"><%=percentage%></output>
                    </fieldset>




                    <% } %>






                <div class="line comment-content">
                    <label for="comment-content" style="display:none;">
                        <?php _text('Comment Content') ?>
                    </label>
                    <textarea id="comment-content" name="comment_content" placeholder="<?php _text('Please input comment')?>"><%=text%></textarea>
                </div>
                <div class="photos"></div>
                <div class="files"></div>

                <?php file_upload('comment')?>


                        <div class="line buttons">
                    <div class="submit">
                        <input class="comment-submit-button" type="submit" value="Submit" name="submit">
                        <% if ( comment_ID ) { %>
                        <button class="comment-cancel-button" type="button"><?php _text('Cancel') ?></button>
                        <% } %>
                    </div>
                </div>

            </form>




        </section>
    </script>

    <div id="comments" class="comments-area">

        <script>
            window.addEventListener( 'load', function() {
                jQuery( function( $ ) {
                    var t = _.template($('#comment-form-template').html());
                    $('.comments-area').prepend(t({ parent_ID : 0, process: post_process, percentage: post_percentage }));
                });
            });
        </script>

        <?php if ( have_comments() ) : ?>

            <div class="comments-title"><?php printf('No. of Comments: %d', get_comments_number()); ?></div>

            <?php /** @todo Version compatibility */if ( function_exists('the_comments_navigation') ) the_comments_navigation(); ?>

            <ol class="comment-list">
                <?php
                wp_list_comments( array(
                    'avatar_size' => 42,
                    'callback' => 'comments_basic'
                ) );
                ?>
            </ol><!-- .comment-list -->

            <?php /** @todo Version compatibility */if ( function_exists('the_comments_navigation') ) the_comments_navigation(); ?>

        <?php endif; // Check for have_comments(). ?>

    </div><!-- .comments-area -->


