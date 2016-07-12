<?php
$ex = explode('/', get_category_parents( forum()->getCategory()->term_id, false, '/', true));
if ( $ex ) {

?>

<script>
    window.addEventListener('load', function(){
        ( function( $ ) {
            $("span[name='help_button']").click(function () {
                    $("#help_content").show();
            });

        }) ( jQuery );
    });
</script>
<ol class="breadcrumb" style="margin-bottom: 5px;">
    <li><a href="<?php home_url()?>">Home</a></li>
    <?php
    foreach ( $ex as $slug ) {
        if ( empty($slug) ) continue;
        if ( $slug == FORUM_CATEGORY_SLUG ) continue;
        if ( $slug == forum()->slug ) continue;
        $url = forum()->getUrlList( $slug );
        echo "<li><a href='$url'>$slug</a></li>";
    }
    ?>
    <li class="active"><?php echo forum()->slug?></li>
</ol>
<?php } ?>

<div>
    Time: <?php echo date('Y-m-d H:i')?>,
    No of works: <?php echo $query->found_posts?>,

    <?php
    $category = forum()->getCategory()->term_id;
    $posts = get_posts();
    foreach ( $posts as $post ) {
        if ( isset($post->parent) ) {
            $children = $post->parent;
            $args = [
                'cat' => $category->term_id,
                'post__in' => [$children]
            ];
            $child[] = get_posts($args);
        }
    }
    ?>
    No of dependent children: <?php echo count($child);?>

    <?php
    $args = [
        'fields' => 'ids',
        'posts_per_page' => -1,
    ];
    $args['meta_query'][] = [
        'key'       => 'deadline',
        'value'     => [date('Y-m-d', time() - 86400 * 365), date('Y-m-d', time()) ],
        'type'      => 'DATE',
        'compare'   => 'BETWEEN',
    ];
    $args['meta_query'][] = [
        'relation' => 'OR',
        [
            'key'       => 'process',
            'value'     => ['A', 'R'],
            'type'      => 'CHAR',
            'compare'   => 'NOT IN',
        ],
        [
            'key'       => 'process',
            'compare'   => 'NOT EXISTS',
        ],
    ];
    $q = new WP_Query( $args );



    $found = $q->found_posts;


    if ( $found ) {

    ?>


    <a href="<?php forum()->urlList()?>&deadline_end=<?php echo date('Y-m-d')?>&process[]=N&process[]=P&process[]=F"><span class="label label-pill label-danger">Overdue: <?php echo $found?></span></a>

        <span class="btn btn-primary btn-sm" name="help_button">Help</span>
        <div id="help_content" style="display: none;">
            <ul>
                <li>
                    Overdue:
                    <ul>
                        <li>Overdue icon appears on the top and if clicked, overdue posts will be shown.</li>
                        <li>Title color is red.</li>
                    </ul>
                </li>
            </ul>
        </div>


    <?php } ?>

</div>
