<?php get_header(); ?>

<?php
/*Custom CSS*/
wp_enqueue_style( 'xforum-edit', URL_XFORUM . 'css/its/forum-edit.css' );

if ( in('post_ID') ) {
    forum()->setCategoryByPostID( in('post_ID') );
    $post = post( in('post_ID') );
}
else {
    $post = post();
}

?>

<!--    <div class="title-text">-->
<!--        --><?php //echo in('slug') ?><!-- EDIT PAGE-->
<!--    </div>-->
        <div class="post-edit-box">

            <form action="?">
                <input type="hidden" name="forum" value="edit_submit">
                <input type="hidden" name="response" value="view">
                <?php if ( in('slug') ) { ?>
                    <input type="hidden" name="slug" value="<?php echo in('slug')?>">
                <?php } else { ?>
                    <input type="hidden" name="post_ID" value="<?php echo in('post_ID')?>">
                <?php } ?>
                <input type="hidden" name="on_error" value="alert_and_go_back">
                <input type="hidden" name="return_url" value="<?php forum()->urlList()?>">

                <div class="col-lg-8">
                    <div class="col-lg-8">
                        <fieldset class="form-group">
                            <label for="post-title">Title</label>
                            <input type="text" class="form-control" id="post-title" name="title" placeholder="Post Title" value="<?php echo esc_html( $post->title() )?>">
                            <small class="text-muted">Please, input post title.</small>
                        </fieldset>
                    </div>

                    <div class="col-lg-8">
                        <fieldset class="form-group">
                            <label for="post-content">Content</label>
                            <?php
                            if ( $post ) {
                                $content = esc_html($post->content());
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
                    </div>

                    <div class="col-lg-6">
                        <div class="buttons">
                            <input type="submit" class="btn btn-danger">
                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
                    <h5>Issue Information</h5>
                    <div class="col-lg-6">
                        <?php $category = forum()->getCategory();?>
                        <?php $members = forum()->meta($category->term_id, 'members'); ?>
                        <div class="input-group padding-top">
                            <div class="input-group-btn">
                                <button tabindex="-1" class="btn btn-secondary" type="button">Assign Workers</button>
                                <button tabindex="-1" data-toggle="dropdown" class="btn btn-secondary dropdown-toggle" type="button">
                                    <span class="caret"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <?php
                                    if( isset($members) ):
                                        $split_members = explode( ',', $members );
                                        foreach( $split_members as $member ): ?>

                                            <a class="dropdown-item" href="#">
                                                <input type="checkbox" value="<?php echo $member; ?>" name="worker[]">
                                                <?php echo $member; ?>
                                            </a>

                                        <?php endforeach;
                                    endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 padding-top">
                        <label for="deadline">Deadline:</label>
                        <input type="date" id="deadline" name="deadline" class="form-control">
                    </div>
                    <div class="col-lg-12 padding-top">
                        <hr>
                        <h5>Issue Status</h5>
                        <div class="col-lg-12 padding-top">
                            <select class="c-select" name="work_priority">
                                <option selected disabled>Work Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="immediate">Immediate</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>

                        <div class="col-lg-12 padding-top">
                            <select class="c-select" name="work_difficulty">
                                <option selected disabled>Work Difficulty</option>
                                <option value="very low">Very Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="very high">Very High</option>
                            </select>
                        </div>

                        <div class="col-lg-12 padding-top">
                            <?php $author_name = get_userdata($post->author())->display_name;
                            if( $author_name === wp_get_current_user()->user_login ):  ?>
                                <select class="c-select" name="work_evaluation">
                                    <option selected disabled>Work Evaluation</option>
                                    <option value="good">Good</option>
                                    <option value="with bugs">Contain Bugs</option>
                                    <option value="bad">Bad</option>
                                </select>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            </form>

            <div class="col-lg-6">
                <?php file_upload()?>
            </div>

        </div>


<?php get_footer(); ?>