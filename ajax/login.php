<?php
session_start();
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/facebook-api.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/user-model.php');
date_default_timezone_set(TIMEZONE);

try {
	$facebook = new FacebookApi();
	$user_data = $facebook->GetUserInformation($_POST['access_token']);
	
	$user_model_ob = new UserApplicationObject();
	$user = $user_model_ob->CheckUserExists($user_data['user_thirdparty_id'], $user_data['registration_source']);
	if($user == -1)
		$user = $user_model_ob->CreateUser($user_data);
    
	$_SESSION['user'] = $user;

	echo json_encode($user);
}
catch(Exception $e) {
    $LANGUAGE_STRINGS = json_decode(file_get_contents('lang/' . $_COOKIE['language_code'] . '.txt'), TRUE);

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