<div id="latest-posts-outer-container" data-in-progress="0" data-current-page="1" data-more-pages="<?= $__DATA['more_pages'] ?>">
    <div id="latest-posts-inner-container">
        <?php
        $loop_counter = 0;
        $html = '';
        foreach($__DATA['posts'] as $post_id => $post_info) {
            if($__AD_UNIT_1_POSITION == $loop_counter && $__AD_UNIT_1 != NULL) {
                $html .= '<div class="latest-post latest-post-ad">';
                $html .= '<div class="latest-post-ad-title">' . $__LANGUAGE_STRINGS['home_page']['ADVERTISEMENT'] . '</div>';
                $html .= '<div class="post-ad-unit" style="width:' . $__AD_UNIT_1['ad_width'] . 'px;height:' . $__AD_UNIT_1['ad_height'] . 'px">' . $__AD_UNIT_1['ad_code'] . '</div>';
                $html .= '</div>';
            }
    
            $html .= '<div class="latest-post">';
            $html .= '<a href="' . $post_info['post_url'] . '" class="latest-post-title">' . $post_info['title']. '</a>';
            $html .= '<a href="' . $post_info['post_url'] . '" class="latest-post-image" style="background-image:url(\'' . LOCATION_SITE . 'img/QUIZ/quiz/m-' . $post_info['image']  . '\')"></a>';
            $html .= '<div class="latest-post-text">'; 
            $html .= '<div class="latest-post-description">' . $post_info['description']. '</div>';
            $html .= '<div class="latest-post-mics"><span class="latest-post-by">' . $__LANGUAGE_STRINGS['home_page']['POST_BY'] . '</span><a href="' . $post_info['profile_url'] . '" class="latest-post-user">' . $__DATA['users'][$post_info['user_id']] . '</a><span class="latest-post-time">' . $post_info['time'] . ' ' . $__LANGUAGE_STRINGS['home_page']['POST_TIME_AGO'] . '</span></div>';
            $html .= '</div>';
            $html .= '</div>';

            $loop_counter++;
        }

        $html .= '<div class="latest-post latest-post-create"><a href="' . LOCATION_SITE . str_replace('URL_NAME', 'user-quiz', $__NO_PARAMETER_URL) . '">' . $__LANGUAGE_STRINGS['home_page']['CREATE_OWN_POST'] . '</a></div>';

        echo $html;
        ?>
    </div>
</div>
<div id="posts-loader"><i class="fa fa-spinner fa-spin"></i></div>
<div id="more-posts-container"><button id="more-posts-button" class="theme-active-button"><?= $__LANGUAGE_STRINGS['home_page']['SHOW_MORE_POSTS'] ?></button></div>