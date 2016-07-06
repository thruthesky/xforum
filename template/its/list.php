
<?php
//$posts = "";
//if($_REQUEST['search'] || in('assigned_to_user')){
//    $posts = post()->issue_search();
////    var_dump(count($posts));
//}else{
//    $posts = post()->getPosts();
//}
//
get_header();

$category = forum()->getCategory();
$posts = get_posts(
    [ 'category' => $category->cat_ID, ]);



/*Custom CSS*/
wp_enqueue_style( 'xforum-list', URL_XFORUM . 'css/its/forum-list.css' );
?>
<div class="wrap">
    <div class="col-lg-12 pull-lg-right padding-bottom">
        <?php forum()->list_menu_user()?>
    </div>

    <form action="?">
        <input type="hidden" name="forum" value="search_submit">
        <input type="hidden" name="response" value="list">
        <?php if ( in('slug') ) { ?>
            <input type="hidden" name="slug" value="<?php echo in('slug')?>">
        <?php } ?>
        <input type="hidden" name="on_error" value="alert_and_go_back">
        <input type="hidden" name="return_url" value="<?php forum()->urlList()?>">

        <div class="col-lg-2">
            <select class="c-select" name="deadline">
                <option selected disabled>Search by Deadline</option>
                <option value="today">Today</option>
                <option value="within_week">Within a Week</option>
                <option value="within_month">Within a Month</option>
                <option value="overdue">Overdue</option>
                <option value="over_week">Over a Week</option>
                <option value="over_month">Over a Month</option>
            </select>
        </div>

        <div class="col-lg-6 input-group">
            <div class="input-group">
                <div class="input-group-btn">
                    <button tabindex="-1" class="btn btn-secondary" type="button">Search Filters</button>
                    <button tabindex="-1" data-toggle="dropdown" class="btn btn-secondary dropdown-toggle" type="button">
                        <span class="caret"></span>
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#"> <input type="checkbox" value="" name="filter[]">Search All</a>
                        <a class="dropdown-item" href="#"> <input type="checkbox" value="" name="filter[]">Members</a>
                        <a class="dropdown-item" href="#"> <input type="checkbox" value="" name="filter[]">Deadline</a>
                        <a class="dropdown-item" href="#"> <input type="checkbox" value="" name="filter[]">Issue Information</a>
                        <a class="dropdown-item" href="#"><input type="checkbox" value="" name="filter[]">Issue Status</a>
                        <a class="dropdown-item" href="#"><input type="checkbox" value="" name="filter[]">Date Posted</a>
                    </div>
                </div>
                <input type="text" class="form-control" name="search_field" placeholder="Search..">
            </div>
        </div>



        <div class="col-lg-2">
            <input type="submit" class="btn btn-danger" value="Search!" name="search">
        </div>
    </form>


    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9">
        <?php
         if ( $posts ) :
            foreach ($posts as $post) : setup_postdata($post); ?>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 padding-top">
                    <div class="card ">
                        <div class="card-header">
                            <b>Work Progress</b> <?php ?>
                        </div>
                        <div class="card-block">
                            <h5 class="card-title">
                                <a href="<?php the_permalink()?>"><?php the_title()?></a>
                                <?php forum()->count_comments( get_the_ID() ) ?>
                            </h5>
                            <p class="card-text">
                                <b>Workers: </b>
                                <?php echo post()->meta( get_the_ID(),'workers' ); ?> <br/>
                                <b>In-charge: </b> <?php the_author(); ?><br/>
                                <b>Deadline: </b> <?php echo post()->meta( get_the_ID(),'deadline' ); ?> <br/>
                                <small>Views: <?php echo post()->getNoOfView( get_the_ID() )?> || Posted on: <?php the_date(); ?></small>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif ?>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3">
        <div class="col-lg-12">
            <a class="btn btn-success btn-block" href="<?php forum()->urlWrite()?>">New Issue</a>
        </div>
        <div class="col-lg-12 padding-top">
            <b>Project Statistics</b><br/>
            Member 1 had finished  40%
            <progress class="progress progress-striped" value="40" max="100">40%</progress>
            Member 2 had finished  20%
            <progress class="progress progress-striped progress-danger" value="20" max="100">20%</progress>
        </div>
    </div>

</div>
<?php get_footer(); ?>


