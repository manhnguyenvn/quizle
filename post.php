<?php 
session_start();
require_once('get-language-settings.php');
require_once('classes/user-model.php');
   
$post_id = $_GET['post_id'];
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

        if($quiz['is_hidden'] == 1) {
            $__DISPLAY_POST = 0;
            if(isset($_SESSION['user'])) {
                if($_SESSION['user']['user_id'] == $quiz['quiz_user_properties']['user_id'])
                    $__DISPLAY_POST = 1;
            }
        }
        else if($quiz['premium_properties']['is_premium'] == 1 && ACTIVATE_PREMIUM == 1) {
            if(!isset($_SESSION['user']) || $_SESSION['user']['user_id'] != $quiz['quiz_user_properties']['user_id'])
                $__DISPLAY_POST = 0;
        }

        $__SIMILAR_QUIZES = $__ADMIN_APP->GetSimilarPostsByTag($post_id, $quiz['quiz_properties']['tags'], $__LANGUAGE_CODE_CURRENT);
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
<meta name="description" content="<?= $__QUIZ_PROPERTIES['description'] ?>" />
<meta property="og:site_name" content="<?= SITE_NAME ?>" />
<meta property="og:url" content="<?= $__POST_URL ?>" />
<meta property="og:title" content="<?= htmlentities($__QUIZ_PROPERTIES['title'], ENT_QUOTES) ?>" />
<meta property="og:description" content="<?= htmlentities($__QUIZ_PROPERTIES['description'], ENT_QUOTES) ?>" />
<meta property="og:image" content="<?= LOCATION_SITE . 'img/QUIZ/quiz/' . $__QUIZ_PROPERTIES['image_id'] ?>" />
<meta property="og:image:width" content="600" />
<meta property="og:image:height" content="325" />
<meta property="og:type" content="article" />
<meta property="og:fb:app_id" content="<?= FACEBOOK_APP_ID ?>" />
<?= FACEBOOK_PAGE_URL != '' ? '<meta property="article:publisher" content="' . FACEBOOK_PAGE_URL. '" />' : '' ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/header/header.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/post/post.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>css/font-awesome.css' rel='stylesheet' type='text/css'>
<script src="<?= LOCATION_SITE ?>js/jquery-1.11.3.min.js"></script>
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

<div id="main-container">
    <div id="main-left-section">
        <?php
        require_once('themes/' . CURRENT_THEME . '/post/post__left.php');
        ?> 
    </div>
    <div id="main-right-section">
        <?php
        require_once('themes/' . CURRENT_THEME . '/sidebar/sidebar.php');
        ?> 
    </div>
</div>

<div id="footer-container">
    <?php
    require_once('themes/' . CURRENT_THEME . '/footer/footer.php');
    ?>    
</div> 

<script>

var LANGUAGE_STRINGS = {
        "HINT": "<?= $__LANGUAGE_STRINGS['post']['HINT'] ?>",
        "FACT": "<?= $__LANGUAGE_STRINGS['post']['FACT'] ?>",
        "NEXT_QUESTION_BUTTON": "<?= $__LANGUAGE_STRINGS['post']['NEXT_QUESTION_BUTTON'] ?>",
        "RESULTS_BUTTON": "<?= $__LANGUAGE_STRINGS['post']['RESULTS_BUTTON'] ?>"
    },
    POST_URL = "<?= $__POST_URL ?>",
    POST_URL_FB = "<?= $__POST_URL_FB ?>",
    UPDATE_CREDITS = '0',
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
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/sidebar/sidebar.js"></script>

<?= GOOGLE_ANALYTICS_CODE == '' ? '' : stripslashes(GOOGLE_ANALYTICS_CODE) ?>

</body>
</html>