$(".user-post-hide-button, .user-post-unhide-button").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    var $post = { post_id: $(this).closest('.user-post-container').attr('data-post-id'), post_type: 'QUIZ' },
        that = this,
        command;

    if($(this).attr('data-type') == 'hide') {
        if(confirm(LANGUAGE_STRINGS['HIDE_POST_BUTTON_MESSAGE']) == false)
            return;
        command = 'HidePost';
    }
    else
        command = 'ShowPost'; 

    $(this).attr('data-in-progress', 1).css('opacity', '0.5');
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/save-post.php?command=' + command,
        cache: false,
        data: $post,
        dataType: 'JSON',
        success: function(response) { 
            $(that).attr('data-in-progress', 0).css('opacity', '1');

            $(that).hide();
            if(command == 'HidePost') {
                $(that).next().show();
                $(that).closest('.post-text').find('.post-hidden-mode-label').show();
            }
            else {
                $(that).prev().show();
                $(that).closest('.post-text').find('.post-hidden-mode-label').hide();
            }
        },
        error: function(response) {
            $(that).attr('data-in-progress', 0).css('opacity', '1');
        }
    });
});

$(".user-post-delete-button").on('click', function() {
    if($(this).attr('data-in-progress') == 1)
        return;

    var $post = { post_id: $(this).closest('.user-post-container').attr('data-post-id'), post_type: 'QUIZ' },
        that = this;

    if(confirm(LANGUAGE_STRINGS['DELETE_POST_BUTTON_MESSAGE']) == false)
        return;

    $(this).attr('data-in-progress', 1).css('opacity', '0.5');
    $.ajax({
        type: 'POST',
        url: LOCATION_SITE + 'ajax/save-post.php?command=DeletePost',
        cache: false,
        data: $post,
        dataType: 'JSON',
        success: function(response) { 
            $(that).closest('.user-post-container').remove();
            if($('.user-post-container').length == 0)
                $("#no-user-posts-message").show();
        },
        error: function(response) {
            $(that).attr('data-in-progress', 0).css('opacity', '1');
        }
    });
});