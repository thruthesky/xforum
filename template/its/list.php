<?php
$category = forum()->getCategory();
get_header();
?>
    <style>
        .post-list {
            margin: 1em 0;
        }
    </style>
    <h1><?php echo in('slug') ?> LIST PAGE</h1>

<?php forum()->list_menu_write()?>
<?php forum()->list_menu_user()?>

<pre>combination of search items:
deadline ( work of deadline in a week, in a day, ... give begin/end date of deadline. so you know deadline work of today, next week, last month )
newly comments( day option. how old days of the comment is considers as new. )
priority,
process,
percentage of work,
title search,
title + content search,</pre>

<style>
    .form-group {

    }
    .form-group .caption {
        display: inline-block;
        margin-right: 10px;
    }
</style>
<form>

    <fieldset>
        <span class="caption">Worker :</span>
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


    <fieldset>
        <div class="caption">Who is in charge?</div>
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



    <fieldset>
        <label for="created-begin">Created</label>
        <input type="date" id="created-begin" name="created_begin" placeholder="Work created" value="">

        <input type="date" id="created-end" name="created_end" placeholder="Work created end" value="">
    </fieldset>

    <fieldset>
        <label for="created-begin">Deadline</label>
        <input type="date" id="deadline-begin" name="deadline_begin" placeholder="Deadline begin" value="">

        <input type="date" id="deadline-end" name="deadline_end" placeholder="Deadline end" value="">
    </fieldset>



</form>






    <div class="post-list">
        <?php
        $posts = get_posts(
            [
                'category' => $category->term_id,
            ]
        );

        if ( $posts ) { ?>
            <table class="table">

                <?php
                foreach ( $posts as $post ) {
                    post()->setup( $post );
                    ?>
                    <tr>
                        <td>
                            <a href="<?php the_permalink()?>">
                                <?php the_title()?>
                                <?php forum()->count_comments( get_the_ID() ) ?>
                            </a>
                        </td>
                        <td>
                            <?php the_author()?>
                        </td>
                        <td><?php echo post()->getNoOfView( get_the_ID() )?></td>

                        <td>
                            <?php e( post()->meta('deadline') ) ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

<?php get_footer(); ?>