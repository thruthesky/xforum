<?php
/**
 *
 *
 * ---------------------------------------------------------------------------
 *
 *
 * W A R N I N G : Theme Development on Desktop version has been discontinued.
 *
 * B E C A U S E : People use mobile for web browsing
 *
 *      And 'Mobile theme version' can handle desktop also.
 *
 *      So, we develop mobile theme version only which works on desktop also.
 *
 *
 * ---------------------------------------------------------------------------
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
?>
<?php
    global $wp_query;
?>
<?php if ( $query->max_num_pages > 1) : // custom pagination  ?>
    <?php
    $orig_query = $wp_query; // fix for pagination to work
    $wp_query = $query;
    ?>

    <?php the_posts_pagination( array(
        'base' => home_url("?page=%#%"),
        // 설명에는 format 파라메타를 사용하라고 하는데, format 파라메타 적용이 잘 안된다.
        // %#% 자리에 실제 페이지 번호가 들어간다.
        // 이렇게 하면 적당히 URL 이 만들어 진다.
        'mid_size' => 3,
        // 양 옆으로 3개. 중간에 1개. 총 7개. 3이 적당.
        'prev_text' => __( 'Prev', 'textdomain' ),
        'next_text' => __( 'Next', 'textdomain' ),
    ) ); ?>

    <?php
    $wp_query = $orig_query; // fix for pagination to work
    ?>
<?php endif; ?>
