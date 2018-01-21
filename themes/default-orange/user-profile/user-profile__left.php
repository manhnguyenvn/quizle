<div id="user-picture-name">
    <img src="<?= $__USER_DETAILS['user_picture_url'] ?>" /><!--
 --><div id="user-name-container"><div id="user-name-inner-container"><span><?= $__USER_DETAILS['user_full_name'] ?></span></div></div>
</div>
<div id="user-email-container">
    <label><?= $__LANGUAGE_STRINGS['user_profile']['EMAIL'] ?></label><!--
 --><div id="user-email-inner-container">
        <input type="text" id="user-email" readonly value="<?= $__USER_DETAILS['user_email'] ?>" maxlength="150" />
        <span id="edit-email"><?= $__LANGUAGE_STRINGS['user_profile']['EDIT'] ?></span>
        <button class="theme-active-button" id="submit-email-button"><?= $__LANGUAGE_STRINGS['user_profile']['SUBMIT'] ?></button>
        <div id="email-error-container"></div>
        <div id="confirmation-code-container" style="<?= ($__USER_DETAILS['user_email_confirmed'] == 0 && $__USER_DETAILS['user_email'] != NULL) ? '' : 'display:none' ?>">
            <span id="confirmation-code-message"><?= $__LANGUAGE_STRINGS['user_profile']['CONFIRMATION_CODE_MESSAGE'] ?></span>
            <input type="text" id="confirmation-code" />
            <button class="theme-active-button" id="confirmation-code-submit-button"><?= $__LANGUAGE_STRINGS['user_profile']['SUBMIT'] ?></button>
            <span id="resend-confirmation-code"><?= $__LANGUAGE_STRINGS['user_profile']['RESEND_CODE'] ?></span>
            <i id="confirmation-code-wrong" class="fa fa-times-circle" style="display:none"></i>
            <i id="confirmation-code-correct" class="fa fa-check-circle" style="display:none"></i>
            <div id="email-error-container-2"></div>
        </div>
    </div>
</div>