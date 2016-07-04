<?php

/**
 *
 * Use this on Forum Create / Forum Edit form.
 *
 * @param null $slug
 */
function forum_edit_line_slug($slug = null) {
    echo <<<EOH

            <fieldset class="form-group">
                <label for="ForumID">
                    Forum ID
                </label>
                <input id='ForumID' class='form-control' type="text" name="slug" placeholder="Please input forum ID" value="$slug">
                <small class="text-muted">Input forum ID in lowercase letters, numbers and hypens. It is a slug.</small>
            </fieldset>


EOH;

}

/**
 * @param null $cat_name
 */
function forum_edit_line_cat_name($cat_name = null)
{
    echo <<<EOH
            <fieldset class="form-group">
                <label for="ForumName">
                    Forum name
                </label>
                <input id='ForumName' class='form-control' type="text" name="cat_name" placeholder="Please input forum name" value="$cat_name">
                <small class="text-muted">Input forum name. It should be less than four words. It is a category name.</small>
            </fieldset>
EOH;
}


function forum_edit_line_category_description($category_description=null) {
    echo <<<EOH

            <fieldset class="form-group">
                <label for="ForumDesc">Forum description</label>
                <textarea name="category_description" class="form-control" id="ForumDesc" rows="3">$category_description</textarea>
                <small class="text-muted">Input forum description. It should be less than 100 words.</small>
            </fieldset>

EOH;

}


function forum_edit_line_category_parent( $category_parent_term_id = 0 ) {
    $categories = forum()->categories();
    $forum_category = get_category_by_slug(FORUM_CATEGORY_SLUG);

    echo <<<EOH
            <fieldset class="form-group">
                <label for="ForumParent">Parent Forum</label>
                <select name="category_parent" class="form-control" id="ForumParent">
                    <option value="{$forum_category->term_id}">Select Parent Forum</option>
EOH;
                    foreach ( $categories as $_category ) {
                        $pads = str_repeat( '----', $_category->depth );
                        if ( $_category->term_id == $category_parent_term_id ) $selected = " selected=1";
                        else $selected = '';
                        echo "<option value='{$_category->term_id}'$selected>$pads{$_category->name}</option>";
                    }

    echo <<<EOH
                </select>
                <small class="text-muted"><?php _e('You can group or categorize forum by selecting Parent Forum', 'xforum')?></small>
            </fieldset>

EOH;

}


function forum_edit_line_admins( $term_id = 0 ) {
    if ( $term_id ) $admins = get_term_meta( $term_id, 'admins', true);
    else $admins = null;
    echo <<<EOH
            <fieldset class="form-group">
                <label for="ForumAdmins">Forum Admins</label>
                <input id='ForumAdmins' class='form-control' type="text" name="admins" placeholder="Please input forum admins" value="$admins">
                <small class="text-muted"><?php _e('Input forum admins.', 'xforum')?></small>
            </fieldset>
EOH;
}

function forum_edit_line_members( $term_id = 0 ) {
    if ( $term_id ) $members = get_term_meta( $term_id, 'members', true);
    else $members = null;
    echo <<<EOH
            <fieldset class="form-group">
                <label for="ForumMembers">Forum Members</label>
                <input id='ForumMembers' class='form-control' type="text" name="members" placeholder="Please input forum members" value="$members">
                <small class="text-muted"><?php _e('Input forum members.', 'xforum')?></small>
            </fieldset>
EOH;
}

function forum_edit_line_template( $term_id = 0 ) {
    if ( $term_id ) $template = get_term_meta( $term_id, 'template', true);
    else $template = null;
    echo <<<EOH
            <fieldset class="form-group">
                <label for="ForumTemplate">Forum Template</label>
                <input id='ForumTemplate' class='form-control' type="text" name="template" placeholder="Please input forum template postfix" value="$template">
                <small class="text-muted"><?php _e('Input forum template post.', 'xforum')?></small>
            </fieldset>
EOH;
}
function forum_edit_line_category( $term_id = 0 ) {
    if ( $term_id ) $category = get_term_meta( $term_id, 'category', true);
    else $category = null;
    echo <<<EOH
            <fieldset class="form-group">
                <label for="ForumCategory">Forum Category ( .ini format )</label>
                <textarea class='form-control' id='ForumCategory' name="category" placeholder="Please input forum category">$category</textarea>
                <small class="text-muted"><?php _e('Input forum category', 'xforum')?></small>
            </fieldset>
EOH;
}

function forum_edit_line_view( $term_id = 0 ) {
    echo <<<EOH
    <fieldset class="form-group">
    <div>
        <div>Who can VIEW?</div>
        <label class="radio-inline">
            <input type="radio" name="view" value="anyone"> Anyone
        </label>
        <label class="radio-inline">
            <input type="radio" name="view" value="site-member"> Site members
        </label>
        <label class="radio-inline">
            <input type="radio" name="view" value="forum-member"> Forum members
        </label>
    </div>
    <small class="text-muted">Choose who can write the posts and comments.</small>
    </fieldset>
EOH;
}

function forum_edit_line_write( $term_id = 0 ) {
    echo <<<EOH
    <fieldset class="form-group">
    <div>
        <div>Who can WRITE?</div>
        <label class="radio-inline">
            <input type="radio" name="write" value="anyone"> Anyone
        </label>
        <label class="radio-inline">
            <input type="radio" name="write" value="site-member"> Site members
        </label>
        <label class="radio-inline">
            <input type="radio" name="write" value="forum-member"> Forum members
        </label>
    </div>
    <small class="text-muted">Choose who can write the posts and comments.</small>
    </fieldset>
EOH;
}

function forum_edit_line_list( $term_id = 0 ) {
    echo <<<EOH
    <fieldset class="form-group">
    <div>
        <div>Who can LIST?</div>
        <label class="radio-inline">
            <input type="radio" name="list" value="anyone"> Anyone
        </label>
        <label class="radio-inline">
            <input type="radio" name="list" value="site-member"> Site members
        </label>
        <label class="radio-inline">
            <input type="radio" name="list" value="forum-member"> Forum members
        </label>
    </div>
    <small class="text-muted">Choose who can list the posts and comments. ( posts in list may change )</small>
    </fieldset>
EOH;
}
