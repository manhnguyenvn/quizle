<?php
session_start();
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/user-model.php');
require_once('../classes/admin-model.php');
require_once('../classes/class.phpmailer.php');
require_once('../classes/class.smtp.php');
date_default_timezone_set(TIMEZONE);

$command = $_GET['command'];

try {
    if(!isset($_SESSION['user']))
        throw new Exception('UNAUTHORIZED_REQUEST', 1);

    $user_app = new UserApplicationObject();
    $admin_app = new AdminApplicationObject();

    switch($command) {
        case 'ChangeEmail':
            $email = $_POST['email'];
            $email_reg_exp = '/^([a-zA-z0-9]{1,}(?:([\._-]{0,1}[a-zA-Z0-9]{1,}))+@{1}([a-zA-Z0-9-]{2,}(?:([\.]{1}[a-zA-Z]{2,}))+))$/';
            
            if(!preg_match($email_reg_exp, $email))
                throw new Exception('BAD_REQUEST', 1);

            $confirmation_code = $user_app->UserEmailChange($_SESSION['user']['user_id'], $email);
            if($confirmation_code == -1)
                $confirmation_required = -1;
            else
                $confirmation_required = 1;

            if($confirmation_required == 1) {
                $user_details = $user_app->GetUser($_SESSION['user']['user_id']);
                $email_confirmation_settings = $admin_app->GetLanguageEmail($_COOKIE['language_code'], 'confirm_email');
                $email_confirmation_settings['email_body'] = str_replace(['CONFIRMATION_CODE', 'USER_FULL_NAME'], [$confirmation_code, $user_details['user_full_name']], $email_confirmation_settings['email_body']);
                
                set_time_limit(60);
                if(USE_SMTP == 1) {
                    $email_sent = SendMail(SMTP_HOST, SMTP_PORT, SMTP_SECURITY, SMTP_USERNAME, SMTP_PASSWORD, FROM_EMAIL, FROM_EMAIL_NAME, $email, $email_confirmation_settings['email_subject'], '<div style="white-space:pre">' . $email_confirmation_settings['email_body'] . '</div>');
                    if($email_sent != 1)
                        throw new Exception($email_sent, 2);
                }
                else {
                    mail($email, $email_confirmation_settings['email_subject'], $email_confirmation_settings['email_body']);
                }
            }   

            echo json_encode(array('confirmation_required' => $confirmation_required));

            break;

        case 'CheckConfirmationCode':
            $confirmation_code_correct = $user_app->UserEmailConfirm($_SESSION['user']['user_id'], $_POST['confirmation_code']);

            echo json_encode(array('confirmation_code_correct' => $confirmation_code_correct));

            break;

        case 'ResendConfirmationCode':
            $confirmation_code = $user_app->GenerateNewConfirmationCode($_SESSION['user']['user_id']);
            
            $user_details = $user_app->GetUser($_SESSION['user']['user_id']);
            $email_confirmation_settings = $admin_app->GetLanguageEmail($_COOKIE['language_code'], 'confirm_email');
            $email_confirmation_settings['email_body'] = str_replace(['CONFIRMATION_CODE', 'USER_FULL_NAME'], [$confirmation_code, $user_details['user_full_name']], $email_confirmation_settings['email_body']);

            set_time_limit(60);
            if(USE_SMTP == 1) {
                $email_sent = SendMail(SMTP_HOST, SMTP_PORT, SMTP_SECURITY, SMTP_USERNAME, SMTP_PASSWORD, FROM_EMAIL, FROM_EMAIL_NAME, $user_details['user_email'], $email_confirmation_settings['email_subject'], '<div style="white-space:pre">' . $email_confirmation_settings['email_body'] . '</div>');
                if($email_sent != 1)
                    throw new Exception($email_sent, 2);
            }
            else {
                mail($email, $email_confirmation_settings['email_subject'], $email_confirmation_settings['email_body']);
            }

            echo json_encode(array('updated' => 1));
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