<?php 
session_start();
require_once('get-language-settings.php'); 
?>
<!DOCTYPE html>
<html dir="<?= $__LANGUAGE_SETTINGS['language_direction'] ?>" lang="<?= $__LANGUAGE_CODE_CURRENT ?>">
<head>
<title><?= str_replace('[[SITE_NAME]]', SITE_NAME, $__LANGUAGE_STRINGS['home_page']['META_TITLE']) ?></title>
<meta name="description" content="<?= $__LANGUAGE_STRINGS['home_page']['META_DESCRIPTION'] ?>" />
<meta property="og:site_name" content="<?= SITE_NAME ?>" />
<meta property="og:url" content="<?= $__SITE_URL ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= str_replace('[[SITE_NAME]]', SITE_NAME, $__LANGUAGE_STRINGS['home_page']['META_TITLE']) ?>" />
<meta property="og:description" content="<?= $__LANGUAGE_STRINGS['home_page']['META_DESCRIPTION'] ?>" />
<meta property="og:image" content="<?= LOCATION_SITE . 'img/' . SHARE_IMAGE_NAME . '?' . SHARE_IMAGE_CACHE ?>" />
<meta property="og:image:width" content="600" />
<meta property="og:image:height" content="325" />
<meta property="fb:app_id" content="<?= FACEBOOK_APP_ID ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0">
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/header/header.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/index/index.css' rel='stylesheet' type='text/css'>
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

try {
    $__DATA = $__ADMIN_APP->GetPublishedPosts(1, $__LANGUAGE_CODE_CURRENT);
    $__AD_UNIT_1 = $__ADMIN_APP->GetAd(1);
    $__AD_UNIT_1_POSITION = rand(0, sizeof($__DATA['posts']) - 1);

    if(sizeof($__DATA['posts']) >= 3) {
        $__FEATURED_POSTS = $__ADMIN_APP->GetFeaturedPosts(1, $__LANGUAGE_CODE_CURRENT);

        if(sizeof($__FEATURED_POSTS) < 5) {
            foreach($__DATA['posts'] as $post_id => $post_info) {
                if(!array_key_exists($post_id, $__FEATURED_POSTS)) {
                    $__FEATURED_POSTS[$post_id] = [ 'title' => $post_info['title'], 'image' => $post_info['image'], 'post_url' => $post_info['post_url'] ];
                    if(sizeof($__FEATURED_POSTS) == 5)
                        break;
                }
            }
        }

        $__LEFT_SIDE_FEATURED_POSTS_COUNT = sizeof($__FEATURED_POSTS) - 2;

        $__LEFT_SIDE_FEATURED_POSTS = array_slice($__FEATURED_POSTS, 0, $__LEFT_SIDE_FEATURED_POSTS_COUNT, true);
        $__RIGHT_TOP_FEATURED_POST = array_slice($__FEATURED_POSTS, -2, 1, true);
        $__RIGHT_BOTTOM_FEATURED_POST = array_slice($__FEATURED_POSTS, -1, 1, true);

        require_once('themes/' . CURRENT_THEME . '/index/index__top.php');
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

<div id="main-container">
    <?php
    require_once('themes/' . CURRENT_THEME . '/index/index__left.php');
    ?>    
</div> 

<div id="footer-container">
    <?php
    require_once('themes/' . CURRENT_THEME . '/footer/footer.php');
    ?>    
</div> 

<script>

var NUM_SLIDES = <?= sizeof($__DATA['posts']) >= 3 ? $__LEFT_SIDE_FEATURED_POSTS_COUNT : 0 ?>,
    CURRENT_SLIDE = 1,
    SLIDER_INTERVAL_VAR,
    LANGUAGE_STRINGS = {
        "POST_BY": "<?= $__LANGUAGE_STRINGS['home_page']['POST_BY'] ?>",
        "POST_TIME_AGO": "<?= $__LANGUAGE_STRINGS['home_page']['POST_TIME_AGO'] ?>",
        "ADVERTISEMENT": "<?= $__LANGUAGE_STRINGS['home_page']['ADVERTISEMENT'] ?>"
    },
    LOCATION_SITE = '<?= LOCATION_SITE ?>';

</script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/index/index.js"></script>

<?= GOOGLE_ANALYTICS_CODE == '' ? '' : stripslashes(GOOGLE_ANALYTICS_CODE) ?>

</body>
</html>