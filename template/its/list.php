<?php
include DIR_XFORUM . 'template/its/its.class.php';
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

<script type="text/javascript">
    jQuery(document).ready(function(){

        $("#order1").change(function() {
            $("#order1_sort").show();
        });

        $("#order2").change(function() {
            $("#order2_sort").show();
        });

        $("#process").change(function () {
            if ($(this).val() == "P") {
                $("#percent").show();
            }
        });

        if( $('#order1 option').is(':selected') ) {
            if ($("#order1 ")[0].selectedIndex <= 0 ) {
                $("#order1_sort").hide();
            } else {
                $("#order1_sort").show();
            }
        }

        if( $('#order2 option').is(':selected') ) {
            if ( $("#order2 ")[0].selectedIndex <= 0 ) {
                $("#order2_sort").hide();
            } else {
                $("#order2_sort").show();
            }

        }

        if( $('#process option').is(':selected') ) {
            if ($("#process").val() == "P") {
                $("#percent").show();
            } else {
                $("#percent").hide();
            }
        }

    });
</script>

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
            <option value="" <?php if ( ! in('priority') ) echo 'selected=1'?>>ALL</option>
            <?php foreach ( its::$priority as $num => $text ) { ?>
                <option value="<?php echo $num?>" <?php if ( in('priority') == $num ) echo 'selected=1'?>><?php echo $text?></option>
            <?php } ?>

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
        <div id="percent">
            <?php
            if ( in('percentage') ) $percent = in('percentage');
            else $percent = 0;
            ?>
            <label class="caption" for="percentage">Percentage</label>
            <input id="percentage" name="percentage" type="range" min="0" max="100" step="1" value="<?php echo $percent; ?>" oninput="percentage_value.value=percentage.value"/>
            <output name="percentage_value"><?php echo $percent; ?></output>
        </div>
    </fieldset>


    <fieldset>
        <label class="caption" for="keyword">Search Text</label>
        <input id="keyword" type="text" name="keyword" value="<?php echo in('keyword') ?>"/>
    </fieldset>


    <fieldset>
        <label class="caption" for="order1">Order by</label>
        <select id="order1" name="order1">
            <option value="" <?php if ( '' == in('order1') ) echo 'selected=1'?>>Random</option>
            <option value="priority" <?php if ( 'priority' == in('order1') ) echo 'selected=1'?>>Priority</option>
            <option value="percentage" <?php if ( 'percentage' == in('order1') ) echo 'selected=1'?>>Percentage</option>
            <option value="created" <?php if ( 'created' == in('order1') ) echo 'selected=1'?>>Created</option>
            <option value="deadline" <?php if ( 'deadline' == in('order1') ) echo 'selected=1'?>>Deadline</option>
            <option value="newly_commented" <?php if ( 'newly_commented' == in('order1') ) echo 'selected=1'?>>Newly commented</option>
        </select>

        <div id="order1_sort">
            <label>
                <input type="radio" name="order1_sort" value="ASC" <?php if ( 'ASC' == in('order1_sort') ) echo 'checked=1'; ?>> Asc,
            </label>
            <label>
                <input type="radio" name="order1_sort" value="DESC" <?php if ( 'DESC' == in('order1_sort') ) echo 'checked=1'; ?>> Desc,
            </label>
        </div>
    </fieldset>

    <fieldset class="order2">
        <label class="caption" for="order2">Order by</label>
        <select id="order2" name="order2">
            <option value="" <?php if ( '' == in('order2') ) echo 'selected=1'?>>Random</option>
            <option value="priority" <?php if ( 'priority' == in('order2') ) echo 'selected=1'?>>Priority</option>
            <option value="percentage" <?php if ( 'percentage' == in('order2') ) echo 'selected=1'?>>Percentage</option>
            <option value="created" <?php if ( 'created' == in('order2') ) echo 'selected=1'?>>Created</option>
            <option value="deadline" <?php if ( 'deadline' == in('order2') ) echo 'selected=1'?>>Deadline</option>
            <option value="newly_commented" <?php if ( 'newly_commented' == in('order2') ) echo 'selected=1'?>>Newly commented</option>
        </select>
        <div id="order2_sort">
            <label>
                <input type="radio" name="order2_sort" value="ASC" <?php if ( 'ASC' == in('order2_sort') ) echo 'checked=1'; ?>> Asc,
            </label>
            <label>
                <input type="radio" name="order2_sort" value="DESC" <?php if ( 'DESC' == in('order2_sort') ) echo 'checked=1'; ?>> Desc,
            </label>
        </div>
    </fieldset>


    <input type="submit" value="Search Works">
    <button type="button">Reset Search</button>





</form>







    <div class="post-list">
        <?php
        $args = [
            'cat' => $category->term_id,
            'posts_per_page' => 40,
        ];

        if ( in('keyword') ) {
            $args += [ 's' => in('keyword') ];
        }

        if ( in('worker') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'worker', 'value'=>in('worker') ];
        }

        if ( in('priority') && in('priority') != 'A' ) {
            $args[ 'meta_query' ][] = [ 'key'=>'priority', 'value'=>in('priority') ];
        }

        if ( in('incharge') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'incharge', 'value'=>in('incharge') ];
        }

        if ( in('process') && in('process') != 'A' ) {
            $args[ 'meta_query' ][] = [ 'key'=>'process', 'value'=>in('process') ];
        }

        if ( in('percentage') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'percentage', 'value'=>array( 1,in('percentage') ), 'compare'=>'BETWEEN' ];
        }

        if ( in('created_begin') ) {
            $begin = date('Y-m-d', strtotime( in('created_begin') ) - 60 * 60 * 24 );
            $args[ 'date_query' ][] = [ 'after'=> $begin ];
        }

        if ( in('created_end') ) {
            $end = date('Y-m-d', strtotime( in('created_end') ) + 60 * 60 * 24 );
            $args[ 'date_query' ][] = [ 'before'=> $end ];
        }

        if ( in('deadline_begin') || in('deadline_end') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'deadline', 'value'=>array(in('deadline_begin'),in('deadline_end')),'compare'=>'BETWEEN','type'=>'DATE' ];
        }



        if ( in('order1') ) {
            if ( in('order1') == 'priority' ) {
                $sort_what = 'priority';
                $args[ 'meta_query' ]['priority'] = [ 'key'=>'priority', 'orderby'=>'meta_value_num' ];
            }

            elseif ( in('order1') == 'created' ) {
                $sort_what = 'date';
            }

            elseif ( in('order1') == 'deadline' ) {
                $sort_what = 'deadline';
                $args[ 'meta_query' ]['deadline'] = [ 'key'=>'deadline', 'orderby'=>'meta_value date' ];
            }

            elseif ( in('order1') == 'percentage' ) {
                $sort_what = 'percentage';
                $args[ 'meta_query' ]['percentage'] = [ 'key'=>'percentage', 'orderby'=>'meta_value_num' ];
            }

            $args[ 'orderby' ] = [ $sort_what=>in('order1_sort') ];
        }



        if ( in('order2') ) {
            if ( in('order2') == 'priority' ) {
                $sort_what = 'priority';
                $args[ 'meta_query' ]['priority'] = [ 'key'=>'priority', 'orderby'=>'meta_value_num' ];
            }

            elseif ( in('order2') == 'created' ) {
                $sort_what = 'post_date';
            }

            elseif ( in('order2') == 'deadline' ) {
                $sort_what = 'deadline';
                $args[ 'meta_query' ]['deadline'] = [ 'key'=>'deadline', 'orderby'=>'meta_value date' ];
            }

            elseif ( in('order2') == 'percentage' ) {
                $sort_what = 'percentage';
                $args[ 'meta_query' ]['percentage'] = [ 'key'=>'percentage', 'orderby'=>'meta_value_num' ];
            }

            if ( in('order1_sort') ) {
                // if there's a sorting selected on 'order1_sort', append to the current array
                $args[ 'orderby' ] += [ $sort_what=>in('order2_sort') ];
            } else {
                // else, do not append or else: Unsupported operand types error
                $args[ 'orderby' ] = [ $sort_what=>in('order2_sort') ];
            }

        }


//        di($args);

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
                            <?php echo get_the_date();?>
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