<?php
if ( ! isset( $_REQUEST['settings-updated'] ) )
    $_REQUEST['settings-updated'] = false;
?>
<div class="wrap">
    <hr>
    <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
        <div class="updated fade"><p><strong><?php _text( 'Settings saved' ); ?></strong></p></div>
    <?php endif; ?>
    <h2>Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields( 'xforum' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    xForum Domain
                </th>
                <td>
                    <input type='text' name="xforum[domain]" value='<?php opt('xforum[domain]') ?>' /><input type="submit">
                </td>
            </tr>
        </table>
    </form>



    <h2>Translation</h2>
    <?php
    if ( isset($_REQUEST['mode'] ) && $_REQUEST['mode'] == 'translate' ) {
        $_REQUEST['original_text'] = stripslashes($_REQUEST['original_text']);
        $_REQUEST['content'] = stripslashes($_REQUEST['content']);
        if ( empty( $_REQUEST['original_text'] ) ) $_REQUEST['original_text'] = '&nbsp;';
        $option_name = $_REQUEST['option_name'];


        delete_option( $option_name );
        add_option( $option_name, ['original_text' => $_REQUEST['original_text'], 'content' => $_REQUEST['content']] );

    }

    $files = getFiles( DIR_XFORUM , true, '/php/' );
    //$files2 = getFiles( get_stylesheet_directory(), true, '/php/' );
    //$files = array_merge( $files2, $files );


    foreach( $files as $file ) {

        $content = file_get_contents($file);

        $count = preg_match_all("/_text\(['\"](.*)['\"]\)/im", $content, $matches);

        if ( $count ) {
            $patterns = $matches[0];
            $codes = $matches[1];
            for( $i = 0; $i < count($patterns); $i ++ ) {
                $pattern = $patterns[$i];
                $code = $str = $codes[$i];
                $md5 = md5($str);
                $option_name = get_text_translation_option_name( $md5 );
                di($option_name);
                $org = esc_html($str);
                $str = _getText($str);
                ?>
                <form action="admin.php?page=xforum%2Ftemplate%2Fadmin-text-translation.php" method="POST">
                    <input type="hidden" name="mode" value="translate">
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
