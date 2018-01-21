<?php if($__DISPLAY_POST != 1) { ?>
    <div id="quiz-unavailable-container"><?= $__LANGUAGE_STRINGS['post']['CONTENT_UNAVAILABLE'] ?></div>
<?php }
    else { ?>
    <div id="quiz-main-container">
        <?php
        /* Start */
        if($__SHOW_TITLE == 1) { ?>
        <div id="quiz-title-container">
            <h3><?= $__QUIZ_PROPERTIES['title'] ?></h3>
            <div id="quiz-title-bottom"></div>
        </div>
        <?php } 
        /* End */
        ?>
        <div id="quiz-current-question-container"><!--
         --><?= $__LANGUAGE_STRINGS['post']['QUESTION'] ?><span id="current-question"></span><?= $__LANGUAGE_STRINGS['post']['QUESTION_OF'] ?><span id="total-questions"></span>
            <div id="quiz-music-control" data-show="<?= SOUND_ALLOWED == 0 ? 0 : 1 ?>" data-muted="<?= isset($_COOKIE['sound_muted']) ? 1 : 0 ?>"><?= isset($_COOKIE['sound_muted']) ? '<i class="fa fa-volume-off">' : '<i class="fa fa-volume-up">' ?></i></div>
            <div id="quiz-music-control-dialog-container" data-show="<?= (SOUND_ALLOWED == 0 || isset($_COOKIE['sound_tip'])) ? 0 : 1 ?>">
                <div id="quiz-music-control-dialog">
                    <div id="quiz-music-control-dialog-container-close"><i class="fa fa-times-circle"></i></div><!--
                 --><div id="quiz-music-control-dialog-title"><?= $__LANGUAGE_STRINGS['post']['SOUND_TIP'] ?></div>
                </div>
            </div>
        </div>
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
        <div id="quiz-source-container">
            <a id="quiz-source-link" href="<?= $__POST_URL ?>" target="_top"><span><?= $__LANGUAGE_STRINGS['embed']['POWERED_BY'] ?></span><img src="<?= LOCATION_SITE . 'img/' . LOGO_NAME . '?' . LOGO_CACHE ?>" /></a>
        </div>
        <?= SOUND_ALLOWED == 1 ? '<audio id="option-correct-music" src="' . LOCATION_SITE . 'music-files/correct.mp3"></audio>' : '' ?>
        <?= SOUND_ALLOWED == 1 ? '<audio id="option-wrong-music" src="' . LOCATION_SITE . 'music-files/wrong.mp3"></audio>' : '' ?>
        <?php
        /* Start */
        if($__SHOW_COMMENTS == 1) { ?>
            <script>
            window.fbAsyncInit = function() {
                FB.init({ appId: '<?= FACEBOOK_APP_ID ?>', xfbml: true, version: 'v2.5', status: false });
                $("#login-button-unloaded").hide();
                $("#login-button-loaded").show();

                /* "play_quiz" defined in post.php */
                if(typeof play_quiz != 'undefined') {
                    FB.Event.subscribe('comment.create', FacebookCommentCreated);
                }
            };

            (function(d, s, id){
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) {return;}
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            </script>
            <div id="quiz-comments-container">
                <div id="quiz-comments-header-container">
                    <div id="quiz-comments-header"><?= $__LANGUAGE_STRINGS['post']['COMMENTS_HEADING'] ?></div>
                </div>
                <div id="quiz-comments">
                    <div class="fb-comments" data-href="<?= $__POST_URL_FB ?>" data-numposts="3" data-width="100%"></div>
                </div>
            </div>
        <?php } 
        /* End */
        ?>
    </div>
<?php } 
?>