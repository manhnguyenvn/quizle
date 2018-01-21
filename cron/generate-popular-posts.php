<?php
set_time_limit(0);
require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../classes/db-wrapper.php');
require_once('../classes/user-model.php');
require_once('../classes/admin-model.php');
date_default_timezone_set(TIMEZONE);

try {
	$admin_app = new AdminApplicationObject();
    $activated_languages = $admin_app->GetLanguages(1, 1);

	$user_app = new UserApplicationObject();
	$user_app->GeneratePopularPosts($activated_languages);

	$fp = fopen('generate-popular-posts-time.txt', "w");
	fwrite($fp, date('Y-m-d H:i:s'));
	fclose($fp);
}
catch(Exception $e) {
	echo $e->getMessage();
}

?>