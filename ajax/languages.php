<?php
header('Content-type: application/json');

require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../settings/settings-3.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/admin-model.php');
date_default_timezone_set(TIMEZONE);

$command = $_GET['command'];

try {
	if(!isset($_COOKIE['_$_']) || !in_array($_COOKIE['_$_'], explode('-', CSRF_TOKEN)))
        throw new Exception('UNAUTHORIZED_REQUEST', 1);

    switch($command) {
        case 'GetActivatedLanguages':
            $admin_app = new AdminApplicationObject();
            $activated_languages = $admin_app->GetLanguages(1);

            echo json_encode(array('languages' => $activated_languages, 'pretty_urls' => PRETTY_URLS));
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