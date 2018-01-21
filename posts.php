<?php 
session_start();
require_once('get-language-settings.php');

try {
    if(isset($_GET['tag_id'])) {
        $__TAG_ID = $_GET['tag_id'];
        $__IS_CAT = 0;
    }
    else if(isset($_GET['cat_id'])) {
        $__TAG_ID = $_GET['cat_id'];
        $__IS_CAT = 1;
    }
    else {
        $__TAG_ID = NULL;
    }

    $__TAG_CURRENT = $__ADMIN_APP->GetTagTitle($__TAG_ID);

    $__DATA = $__ADMIN_APP->GetPublishedPostsByTag($__TAG_ID, $__IS_CAT, 1, $__LANGUAGE_CODE_CURRENT);
    $__AD_UNIT_1 = $__ADMIN_APP->GetAd(1);
    $__AD_UNIT_1_POSITION = rand(0, sizeof($__DATA['posts']) - 1);
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
<title><?= str_replace(['[[SITE_NAME]]', '[[TAG_NAME]]'], [SITE_NAME, $__TAG_CURRENT], $__LANGUAGE_STRINGS['home_page']['META_TITLE_TAGS']) ?></title>
<meta name="description" content="<?= $__LANGUAGE_STRINGS['home_page']['META_DESCRIPTION'] ?>" />
<meta property="og:site_name" content="<?= SITE_NAME ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= str_replace(['[[SITE_NAME]]', '[[TAG_NAME]]'], [SITE_NAME, $__TAG_CURRENT], $__LANGUAGE_STRINGS['home_page']['META_TITLE_TAGS']) ?>" />
<meta property="og:description" content="<?= $__LANGUAGE_STRINGS['home_page']['META_DESCRIPTION'] ?>" />
<meta property="og:image" content="<?= LOCATION_SITE . 'img/' . SHARE_IMAGE_NAME . '?' . SHARE_IMAGE_CACHE ?>" />
<meta property="og:image:width" content="600" />
<meta property="og:image:height" content="325" />
<meta property="fb:app_id" content="<?= FACEBOOK_APP_ID ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/header/header.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/posts/posts.css' rel='stylesheet' type='text/css'>
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
    <?php
    require_once('themes/' . CURRENT_THEME . '/posts/posts__top.php');
    require_once('themes/' . CURRENT_THEME . '/posts/posts__left.php');
    ?>  
</div>

<div id="footer-container">
    <?php
    require_once('themes/' . CURRENT_THEME . '/footer/footer.php');
    ?>    
</div> 

<script>

var LANGUAGE_STRINGS = {
        "POST_BY": "<?= $__LANGUAGE_STRINGS['home_page']['POST_BY'] ?>",
        "POST_TIME_AGO": "<?= $__LANGUAGE_STRINGS['home_page']['POST_TIME_AGO'] ?>",
        "ADVERTISEMENT": "<?= $__LANGUAGE_STRINGS['home_page']['ADVERTISEMENT'] ?>"
    },
    LOCATION_SITE = '<?= LOCATION_SITE ?>';

</script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/posts/posts.js"></script>

<?= GOOGLE_ANALYTICS_CODE == '' ? '' : stripslashes(GOOGLE_ANALYTICS_CODE) ?>

</body>
</html>