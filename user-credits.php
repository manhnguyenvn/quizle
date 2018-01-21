<?php 
session_start();
if(!isset($_SESSION['user']))
    header('Location: ./');
    
require_once('get-language-settings.php');
?>
<!DOCTYPE html>
<html dir="<?= $__LANGUAGE_SETTINGS['language_direction'] ?>" lang="<?= $__LANGUAGE_CODE_CURRENT ?>">
<head>
<title><?= SITE_NAME . ' | ' . $__LANGUAGE_STRINGS['user_credits']['PAGE_TITLE'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale = 1.0, user-scalable=no">
<?= $__FONT_TYPE == 'GOOGLE' ? '<link href="http://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $__FONT_NAME) . ':400,700" rel="stylesheet" type="text/css">' : '' ?>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/common.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/header/header.css' rel='stylesheet' type='text/css'>
<link href='<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/user-credits/user-credits.css' rel='stylesheet' type='text/css'>
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
require_once('classes/user-model.php');

$user_app = new UserApplicationObject();

if(isset($_GET['success']) && isset($_GET['token']) && isset($_GET['PayerID'])) {
    require_once('classes/paypal-api.php');

    $__TRANSACTION_IN_PROCESS = 1;

    $paypal_token = $_GET['token'];
    $paypal_payer_id = $_GET['PayerID'];

    $is_sandbox = PAYPAL_SANDBOX_ACTIVATED;
    if($is_sandbox == 1) {
        $paypal_api_username = PAYPAL_SANDBOX_API_USERNAME;
        $paypal_api_password = PAYPAL_SANDBOX_API_PASSWORD;
        $paypal_api_signature = PAYPAL_SANDBOX_API_SIGNATURE;
    }
    else {
        $paypal_api_username = PAYPAL_API_USERNAME;
        $paypal_api_password = PAYPAL_API_PASSWORD;
        $paypal_api_signature = PAYPAL_API_SIGNATURE;
    }

    try {
        $paypal = new PaypalApi($is_sandbox);
        $checkout_details = $paypal->GetExpressCheckoutDetails($paypal_api_username, $paypal_api_password, $paypal_api_signature, $paypal_token);
        if($checkout_details['checkout_status'] == 'PaymentActionNotInitiated' && $paypal_token == $_SESSION['paypal_token']) {
            $payment_details = $paypal->DoExpressCheckout($paypal_api_username, $paypal_api_password, $paypal_api_signature, $paypal_payer_id, $paypal_token, $checkout_details['price_per_unit'], $checkout_details['quantity'], $checkout_details['currency'], $checkout_details['payment_name']);
            if($payment_details['payment_status'] == 'Failed') {
                throw new Exception('PAYMENT_FAILED', 1);
            }
            else {
                $__PAYMENT_SUCCESS = 1;
                
                $user_app->SetUserTransaction($_SESSION['user']['user_id'], $payment_details, $_SESSION['payment_num_credits']);
                if($payment_details['payment_status'] == 'Completed')
                    $user_app->UserCreditsUpdate($_SESSION['user']['user_id'], '+', $_SESSION['payment_num_credits']);

                $_SESSION['user']['user_premium'] = 1;
                
                unset($_SESSION['payment_num_credits']);
                unset($_SESSION['paypal_token']);
            }
        }
        else 
            throw new Exception('Incorrect Paypal Token', 2);
    }
    catch(Exception $e) {
        $__PAYMENT_SUCCESS = -1;

        if($e->getCode() == 2) {
            $__PAYMENT_ERROR_MESSAGE = (DEBUG_MODE == 0 ? $__LANGUAGE_STRINGS['general']['SERVER_FAILED'] : $e->getMessage());
        }
        else {
            $__PAYMENT_ERROR_MESSAGE = $__LANGUAGE_STRINGS['user_credits'][$e->getMessage()];
        }
    }
}
else if(isset($_GET['error'])) {
    $__TRANSACTION_IN_PROCESS = 1;
    $__PAYMENT_SUCCESS = -1;
    $__PAYMENT_ERROR_MESSAGE = $__LANGUAGE_STRINGS['user_credits']['PAYMENT_FAILED'];
}
else {
    $__TRANSACTION_IN_PROCESS = 0;
}

$__USER_AVAILABLE_CREDITS = $user_app->GetUserCredits($_SESSION['user']['user_id']);

?>

<div id="main-container" data-reload-on-logout="1">
    <div id="main-left-section">
        <?php
        require_once('themes/' . CURRENT_THEME . '/user-credits/user-credits__left.php');
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
        "CREDITS_PACK": "<?= CREDITS_QUANTITY . ' ' . $__LANGUAGE_STRINGS['user_credits']['CREDITS_PACK'] ?>",
        "PAYMENT_PENDING_MESSAGE": "<?= $__LANGUAGE_STRINGS['user_credits']['PAYMENT_PENDING_MESSAGE'] ?>",
        "PAYMENT_FAILED_MESSAGE": "<?= $__LANGUAGE_STRINGS['user_credits']['PAYMENT_FAILED_MESSAGE'] ?>",
        "PAYPAL": "<?= $__LANGUAGE_STRINGS['user_credits']['PAYPAL'] ?>",
        "TRANSACTION_DATE": "<?= $__LANGUAGE_STRINGS['user_credits']['TRANSACTION_DATE'] ?>",
        "TRANSACTION_ID": "<?= $__LANGUAGE_STRINGS['user_credits']['TRANSACTION_ID'] ?>",
        "TRANSACTION_AMOUNT": "<?= $__LANGUAGE_STRINGS['user_credits']['TRANSACTION_AMOUNT'] ?>",
        "CREDITS": "<?= $__LANGUAGE_STRINGS['user_credits']['CREDITS'] ?>",
    },
    LOCATION_SITE = '<?= LOCATION_SITE ?>';

</script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/user-credits/user-credits.js"></script>
<script src="<?= LOCATION_SITE ?>themes/<?= CURRENT_THEME ?>/sidebar/sidebar.js"></script>

<?= GOOGLE_ANALYTICS_CODE == '' ? '' : stripslashes(GOOGLE_ANALYTICS_CODE) ?>

</body>
</html>