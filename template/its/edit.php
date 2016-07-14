<?php
//include_once DIR_XFORUM . 'template/its/init.php'; // this is done by core.
?>
<?php get_header(); ?>

<?php



wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js', ['jquery']);

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

    form .buttons {
        float: right;
    }
    .file-upload-form {
        width: 240px;
        float: left;
        bottom: 0;
    }
    .selected {
        border: 1px solid #0073aa;
    }
</style>


<script>
    window.addEventListener('load', function(){
        ( function( $ ) {

            $('label:has(input:radio:checked)').addClass('selected');
            $('label:has(input:radio:not(:checked))').removeClass('selected');

            $("input[type=radio]").change(function() {
                $('label:has(input:radio:checked)').addClass('selected');
                $('label:has(input:radio:not(:checked))').removeClass('selected');

            });


            if( $("input[name='process']:checked").val() == 'P' ) {
                $("#percent").show();
            } else if( $("input[name='process']:checked").val() == 'A' ) {
                $("#evaluate").show();
            }

            
            $("input[name='process']").change(function () {

                $("#percent").hide();
                $("#evaluate").hide();

                if ($(this).val() == "P") {
                    $("#percent").show();
                }
                else if ($(this).val() == "A") {
                    $("#evaluate").show();
                }
            });

        }) ( jQuery );
    });
</script>


<div class="post-edit-box">

    <form action="?" method="post">
        <input type="hidden" name="forum" value="edit_submit">
        <input type="hidden" name="parent" value="<?php echo in('parent')?>">
        <?php if ( in('slug') ) { ?>
            <input type="hidden" name="slug" value="<?php echo in('slug')?>">
        <?php } else { ?>
            <input type="hidden" name="post_ID" value="<?php echo in('post_ID')?>">
        <?php } ?>
        <input type="hidden" name="response" value="view">


        <?php if ( in('parent') ) { ?>
            <div class="its-parent">
                <?php
                $parent = post()->get( in('parent') );
                if ( $parent ) {
                    echo $parent->post_title;
                }
                ?>
            </div>
        <?php } ?>

        <fieldset class="form-group">
            <label for="post-title">Title</label>
            <input type="text" class="form-control" id="post-title" name="title" placeholder="Input title..." value="<?php echo esc_html( post()->title() );?>">
        </fieldset>




        <?php
        $cats = forum()->getCategory()->config['category'];
        if ( $cats ) {
            ?>
            <fieldset class="form-group">
                <div class="caption">Category</div>
                <label class="radio-inline" for="category">
                    <input type="radio" name="category" value=""<?php if ( ! in('category') ) echo ' checked=1'?>> none
                </label>
                <?php
                foreach( $cats as $cat ) {
                    ?>
                    <label class="radio-inline" for="category">
                        <input type="radio" name="category" value="<?php echo $cat?>" <?php if ( $cat == post()->category ) echo 'checked=1'; ?>> <?php echo $cat?>
                    </label>
                    <?php
                }
                ?>
            </fieldset>
        <?php } ?>


        <?php
        $members = forum()->getCategory()->config['members'];
        if ( $members ) {
            ?>
            <fieldset class="form-group">
                <div class="caption">Worker</div>
                <?php
                foreach( $members as $member ) {
                    ?>
                    <label class="radio-inline">
                        <input type="radio" name="worker" value="<?php echo $member?>" <?php if ( $member == post()->worker ) echo 'checked=1'; ?>> <?php echo $member?>
                    </label>
                    <?php
                }
                ?>
            </fieldset>
        <?php } ?>


        <?php
        $members = forum()->getCategory()->config['members'];
        if ( $members ) {
            ?>
            <fieldset class="form-group">
                <div class="caption">In charge : who is in charge of this work?</div>
                <?php
                foreach( $members as $member ) {
                    ?>
                    <label class="radio-inline">
                        <input type="radio" name="incharge" value="<?php echo $member?>" <?php if ( $member == post()->incharge ) echo 'checked=1'; ?>> <?php echo $member?>
                    </label>
                    <?php
                }
                ?>
            </fieldset>
        <?php } ?>

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
            <?php foreach ( its::$process as $code => $text ) {
                if ( empty($text) ) continue;
                if ( $code == 'A' || $code == 'R' ) {
                    if ( ! forum()->admin() ) continue;
                }

                $p = post()->process;
                if ( empty($p) ) $p = 'N';
                ?>
                <label class="radio-inline">
                    <input type="radio" name="process" value="<?php echo $code?>" <?php if ( $code == $p ) echo 'checked=1'; ?>> <?php echo $text?>
                </label>
            <?php } ?>
        </fieldset>


        <fieldset id="percent" style="display:none;">
            <?php
            if ( post()->percentage != NULL ) $percent = post()->percentage;
            else $percent = 0;
            ?>
            <label class="caption" for="percentage">Percentage</label>
            <input id="percentage" name="percentage" type="range" min="0" max="100" step="1" value="<?php echo $percent; ?>" oninput="percentage_value.value=percentage.value"/>
            <output name="percentage_value"><?php echo $percent; ?></output>
        </fieldset>


        <fieldset id="evaluate" style="display:none;">
            <?php
            if ( post()->evaluate != NULL ) $evaluate = post()->evaluate;
            else $evaluate = 0;
            ?>
            <label class="caption" for="evaluate">Evaluation : </label>
            <input id="evaluate" name="evaluate" type="range" min="0" max="10" step="1" value="<?php echo $evaluate; ?>" oninput="evaluate_value.value=evaluate.value"/>
            <output name="evaluate_value"><?php echo $evaluate; ?></output>

            <label class="caption" for="evaluate-comment">Comment : </label>
            <input id="evaluate-comment" name="evaluate_comment" type="text" value="<?php echo post()->evaluate_comment; ?>"/>
        </fieldset>




        <fieldset class="form-group">
            <?php
            if ( $post ) {
                $content = post()->content();
            }
            else {
                $content = '';
            }
            $editor_id = 'new-content';
            $settings = array(
                'textarea_name' => 'content',
                'media_buttons' => false,
                'textarea_rows' => 16,
                'quicktags' => false
            );
            wp_editor( $content, $editor_id, $settings );
            ?>
        </fieldset>

        <hr/>
        <fieldset class="form-group">

        </fieldset>

        <div class="buttons">
            <input type="submit" value="Issue a task" class="btn btn-secondary">
            <a class="btn btn-secondary" href="<?php echo forum()->urlView( in('post_ID'));?>">Cancel</a>
        </div>
    </form>

</div>
<?php file_upload();?>

<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>

<?php get_footer(); ?>

