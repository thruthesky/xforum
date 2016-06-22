<?php


$cat = forum()->getForumCategory();
$categories = lib()->get_categories_with_depth( $cat->term_id );



if ( isset($_REQUEST['category_id']) ) { // editing
    $category = get_category( $_REQUEST['category_id'] );
    $category_id = $category->term_id;
    $parent_category = get_category($category->parent);
}
else {
    $category = null;
    $parent_category = get_category_by_slug(FORUM_CATEGORY_SLUG);
}

?>

<style>
    <?php if ( isset($_REQUEST['category_id']) ) : ?>
    .forum-list {
        display: none;
    }
    <?php else : ?>
    .forum-create {
        display: none;
    }
    <?php endif; ?>
    .forum-create {
        margin-bottom: 2em;
    }
</style>
<script>

    jQuery(function($) {
        $('.forum-create-button').click(function(){
            $('.forum-create').show();
            $('.forum-list').hide();
        });
        $('.forum-create-cancel-button').click(function(){
            $('.forum-create').hide();
            $('.forum-list').show();
        });
        <?php if ( $category ) : ?>
        $('.forum-create [name="parent"]').val("<?php echo $category->parent?>");
        <?php endif; ?>

        $('body').on('submit', '.forum-create form', function(e) {
            e.preventDefault();

            var $form = $(this);
            var action = $form.prop('action');
            var data = $form.serialize();
            var url = action + '&' + data;

            console.log(url);

            $.get(url, function(re){
                if ( re['success'] ) {
                    location.reload( true );
                }
                else {
                    alert( "ERROR CODE: " + re['data']['code'] + ", message: " + re['data']['message']);
                }
            });

            return false;
        });

    });
</script>

<div class="wrap xforum">

    <h2>X Forum</h2>


    <div class="forum-create">
        <h2>
            <?php if ( $category ) : ?>
                <?php _e('Update a Forum', 'xforum')?>
            <?php else : ?>
                <?php _e('Create a Forum', 'xforum')?>
            <?php endif; ?>
        </h2>



        <form action="<?php echo forum()->doURL('forum_create')?>" method="post">
            <?php if ( $category ) { ?>
                <input type="hidden" name="cat_ID"  value="<?php if ( $category ) echo $category->term_id ?>">
            <?php } ?>
            <fieldset class="form-group">
                <label for="ForumID">
                    <?php _e('Forum ID', 'xforum')?>
                </label>
                <input id='ForumID' class='form-control' type="text" name="category_nicename" placeholder="<?php _e('Please input forum ID', 'xforum')?>" value="<?php if ( $category ) echo $category->slug ?>">
                <small class="text-muted"><?php _e('Input forum ID in lowercase letters, numbers and hypens. It is a slug.', 'xforum')?></small>
            </fieldset>

            <fieldset class="form-group">
                <label for="ForumName">
                    <?php _e('Forum name', 'xforum')?>
                </label>
                <input id='ForumName' class='form-control' type="text" name="cat_name" placeholder="<?php _e('Please input forum name', 'xforum')?>" value="<?php if ( $category ) echo $category->name ?>">
                <small class="text-muted"><?php _e('Input forum name. It should be less than four words. It is a category name.', 'xforum')?></small>
            </fieldset>

            <fieldset class="form-group">
                <label for="ForumDesc"><?php _e('Forum description', 'xforum')?></label>
                <textarea name="category_description" class="form-control" id="ForumDesc" rows="3"><?php if ( $category ) echo $category->description ?></textarea>
                <small class="text-muted"><?php _e('Input forum description. It should be less than 100 words.', 'xforum')?></small>
            </fieldset>


            <fieldset class="form-group">
                <label for="ForumParent"><?php _e('Parent Forum', 'xforum')?></label>
                <select name="category_parent" class="form-control" id="ForumParent">
                    <option value=""><?php _e('Select Parent Forum', 'xforum')?></option>
                    <?php
                    foreach ( $categories as $_category ) {
                        $pads = str_repeat( '----', $_category->depth );
                        echo "<option value='{$_category->term_id}'>$pads{$_category->name}</option>";
                    }
                    ?>
                </select>
                <small class="text-muted"><?php _e('You can group or categorize forum by selecting Parent Forum', 'xforum')?></small>
            </fieldset>


            <fieldset class="form-group">
                <label for="ForumTemplate"><?php _e('Forum Template', 'xforum')?></label>
                <input id='ForumTemplate' class='form-control' type="text" name="template" placeholder="<?php _e('Please input forum template postfix', 'xforum')?>" value="<?php if ( $category ) echo get_term_meta( $category->term_id, 'template', true) ?>">
                <small class="text-muted"><?php _e('Input forum template post.', 'xforum')?></small>
            </fieldset>


            <div class="error-message">

            </div>

            <?php if ( $category ) : ?>
                <input type="submit" class="btn btn-primary" value="<?php _e('Update Forum', 'xforum')?>">
            <?php else : ?>
                <input type="submit" class="btn btn-primary" value="<?php _e('Create Forum', 'xforum')?>">
            <?php endif; ?>
            <button type="button" class="btn btn-secondary forum-create-cancel-button"><?php _e('Cancel', 'xforum')?></button>

        </form>
    </div>

    <div class="forum-list">

        <button class="button btn-primary forum-create-button"><?php _e('Create Forum', 'xforum')?></button>


        <h2><?php _e('Forum List', 'xforum')?></h2>

        <div class="forum-list container">
            <div class="row">
                <div class="col-xs-4 col-sm-4">Category</div>
                <div class="col-xs-2 col-sm-1">View(Slug)</div>
                <div class="col-xs-2 col-sm-1">Edit</div>
                <div class="col-xs-2 col-sm-1">Delete</div>
                <div class="col-xs-2 col-sm-1">Posts</div>
                <div class="col-xs-12 col-sm-4">Description</div>
            </div>
            <?php
            if ( $categories ) {
                foreach($categories as $category) {
                    ?>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4">
                            <a href="<?php echo forum()->listURL($category->slug)?>" target="_blank">
                                <?php
                                $pads = str_repeat( '----', $category->depth );
                                echo $pads;
                                ?>
                                <?php echo $category->name?>
                            </a>
                        </div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->listURL($category->slug)?>"><?php echo $category->slug?></a></div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->adminURL()?>&category_id=<?php echo $category->term_id?>">Edit</a></div>
                        <div class="col-xs-2 col-sm-1"><a href="<?php echo forum()->doURL('forum_delete')?>&category_id=<?php echo $category->term_id?>">Delete</a></div>
                        <div class="col-xs-2 col-sm-1"><?php echo $category->count?></div>
                        <div class="col-xs-12 col-sm-4"><?php echo $category->description?></div>
                    </div>
                <?php } } ?>

        </div>
    </div>
</div>
