<?php
require_once('../settings/settings-1.php');
require_once('../settings/settings-2.php');
require_once('../settings/settings-3.php');
date_default_timezone_set(TIMEZONE);

$tokens = explode('-', CSRF_TOKEN);

$fp = fopen('../settings/settings-3.php', "w");
$file_contents = "<?php" . PHP_EOL . PHP_EOL;
$file_contents .= "define('CSRF_TOKEN', '" . $tokens[1] . '-' . uniqid() . "');" . PHP_EOL . PHP_EOL;
$file_contents .= "?>";
$result = fwrite($fp, $file_contents);
fclose($fp);

$fp = fopen('refresh-csrf-token-time.txt', "w");
fwrite($fp, date('Y-m-d H:i:s'));
fclose($fp);

?>