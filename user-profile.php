<?php 
session_start();
if(!isset($_SESSION['user']))
    header('Location: ./');

require_once('get-language-settings.php');
require_once('classes/user-model.php');

try {
    $user_app = new UserApplicationObject();
    $__USER_DETAILS = $user_app->GetUser($_SESSION['user']['user_id']);
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
<title><?= SITE_NAME . ' | ' . $__USER_DETAILS['user_full_name'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/header/header.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/user-profile/user-profile.css' rel='stylesheet' type='text/css'>
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

<div id="main-container" data-reload-on-logout="1">
    <div id="main-left-section">
        <?php
        require_once('themes/' . CURRENT_THEME . '/user-profile/user-profile__left.php');
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
        "INCORRECT_EMAIL": "<?= $__LANGUAGE_STRINGS['user_profile']['INCORRECT_EMAIL'] ?>"
    },
    LOCATION_SITE = '<?= LOCATION_SITE ?>';

</script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/user-profile/user-profile.js"></script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/sidebar/sidebar.js"></script>

<?= GOOGLE_ANALYTICS_CODE == '' ? '' : stripslashes(GOOGLE_ANALYTICS_CODE) ?>

</body>
</html>