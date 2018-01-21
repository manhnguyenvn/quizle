<?php
require_once('settings/settings-1.php');
require_once('settings/settings-2.php');
require_once('settings/settings-3.php');
require_once('classes/db-wrapper.php');
require_once('classes/admin-model.php');

date_default_timezone_set(TIMEZONE);

try {
    $__ADMIN_APP = new AdminApplicationObject();
    
    $__URL_PARAMETERS = [];
    if(PRETTY_URLS == 0)
        $__NO_PARAMETER_URL = 'URL_NAME.php';
    else
        $__NO_PARAMETER_URL = 'URL_NAME';

    if(!isset($_GET['language_code'])) {
        $__LANGUAGE_CODE_CURRENT = DEFAULT_LANGUAGE;
    }
    else if($_GET['language_code'] == DEFAULT_LANGUAGE) {
        $__LANGUAGE_CODE_CURRENT = DEFAULT_LANGUAGE;
    }
    else {
        $__LANGUAGE_CODE_CURRENT = $_GET['language_code'];
        if(PRETTY_URLS == 0) {
            $__URL_PARAMETERS['language_code'] = $__LANGUAGE_CODE_CURRENT;
            $__NO_PARAMETER_URL = 'URL_NAME.php?' . http_build_query($__URL_PARAMETERS);
        }
        else {
            $__URL_PARAMETERS[] = $__LANGUAGE_CODE_CURRENT;
            $__NO_PARAMETER_URL = implode('/', $__URL_PARAMETERS) . '/URL_NAME';
        }
    }

    $__LANGUAGE_SETTINGS = $__ADMIN_APP->GetLanguageSettings($__LANGUAGE_CODE_CURRENT, 1);
    if($__LANGUAGE_SETTINGS == -1)
        throw new Exception('Error: This language is not supported');
    if($__LANGUAGE_SETTINGS['language_activated'] == 0)
        throw new Exception('Error: This language is not activated');

    setcookie('language_code', $__LANGUAGE_CODE_CURRENT, 0, '/');
    setcookie('_$_', explode('-', CSRF_TOKEN)[1], 0, '/');

    $__LANGUAGE_STRINGS = json_decode(file_get_contents('lang/' . $__LANGUAGE_CODE_CURRENT . '.txt'), TRUE);
    $__SITE_URL = LOCATION_SITE . ($__LANGUAGE_CODE_CURRENT == DEFAULT_LANGUAGE ? '' : (PRETTY_URLS == 0 ? '?language_code=' : '') . $__LANGUAGE_CODE_CURRENT);

    list($__FONT_TYPE, $__FONT_NAME) = explode('-', FONT_FAMILY);
}
catch(Exception $e) {
    echo $e->getMessage();
    exit();
}

?>