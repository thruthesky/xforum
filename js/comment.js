( function ( $ ) {

    var $body = $('body');
    $body.on('click', '.comment .reply', comment_reply);
    $body.on('click', '.comment .edit', comment_edit);
    $body.on('click', '.comment .delete', comment_delete);
    $body.on('click', '.comment .report', comment_report);
    $body.on('click', '.comment .like', comment_like);
    $body.on('click', '.comment-cancel-button', comment_cancel);

    function comment_reply() {
        remove_all_comment_form_under_comment_list();
        var $this = $(this);
        var $comment = get_comment_parent( $this );
        var $content = get_closest_content( $this );
        var parent_ID = get_comment_ID( $comment );
        var t = _.template($('#comment-form-template').html());
        $content.append(t({ parent_ID : parent_ID, comment_ID : 0, text: text }));
    }
    function comment_edit() {
        remove_all_comment_form_under_comment_list();
        var $this = $(this);
        var $comment = get_comment_parent( $this );
        var comment_ID = get_comment_ID( $comment );
        //$('.comment[comment_ID="'+comment_ID+'"]');
        $comment.hide();
        var text = $comment.find('.text').text();
        var t = _.template($('#comment-form-template').html());
        $comment.after(t({ parent_ID : 0, comment_ID : comment_ID, text: text }));
    }
    function comment_delete() {
        var $this = $(this);
        var $comment = get_comment_parent( $this );
        var comment_ID = get_comment_ID( $comment );
        alert( comment_ID );
    }
    function comment_report() {
        var $this = $(this);
        var $comment = get_comment_parent( $this );
    }
    function comment_like() {
        var $this = $(this);
        var $comment = get_comment_parent( $this );
    }
    function comment_cancel() {
        var $button = $(this);
        var $form = $button.parents('form');
        var comment_ID = $form.find("[name='comment_ID']").val();
        remove_all_comment_form_under_comment_list();
    }

    /**
     *
     * @param $this - jQuery object of .comment children.
     * @returns {*}
     */
    function get_comment_parent( $this ) {
        return $this.closest('.comment');
    }

    function get_closest_content( $this ) {
        return $this.closest('.content');
    }



    /**
     *
     * @param $comment - jQuery object of .comment
     * @returns int - comment ID
     */
    function get_comment_ID( $comment ) {
        return $comment.attr('comment_ID');
    }



    function remove_all_comment_form_under_comment_list() {
        $(".comment-list .comment-form").remove();
        $(".comment-list .comment").show();

    }


} ) ( jQuery );
