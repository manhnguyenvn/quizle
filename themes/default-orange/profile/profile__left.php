<div id="user-picture-name">
    <img src="<?= $__USER_DETAILS['user_picture_url'] ?>" /><!--
 --><div id="user-name-container"><div id="user-name-inner-container"><span><?= $__USER_DETAILS['user_full_name'] ?></span></div></div>
</div>
<div id="latest-posts-outer-container" data-in-progress="0" data-current-page="1" data-more-pages="<?= $__DATA['more_pages'] ?>" data-user-id="<?= $_GET['user_id'] ?>">
    <div id="latest-posts-inner-container">
        <?php
        $html = '<div id="no-user-posts-message" style="' . (sizeof($__DATA['posts']) == 0 ? '' : 'display:none') . '">' . $__LANGUAGE_STRINGS['user_post_list']['NO_AVAILABLE_POSTS'] . '</div>';
        for($i=0; $i<sizeof($__DATA['posts']); $i++) {
            $html .= '<div class="user-post-container">';
                $html .= '<a class="post-image" href="' . $__DATA['posts'][$i]['post_url'] . '"><img src="' . LOCATION_SITE . 'img/QUIZ/quiz/m-' . $__DATA['posts'][$i]['post_image_id'] . '" /></a>';
                $html .= '<div class="post-text">';
                    $html .= '<a class="post-title" href="' . $__DATA['posts'][$i]['post_url'] . '">' . $__DATA['posts'][$i]['post_title'] . '</a>';
                    $html .= '<div class="post-description">' . $__DATA['posts'][$i]['post_description'] . '</div>';
                $html .= '</div>';
            $html .= '</div>';
        }
        echo $html;
        ?>
    </div>
</div>
<div id="posts-loader"><i class="fa fa-spinner fa-spin"></i></div>
<div id="more-posts-container" style="<?= $__DATA['more_pages'] == 1 ? '' : 'display:none' ?>"><button id="more-posts-button" class="theme-active-button"><?= $__LANGUAGE_STRINGS['home_page']['SHOW_MORE_POSTS'] ?></button></div>