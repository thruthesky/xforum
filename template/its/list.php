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

<style>
    .form-group {

    }
    .form-group .caption {
        display: inline-block;
        margin-right: 10px;
    }
</style>
<form action="?">
    <input type="hidden" name="forum" value="list">
    <input type="hidden" name="slug" value="<?php echo forum()->getCategory()->slug?>">


    <fieldset>
        <span class="caption">Worker :</span>
        <?php
        $members = forum()->getCategory()->config['members'];
        foreach( $members as $member ) {
            ?>
            <label class="radio-inline">
                <input type="radio" name="worker" value="<?php echo $member?>" <?php if ( $member == in('worker') ) echo 'checked=1'; ?>> <?php echo $member?>
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
                <input type="radio" name="incharge" value="<?php echo $member?>" <?php if ( $member == in('incharge') ) echo 'checked=1'; ?>> <?php echo $member?>
            </label>
            <?php
        }
        ?>
    </fieldset>



    <fieldset>
        <label for="created-begin">Created</label>
        <input type="date" id="created-begin" name="created_begin" placeholder="Work created" value="<?php echo in('created_begin') ?>">

        <input type="date" id="created-end" name="created_end" placeholder="Work created end" value="<?php echo in('created_end') ?>">
    </fieldset>

    <fieldset>
        <label for="created-begin">Deadline</label>
        <input type="date" id="deadline-begin" name="deadline_begin" placeholder="Deadline begin" value="<?php echo in('deadline_begin') ?>">

        <input type="date" id="deadline-end" name="deadline_end" placeholder="Deadline end" value="<?php echo in('deadline_end') ?>">
    </fieldset>


    <fieldset>
        <label class="caption" for="newly-commented">Newly commented</label>
        <select id="newly-commented" name="newly_commented">
            <option value="0" <?php if ( '0' == in('newly_commented') ) echo 'selected=1'?>>Select</option>
            <option value="1" <?php if ( '1' == in('newly_commented') ) echo 'selected=1'?>>Today</option>
            <option value="2" <?php if ( '2' == in('newly_commented') ) echo 'selected=1'?>>Today + Yesterday</option>
            <option value="3" <?php if ( '3' == in('newly_commented') ) echo 'selected=1'?>>Within 3 days</option>
            <option value="5" <?php if ( '5' == in('newly_commented') ) echo 'selected=1'?>>Within 5 days</option>
            <option value="7" <?php if ( '7' == in('newly_commented') ) echo 'selected=1'?>>Within 7 days</option>
            <option value="30" <?php if ( '30' == in('newly_commented') ) echo 'selected=1'?>>Within 30 days</option>
        </select>
    </fieldset>

    <fieldset>
        <label class="caption" for="priority">Priority</label>
        <select id="priority" name="priority">
            <option value="A" <?php if ( 'A' == in('priority') ) echo 'selected=1'?>>ALL</option>
            <option value="N" <?php if ( 'N' == in('priority') ) echo 'selected=1'?>>Never mind</option>
            <option value="L" <?php if ( 'L' == in('priority') ) echo 'selected=1'?>>Low</option>
            <option value="M" <?php if ( 'M' == in('priority') ) echo 'selected=1'?>>Medium</option>
            <option value="H" <?php if ( 'H' == in('priority') ) echo 'selected=1'?>>High</option>
            <option value="I" <?php if ( 'I' == in('priority') ) echo 'selected=1'?>>Immediate</option>
            <option value="C" <?php if ( 'C' == in('priority') ) echo 'selected=1'?>>Critical</option>
        </select>
    </fieldset>

    <fieldset>
        <label class="caption" for="process">Process</label>
        <select id="process" name="process">
            <option value="A" <?php if ( 'A' == in('process') ) echo 'selected=1'?>>ALL</option>
            <option value="N" <?php if ( 'N' == in('process') ) echo 'selected=1'?>>Not started</option>
            <option value="S" <?php if ( 'S' == in('process') ) echo 'selected=1'?>>Started</option>
            <option value="P" <?php if ( 'P' == in('process') ) echo 'selected=1'?>>In progress</option>
            <option value="F" <?php if ( 'F' == in('process') ) echo 'selected=1'?>>Finished</option>
        </select>
    </fieldset>


    <fieldset>
        <label class="caption" for="percentage">Percentage</label>
        <input id="percentage" name="percentage" type="range" min="0" max="100" step="1" value="0" oninput="percentage_value.value=percentage.value"/>
        <output name="percentage_value">0</output>
<!--        @todo: show percentage in text.-->
    </fieldset>


    <fieldset>
        <label class="caption" for="title">Title</label>
        <input id="title" type="text" name="title" value="<?php echo in('title') ?>"/>
    </fieldset>

    <fieldset>
        <label class="caption" for="title_content">Title + Content</label>
        <input id="title_content" type="text" name="title_content" value="<?php echo in('title_content') ?>"/>
    </fieldset>

    <fieldset>
        <label class="caption" for="order1">Order by</label>
        <select id="order1" name="order1">
            <option value="" <?php if ( '' == in('order1') ) echo 'selected=1'?>>Random</option>
            <option value="priority" <?php if ( 'priority' == in('order1') ) echo 'selected=1'?>>Priority</option>
            <option value="process" <?php if ( 'process' == in('order1') ) echo 'selected=1'?>>Process</option>
            <option value="percentage" <?php if ( 'percentage' == in('order1') ) echo 'selected=1'?>>Percentage</option>
            <option value="created" <?php if ( 'created' == in('order1') ) echo 'selected=1'?>>Created</option>
            <option value="deadline" <?php if ( 'deadline' == in('order1') ) echo 'selected=1'?>>Deadline</option>
            <option value="newly_commented" <?php if ( 'newly_commented' == in('order1') ) echo 'selected=1'?>>Newly commented</option>
        </select>
        <label>
            <input type="radio" name="order1_sort" value="ASC" <?php if ( 'ASC' == in('order1_sort') ) echo 'checked=1'; ?>> Asc,
        </label>
        <label>
            <input type="radio" name="order1_sort" value="DESC" <?php if ( 'DESC' == in('order1_sort') ) echo 'checked=1'; ?>> Desc,
        </label>
    </fieldset>

    <fieldset class="order2">
        <label class="caption" for="order2">Order by</label>
        <select id="order2" name="order2">
            <option value="" <?php if ( '' == in('order2') ) echo 'selected=1'?>>Random</option>
            <option value="priority" <?php if ( 'priority' == in('order2') ) echo 'selected=1'?>>Priority</option>
            <option value="process" <?php if ( 'process' == in('order2') ) echo 'selected=1'?>>Process</option>
            <option value="percentage" <?php if ( 'percentage' == in('order2') ) echo 'selected=1'?>>Percentage</option>
            <option value="created" <?php if ( 'created' == in('order2') ) echo 'selected=1'?>>Created</option>
            <option value="deadline" <?php if ( 'deadline' == in('order2') ) echo 'selected=1'?>>Deadline</option>
            <option value="newly_commented" <?php if ( 'newly_commented' == in('order2') ) echo 'selected=1'?>>Newly commented</option>
        </select>
        <label>
            <input type="radio" name="order2_sort" value="ASC" <?php if ( 'ASC' == in('order2_sort') ) echo 'checked=1'; ?>> Asc,
        </label>
        <label>
            <input type="radio" name="order2_sort" value="DESC" <?php if ( 'DESC' == in('order2_sort') ) echo 'checked=1'; ?>> Desc,
        </label>
    </fieldset>


    <input type="submit" value="Search Works">
    <button type="button">Reset Search</button>




</form>






    <div class="post-list">
        <?php
        $args = ['category' => $category->term_id];

        if( in('title_content') ){
            $args += [ 's' => in('title_content') ];
        }

        if ( in('worker') ) {
            $args[ 'meta_query' ][] = ['key'=>'worker', 'value'=>in('worker')];
        }

        if ( in('priority') ) {
            $args[ 'meta_query' ][] = ['key'=>'priority', 'value'=>in('priority')];
        }

        if ( in('incharge') ) {
            $args[ 'meta_query' ][] = ['key'=>'incharge', 'value'=>in('incharge')];
        }

        if ( in('process') ) {
            $args[ 'meta_query' ][] = ['key'=>'process', 'value'=>in('process')];
        }

        if ( in('created_begin') && in('created_end') ) {
            $args[ 'date_query' ][] = ['after'=>in('created_begin'), 'before'=>in('created_end')];
        }

        if ( in('deadline_begin') && in('deadline_end') ) {
            $args[ 'meta_query' ][] = ['key'=>'deadline', 'value'=>array(in('deadline_begin'),in('deadline_end')),'compare'=>'BETWEEN','type'=>'DATE' ];
        }
        
        $posts = get_posts( $args );


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
                            <?php echo post()->priority?>
                        </td>
                        <td>
                            <?php echo post()->worker?>
                        </td>
                        <td><?php echo post()->getNoOfView( get_the_ID() )?></td>

                        <td>
                            <?php e( post()->deadline ) ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>

<?php get_footer(); ?>