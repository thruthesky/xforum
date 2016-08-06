<?php
// include_once DIR_XFORUM . 'template/its/init.php'; // this is done by core.
$category = forum()->getCategory();
get_header();




?>
    <style>

        .form-group {

        }
        .form-group .caption {
            display: inline-block;
            margin-right: 10px;
        }


        .post-list {
            margin: 1em 0;
        }

        #deadline-begin,
        #deadline-end,
        #create-begin,
        #create-end
        {
            width: 140px;
        }

        .overdue {
            color: #C80000;
        }
        .overdue a {
            color: #C80000;
        }

        table progress {
            width: 60px;
        }

        table.list .category,
        table.list .priority,
        table.list .process,
        table.list .percentage,
        table.list .worker,
        table.list .incharge,
        table.list .view,
        table.list .deadline,
        table.list .created {
            display:none;
        }



        .ajax-search {
            position: absolute;
            z-index: 1001234;
            padding: 1em;
            background-color: white;
        }
    </style>

    <script type="text/javascript">
        window.addEventListener('load', function(){
            (function( $ ) {


                $("#order1").change(function() {
                    $("fieldset.order2").show();
                });

                $("#process").change(function () {
                    if ($(this).val() == "P") {
                        $("#percent").show();
                    } else if ($(this).val() == "A") {
                        $("#evaluate").show();
                    }
                });

                if( $('#process option').is(':selected') ) {
                    if ($("#process").val() == "P") {
                        $("#percent").show();
                    } else {
                        $("#percent").hide();
                    }
                }


                $('.search').click( function() {
                    var $search = $(this);
                    var column = $search.val();
                    var checked = $search.prop('checked');

                    Cookies.set( 'save_search_' + column, checked );

                });


                $('.display-column').click( function() {
                    var $checkbox = $(this);
                    var column = $checkbox.val();
                    var checked = $checkbox.prop('checked');
                    if ( checked ) $('table.list .' + column).show();
                    else $('table.list .' + column).hide();

                    Cookies.set( 'its_column_' + column, checked );

                });


                var cooks = Cookies.get();
                if ( cooks ) {
                    for ( var c in cooks ) {
                        if ( ! cooks.hasOwnProperty ( c ) ) continue;
                        if ( c.indexOf('its_column_') != -1 ) {
                            var column = c.replace('its_column_', '');
                            var checked = Cookies.get( c );
                            if ( checked == 'true' ) {
                                $('.display-column[value="'+column+'"]').prop('checked', true);
                                $('table.list .' + column).show();
                            }
                        }
                    }
                }




                var autocomplete_ajax_progress = false;
                $('[name="keyword"]').keyup(function( e ){

                    if ( e.keyCode == 27 ) {
                        $('.ajax-search').remove();
                        return;
                    }

                    var $this = $(this);
                    var keyword = $this.val();
                    if ( keyword.length >= 2 ) {
                        if ( autocomplete_ajax_progress ) return false;
                        autocomplete_ajax_progress = true;
                        var url = '<?php echo home_url("?forum=ajax_search&slug=" . forum()->slug)?>&keyword=' + keyword + '&attributes[]=priority&attributes[]=process&attributes[]=worker';
                        console.log ( url );
                        $.get( url, function( re ) {
                            autocomplete_ajax_progress = false;
                            $('.ajax-search').remove();
                            $this.after(re.data);
                        });
                    }
                    else {
                        $('.ajax-search').remove();
                    }
                });
                $(document).click(function(e) {
                    $('.ajax-search').remove();
                });

            })( jQuery );
        });

    </script>

    <h1><?php echo in('slug') ?> LIST PAGE</h1>


<?php forum()->button_write()?>
<?php forum()->button_list(['text'=>'TOP'])?>
<?php forum()->list_menu_user()?>



    <form action="?">
        <input type="hidden" name="forum" value="list">
        <input type="hidden" name="slug" value="<?php echo forum()->getCategory()->slug?>">

        <?php
        $cats = forum()->getCategory()->config['category'];
        $args = array(
        );
        $child_categories = get_categories( [
            'child_of'                 => forum()->getCategory()->term_id,
            'hide_empty'               => FALSE
            ] );




        if ( $cats || $child_categories) {
            ?>
            <fieldset class="form-group">
                <span class="caption"><?php _text('Category') ?> : </span>
                <?php
                $in_category = in('category') ? in('category') : [];
                foreach( $cats as $cat ) {
                    ?>
                    <label class="checkbox-inline">
                        <input type="checkbox" class="search" name="category[]" value="<?php echo $cat?>"<?php if ( in_array( $cat, $in_category ) ) echo ' checked=1'?>> <?php echo $cat?>
                    </label>
                    <?php
                }
                ?>
                <label>
                    <select onchange="location.href='?forum=list&slug='+$(this).val();">
                        <option value=""><?php _text('Sub ITS Category') ?></option>
                        <?php foreach( $child_categories as $cat )  { ?>
                            <option value="<?php echo $cat->slug?>"> <?php echo $cat->name?></option>
                        <?php } ?>
                    </select>
                </label>
            </fieldset>
        <?php } ?>


        <fieldset>
            <span class="caption"><?php _text('Process') ?> : </span>
            <?php
            $in_process = in('process') ? in('process') : [];
            foreach( its::$process as $code => $text ) {
                if ( empty($text) ) continue;
                ?>
                <label class="checkbox-inline">
                    <input type="checkbox" class="search" name="process[]" value="<?php echo $code?>"<?php if ( in_array( $code, $in_process ) ) echo ' checked=1'?>> <?php echo $text?>
                </label>
                <?php
            }
            ?>

        </fieldset>




        <fieldset>

            <?php
            $members = forum()->getCategory()->config['members'];
            if ( $members ) {
            ?>

            <label for="worker">
                <select name="worker">
                    <option value=""><?php _text('Worker') ?></option>
                    <?php
                    foreach( $members as $member ) {
                        ?>
                        <option value="<?php echo $member?>"<?php if ( $member == in('worker') ) echo ' selected=1'; ?>><?php echo $member?></option>
                        <?php
                    }
                    ?>
                </select>
            </label>

            <label for="incharge">
                <select name="incharge">
                    <option value=""><?php _text('In charge') ?></option>
                    <?php
                    foreach( $members as $member ) {
                        ?>
                        <option value="<?php echo $member?>"<?php if ( $member == in('incharge') ) echo ' selected=1'; ?>><?php echo $member?></option>
                        <?php
                    }
                    ?>
                </select>
            </label>
            <?php } ?>


            <label for="deadline-begin"><?php _text('Deadline') ?></label>
            <input type="date" id="deadline-begin" name="deadline_begin" placeholder="Deadline begin" value="<?php echo in('deadline_begin') ?>">
            <input type="date" id="deadline-end" name="deadline_end" placeholder="Deadline end" value="<?php echo in('deadline_end') ?>">



            <label for="created-begin"><?php _text('Created') ?></label>
            <input type="date" id="created-begin" name="created_begin" placeholder="Work created" value="<?php echo in('created_begin') ?>">
            <input type="date" id="created-end" name="created_end" placeholder="Work created end" value="<?php echo in('created_end') ?>">


        </fieldset>





        <fieldset>
            <label class="caption" for="priority"><?php _text('Priority') ?></label>
            <select id="priority" name="priority">
                <option value="" <?php if ( ! in('priority') ) echo 'selected=1'?>>ALL</option>
                <?php foreach ( its::$priority as $num => $text ) {
                    if ( empty($text) ) continue;
                    ?>
                    <option value="<?php echo $num?>" <?php if ( in('priority') == $num ) echo 'selected=1'?>><?php echo $text?></option>
                <?php } ?>

            </select>


            <span id="percent">
                <?php
                if ( in('percentage') ) $percent = in('percentage');
                else $percent = 0;
                ?>
                <label class="caption" for="percentage"><?php _text('Percentage') ?>: </label>
                <input id="percentage" name="percentage" type="range" min="0" max="100" step="1" value="<?php echo $percent; ?>" oninput="percentage_value.value=percentage.value"/>
                <output name="percentage_value"><?php echo $percent; ?></output>
            </span>

            <span id="evaluate">
                <?php
                if ( in('evaluate') ) $evaluate = in('evaluate');
                else $evaluate = 0;
                ?>
                <label class="caption" for="evaluate"><?php _text('Evaluation Rate') ?>: </label>
                <input id="evaluate" name="evaluate" type="range" min="0" max="100" step="1" value="<?php echo $evaluate; ?>" oninput="evaluate_value.value=evaluate.value"/>
                <output name="evaluate_value"><?php echo $evaluate; ?></output>
            </span>


            <label class="caption" for="newly-commented"><?php _text('Comment') ?></label>
            <select id="newly-commented" name="newly_commented">
                <option value="0" <?php if ( '0' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Newly commented') ?></option>
                <option value="1" <?php if ( '1' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Today') ?></option>
                <option value="2" <?php if ( '2' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Today + Yesterday') ?></option>
                <option value="3" <?php if ( '3' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Within 3 days') ?></option>
                <option value="5" <?php if ( '5' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Within 5 days') ?></option>
                <option value="7" <?php if ( '7' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Within 7 days') ?></option>
                <option value="30" <?php if ( '30' == in('newly_commented') ) echo 'selected=1'?>><?php _text('Within 30 days') ?></option>
            </select>


            <label class="caption" for="newly-edited"><?php _text('Edited') ?></label>
            <select id="newly-edited" name="newly_edited">
                <option value="0" <?php if ( '0' == in('newly_edited') ) echo 'selected=1'?>><?php _text('Newly Edited') ?></option>
                <option value="1" <?php if ( '1' == in('newly_edited') ) echo 'selected=1'?>><?php _text('Today') ?></option>
                <option value="2" <?php if ( '2' == in('newly_edited') ) echo 'selected=1'?>><?php _text('Yesterday') ?></option></option>
                <option value="7" <?php if ( '7' == in('newly_edited') ) echo 'selected=1'?>><?php _text('Within 7 days') ?></option>
                <option value="30" <?php if ( '30' == in('newly_edited') ) echo 'selected=1'?>><?php _text('Within 30 days') ?></option>
            </select>

        </fieldset>

        <fieldset>
            <label class="caption" for="keyword"><?php _text('Search Text') ?></label>
            <input id="keyword" type="text" name="keyword" value="<?php echo in('keyword') ?>"/>


            <label for="created-begin">
                Works per page:
                <input type="text" name="works_per_page" size="2" value="<?php echo in('works_per_page')?>">
            </label>

        </fieldset>


        <fieldset>
            <label class="caption" for="order1"><?php _text('Order by') ?></label>
            <select id="order1" name="order1">
                <option value="" <?php if ( '' == in('order1') ) echo 'selected=1'?>><?php _text('Random') ?></option>
                <option value="priority" <?php if ( 'priority' == in('order1') ) echo 'selected=1'?>><?php _text('Priority') ?></option>
                <option value="percentage" <?php if ( 'percentage' == in('order1') ) echo 'selected=1'?>><?php _text('Percentage') ?></option>
                <option value="created" <?php if ( 'created' == in('order1') ) echo 'selected=1'?>><?php _text('Created') ?></option>
                <option value="deadline" <?php if ( 'deadline' == in('order1') ) echo 'selected=1'?>><?php _text('Deadline') ?></option>
                <option value="newly_commented" <?php if ( 'newly_commented' == in('order1') ) echo 'selected=1'?>><?php _text('Newly commented') ?></option>
            </select>

            <span id="order1_sort">
                <label>
                    <input type="radio" name="order1_sort" value="ASC" <?php if ( 'ASC' == in('order1_sort') ) echo 'checked=1'; ?>> <?php _text('Asc') ?>,
                </label>
                <label>
                    <input type="radio" name="order1_sort" value="DESC" <?php if ( 'DESC' == in('order1_sort') ) echo 'checked=1'; ?>> <?php _text('Desc') ?>,
                </label>
            </span>
        </fieldset>

        <fieldset class="order2" style="display:none;">
            <label class="caption" for="order2"><?php _text('Order by') ?></label>
            <select id="order2" name="order2">
                <option value="" <?php if ( '' == in('order2') ) echo 'selected=1'?>><?php _text('Random') ?></option>
                <option value="priority" <?php if ( 'priority' == in('order2') ) echo 'selected=1'?>><?php _text('Priority') ?></option>
                <option value="percentage" <?php if ( 'percentage' == in('order2') ) echo 'selected=1'?>><?php _text('Percentage') ?></option>
                <option value="created" <?php if ( 'created' == in('order2') ) echo 'selected=1'?>><?php _text('Created') ?></option>
                <option value="deadline" <?php if ( 'deadline' == in('order2') ) echo 'selected=1'?>><?php _text('Deadline') ?></option>
                <option value="newly_commented" <?php if ( 'newly_commented' == in('order2') ) echo 'selected=1'?>><?php _text('Newly commented') ?></option>
            </select>

            <span id="order2_sort">
                <label>
                    <input type="radio" name="order2_sort" value="ASC" <?php if ( 'ASC' == in('order2_sort') ) echo 'checked=1'; ?>> <?php _text('Asc') ?>,
                </label>
                <label>
                    <input type="radio" name="order2_sort" value="DESC" <?php if ( 'DESC' == in('order2_sort') ) echo 'checked=1'; ?>> <?php _text('Desc') ?>,
                </label>
            </span>

        </fieldset>

        <fieldset>
            <span class="caption"><?php _text('Display Columns') ?> : </span>
            <?php
                $cols = [
                    'ID' => 'No.',
                    'category' => 'Category',
                    'priority' =>  'Priority',
                    'process' =>  'Process',
                    'percentage' =>  'Percentage',
                    'worker' => 'Worker',
                    'incharge' => 'In charge',
                    'view' => 'No. of view',
                    'deadline' => 'Deadline',
                    'created' => 'Created',
                    'edited' => 'Edited'
                ];
            $in_column = in('column') ? in('column') : [];
            foreach ( $cols as $k => $v ) {
            ?>
                <label class="checkbox-inline">
                    <input class="display-column" type="checkbox" name="column[]" value="<?php echo $k?>"<?php if ( in_array( $k, $in_column ) ) echo ' checked=1'?>> <?php echo $v?>
                </label>
                <?php } ?>
        </fieldset>


        <input type="submit" value="Search Works">
        <a href="<?php forum()->urlList()?>"><?php _text('Reset Search') ?></a>
        <input type="submit" value="Show Parents" name="parent">




    </form>







    <div class="post-list">
        <?php
        $page = in('page');
        $works_per_page = in('works_per_page') ? in('works_per_page') : forum()->meta( 'posts_per_page' );
        $args = [
            'cat' => $category->term_id,
            'posts_per_page' => $works_per_page,
            'paged' => $page,
        ];

        if ( in('keyword') ) {
            $args += [ 's' => in('keyword') ];
        }


        if ( in('category') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'category', 'value'=>in('category'), 'compare' => 'IN' ];
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

        /*
        if ( in('process') && in('process') != 'A' ) {
            $args[ 'meta_query' ][] = [ 'key'=>'process', 'value'=>in('process') ];
        }
        */

        if ( in('process') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'process', 'value'=>in('process') ];
        }

        if ( in('percentage') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'percentage', 'value'=>array( 0,in('percentage') ), 'compare'=>'BETWEEN' ];
        }

        if ( in('evaluate') ) {
            $args[ 'meta_query' ][] = [ 'key'=>'evaluate', 'value'=>array( 0,in('evaluate') ), 'compare'=>'BETWEEN' ];
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
            $args[ 'meta_query' ][] = [ 'key'=>'deadline', 'value'=>array( in('deadline_begin' ),in( 'deadline_end') ),'compare'=>'BETWEEN','type'=>'DATE' ];
        }

        if ( in('newly_edited') ) {
            $today = getdate();

            if ( in('newly_edited') == '1' ){
                $args[ 'date_query' ][] = [ 'column'=>'post_modified_gmt', 'year'  => $today['year'], 'month' => $today['mon'], 'day'   => $today['mday'] ];
            }

            elseif ( in('newly_edited') == '2' ){
                $args[ 'date_query' ][] = [ 'column'=>'post_modified_gmt', 'year'  => $today['year'], 'month' => $today['mon'], 'day'   => $today['mday'] - 1 ];
            }

            elseif ( in('newly_edited') == '7' ){
                $args[ 'date_query' ][] = [ 'column'=>'post_modified_gmt', 'year' => date( 'Y' ), 'week' => date( 'W' ) ];
            }

            elseif ( in('newly_edited') == '30' ){
                $args[ 'date_query' ][] = [ 'column'=>'post_modified_gmt', 'after'=>'1 month ago' ];
            }

        }




        /*
        if ( in('order1') ) {
            if ( in('order1') == 'priority' ) {
                $sort_what = 'priority';
                $args[ 'meta_query' ]['priority'] = [ 'key'=>'priority', 'orderby'=>'meta_value_num' ];
            }

            elseif ( in('order1') == 'created' ) {
                $sort_what = 'post_date';
            }

            elseif ( in('order1') == 'deadline' ) {
                $sort_what = 'deadline';
                $args[ 'meta_query' ][] = [ 'key'=>'deadline', 'orderby'=>'meta_value date' ];
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

            if ( in('order1') ) {
                // if there's a sorting selected on 'order1', append to the current array
                $args[ 'orderby' ] += [ $sort_what=>in('order2_sort') ];
            } else {
                // else, do not append or else: Unsupported operand types error
                $args[ 'orderby' ] = [ $sort_what=>in('order2_sort') ];
            }

        }
        */


        /***
         * @todo what is this?
         */
        if( in('parent') ) {
            $arguments = array( 'posts_per_page' => -1, 'meta_key'=>'parent' );
            $posts = get_posts( $arguments );

            foreach( $posts as $post ) {
                $parent = post()->meta( $post->ID, 'parent' );
                if ( $parent != 0 ) $parents[] = $parent;
            }

            $unique = array_unique($parents);
            $args += array( 'post__in' => $unique );
        }

        $query = new WP_Query( $args );



        if ( $query->have_posts() ) { ?>

            <?php include forum()->locateTemplate( forum()->slug, 'list-meta-top') ?>

            <table class="table list">
                <thead>
                <tr>
                    <th class="ID">No.</th>
                    <th>Title</th>
                    <th class="category"><?php _text('Category') ?></th>
                    <th class="priority"><?php _text('Priority') ?></th>
                    <th class="process"><?php _text('Process') ?></th>
                    <th class="percentage"><?php _text('Percentage') ?></th>
                    <th class="worker"><?php _text('Worker') ?></th>
                    <th class="incharge"><?php _text('Incharge') ?></th>
                    <th class="view"><?php _text('View') ?></th>
                    <th class="deadline"><?php _text('Deadline') ?></th>
                    <th class="created"><?php _text('Created') ?></th>
                </tr>
                </thead>
                <tbody>

                <?php
                while ( $query->have_posts() ) {
                    post()->setup( $query );

                    $parent =  post()->meta( 'parent' );
                    if ( isset($parent) && !empty($parent) ) $parents[] = $parent;
                    ?>
                    <tr>

                        <td class="ID"><?php the_ID()?></td>
                        <td>


                            <?php
                            if ( post()->parent ) {
                                ?>
                                <span class="label label-pill label-default"><?php _text('P') ?>: <?php echo post()->parent ?></span>
                                <?php
                            }

                            if ( isset($parents) && !empty($parents) ) {
                                $unique = array_unique($parents);

                                if ( in_array( post()->ID, $unique) ) {
                                    $count = array_count_values($parents);
                                    ?>
                                    <span class="label label-pill label-default"><?php _text('children') ?>: <?php echo $count[post()->ID];  ?></span>
                                    <?php
                                }
                            }


                            ?>
                            <?php
                            if ( its::isOverdue() ) {
                                $class = 'overdue';
                            }
                            else {
                                $class = '';
                            }
                            ?>
                            <a class="<?php echo $class?>" href="<?php the_permalink()?>">

                                <?php the_title()?>
                                <?php forum()->count_comments( get_the_ID() ) ?>
                                <?php if ( $p = post()->percentage ) {
                                    if ( $p < 50 ) $effect = "label-info";
                                    else if ( $p < 70 ) $effect = "label-info";
                                    else if ( $p < 90 ) $effect = "label-warning";
                                    else $effect = "label-danger";
                                    ?>
                                    <span class="label label-pill <?php echo $effect?>" title="Percentage of work."><?php _text('P') ?>: <?php echo $p?>%</span>
                                <?php } ?>

                                <?php if ( post()->process == 'A') { ?>
                                    <span class="label label-pill label-primary"><?php _text('approved') ?></span>
                                <?php } else if ( post()->process == 'R') { ?>
                                    <span class="label label-pill label-warning"><?php _text('rejected') ?></span>
                                <?php } else if ( its::isOverdue() )  { ?>
                                    <span class="label label-pill label-danger"><?php _text('overdue') ?></span>
                                <?php } ?>


                                <?php
                                /**
                                 * @todo comparing with numeric index(key) is no good.
                                 */
                                if ( post()->priority == 60 ) { ?>
                                    <span class="label label-pill label-danger"><?php _text('critical') ?></span>
                                <?php } else if ( post()->priority == 50 ) { ?>
                                    <span class="label label-pill label-warning"><?php _text('immediate') ?></span>
                                <?php } ?>

                            </a>
                        </td>
                        <td class="category"><?php echo post()->category?></td>
                        <td class="priority"><?php if ( post()->priority ) echo its::$priority[ post()->priority ]; ?></td>
                        <td class="process"><?php echo post()->process?></td>
                        <td class="percentage"><progress value='<?php echo $p?>' max='100'></progress></td>
                        <td class="worker"><?php echo post()->worker?></td>
                        <td class="incharge"><?php echo post()->incharge?></td>
                        <td class="view"><?php echo post()->getNoOfView( get_the_ID() )?></td>
                        <td class="deadline"><?php e( post()->deadline ) ?></td>
                        <td class="created"><?php echo get_the_date();?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <?php include forum()->locateTemplate( forum()->slug, 'pagination') ?>


        <?php } ?>
    </div>

<?php get_footer(); ?>