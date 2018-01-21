<?php 
session_start(); 
if(!isset($_SESSION['user']))
    $show_quiz_editor = 0;
else 
    $show_quiz_editor = 1;

require_once('get-language-settings.php');
if($show_quiz_editor == 1) {
    require_once('classes/user-model.php');
        
    try {
        $user_app = new UserApplicationObject();
        $__USER_AVAILABLE_CREDITS = $user_app->GetUserCredits($_SESSION['user']['user_id']);

        $__ACTIVATED_LANGUAGES = $__ADMIN_APP->GetLanguages(1); 

        if(isset($_GET['post_id'])) {
            $post_id_parts = explode('-', $_GET['post_id']);
            $post_id = $post_id_parts[0];
            if(sizeof($post_id_parts) == 1)
                $show_premium = 0;
            else if(sizeof($post_id_parts) == 2)
                $show_premium = 1;

            $quiz = $user_app->GetPostFull($post_id, $__LANGUAGE_CODE_CURRENT);
            $__NEW_QUIZ = 0;
            $__PREMIUM_MODE = $quiz['premium_properties']['is_premium'] == 1 ? 1 : 0;
            $__HIDDEN_MODE = $quiz['is_published'] == 1 ? ($quiz['is_hidden'] == 1 ? 1 : 0) : 0;
            $__DRAFT_MODE = $quiz['is_published'] == 0 ? 1 : 0;
            $__QUIZ_CONTAINER_TITLE = $quiz['quiz_properties']['title'] == '' ? $__LANGUAGE_STRINGS['user_quiz']['UNTITLED_QUIZ_LABEL'] : strtoupper($quiz['quiz_properties']['title']);

            $__LANGUAGE_TAGS = $__ADMIN_APP->GetLanguageTags($quiz['quiz_properties']['language_code']);
            $__QUIZ_LANGUAGE_CODE = $quiz['quiz_properties']['language_code'];
        }
        else {
            $quiz = [ 'quiz_properties' => null, 'quiz_data' => null, 'premium_properties' => null ];
            $show_premium = 0;
            $__NEW_QUIZ = 1;
            $__PREMIUM_MODE = 0;
            $__HIDDEN_MODE = 0;
            $__DRAFT_MODE = 0;
            $__QUIZ_CONTAINER_TITLE = $__LANGUAGE_STRINGS['user_quiz']['NEW_QUIZ_LABEL'];

            $__LANGUAGE_TAGS = $__ADMIN_APP->GetLanguageTags($__LANGUAGE_CODE_CURRENT);
            $__QUIZ_LANGUAGE_CODE = $__LANGUAGE_CODE_CURRENT;
        }
    }
    catch(Exception $e) {
        if($e->getCode() == 2)
        echo (DEBUG_MODE == 0 ? $__LANGUAGE_STRINGS['general']['SERVER_FAILED'] : $e->getMessage());
    else
        echo $__LANGUAGE_STRINGS['general'][$e->getMessage()];

    exit();
    }

    if($_SESSION['user']['user_premium'] == 1 || $show_premium == 1) 
        $__QUIZ_TAB_CLASS = 'quiz-tab-5';
    else 
        $__QUIZ_TAB_CLASS = 'quiz-tab-4';
}
    
?>
<!DOCTYPE html>
<html dir="<?= $__LANGUAGE_SETTINGS['language_direction'] ?>" lang="<?= $__LANGUAGE_CODE_CURRENT ?>">
<head>
<title><?= SITE_NAME . ' | ' . $__LANGUAGE_STRINGS['user_quiz']['META_TITLE'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no" />
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':300,400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/header/header.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/user-quiz/user-quiz.css' rel='stylesheet' type='text/css'>
<link href="<?= LOCATION_SITE ?>css/cropper.css" rel="stylesheet" />
<link href="<?= LOCATION_SITE ?>css/font-awesome.css" rel="stylesheet" />
<script src="<?= LOCATION_SITE ?>js/jquery-1.11.3.min.js"></script>
<script src="<?= LOCATION_SITE ?>js/cropper.min.js"></script>
<script src="<?= LOCATION_SITE ?>js/tooltipsy.min.js"></script>
<style type="text/css">
body {
    font-family: '<?= $__FONT_NAME ?>';
}
</style>
</head>

<body class="<?= $__LANGUAGE_SETTINGS['language_direction'] == 'rtl' ? 'rtl-language' : '' ?>">

<?php
require_once('themes/' . CURRENT_THEME . '/header/header.php');
?>

<div id="main-container" data-reload-on-logout="1">
    <?php
    if($show_quiz_editor == 1)
        require_once('themes/' . CURRENT_THEME . '/user-quiz/user-quiz__left.php');
    else
        require_once('themes/' . CURRENT_THEME . '/user-quiz/user-quiz-without-login__left.php');
    ?> 
</div>

<div id="footer-container">
    <?php
    require_once('themes/' . CURRENT_THEME . '/footer/footer.php');
    ?>    
</div> 

<script>

var LANGUAGE_STRINGS = {
        "UNTITLED_QUIZ_LABEL": "<?= $__LANGUAGE_STRINGS['user_quiz']['UNTITLED_QUIZ_LABEL'] ?>",
        "MAX_18_QUESTIONS": "<?= $__LANGUAGE_STRINGS['user_quiz']['MAX_18_QUESTIONS'] ?>",
        "MIN_2_QUESTIONS": "<?= $__LANGUAGE_STRINGS['user_quiz']['MIN_2_QUESTIONS'] ?>",
        "MAX_5_OPTIONS": "<?= $__LANGUAGE_STRINGS['user_quiz']['MAX_5_OPTIONS'] ?>",
        "MIN_2_OPTIONS": "<?= $__LANGUAGE_STRINGS['user_quiz']['MIN_2_OPTIONS'] ?>",
        "ERRORS_FOUND": "<?= $__LANGUAGE_STRINGS['user_quiz']['ERRORS_FOUND'] ?>",
        "ERRORS_DIALOG_TITLE": "<?= $__LANGUAGE_STRINGS['user_quiz']['ERRORS_DIALOG_TITLE'] ?>",
        "QUIZ_TITLE_EMPTY": "<?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_TITLE_EMPTY'] ?>",
        "QUIZ_PICTURE_EMPTY": "<?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_PICTURE_EMPTY'] ?>",
        "QUIZ_DESCRIPTION_EMPTY": "<?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_DESCRIPTION_EMPTY'] ?>",
        "NO_CORRECT_OPTION_GIVEN": "<?= $__LANGUAGE_STRINGS['user_quiz']['NO_CORRECT_OPTION_GIVEN'] ?>",
        "WEIGHT_SHOULD_BE_NUMBER": "<?= $__LANGUAGE_STRINGS['user_quiz']['WEIGHT_SHOULD_BE_NUMBER'] ?>",
        "OPTION": "<?= $__LANGUAGE_STRINGS['user_quiz']['OPTION'] ?>",
        "REQUIRES_ATLEAST_PIC_OR_TEXT": "<?= $__LANGUAGE_STRINGS['user_quiz']['REQUIRES_ATLEAST_PIC_OR_TEXT'] ?>",
        "ERRORS_FOUND_IN_QUESTION": "<?= $__LANGUAGE_STRINGS['user_quiz']['ERRORS_FOUND_IN_QUESTION'] ?>",
        "ERRORS_FOUND_IN_RESULT": "<?= $__LANGUAGE_STRINGS['user_quiz']['ERRORS_FOUND_IN_RESULT'] ?>",
        "RESULT_TITLE_EMPTY": "<?= $__LANGUAGE_STRINGS['user_quiz']['RESULT_TITLE_EMPTY'] ?>",
        "ERRORS_FOUND_TAGS": "<?= $__LANGUAGE_STRINGS['user_quiz']['ERRORS_FOUND_TAGS'] ?>",
        "MIN_1_TAG": "<?= $__LANGUAGE_STRINGS['user_quiz']['MIN_1_TAG'] ?>",
        "INVALID_DOMAIN_FORMAT": "<?= $__LANGUAGE_STRINGS['user_quiz']['INVALID_DOMAIN_FORMAT'] ?>",
        "CONFIRM_QUIZ_PREMIUM_ACTIVATE": "<?= $__LANGUAGE_STRINGS['user_quiz']['CONFIRM_QUIZ_PREMIUM_ACTIVATE'] ?>",
        "CONFIRM_QUIZ_PREMIUM_DEACTIVATE": "<?= $__LANGUAGE_STRINGS['user_quiz']['CONFIRM_QUIZ_PREMIUM_DEACTIVATE'] ?>",
        "QUIZ_UNSAVED_MESSAGE": "<?= $__LANGUAGE_STRINGS['user_quiz']['QUIZ_UNSAVED_MESSAGE'] ?>"
    },
    LANGUAGE_CODE_CURRENT = '<?= $__LANGUAGE_CODE_CURRENT ?>',
    MAX_IMAGE_SIZE_ALLOWED_MB = '<?= MAX_IMAGE_SIZE_ALLOWED_MB ?>',
    PRETTY_URLS = '<?= PRETTY_URLS ?>',
    IS_DEFAULT_LANGUAGE = '<?= $__LANGUAGE_CODE_CURRENT == DEFAULT_LANGUAGE ? 1 : 0 ?>',
    LOCATION_SITE = '<?= LOCATION_SITE ?>';

</script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/user-quiz/user-quiz.js"></script>
<script>
<?php 
if($show_quiz_editor == 1) { 
?>
    var new_quiz = new NewQuiz($("#quiz-container"), <?= $__NEW_QUIZ ?>, <?= json_encode($quiz['quiz_properties']) ?>, <?= json_encode($quiz['quiz_data']) ?>, <?= json_encode($quiz['premium_properties']) ?>, <?= $show_premium ?>);
<?php 
} 
?>
</script>

<?= GOOGLE_ANALYTICS_CODE == '' ? '' : stripslashes(GOOGLE_ANALYTICS_CODE) ?>

</body>
</html>