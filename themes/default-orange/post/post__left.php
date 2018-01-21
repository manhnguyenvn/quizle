<?php if($__DISPLAY_POST != 1) { ?>
    <div id="quiz-unavailable-container"><?= $__LANGUAGE_STRINGS['post']['CONTENT_UNAVAILABLE'] ?></div>
<?php }
    else { ?>
    <div id="quiz-main-container">
        <div id="quiz-title-container">
            <h3><?= $__QUIZ_PROPERTIES['title'] ?></h3>
            <?= SOUND_ALLOWED == 1 ? '<audio id="option-correct-music" src="' . LOCATION_SITE . 'music-files/correct.mp3"></audio>' : '' ?>
            <?= SOUND_ALLOWED == 1 ? '<audio id="option-wrong-music" src="' . LOCATION_SITE . 'music-files/wrong.mp3"></audio>' : '' ?>
            <div id="quiz-title-bottom"></div>
        </div>
        <div id="quiz-user-container">
            <a href="<?= $__QUIZ_USER_DATA['profile_url'] ?>" id="quiz-user-link-picture"><img src="<?= $__QUIZ_USER_DATA['user_picture_url'] ?>" /></a>
            <div id="quiz-user-text-container">
                <a href="<?= $__QUIZ_USER_DATA['profile_url'] ?>" id="quiz-user-link"><?= $__QUIZ_USER_DATA['user_full_name'] ?></a>
                <div id="quiz-updated-time"><?= $__QUIZ_USER_DATA['updated_time'] . ' ' . $__LANGUAGE_STRINGS['home_page']['POST_TIME_AGO'] ?></div>
            </div>
            <div id="quiz-music-control" data-show="<?= SOUND_ALLOWED == 0 ? 0 : 1 ?>" data-muted="<?= isset($_COOKIE['sound_muted']) ? 1 : 0 ?>"><?= isset($_COOKIE['sound_muted']) ? '<i class="fa fa-volume-off">' : '<i class="fa fa-volume-up">' ?></i></div>
            <div id="quiz-music-control-dialog-container" data-show="<?= (SOUND_ALLOWED == 0 || isset($_COOKIE['sound_tip'])) ? 0 : 1 ?>">
                <div id="quiz-music-control-dialog">
                    <div id="quiz-music-control-dialog-container-close"><i class="fa fa-times-circle"></i></div><!--
                 --><div id="quiz-music-control-dialog-title"><?= $__LANGUAGE_STRINGS['post']['SOUND_TIP'] ?></div>
                </div>
            </div>
        </div>
        <div id="quiz-current-question-container"><?= $__LANGUAGE_STRINGS['post']['QUESTION'] ?><span id="current-question"></span><?= $__LANGUAGE_STRINGS['post']['QUESTION_OF'] ?><span id="total-questions"></span></div>
        <div id="quiz-container">
            <div class="quiz-container-slide">
                <div id="quiz-properties-container">
                    <div id="quiz-picture-description-container">
                        <img src="<?= LOCATION_SITE . 'img/QUIZ/quiz/' . $__QUIZ_PROPERTIES['image_id'] ?>" />
                        <div id="quiz-description-container">
                            <div id="quiz-description"><?= $__QUIZ_PROPERTIES['description'] ?></div><!--
                         --><button id="play-quiz-button" class="theme-active-button"><?= $__LANGUAGE_STRINGS['post']['PLAY_QUIZ_BUTTON'] ?></button>
                        </div>
                        <?= $__QUIZ_PROPERTIES['image_attribution'] != -1 ? '<div class="quiz-image-attribution" title="' . $__QUIZ_PROPERTIES['image_attribution'] . '">' . $__QUIZ_PROPERTIES['image_attribution'] . '</div>' : '' ?>
                    </div>
                </div>
            </div><!--
     --></div>
        <div id="quiz-share-container">
            <button id="quiz-facebook-share"><?= $__LANGUAGE_STRINGS['post']['FACEBOOK_SHARE'] ?></button>
            <button id="quiz-embed"><?= $__LANGUAGE_STRINGS['post']['EMBED_BUTTON'] ?></button>
            <span id="quiz-report"><?= $__LANGUAGE_STRINGS['post']['REPORT_BUTTON'] ?></span>
        </div>
        <div id="quiz-comments-container">
            <div id="quiz-comments-header-container">
                <div id="quiz-comments-header"><?= $__LANGUAGE_STRINGS['post']['COMMENTS_HEADING'] ?></div>
            </div>
            <div id="quiz-comments">
                <div class="fb-comments" data-href="<?= $__POST_URL_FB ?>" data-numposts="3" data-width="100%"></div>
            </div>
        </div>
    </div>
<?php } 
if($__DISPLAY_POST != -1) { ?>
    <div id="similar-quizzes-container">
        <div id="similar-quizzes-header-container">
            <div id="similar-quizzes-header"><?= $__LANGUAGE_STRINGS['post']['SIMILAR_QUIZZES'] ?></div>
        </div>
        <div id="similar-quizzes">
        <?php
        $html = '';
        for($i=0; $i<sizeof($__SIMILAR_QUIZES); $i++) {
            $html .= '<div class="similar-post-container">';
                $html .= '<a class="similar-post-image" href="' . $__SIMILAR_QUIZES[$i]['post_url']. '"><img src="' . LOCATION_SITE . 'img/QUIZ/quiz/m-' . $__SIMILAR_QUIZES[$i]['image'] . '" /></a>';
                $html .= '<div class="similar-post-text">';
                    $html .= '<a class="similar-post-title" href="' . $__SIMILAR_QUIZES[$i]['post_url']. '">' . $__SIMILAR_QUIZES[$i]['title'] . '</a>';
                    $html .= '<div class="similar-post-description">' . $__SIMILAR_QUIZES[$i]['description'] . '</div>';
                $html .= '</div>';
            $html .= '</div>';
        }
        echo $html;
        ?>
        </div>
    </div>
<?php } ?>

<div id="quiz-embed-lightbox-container">
    <div id="quiz-embed-lightbox">
        <div id="quiz-embed-lightbox-header"><?= $__LANGUAGE_STRINGS['post']['EMBED_DIALOG_HEADER'] ?></div>
        <div id="quiz-embed-lightbox-sub-header"><?= $__LANGUAGE_STRINGS['post']['EMBED_DIALOG_SUB_HEADER'] ?></div>
        <code id="quiz-embed-code"></code>
        <div id="quiz-embed-options">
            <div class="quiz-embed-option"><input type="checkbox" id="quiz-embed-show-title" /><label for="quiz-embed-show-title"><?= $__LANGUAGE_STRINGS['post']['EMBED_DIALOG_SHOW_TITLE'] ?></label></div>
            <div class="quiz-embed-option"><input type="checkbox" id="quiz-embed-show-comments" /><label for="quiz-embed-show-comments"><?= $__LANGUAGE_STRINGS['post']['EMBED_DIALOG_SHOW_COMMENTS'] ?></label></div>
        </div>
        <div id="quiz-embed-lightbox-close"><i class="fa fa-times"></i></div>
    </div>
</div>