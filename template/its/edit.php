<?php
include DIR_XFORUM . 'template/its/its.class.php';
?>
<?php get_header(); ?>

<?php



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

    <script type="text/javascript">
        jQuery(document).ready(function(){

            $("#process").change(function() {
                $("#percent").show();
            });

            if($('#process').is(':checked')) {
                    $("#percent").show();
            }

        });
    </script>
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
                <label for="post-title">Title</label>
                <input type="text" class="form-control" id="post-title" name="title" placeholder="Input title..." value="<?php echo esc_html( $post->title() );?>">
            </fieldset>



            <fieldset class="form-group">
                <div class="caption">Category</div>

                <input type="radio" name="category" value=""<?php if ( ! in('category') ) echo ' checked=1'?>> none

                <?php
                $cats = forum()->getCategory()->config['category'];
                foreach( $cats as $cat ) {
                    ?>
                    <label class="radio-inline">
                        <input type="radio" name="category" value="<?php echo $cat?>" <?php if ( $cat == post()->category ) echo 'checked=1'; ?>> <?php echo $cat?>
                    </label>
                    <?php
                }
                ?>
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
                <div class="caption">In charge : who is in charge of this work?</div>
                <?php
                $members = forum()->getCategory()->config['members'];
                foreach( $members as $member ) {
                ?>
                <label class="radio-inline">
                    <input type="radio" name="incharge" value="<?php echo $member?>" <?php if ( $member == post()->incharge ) echo 'checked=1'; ?>> <?php echo $member?>
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
                <?php foreach ( its::$priority as $num => $text ) {
                    if ( empty($text) ) continue;
                    ?>
                <label class="radio-inline">
                    <input type="radio" name="priority" value="<?php echo $num?>" <?php if ( post()->priority == $num ) echo 'checked=1'; ?>> <?php echo $text?>
                </label>
                <?php } ?>
            </fieldset>


            <fieldset class="form-group">
                <div class="caption">Process</div>
                <?php foreach ( its::$process as $num => $text ) {
                    if ( empty($text) ) continue;
                    ?>
                    <label class="radio-inline">
                        <input type="radio" name="process" value="<?php echo $num?>" <?php if ( post()->process == $num ) echo 'checked=1'; ?>> <?php echo $text?>
                    </label>
                <?php } ?>
            </fieldset>



            <b>@TODO :</b> If the work is in progress, let worker select what percentage he is in and how it on the list.
            <fieldset id="percent" style="display: none;">
                <?php
                if ( post()->percentage != NULL ) $percent = post()->percentage;
                else $percent = 0;
                ?>
                <label class="caption" for="percentage">Percentage</label>
                <input id="percentage" name="percentage" type="range" min="0" max="100" step="1" value="<?php echo $percent; ?>" oninput="percentage_value.value=percentage.value"/>
                <output name="percentage_value"><?php echo $percent; ?></output>
            </fieldset>


            <?php if( forum()->isEdit() ) { ?>
                <fieldset class="form-group">
                    <div class="caption">Work evaluation</div>
                    <label class="radio-inline">
                        <input type="radio" name="evaluation" value="A" <?php if ( post()->evaluation == 'A' ) echo 'checked=1'; ?>> Approved
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="evaluation" value="R" <?php if ( post()->evaluation == 'R' ) echo 'checked=1'; ?>> Rejected
                    </label>
                </fieldset>
            <?php } ?>

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

