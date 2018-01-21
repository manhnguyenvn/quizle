<div id="quiz-container-title"><span id="quiz-container-title-label"><?= $__QUIZ_CONTAINER_TITLE ?></span><span id="quiz-draft-mode-label" style="<?= $__DRAFT_MODE == 0 ? 'display:none' : '' ?>"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_DRAFT_MODE'] ?></span><span id="quiz-hidden-label" style="<?= $__HIDDEN_MODE == 0 ? 'display:none' : '' ?>"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_HIDDEN'] ?></span><span id="quiz-premium-label" style="<?= $__PREMIUM_MODE == 0 ? 'display:none' : '' ?>"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_PREMIUM_MODE'] ?></span></div>
<div id="quiz-container" class="<?= $__LANGUAGE_SETTINGS['language_direction'] == 'rtl' ? 'quiz-container-rtl' : '' ?>">
    <div id="quiz-tabs-container">
        <button class="theme-active-button quiz-tab <?= $__QUIZ_TAB_CLASS ?>" id="quiz-tab-info"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_INFO_BUTTON'] ?></button>
        <button class="theme-passive-button quiz-tab <?= $__QUIZ_TAB_CLASS ?>" id="quiz-tab-questions"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_QUESTIONS_BUTTON'] ?></button>
        <button class="theme-passive-button quiz-tab <?= $__QUIZ_TAB_CLASS ?>" id="quiz-tab-results"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_RESULTS_BUTTON'] ?></button>
        <button class="theme-passive-button quiz-tab <?= $__QUIZ_TAB_CLASS ?>" id="quiz-tab-tags"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_SOCIAL_BUTTON'] ?></button>
        <?= $__QUIZ_TAB_CLASS == 'quiz-tab-5' ? '<button class="theme-passive-button quiz-tab quiz-tab-5" id="quiz-tab-premium">' . $__LANGUAGE_STRINGS['user_quiz']['QUIZ_PREMIUM_BUTTON'] . '</button>' : '' ?>
    </div> 
    <div id="quiz-info-container" class="quiz-tab-content">
        <div class="quiz-image"><i class="fa fa-picture-o quiz-image-placeholder" data-image-type="quiz"></i></div><!--
     --><div class="quiz-text">
            <input type="text" id="quiz-title" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_TITLE_PLACEHOLDER'] ?>" autocomplete="off" maxlength="90" />
            <textarea id="quiz-description" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_DESCRIPTION_PLACEHOLDER'] ?>" autocomplete="off" maxlength="150"></textarea>
            <select id="quiz-type" autocomplete="off">
                <option value="1"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_CORRECT_OPTION_TYPE'] ?></option>
                <option value="2"><?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_WEIGHT_TYPE'] ?></option>
            </select>
            <select id="quiz-language" autocomplete="off">
                <?php
                for($i=0; $i<sizeof($__ACTIVATED_LANGUAGES); $i++) {
                    echo '<option value="' . $__ACTIVATED_LANGUAGES[$i]['language_code'] . '" data-direction="' . $__ACTIVATED_LANGUAGES[$i]['language_direction'] . '"' . ($__ACTIVATED_LANGUAGES[$i]['language_code'] == $__QUIZ_LANGUAGE_CODE ? ' selected' : '') . '>' . $__LANGUAGE_STRINGS['user_quiz']['QUIZ_LANGUAGE'] . ' - ' . $__ACTIVATED_LANGUAGES[$i]['language_name'] . '</option>';
                }
                ?>
            </select>
        </div>
        <div id="quiz-title-textcount" class="quiz-textcount">0 / 90</div>
        <div id="quiz-description-textcount" class="quiz-textcount">0 / 150</div>
    </div>
    <div id="quiz-questions-main-container" class="quiz-tab-content">
        <div id="quiz-questions-buttons">
            <div id="quiz-questions-links"></div>
            <button class="theme-active-button theme-active-button-small" id="quiz-add-question"><?= $__LANGUAGE_STRINGS['user_quiz']['ADD_QUESTION_BUTTON'] ?></button> 
        </div>
        <div id="quiz-question-container">
            <div class="quiz-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['QUESTION_TEXT_IMAGE_HINT'] ?></div>
            <div id="quiz-question-info-container">
                <div class="question-image"><i class="fa fa-picture-o quiz-image-placeholder" data-image-type="question"></i></div><!--
             --><div class="question-text">
                    <textarea id="quiz-question" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['QUESTION_TEXT_PLACEHOLDER'] ?>" autocomplete="off" maxlength="180"></textarea>
                </div>
            </div>
            <i id="quiz-delete-question" class="fa fa-trash"></i>
            <div id="quiz-question-textcount" class="quiz-textcount">0 / 180</div>
            <div id="quiz-option-template">
                <div class="quiz-option-container">
                    <div class="quiz-option-info-container">
                        <div class="option-image-container">
                            <div class="option-image"><i class="fa fa-picture-o quiz-image-placeholder" data-image-type="option"></i></div>
                        </div><!--
                     --><div class="option-text">
                            <textarea class="quiz-option" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['OPTION_TEXT_PLACEHOLDER'] ?>" maxlength="120"></textarea>
                            <div class="quiz-option-correct-container">
                                <label><?= $__LANGUAGE_STRINGS['user_quiz']['CORRECT_OPTION'] ?></label>
                                <input type="checkbox" class="quiz-option-correct" />
                            </div>
                            <form novalidate><input type="number" class="quiz-option-weight" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['WEIGHT_PLACEHOLDER'] ?>" maxlength="3" /></form>
                        </div>
                    </div>
                    <i class="fa fa-trash quiz-delete-option"></i>
                    <div class="quiz-option-textcount quiz-textcount">0 / 120</div>
                </div>
            </div>
            <div id="quiz-option-instruction" class="quiz-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['OPTION_TEXT_IMAGE_HINT'] ?><span id="quiz-option-correct-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['ONLY_ONE_CORRECT_OPTION'] ?></span><span id="quiz-option-weight-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['WEIGHT_IS_NUMBER'] ?></span></div>
            <div id="quiz-question-options-container"></div>
            <div id="quiz-add-option-button-container"><button class="theme-active-button theme-active-button-small" id="quiz-add-option-button"><?= $__LANGUAGE_STRINGS['user_quiz']['ADD_OPTION_BUTTON'] ?></button></div>
            <div id="quiz-question-hint-container">
                <div id="quiz-question-hint-button"><?= $__LANGUAGE_STRINGS['user_quiz']['ADD_HINT_BUTTON'] ?><span class="quiz-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['ADD_HINT_BUTTON_OPTONAL'] ?></div>
                <div id="quiz-question-hint-textbox-container">
                    <label for="quiz-question-hint-text"><?= $__LANGUAGE_STRINGS['user_quiz']['QUESTION_HINT'] ?></label><!--
                 --><textarea id="quiz-question-hint-text" maxlength="150"></textarea>
                    <div id="quiz-question-hint-textcount" class="quiz-textcount">0 / 150</div>
                </div>
            </div>
            <div id="quiz-question-fact-container">
                <div id="quiz-question-fact-button"><?= $__LANGUAGE_STRINGS['user_quiz']['ADD_FACT_BUTTON'] ?><span class="quiz-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['ADD_FACT_BUTTON_OPTONAL'] ?></span></div>
                <div id="quiz-question-fact-textbox-container">
                    <label for="quiz-question-fact-text"><?= $__LANGUAGE_STRINGS['user_quiz']['QUESTION_FACT'] ?></label><!--
                 --><textarea id="quiz-question-fact-text" maxlength="150"></textarea>
                    <div id="quiz-question-fact-textcount" class="quiz-textcount">0 / 150</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quiz Results Container -->
    <div id="quiz-results-container" class="quiz-tab-content">
        <div id="quiz-single-result-container">
            <div class="quiz-result-image"><i class="fa fa-picture-o quiz-image-placeholder" data-image-type="result"></i></div><!--
         --><div class="quiz-result-text">
                <input type="text" id="quiz-result-title" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_TITLE_PLACEHOLDER'] ?>" autocomplete="off" maxlength="100" />
                <textarea id="quiz-result-description" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_DESCRIPTION_PLACEHOLDER'] ?>" autocomplete="off" maxlength="200"></textarea>
            </div>
            <div id="quiz-result-title-textcount" class="quiz-textcount">0 / 100</div>
            <div id="quiz-result-description-textcount" class="quiz-textcount">0 / 200</div>
        </div>
        <div id="quiz-results-container-footer">
            <div id="quiz-results-buttons-container">
                <button class="theme-active-button-small quiz-result-button" data-result-no="1"><?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_BUTTON_0_25'] ?></button>
                <button class="theme-passive-button-small quiz-result-button" data-result-no="2"><?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_BUTTON_25_50'] ?></button>
                <button class="theme-passive-button-small quiz-result-button" data-result-no="3"><?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_BUTTON_50_75'] ?></button>
                <button class="theme-passive-button-small quiz-result-button" data-result-no="4"><?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_BUTTON_75_100'] ?></button>
            </div>
        </div>
    </div>
    <!-- Quiz Social Image & Tags Container -->
    <div id="quiz-social-tags-container" class="quiz-tab-content">
        <div id="quiz-social-media-container">
            <div id="quiz-social-media-container-title"><?= $__LANGUAGE_STRINGS['user_quiz']['SOCIAL_MEDIA_IMAGE'] ?></div>
            <div class="quiz-social-media-image"><i class="fa fa-picture-o quiz-image-placeholder" data-image-type="social"></i></div>
        </div><!--
     --><div id="quiz-tags-container">
            <div id="quiz-tags-container-title"><?= $__LANGUAGE_STRINGS['user_quiz']['TAGS'] ?><span class="quiz-instruction"><?= $__LANGUAGE_STRINGS['user_quiz']['3_TAGS_ALLOWED'] ?></span></div>
            <div id="quiz-tags">
            <?php
            for($i=0; $i<sizeof($__LANGUAGE_TAGS); $i++) {
                echo '<div class="quiz-single-tag"><input type="checkbox" value="' . $__LANGUAGE_TAGS[$i]['id'] . '" autocomplete="off" /><label>' . $__LANGUAGE_TAGS[$i]['name'] . '</label></div>';
            }
            ?>
            </div>
        </div>
    </div>
    <!-- Quiz Premium Container -->
    <div id="quiz-premium-container" class="quiz-tab-content">
        <a id="premium-quiz-info-link" href="<?= LOCATION_SITE ?>pages/premium-quiz.php" target="_blank"><?= $__LANGUAGE_STRINGS['user_quiz']['WHAT_IS_PREMIUM_QUIZ'] ?></a>
        <div id="premiun-domain-container">
            <label for="premium-domain"><?= $__LANGUAGE_STRINGS['user_quiz']['DOMAIN'] ?><span class="hastip" title="<?= $__LANGUAGE_STRINGS['user_quiz']['DOMAIN_TOOLTIP'] ?>">?</span></label>
            <input type="text" id="premium-domain" autocomplete="off" />
            <span id="premium-domain-edit-button"><i class="fa fa-pencil-square-o"></i></span>
            <span id="premium-domain-save-button"><i class="fa fa-floppy-o"></i></span>
            <button id="premium-activate-button" class="theme-active-button theme-active-button-small"><?= $__LANGUAGE_STRINGS['user_quiz']['MAKE_QUIZ_PREMIUM_BUTTON'] ?></button>
            <a id="premium-deactivate-button"><?= $__LANGUAGE_STRINGS['user_quiz']['DEACTIVATE_PREMIUM_BUTTON'] ?></a>
        </div>
        <div id="user-credits-container">
            <span id="user-credits-remaining-count"><?= $__USER_AVAILABLE_CREDITS ?></span><span id="user-credits-remaining-label"><?= $__LANGUAGE_STRINGS['user_quiz']['CREDITS_REMAINING'] ?></span>
            <span id="user-credits-refresh-button"><i class="fa fa-spinner"></i></span>
            <a id="credits-purchase-link" href="<?= LOCATION_SITE . str_replace('URL_NAME', 'user-credits', $__NO_PARAMETER_URL) ?>" target="_blank"><?= $__LANGUAGE_STRINGS['user_quiz']['PURCHASE_CREDITS'] ?></a>
        </div>
    </div>
    <!-- Quiz Images Upload Lightbox -->
    <div id="quiz-images-lightbox">
        <div id="quiz-images-container">
            <div id="quiz-images-upload-container">
                <div id="quiz-images-upload-title" class="theme-light-background theme-light-background-color theme-light-background-border"><?= $__LANGUAGE_STRINGS['user_quiz']['UPLOAD_IMAGE'] ?></div>
                <div id="quiz-images-upload-dimenson">
                    <div class="theme-light-background-color" id="quiz-images-option-type-upload-dimenson"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_OPTION_TYPE_DIMENSIONS_MESSAGE'] ?></div>
                    <div class="theme-light-background-color" id="quiz-images-other-type-upload-dimenson"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_OTHER_TYPES_DIMENSIONS_MESSAGE'] ?></div>
                </div>
            </div>
            <input type="file" id="quiz-image-file" />
            <div id="quiz-image-new-container">
                <div id="quiz-image-new-header"><?= $__LANGUAGE_STRINGS['user_quiz']['CROP_IMAGE_HEADER'] ?></div>
                <div id="quiz-image-new-subheader"><?= $__LANGUAGE_STRINGS['user_quiz']['CROP_IMAGE_SUBHEADER'] ?></div>
                <div id="quiz-cropper-container">
                    <img id="quiz-image-new" />
                </div>
                <div id="quiz-image-new-buttons">
                    <input type="text" id="quiz-image-new-attribution" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_ATTRIBUTION'] ?>" autocomplete="off" /><!--
                 --><button id="quiz-image-new-save" class="theme-active-button"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_SAVE'] ?></button>
                </div>
                <div id="quiz-image-new-no-attribution-container">
                    <input type="checkbox" id="quiz-image-new-no-attribution" autocomplete="off" /><label for="quiz-image-new-no-attribution"><?= $__LANGUAGE_STRINGS['user_quiz']['NO_ATTRIBUTION_REQUIRED'] ?></label>
                </div>
            </div>
        </div>
        <i id="quiz-images-lightbox-close" class="fa fa-remove"></i>
    </div>
    <!-- Quiz Image Attribution Lightbox -->
    <div id="quiz-image-attribution-lightbox">
        <div id="quiz-image-old-container">
            <div>
                <img id="quiz-image-old" />
            </div>
            <div id="quiz-image-old-buttons">
                <input type="text" id="quiz-image-old-attribution" placeholder="<?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_ATTRIBUTION'] ?>" /><!--
             --><button id="quiz-image-old-save" class="theme-active-button"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_SAVE'] ?></button>
            </div>
            <div id="quiz-image-old-no-attribution-container">
                <input type="checkbox" id="quiz-image-old-no-attribution" /><label for="quiz-image-old-no-attribution"><?= $__LANGUAGE_STRINGS['user_quiz']['NO_ATTRIBUTION_REQUIRED'] ?></label>
            </div>
        </div>
        <i id="quiz-image-attribution-lightbox-close" class="fa fa-remove"></i>
    </div>
    <!-- Quiz Error Lightbox -->
    <div id="quiz-error-dialog-container">
        <div id="quiz-error-dialog">
            <div id="quiz-error-dialog-title"></div>
            <ul id="quiz-error-dialog-list"></ul>
            <button id="quiz-error-dialog-close" class="theme-active-button"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_ERROR_DIALOG_CLOSE_BUTTON'] ?></button>
        </div>
    </div>
</div>
<div id="quiz-publish-buttons-container">
    <button id="publish-quiz-button" class="theme-active-button" data-in-progress="0">
        <span id="publish-quiz-button-1" style="<?= ($__NEW_QUIZ == 1 || $__DRAFT_MODE == 1) ? '' : 'display:none' ?>"><?= $__LANGUAGE_STRINGS['user_quiz']['PUBLISH_QUIZ_BUTTON'] ?></span>
        <span id="publish-quiz-button-2" style="<?= ($__NEW_QUIZ == 0 && $__DRAFT_MODE == 0) ? '' : 'display:none' ?>"><?= $__LANGUAGE_STRINGS['user_quiz']['UPDATE_QUIZ_BUTTON'] ?></span>
    </button>
    <button id="save-draft-button" class="theme-active-button" data-in-progress="0" style="<?= ($__NEW_QUIZ == 1 || $__DRAFT_MODE == 1) ? '' : 'display:none' ?>"><?= $__LANGUAGE_STRINGS['user_quiz']['SAVE_DRAFT_BUTTON'] ?></button>
</div>

<div id="quiz-image-uploaded-container-template">
    <div class="quiz-image-uploaded-container">
        <img />
        <div class="quiz-image-delete"><i class="fa fa-trash"></i></div>
        <div class="quiz-image-edit"><i class="fa fa-pencil-square-o"></i></div>
        <div class="quiz-image-upload"><i class="fa fa-upload"></i></div>
    </div>
</div>

<div id="quiz-image-upload-error-templates">
    <div id="quiz-image-upload-size-error" class="quiz-image-upload-error theme-error-background"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_ERROR'] . ' : '. $__LANGUAGE_STRINGS['user_quiz']['IMAGE_SIZE_MESSAGE'] . MAX_IMAGE_SIZE_ALLOWED_MB . ' MB' ?></div>
    <div id="quiz-image-upload-extension-error" class="quiz-image-upload-error theme-error-background"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_ERROR'] . ' : '. $__LANGUAGE_STRINGS['user_quiz']['IMAGE_EXTENSION_MESSAGE'] ?></div>
    <div id="quiz-image-upload-dimension-error" class="quiz-image-upload-error theme-error-background"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_ERROR'] . ' : '. $__LANGUAGE_STRINGS['user_quiz']['IMAGE_OTHER_TYPES_DIMENSIONS_MESSAGE'] ?></div>
    <div id="quiz-image-upload-dimension-option-error" class="quiz-image-upload-error theme-error-background"><?= $__LANGUAGE_STRINGS['user_quiz']['IMAGE_UPLOAD_ERROR'] . ' : '. $__LANGUAGE_STRINGS['user_quiz']['IMAGE_OPTION_TYPE_DIMENSIONS_MESSAGE'] ?></div>
</div>

<div id="quiz-question-link-template">
    <button class="theme-active-button-small quiz-question-link"></button>
</div>

<div id="quiz-button-loader-template">
    <i class="fa fa-spin fa-spinner quiz-button-loader-icon"></i>
</div>