<?php
//include_once DIR_XFORUM . 'template/its/init.php'; // this is done by core.

get_header();
wp_enqueue_script('xforum-post', URL_XFORUM . 'js/post.js');
?>


<?php

// uploaded files
// di( post()->meta( get_the_ID(), 'files' ) );



?>


<style>

    dl dd {
        display: inline;
        margin: 0;
    }
    dl dd:after{
        display: block;
        content: '';
    }
    dl dt{
        display: inline-block;
        min-width: 120px;
    }


    .children {

    }
    .children a {
        display: block;
        padding: .4em 0;
    }
    .children a[depth="1"] {
        color: blue;
    }
    .children a[depth="2"] {
        margin-left: 1em;
        color: #444;
    }
    .children a[depth="3"] {
        margin-left: 2em;
        color: #777;
    }
    .children a[depth="4"] {
        margin-left: 3em;
        color: #999;
    }
    .children a[depth="5"],
    .children a[depth="6"],
    .children a[depth="7"],
    .children a[depth="8"]
    {
        margin-left: 4em;
        color: #bbb;
    }
</style>

<article class="forum its">
    <header>
        <h1><?php the_title()?></h1>
        <dl class="meta">
            <dt><?php _text('Author') ?>:</dt>
            <dd><address rel="author"><?php the_author()?></address></dd>
            <dt><?php _text('Date') ?>:</dt>
            <dd><time pubdate datetime="<?php echo get_the_date("Y-m-d")?>" title="<?php echo get_the_date("h:i a on F dS, Y")?>"><?php echo get_the_date("h:i a - F dS, Y")?></time></dd>
            <dt><?php _text('No of views') ?>:</dt>
            <dd><?php echo $GLOBALS['post_view_count']?></dd>
            <dt><?php _text('Worker') ?></dt><dd><?php echo post()->worker; ?></dd>
            <dt><?php _text('Deadline') ?></dt><dd><?php echo date( 'M d, Y', strtotime( post()->deadline) );?></dd>
            <dt><?php _text('Work Status') ?></dt>
            <dd><?php
                $p = post()->meta( 'process' );
                if ( $p ) {
                    echo its::$process[ $p ];
                    if ( $p == 'P' ) {
                        $percentage = post()->percentage;
                        echo "<progress value='$percentage' max='100'></progress> $percentage%";
                    }
                }
                else {
                    echo "No process code";
                }

                ?>

                <?php
                $evaluation = post()->evaluate;
                $comment = post()->evaluate_comment;
                if ( $evaluation ) {
                ?>
            <dt><?php _text('Work Evaluation') ?></dt>
            <dd>
            <?php
            echo "<progress value='$evaluation' max='100'></progress> $evaluation%";
            }
            if ( $comment ) {
                echo "<br/><b><?php _text('valuation Comment') ?>E:</b> $comment";
            }

            ?>
            </dd>
            <dt><?php _text('In Charge') ?></dt><dd><?php echo post()->meta( 'incharge' ); ?></dd>
            <dt><?php _text('Prority') ?></dt><dd><?php echo @its::$priority[ post()->priority ]?></dd>
            <dt><?php _text('Select parent of this work.') ?></dt><dd>

                <style>
                    .ajax-search {
                        position: absolute;
                        z-index: 1001234;
                        padding: 1em;
                        background-color: white;
                    }
                </style>
                <script>

                    window.addEventListener('load', function() {
                        var autocomplete_ajax_progress = false;
                        $('[name="parent"]').keyup(function( e ){
                            if ( e.keyCode == 27 ) {
                                $('.ajax-search').remove();
                                return;
                            }
                            var $this = $(this);
                            var keyword = $this.val();
                            if ( keyword.length >= 2 ) {
                                if ( autocomplete_ajax_progress ) return false;
                                autocomplete_ajax_progress = true;
                                var url = '<?php echo home_url("?forum=ajax_search&slug=" . forum()->slug)?>&keyword=' + keyword;
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
                        $('body').on('click', '.ajax-search a', function(e) {
                            e.preventDefault();
                            var no = $(this).parent().attr('no');
                            $('.ajax-search').remove();
                            var url = "<?php echo home_url()?>?forum=api&action=meta_update&post_ID=<?php the_ID()?>&key=parent&value="+no;
                            console.log(url);
                            $.get( url, function(re) {
                                if ( re.success ) location.reload();
                                else {
                                    alert("Error: failed to set parent");
                                    console.log(re);
                                }
                            } );
                        });
                        $(document).click(function(e) {
                            $('.ajax-search').remove();
                        });

                    });

                </script>
                <input type="text" name="parent" value="<?php post()->parent?>" placeholder="<?php _text('Search a post and put it as dependency parent') ?>">

            </dd>
        </dl>
    </header>
    <main class="content">
        <?php the_content()?>
    </main>

    <div class="children">
        <?php
        function get_its_root( $post_ID ) {
            $parent = post( $post_ID )->parent;
            if ( $parent ) get_its_root( $parent );

        }
        $parent = 0;
        $pid = get_the_ID();
        while ( $pid ) {
            $parent = $pid;
            $pid = post( $pid )->parent;
        }
        function recursive_children( $post_ID, $depth = 0 ) {
            $depth ++;
            $args = [
                'meta_query' => [
                    ['key' => 'parent', 'value'=>$post_ID],
                ]
            ];
            $q = new WP_Query( $args );
            if ( $q->have_posts() ) {
                while( $q->have_posts() ) {
                    $q->the_post();
                    ?>
                    <a depth="<?php echo $depth?>" href="<?php the_permalink()?>">
                        [<?php the_ID()?>]
                        <?php the_title()?>
                        by <?php the_author()?>
                    </a>
                    <?php
                    recursive_children( get_the_ID(), $depth );
                }
            }
            wp_reset_postdata();
        }
        recursive_children( $parent );
        ?>
    </div>
</article>

<nav class="buttons">
    <?php forum()->button_new(['text'=>'Create Dependent', 'query'=>"parent=".get_the_ID()])?>
    <?php forum()->button_edit()?>
    <?php forum()->button_delete()?>
    <?php forum()->button_list()?>
    <?php forum()->list_menu_user()?>

</nav>


<?php
// If comments are open or we have at least one comment, load up the comment template.
if ( comments_open() || get_comments_number() ) {
    comments_template();
}

?>


<?php get_footer(); ?>

