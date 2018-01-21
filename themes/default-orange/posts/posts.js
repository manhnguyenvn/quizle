function GetPosts() {
    if($("#latest-posts-outer-container").attr('data-in-progress') == 1 || $("#latest-posts-outer-container").attr('data-more-pages') == 0)
        return;

    $("#latest-posts-outer-container").attr('data-in-progress', 1);
    $("#posts-loader").show();

    var current_page = parseInt($("#latest-posts-outer-container").attr('data-current-page'), 10);
    $.ajax({
        type: 'GET',
        url: LOCATION_SITE + 'ajax/get-posts.php?command=GetPostsByTag',
        cache: false,
        data: { page_no: current_page + 1, tag_id: $("#latest-posts-outer-container").attr('data-tag-id'), is_cat: $("#latest-posts-outer-container").attr('data-is-cat') },
        dataType: 'JSON',
        success: function(response) { 
            $("#latest-posts-outer-container").attr('data-in-progress', 0).attr('data-more-pages', response.more_pages).attr('data-current-page', current_page + 1);
            $("#posts-loader").hide();

            var loop_counter = 0,
                html = '',
                ad_unit_position = Math.floor((Math.random() * Object.keys(response.posts).length));
            
            for(var post_id in response.posts) {
                if(ad_unit_position == loop_counter && response.ad_unit != null) {
                    html += '<div class="latest-post latest-post-ad">';
                    html += '<div class="latest-post-ad-title">' + LANGUAGE_STRINGS['ADVERTISEMENT'] + '</div>';
                    html += '<div class="post-ad-unit" style="width:' + response.ad_unit['ad_width'] + 'px;height:' + response.ad_unit['ad_height'] + 'px">' + response.ad_unit['ad_code'] + '</div>';
                    html += '</div>';
                }

                html += '<div class="latest-post">';
                html += '<a href="' + response.posts[post_id]['post_url'] + '" class="latest-post-title">' + response.posts[post_id]['title'] + '</a>';
                html += '<a href="' + response.posts[post_id]['post_url'] + '" class="latest-post-image" style="background-image:url(\'' + LOCATION_SITE + 'img/QUIZ/quiz/m-' + response.posts[post_id]['image']  + '\')"></a>';
                html += '<div class="latest-post-text">'; 
                html += '<div class="latest-post-description">' + response.posts[post_id]['description'] + '</div>';
                html += '<div class="latest-post-mics"><span class="latest-post-by">' + LANGUAGE_STRINGS['POST_BY'] + '</span><a href="' + response.posts[post_id]['profile_url'] + '" class="latest-post-user">' + response['users'][response.posts[post_id]['user_id']] + '</a><span class="latest-post-time">' + response.posts[post_id]['time'] + ' ' + LANGUAGE_STRINGS['POST_TIME_AGO'] + '</span></div>';
                html += '</div>';
                html += '</div>';

                loop_counter++;
            }

            $("#latest-posts-inner-container").append(html);

            if($("#latest-posts-outer-container").attr('data-current-page') == 2) {
                $(window).off('scroll');
                if($("#latest-posts-outer-container").attr('data-more-pages') == 1)
                    $("#more-posts-container").show();
            }
            else if($("#latest-posts-outer-container").attr('data-current-page') > 2) {
                if($("#latest-posts-outer-container").attr('data-more-pages') == 0)
                    $("#more-posts-container").hide();
                else
                    $("#more-posts-container").show();
            }
        },
        error: function(response) {
            $("#latest-posts-outer-container").attr('data-in-progress', 0);
            $("#posts-loader").hide();
        }
    });
}

$(window).on('scroll', function() {
    if($(window).scrollTop() > $(document).height() - $(window).height() - 300 ) {
        GetPosts();
    }
});

$("#more-posts-button").on('click', function() {
    $("#more-posts-container").hide();
    GetPosts();
});