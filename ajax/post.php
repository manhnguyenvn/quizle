<?php
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../settings/settings-3.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/admin-model.php');
require_once('../classes/user-model.php');
date_default_timezone_set(TIMEZONE);

$command = $_GET['command'];

try {
	if(!isset($_COOKIE['_$_']) || !in_array($_COOKIE['_$_'], explode('-', CSRF_TOKEN)))
        throw new Exception('UNAUTHORIZED_REQUEST', 1);

    $user_app = new UserApplicationObject();

    switch($command) {
        case 'UpdatePostViewData':
            $user_app->UpdatePostViewData($_POST['post_id']);

            echo json_encode(['success' => 1]);

            break;

        case 'UpdatePostPlayedData':
            $user_app->UpdatePostPlayedData($_POST['post_id'], 0);

            echo json_encode(['success' => 1]);

            break;

        case 'UpdatePostFullyPlayedData':
            $user_app->UpdatePostPlayedData($_POST['post_id'], 1);

            echo json_encode(['success' => 1]);

            break;

        case 'UpdatePostSharesData':
            $user_app->UpdatePostSharesData($_POST['post_id']);

            echo json_encode(['success' => 1]);

            break;

        case 'UpdatePostCommentsData':
            $user_app->UpdatePostCommentsData($_POST['post_id']);

            echo json_encode(['success' => 1]);

            break;

        case 'UpdatePostCreditsData':
            $data = $user_app->UpdatePostCreditsData($_POST['post_id'], 1, 1);
            if($data['credits_available'] == 0) {
                $admin_app = new AdminApplicationObject();
                $email_template_settings = $admin_app->GetLanguageEmail($_COOKIE['language_code'], 'credits_finished');
                if($email_template_settings['email_send'] == 1) {
                    require_once('../classes/class.phpmailer.php');
                    require_once('../classes/class.smtp.php');

                    $user_details = $user_app->GetUser($data['user_id']);
                    $email_template_settings['email_body'] = str_replace('USER_FULL_NAME', $user_details['user_full_name'], $email_template_settings['email_body']);
                
                    if($user_details['user_email_confirmed'] == 1) {
                        ignore_user_abort(true);
                        set_time_limit(60);
                        if(USE_SMTP == 1) {
                            $email_sent = SendMail(SMTP_HOST, SMTP_PORT, SMTP_SECURITY, SMTP_USERNAME, SMTP_PASSWORD, FROM_EMAIL, FROM_EMAIL_NAME, $user_details['user_email'], $email_template_settings['email_subject'], '<div style="white-space:pre">' . $email_template_settings['email_body'] . '</div>');
                            if($email_sent != 1)
                                throw new Exception($email_sent, 2);
                        }
                        else {
                            mail($email, $email_template_settings['email_subject'], $email_template_settings['email_body']);
                        }
                    }
                }
            }

            echo json_encode(['success' => 1]);

            break;
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