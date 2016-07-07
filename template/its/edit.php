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
                <input type="text" class="form-control" id="post-title" name="title" placeholder="Input title..." value="<?php echo esc_html( $post->title() );?>">
            </fieldset>

            <fieldset class="form-group">
                <div class="caption">Worker</div>
                <?php
                $members = forum()->getCategory()->config['members'];
                foreach( $members as $member ) {
                ?>
                <label class="radio-inline">
                    <input type="radio" name="worker" value="<?php echo $member?>" <?php if ( $member == post()->worker ) echo 'checked=1'; ?>> <?php echo $member?>
                </label>
                <?php
                }
                 ?>
            </fieldset>


            <fieldset class="form-group">
                <label for="dead-line">Deadline</label>
                <input type="date" class="form-control" id="dead-line" name="deadline" placeholder="Input who is in charge" value="<?php e( post()->deadline ); ?>">
            </fieldset>



            <fieldset class="form-group">
                <div class="caption">Priority</div>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="N" <?php if ( post()->priority == 'N' ) echo 'checked=1'; ?>> Never mind
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="L" <?php if ( post()->priority == 'L' ) echo 'checked=1'; ?>> Low
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="M" <?php if ( post()->priority == 'M' ) echo 'checked=1'; ?>> Medium
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="H" <?php if ( post()->priority == 'H' ) echo 'checked=1'; ?>> High
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="I" <?php if ( post()->priority == 'I' ) echo 'checked=1'; ?>> Immediate
                </label>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="C" <?php if ( post()->priority == 'C' ) echo 'checked=1'; ?>> Critical
                </label>
            </fieldset>


            <fieldset class="form-group">
                <div class="caption">Work process</div>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="N" <?php if ( post()->difficulty == 'N' ) echo 'checked=1'; ?>> Not started
                </label>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="S" <?php if ( post()->difficulty == 'S' ) echo 'checked=1'; ?>> Started
                </label>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="P" <?php if ( post()->difficulty == 'P' ) echo 'checked=1'; ?>> In Progress
                </label>
                <label class="radio-inline">
                    <input type="radio" name="difficulty" value="F" <?php if ( post()->difficulty == 'F' ) echo 'checked=1'; ?>> Finished
                </label>
            </fieldset>


            <b>@TODO :</b> If the work is in progress, let worker select what percentage he is in and how it on the list.




            <?php if ( forum()->isEdit() ) { ?>
                <fieldset class="form-group">
                    <div class="caption">Work evaluation</div>
                    <label class="radio-inline">
                        <input type="radio" name="evaluation" value="A" <?php if ( post()->evaluation == 'A' ) echo 'checked=1'; ?>> Approved
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="evaluation" value="R" <?php if ( post()->evaluation == 'R' ) echo 'checked=1'; ?>> Rejected
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

    </div>
<?php file_upload();?>

    </div>

<?php get_footer(); ?>