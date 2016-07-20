<?php
if ( ! isset( $_REQUEST['settings-updated'] ) )
    $_REQUEST['settings-updated'] = false;


wp_enqueue_style( 'font-awesome', URL_XFORUM . 'css/font-awesome/css/font-awesome.min.css' );
wp_enqueue_style( 'bootstrap', URL_XFORUM . 'css/bootstrap/css/bootstrap.min.css');

?>
<div class="wrap">
    <hr>
    <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
        <div class="updated fade"><p><strong><?php _text( 'Settings saved' ); ?></strong></p></div>
    <?php endif; ?>
    <h2>Settings</h2>

    <h3>Selection Language</h3>
    <p>
        Select a language to translate.
    </p>
    <a class="btn btn-secondary" href="admin.php?page=xforum%2Ftemplate%2FadminTextTranslation.php&language=en">English</a>
    <a class="btn btn-secondary" href="admin.php?page=xforum%2Ftemplate%2FadminTextTranslation.php&language=ko">Korean</a>



    <h3>Translation</h3>
    <?php
    $language = in('language') ? in('language') : 'en';


    if ( isset($_REQUEST['mode'] ) && $_REQUEST['mode'] == 'submit' ) {
        $_REQUEST['original_text'] = stripslashes($_REQUEST['original_text']);
        $_REQUEST['content'] = stripslashes($_REQUEST['content']);
        if ( empty( $_REQUEST['original_text'] ) ) $_REQUEST['original_text'] = '&nbsp;';
        $option_name = $_REQUEST['option_name'];

        delete_option( $option_name );
        $option = ['original_text' => $_REQUEST['original_text'], 'content' => $_REQUEST['content']];
        add_option( $option_name, $option );

    }



    $files = getFiles( DIR_XFORUM , true, '/php/' );
    $files2 = getFiles( get_stylesheet_directory(), true, '/php/' );
    $files = array_merge( $files2, $files );



    foreach( $files as $file ) {

        $content = file_get_contents($file);

        $count = preg_match_all("/_text\(['\"](.*)['\"]\)/im", $content, $matches);

        if ( $count ) {
            $patterns = $matches[0];
            $codes = $matches[1];
            echo "file: $file<br>";
            for( $i = 0; $i < count($patterns); $i ++ ) {
                $pattern = $patterns[$i];
                $code = $str = $codes[$i];
                $option_name = _getLanguageCode( $language, $str );

                $org = esc_html($str);
                $str = _getText($str, $option_name);
                di($option_name);
                ?>
                <form action="admin.php?page=xforum%2Ftemplate%2FadminTextTranslation.php" method="POST">
                    <input type="hidden" name="mode" value="submit">
                    <input type="hidden" name="language" value="<?php echo $language?>">
                    <input type="hidden" name="option_name" value="<?php echo $option_name?>">
                    <input type="hidden" name="original_text" value="<?php echo $org?>">

                    <?php echo $code?><br>
                    <textarea name="content" style='width:80%;'><?php echo $str?></textarea>
                    <input type="submit">
                </form>
                <?php
            }
            echo "<hr>";
        }
    }
    ?>
</div><!--/wrap-->

