<?php
require_once('get-language-settings.php');
require_once('classes/user-model.php');
   
$post_id = $_GET['post_id'];
$__SHOW_COMMENTS = $_GET['show_comments'];
$__SHOW_TITLE = $_GET['show_title'];
$__DISPLAY_POST = 1;

try {
    $user_app = new UserApplicationObject();
    $quiz = $user_app->GetPostFull($post_id, $__LANGUAGE_CODE_CURRENT);
    if($quiz == -1) {
        $__DISPLAY_POST = -1;
    }
    else {
        $__QUIZ_PROPERTIES = $quiz['quiz_properties'];
        $__QUIZ_DATA = $quiz['quiz_data'];
        $__QUIZ_USER_DATA = $quiz['quiz_user_properties'];
        $__POST_URL = $quiz['post_url'];
        $__POST_URL_FB = $quiz['post_url_fb'];

        if($quiz['is_hidden'] == 1)
            $__DISPLAY_POST = 0;
        else if($quiz['premium_properties']['is_premium'] == 1 && ACTIVATE_PREMIUM == 1) {
            $user_credits = $user_app->GetUserCredits($__QUIZ_USER_DATA['user_id']);
            if($user_credits == 0)
                $__DISPLAY_POST = 0;

            header("X-Frame-Options: ALLOW-FROM http://" . $quiz['premium_properties']['domain'] . "/", false);
        }
    }
}
catch(Exception $e) {
    if($e->getCode() == 2)
        echo (DEBUG_MODE == 0 ? $__LANGUAGE_STRINGS['general']['SERVER_FAILED'] : $e->getMessage());
    else
        echo $__LANGUAGE_STRINGS['general'][$e->getMessage()];

    exit();
}

?>
<!DOCTYPE html>
<html dir="<?= $__LANGUAGE_SETTINGS['language_direction'] ?>" lang="<?= $__LANGUAGE_CODE_CURRENT ?>">
<head>
<title><?= $__QUIZ_PROPERTIES['title'] . ' | ' . SITE_NAME ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/post/post.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/embed/embed.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>css/font-awesome.css' rel='stylesheet' type='text/css'>
<script src="<?= LOCATION_SITE ?>js/jquery-1.11.3.min.js"></script>
<style type="text/css">
body {
    font-family: '<?= $__FONT_NAME ?>';
}
</style>
</head>

<body class="<?= $__LANGUAGE_SETTINGS['language_direction'] == 'rtl' ? 'rtl-language' : '' ?>">

<div id="main-container">
    <div id="main-left-section">
        <?php
        require_once('themes/' . CURRENT_THEME . '/embed/embed__left.php');
        ?> 
    </div>
</div>

<script>
/*var parent_url = (window.location != window.parent.location) ? document.referrer : document.location;

var parser = document.createElement('a');
parser.href = parent_url;

if(<?= ($quiz['premium_properties']['is_premium'] == 1 && ACTIVATE_PREMIUM == 1 && $__DISPLAY_POST == 1) ? 'true' : 'false' ?>) {
    if(parser.hostname != "<?= $quiz['premium_properties']['domain'] ?>") {
        $("#main-container").html("<div id=\"quiz-unavailable-container\"><?= $__LANGUAGE_STRINGS['post']['CONTENT_UNAVAILABLE'] ?></div>");
    }
}*/

var LANGUAGE_STRINGS = {
        "HINT": "<?= $__LANGUAGE_STRINGS['post']['HINT'] ?>",
        "FACT": "<?= $__LANGUAGE_STRINGS['post']['FACT'] ?>",
        "NEXT_QUESTION_BUTTON": "<?= $__LANGUAGE_STRINGS['post']['NEXT_QUESTION_BUTTON'] ?>",
        "RESULTS_BUTTON": "<?= $__LANGUAGE_STRINGS['post']['RESULTS_BUTTON'] ?>"
    },
    UPDATE_CREDITS = '<?= $quiz["premium_properties"]["is_premium"] == 1 && ACTIVATE_PREMIUM == 1 ? 1 : 0?>',
    LOCATION_SITE = '<?= LOCATION_SITE ?>';

</script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/post/post.js"></script>
<script>
if(<?= $__DISPLAY_POST == 1 ? 'true' : 'false' ?>) { 
    var play_quiz = new PlayQuiz(<?= json_encode($quiz['quiz_properties']) ?>, <?= json_encode($quiz['quiz_data']) ?>, <?= json_encode($quiz['premium_properties']) ?>);
    play_quiz.loadQuestion(1);
    play_quiz.adjustHeight();
    play_quiz.ajaxRequest('UpdatePostViewData');
}
</script>

<script>
setInterval(function() {
    window.top.postMessage($("body").get(0).scrollHeight, "*");
}, 1000);
</script>

</body>
</html>