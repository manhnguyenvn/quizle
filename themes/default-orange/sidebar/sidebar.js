$.ajax({
      type: 'GET',
      url: LOCATION_SITE + 'ajax/sidebar.php',
      cache: false,
      dataType: 'JSON',
      success: function(response) { 
            var html = '';

            if(response.ads.length > 0) {
                  html += '<div class="sidebar-ad-container sidebar-container"><div class="sidebar-ad" style="width:' + response.ads[0]['ad_width'] + 'px;height:' + response.ads[0]['ad_height'] + 'px">' + response.ads[0]['ad_code'] + '</div></div>';
            }

            if(response.posts.length > 0) {
                  for(var i=0; i<(response.posts.length >= 3 ? 3 : response.posts.length); i++) {
                        html += '<a class="sidebar-post-container sidebar-container" href="' + response.posts[i]['post_url'] + '"><img src="' + LOCATION_SITE + 'img/QUIZ/quiz/m-' + response.posts[i]['image'] + '" /><div class="sidebar-post-title">' + response.posts[i]['title'] + '</div></a>';
                  }
            }

            if(response.ads.length > 1) {
                  html += '<div class="sidebar-ad-container sidebar-container"><div class="sidebar-ad" style="width:' + response.ads[1]['ad_width'] + 'px;height:' + response.ads[1]['ad_height'] + 'px">' + response.ads[1]['ad_code'] + '</div></div>';
            }

            if(response.posts.length > 3) {
                  for(i=3; i<(response.posts.length >= 6 ? 6 : response.posts.length); i++) {
                        html += '<a class="sidebar-post-container sidebar-container" href="' + response.posts[i]['post_url'] + '"><img src="' + LOCATION_SITE + 'img/QUIZ/quiz/m-' + response.posts[i]['image'] + '" /><div class="sidebar-post-title">' + response.posts[i]['title'] + '</div></a>';
                  }
            }

            $("#sidebar-posts-ad-container").prepend(html);
      },
      error: function(response) {
          
      }
});