<?php get_header(); ?>

<?php
//di( forum()->getCategory() );

wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');

if ( in('post_ID') ) {
    forum()->setCategoryByPostID( in('post_ID') );
    $post = post( in('post_ID') );
}
else {
    $post = post();
}


?>

    <style>
        .post-edit-box {
            position: relative;
        }
        .post-edit-box .buttons {
            text-align:right;
        }
        .file-upload-form {
            position: absolute;
            bottom: 0;
        }
    </style>

    <div class="post-edit-box">

        <form action="?" method="post">
            <input type="hidden" name="forum" value="edit_submit">
            <input type="hidden" name="response" value="view">
            <?php if ( in('slug') ) { ?>
                <input type="hidden" name="slug" value="<?php echo in('slug')?>">
            <?php } else { ?>
                <input type="hidden" name="post_ID" value="<?php echo in('post_ID')?>">
            <?php } ?>
            <input type="hidden" name="on_error" value="alert_and_go_back">

            <fieldset class="form-group">
                <label for="post-title">Issue Title</label>
                <input type="text" class="form-control" id="post-title" name="title" placeholder="Input title..." value="<?php echo esc_html( $post->title() )?>">
            </fieldset>



            <fieldset class="form-group">
                <label for="worker">Worker</label>
                <input type="text" class="form-control" id="worker" name="worker" placeholder="Input worker" value="<?php e( post()->worker )?>">
            </fieldset>

            <fieldset class="form-group">
                <label for="incharge">Who is in charge?</label>
                <input type="text" class="form-control" id="incharge" name="incharge" placeholder="Input who is in charge" value="<?php e( post()->incharge ) ?>">
            </fieldset>

            <fieldset class="form-group">
                <label for="dead-line">Deadline</label>
                <input type="date" class="form-control" id="dead-line" name="deadline" placeholder="Input who is in charge" value="">
            </fieldset>



            <fieldset class="form-group">
                <div class="caption">Priority</div>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="N"> Never mind
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="L"> Low
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="M"> Medium
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="H"> High
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="I"> Immediate
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="C"> Critical
                </label>
            </fieldset>


            <fieldset class="form-group">
                <div class="caption">Work process</div>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="N"> Not started
                </label>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="S"> Started
                </label>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="P"> In Progress
                </label>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="F"> Finished
                </label>
            </fieldset>



            <?php if ( forum()->isEdit() ) { ?>
                <fieldset class="form-group">
                    <div class="caption">Work evaluation</div>
                    <label class="radio-inline">
                        <input type="radio" name="difficulty" value="A"> Approved
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="difficulty" value="R"> Rejected
                    </label>
                </fieldset>
            <? } ?>





            <fieldset class="form-group">
                <?php
                if ( $post ) {
                    $content = $post->content();
                }
                else {
                    $content = '';
                }
                $editor_id = 'new-content';
                $settings = array(
                    'textarea_name' => 'content',
                    'media_buttons' => false,
                    'textarea_rows' => 5,
                    'quicktags' => false
                );
                wp_editor( $content, $editor_id, $settings );
                ?>
            </fieldset>
            <div class="buttons">
                <input type="submit" value="Issue a task">
                <button type="button">Cancel</button>
            </div>
        </form>

        <?php file_upload()?>

    </div>

<?php get_footer(); ?>