<div id="user-posts-container">
    <div id="user-posts-title-container"><h3 id="user-posts-title"><?= $__LANGUAGE_STRINGS['user_post_list']['PAGE_TITLE'] ?></h3></div>
    <?php
    $html = '<div id="no-user-posts-message" style="' . (sizeof($__USER_POSTS) == 0 ? '' : 'display:none') . '">' . $__LANGUAGE_STRINGS['user_post_list']['NO_AVAILABLE_POSTS'] . '</div>';
    for($i=0; $i<sizeof($__USER_POSTS); $i++) {
        $hidden_mode = $__USER_POSTS[$i]['post_published'] == 1 ? ($__USER_POSTS[$i]['post_hidden'] == 1 ? 1 : 0) : 0;
        $draft_mode = $__USER_POSTS[$i]['post_published'] == 0 ? 1 : 0;
        $post_title = $__USER_POSTS[$i]['post_title'] == '' ? $__LANGUAGE_STRINGS['user_quiz']['UNTITLED_QUIZ_LABEL'] : $__USER_POSTS[$i]['post_title'];

        $html .= '<div class="user-post-container" data-post-id="' . $__USER_POSTS[$i]['post_id'] . '">';
            if($__USER_POSTS[$i]['post_image_id'] == NULL)
                $html .= '<div class="post-no-image">' . $__LANGUAGE_STRINGS['user_post_list']['NO_IMAGE_IN_POST'] . '</div>';
            else 
                $html .= '<img class="post-image" src="' . LOCATION_SITE . 'img/QUIZ/quiz/m-' . $__USER_POSTS[$i]['post_image_id'] . '" />';
            $html .= '<div class="post-text">';
                $html .= '<div class="post-title">' . $post_title . '</div>';
                $html .= '<div class="post-description">' . $__USER_POSTS[$i]['post_description'] . '</div>';
                $html .= '<div class="post-controls">';
                    $html .= '<a class="user-post-edit-button" href="' . $__USER_POSTS[$i]['edit_url'] . '">' . $__LANGUAGE_STRINGS['user_post_list']['EDIT_POST_BUTTON'] . '</a>';
                    if($draft_mode == 0) {
                        $html .= '<a class="user-post-view-button" href="' . $__USER_POSTS[$i]['post_url'] . '">' . $__LANGUAGE_STRINGS['user_post_list']['VIEW_POST_BUTTON'] . '</a>';
                        $html .= '<span class="user-post-hide-button" data-type="hide" style="' . ($hidden_mode == 1 ? 'display:none' : '') . '">' . $__LANGUAGE_STRINGS['user_post_list']['HIDE_POST_BUTTON'] . '</span>';
                        $html .= '<span class="user-post-unhide-button" data-type="unhide" style="' . ($hidden_mode == 0 ? 'display:none' : '') . '">' . $__LANGUAGE_STRINGS['user_post_list']['UNHIDE_POST_BUTTON'] . '</span>';
                    }
                    $html .= '<span class="user-post-delete-button">' . $__LANGUAGE_STRINGS['user_post_list']['DELETE_POST_BUTTON'] . '</span>';
                    if($_SESSION['user']['user_premium'] == 0 && ACTIVATE_PREMIUM == 1)
                        $html .= '<a class="user-post-premium-button" href="' . $__USER_POSTS[$i]['edit_url'] . '-PM">' . $__LANGUAGE_STRINGS['user_post_list']['GO_PREMIUM_POST_BUTTON'] . '</a>';
                $html .= '</div>';
                $html .= '<div class="post-labels">';
                    if($__USER_POSTS[$i]['post_is_premium'] == 1 && ACTIVATE_PREMIUM == 1)
                        $html .= '<div class="post-premium-mode-label post-label">'. $__LANGUAGE_STRINGS['user_quiz']['QUIZ_PREMIUM_MODE'] . '</div>';
                    if($draft_mode == 1)
                        $html .= '<div class="post-draft-mode-label post-label">'. $__LANGUAGE_STRINGS['user_quiz']['QUIZ_DRAFT_MODE'] . '</div>';
                    $html .= '<div class="post-hidden-mode-label post-label" style="' . ($hidden_mode == 1 ? '' : 'display:none') . '">' . $__LANGUAGE_STRINGS['user_quiz']['QUIZ_HIDDEN'] . '</div>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
    }
    echo $html;
    ?>
</div>