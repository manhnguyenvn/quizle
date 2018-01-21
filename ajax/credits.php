<?php
session_start();
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/user-model.php');
require_once('../classes/paypal-api.php');
date_default_timezone_set(TIMEZONE);

$command = $_GET['command'];

try {
    if(!isset($_SESSION['user']))
        throw new Exception('UNAUTHORIZED_REQUEST', 1);

    $user_app = new UserApplicationObject();

    switch($command) {
        case 'GetPaymentToken':
            $digits_reg_exp = '/^[0-9]{1,}$/';
            $quantity = trim($_POST['quantity']);

            if(!preg_match($digits_reg_exp, $quantity))
                throw new Exception('BAD_REQUEST', 1);
            else if(!in_array($quantity, [1, 2, 5, 10, 20]))
                throw new Exception('BAD_REQUEST', 1);

            $is_sandbox = PAYPAL_SANDBOX_ACTIVATED;
            if($is_sandbox == 1) {
                $paypal_api_username = PAYPAL_SANDBOX_API_USERNAME;
                $paypal_api_password = PAYPAL_SANDBOX_API_PASSWORD;
                $paypal_api_signature = PAYPAL_SANDBOX_API_SIGNATURE;
                $payment_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
            }
            else {
                $paypal_api_username = PAYPAL_API_USERNAME;
                $paypal_api_password = PAYPAL_API_PASSWORD;
                $paypal_api_signature = PAYPAL_API_SIGNATURE;
                $payment_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
            }

            $paypal = new PaypalApi($is_sandbox);
            $token = $paypal->SetExpressCheckout($paypal_api_username, $paypal_api_password, $paypal_api_signature, CREDITS_VALUE, $quantity, TRANSACTION_CURRENCY, $_POST['payment_name'], (LOCATION_SITE . 'img/' . LOGO_NAME), LOCATION_SITE . 'user-credits.php?success', LOCATION_SITE . 'user-credits.php?error', (str_replace('-', '_', $_COOKIE['language_code'])), SITE_NAME);
            
            $_SESSION['paypal_token'] = $token;
            $_SESSION['payment_num_credits'] = CREDITS_QUANTITY*$quantity;

            echo json_encode(array('url' => $payment_url . $token));

            break;

        case 'GetTransactions':
            $transactions = $user_app->GetUserTransactions($_SESSION['user']['user_id']);

            echo json_encode(array('transactions' => $transactions));
    }
}
catch(Exception $e) {
    $LANGUAGE_STRINGS = json_decode(file_get_contents('../lang/' . $_COOKIE['language_code'] . '.txt'), TRUE);

    if($e->getCode() == 2) {
        header('Internal Server Error', true, 500);
        echo json_encode(array( 'error' => 1, 'message' => (DEBUG_MODE == 0 ? $LANGUAGE_STRINGS['general']['SERVER_FAILED'] : $e->getMessage()) ));
    }
    else {
        header('Bad Request', true, 400);
        echo json_encode(array( 'error' => 1, 'message' => $LANGUAGE_STRINGS['general'][$e->getMessage()] ));
    }

    exit();
}

?>