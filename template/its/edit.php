<?php get_header(); ?>

    <h1>Create an Issue</h1>

    <form action="?">
        <input type="hidden" name="forum" value="edit_submit">
        <input type="hidden" name="slug" value="<?php echo in('slug')?>">
        <input type="hidden" name="on_error" value="alert_and_go_back">
        <input type="hidden" name="return_url" value="?forum=list&id=<?php echo in('slug')?>">

        <div><input type="text" name="title"></div>
        <div>

        </div>
        <div><textarea name="content"></textarea></div>
        <input type="submit">
    </form>

<?php get_footer(); ?>