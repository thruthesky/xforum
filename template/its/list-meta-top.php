<div>
    Time: <?php echo date('Y-m-d H:i')?>,
    No of works: <?php echo $query->found_posts?>

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

        <span class="btn btn-primary btn-sm">Help</span>
        <div>
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
