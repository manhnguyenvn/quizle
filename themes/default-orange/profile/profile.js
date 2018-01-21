function GetPosts() {
    if($("#latest-posts-outer-container").attr('data-in-progress') == 1 || $("#latest-posts-outer-container").attr('data-more-pages') == 0)
        return;

    $("#latest-posts-outer-container").attr('data-in-progress', 1);
    $("#posts-loader").show();

    var current_page = parseInt($("#latest-posts-outer-container").attr('data-current-page'), 10),
        user_id = $("#latest-posts-outer-container").attr('data-user-id');
    $.ajax({
        type: 'GET',
        url: LOCATION_SITE + 'ajax/get-posts.php?command=GetUserPosts',
        cache: false,
        data: { page_no: current_page + 1 , user_id: user_id },
        dataType: 'JSON',
        success: function(response) { 
            $("#latest-posts-outer-container").attr('data-in-progress', 0).attr('data-more-pages', response.more_pages).attr('data-current-page', current_page + 1);
            $("#posts-loader").hide();

            var html = '';
            
            for(var i=0; i<response.posts.length; i++) {
                html += '<div class="user-post-container">';
                html += '<a class="post-image" href="post.php?post_id=' + response['posts'][i]['post_id'] + '"><img src="' + LOCATION_SITE + 'img/QUIZ/quiz/m-' + response['posts'][i]['post_image_id'] + '" /></a>';
                html += '<div class="post-text">';
                html += '<a class="post-title" href="post.php?post_id=' + response['posts'][i]['post_id'] + '">' + response['posts'][i]['post_title'] + '</a>';
                html += '<div class="post-description">' + response['posts'][i]['post_description'] + '</div>';
                html += '</div>';
                html += '</div>';
            }

            $("#latest-posts-inner-container").append(html);

            if($("#latest-posts-outer-container").attr('data-more-pages') == 0)
                $("#more-posts-container").hide();
            else
                $("#more-posts-container").show();
        },
        error: function(response) {
            $("#latest-posts-outer-container").attr('data-in-progress', 0);
            $("#posts-loader").hide();
        }
    });
}

$("#more-posts-button").on('click', function() {
    $("#more-posts-container").hide();
    GetPosts();
});