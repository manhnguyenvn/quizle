<?php
require_once('../classes/db-wrapper.php');
require_once('../classes/admin-model.php');
require_once('../settings/settings-2.php');

if(isset($_POST['command'])) {
	header('Content-type: application/json');

	define('SERVER_NAME', $_POST['server_name']);
	define('MYSQL_DBNAME', $_POST['database_name']);
	define('MYSQL_USER', $_POST['mysql_username']);
	define('MYSQL_PASSWORD', $_POST['mysql_password']);
	
	try {
		$app = new AdminApplicationObject();
		$app->CreateTables($_POST['login_email'], $_POST['login_password']);

		/* Find root url */
		$root_url = explode('/', $_SERVER["REQUEST_URI"]);
		array_pop($root_url);
		array_pop($root_url);
		array_values($root_url);
		$root_url = 'http://' . $_SERVER['HTTP_HOST'] . implode('/', $root_url) . '/';

		/* Find mod_rewrite enabled */
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $root_url . 'install/htaccess/');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$pretty_urls = ($http_code == 200 ? 1 : 0);
		if($pretty_urls == 1)
			rename('htaccess/.htaccess', '../.htaccess');

		$fp = fopen('../settings/settings-1.php', 'w+');
		$file_contents = "<?php" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('SERVER_NAME', '" . $_POST['server_name'] . "');" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('MYSQL_DBNAME', '" . $_POST['database_name'] . "');" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('MYSQL_USER', '" . $_POST['mysql_username'] . "');" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('MYSQL_PASSWORD', '" . $_POST['mysql_password'] . "');" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('LOCATION_SITE', '" . $root_url . "');" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('DEBUG_MODE', '0');" . PHP_EOL . PHP_EOL;
		$file_contents .= "define('PRETTY_URLS', '" . $pretty_urls . "');" . PHP_EOL . PHP_EOL;
		$file_contents .= "?>";
		$result = fwrite($fp, $file_contents);
		fclose($fp);
		if($result == FALSE) {
			echo json_encode(array('error' => 1, 'message' => 'Settings File Write Failed'));
			exit();
		}

		/* Setup cron jobs */
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		    $cron_setup = 0;
		} 
		else {
		    if(is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec')) {
			    $output = shell_exec('crontab -l');
				$regEx = '/((SHELL|shell)=(\"|\').+(\"|\'))/';
				$output = preg_replace($regEx, '', $output);
				file_put_contents('crontab.txt', $output . '0 0 * * * wget -q -O - ' . ($root_url . 'cron/generate-popular-posts.php') . ' >/dev/null 2>&1' . PHP_EOL . '*/15 * * * * wget -q -O - ' . ($root_url . 'cron/refresh-csrf-token.php') . ' >/dev/null 2>&1' . PHP_EOL);
				exec('crontab crontab.txt');
				unlink('crontab.txt');

				$cron_setup = 1;
			}
			else {
				$cron_setup = 0;
			}
		}

		unlink(__FILE__);
		if($pretty_urls == 0)
			unlink('htaccess/.htaccess');
		unlink('htaccess/index.php');
		rmdir('htaccess');
		chdir('../');
		rmdir('install');

		echo json_encode(array('error' => 0, 'cron_setup' => $cron_setup));
		exit();
	}
	catch(Exception $e) {
		echo json_encode(array('error' => 1, 'message' => $e->getMessage()));
		exit();
	}		
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Quizzio Installation</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Merriweather+Sans">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<link href='../css/font-awesome.css' rel='stylesheet' type='text/css'>
<style type="text/css">
body {
	font-family: "Merriweather Sans",Verdana;
	margin: 0 auto;
	color: #444444;
}

#content {
	width: 700px;
	margin: 10px auto 0 auto;
}

#logo {
	text-align: center;
	background-color: #36538f;
	font-size: 16px;
	padding: 10px;
	color: white;
	margin: 0 0 60px 0;
	border-radius: 2px;
}

.separator {
	margin: 0 10px;
	color: rgba(255, 255, 255, 0.3);
}

.version {
	font-size: 12px;
}

.requirement-container {
	overflow: hidden;
	margin: 0 0 20px 0;
}

.requirement-container label {
	margin: 0 10px 0 0;
	float: left;
	width: 200px;
}

.requirement-container span {
	float: left;
}

.fa-check-circle {
	color: green;
	font-size: 20px;
}

.fa-times-circle {
	color: red;
	font-size: 20px;
}

.button {
	min-width: 150px;
	display: block;
	margin: 35px 0 0 0;
	font-family: "Merriweather Sans",Verdana;
	background: none;
	color: white;
	border-radius: 2px;
	background-color: #36538f;
	cursor: pointer;
	border: 1px solid #36538f;
	font-size: 13px;
	padding: 4px;
	transition-property: background,border;
	transition-duration: 0.5s;
	transition-timing-function: linear;
}

.button-inactive {
	background-color: #5477BC !important;
	border: 1px solid #5477BC !important;
	opacity: 0.7;
	cursor: wait !important;
}

.button:hover {
	background-color: #5477BC;
	border: 1px solid #5477BC;
}

label {
	cursor: pointer;
	display: block;
	margin: 0 0 7px 0;
	font-size: 14px;
}

input[type="text"] {
	display: block;
	border: 1px solid #cccccc;
	padding: 6px;
	width: 100%;
	font-family: "Merriweather Sans",Verdana;
	font-size: 13px;
	outline: none;
	resize: none;
	border-radius: 2px;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
}

.section {
	margin: 0 0 100px 0;
}

#section-2, #section-3, #section-4, #section-5 {
	display: none;
}

.section-caption {
	font-size: 20px;
	margin: 0 0 30px 0;
}

.input-section {
	margin: 0 0 30px 0;
}

.error {
	padding: 8px;
	font-size: 14px;
	background-color: #FF9E9E;
	color: black;
	border-radius: 2px;
	display: none;
	margin: 35px 0 0 0;
}

#success {
	margin: 50px auto 0 auto;
	padding: 8px 5px;
	text-align: center;
	font-size: 14px;
	background-color: #A5FF87;
	display: none;
	border-radius: 2px;
}
</style>
</head>

<body>

<div id="content">
	<div id="logo">Quizzio <span class="separator">|</span> PHP Quiz Website Script <span class="separator">|</span> Social Quizzing Platform <br /><br /><span class="version">Version 1</span></div>
	<div id="section-1" class="section">
		<?php
		if(version_compare(PHP_VERSION, '5.4.0') >= 0)
			$php_ok = 1;
		else
			$php_ok = 0;
		
		if(is_writable(dirname(dirname(__FILE__))))
			$writable_ok = 1;
		else
			$writable_ok = 0;

		if(function_exists('mysqli_connect'))
			$mysqli_ok = 1;
		else
			$mysqli_ok = 0;

		if(function_exists('curl_version'))
			$curl_ok = 1;
		else
			$curl_ok = 0;

		if(extension_loaded('gd') && function_exists('gd_info'))
			$gd_ok = 1;
		else
			$gd_ok = 0;

		if($php_ok == 1 && $writable_ok == 1 && $mysqli_ok == 1 && $curl_ok && $gd_ok == 1)
			$all_requirements_met = 1;
		else
			$all_requirements_met = 0;
		?>
		<div class="requirement-container">
			<label>PHP Version at least 5.4.0</label>
			<span><i class="fa <?= $php_ok == 1 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i></span>
		</div>
		<div class="requirement-container">
			<label>Quizzio Directory Writable</label>
			<span><i class="fa <?= $writable_ok == 1 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i></span>
		</div>
		<div class="requirement-container">
			<label>MySqli Extension Enabled</label>
			<span><i class="fa <?= $mysqli_ok == 1 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i></span>
		</div>
		<div class="requirement-container">
			<label>Curl Extension Enabled</label>
			<span><i class="fa <?= $curl_ok == 1 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i></span>
		</div>
		<div class="requirement-container">
			<label>GD Extension Enabled</label>
			<span><i class="fa <?= $gd_ok == 1 ? 'fa-check-circle' : 'fa-times-circle' ?>"></i></span>
		</div>
		<div id="section-1-error" class="error" style="<?= $all_requirements_met == 1 ? '' : 'display:block' ?>">All Server Requirements of Quizzio Are Not Met</div>
		<button class="button" id="section-1-button" style="<?= $all_requirements_met == 1 ? '' : 'display:none' ?>">Proceed To Installation</button>
	</div>
	<div id="section-2" class="section">
		<div class="section-caption">Database Details</div>
		<div class="input-section">
			<label for="server-name">Server Name</label>
			<input type="text" id="server-name" />
		</div>
		<div class="input-section">
			<label for="database-name">Database Name</label>
			<input type="text" id="database-name" />
		</div>
		<div class="input-section">
			<label for="mysql-username">MySql Username</label>
			<input type="text" id="mysql-username" />
		</div>
		<div class="input-section">
			<label for="mysql-password">MySql Password</label>
			<input type="text" id="mysql-password" />
		</div>
		<div class="section-caption" style="margin-top:100px">Admin Section Details</div>
		<div class="input-section">
			<label for="login-email">Login Email <em>(This email will be used in the Contact section of the website)</em></label>
			<input type="text" id="login-email" />
		</div>
		<div class="input-section">
			<label for="login-password">Login Password</label>
			<input type="text" id="login-password" />
		</div>
		<div class="input-section">
			<label for="login-password-confirm">Login Password Confirm</label>
			<input type="text" id="login-password-confirm" />
		</div>
		<div id="section-2-error" class="error"></div>
		<button class="button" id="section-2-button">Install</button>
	</div>
	<div id="section-3" class="section">
		<div class="section-caption" style="text-align:center">Congratulations! Installation is completed!</div>
		<div style="text-align:center"><a href="../admin/">Proceed to Admin Section</div>
	</div>
</div>


<script type="text/javascript">

$(document).ready(function() {
	$("#section-1-button").on('click', function() {
		$("#section-1").hide();
		$("#section-2").show();
	});

	$("#section-2-button").on('click', function() {
		if($(this).attr('data-in-progress') == 1)
			return;

		$("#section-2-error").hide();
		
		var email_reg_exp = /^([a-zA-z0-9]{1,}(?:([\._-]{0,1}[a-zA-Z0-9]{1,}))+@{1}([a-zA-Z0-9-]{2,}(?:([\.]{1}[a-zA-Z]{2,}))+))$/,
			blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/;

		if(!blank_reg_exp.test($("#server-name").val())) {
			$("#section-2-error").html('Error : No Server Name Given').show();
			return;
		}
		
		if(!blank_reg_exp.test($("#database-name").val())) {
			$("#section-2-error").html('Error : No Database Name Given').show();
			return;
		}
		
		if(!blank_reg_exp.test($("#mysql-username").val())) {
			$("#section-2-error").html('Error : No MySql Username Given').show();
			return;
		}

		if(!email_reg_exp.test($("#login-email").val())) {
			$("#section-2-error").html('Error : Wrong Format of Login Email').show();
			return;
		}

		if(!blank_reg_exp.test($("#login-password").val())) {
			$("#section-2-error").html('Error : No Admin Password Given').show();
			return;
		}

		if($.trim($("#login-password").val()) != $.trim($("#login-password-confirm").val())) {
			$("#section-2-error").html('Error : Both Passwords Do Not Match').show();
			return;
		}
		
		$("#section-2-button").addClass('button-inactive').attr('data-in-progress', 1);
		$.ajax ({
			url: 'index.php',
			type: 'POST',
			data: { command: 'CreateDBTables', server_name: $.trim($("#server-name").val()), database_name: $.trim($("#database-name").val()), mysql_username: $.trim($("#mysql-username").val()), mysql_password: $.trim($("#mysql-password").val()), login_email: $.trim($("#login-email").val()), login_password: $.trim($("#login-password").val()) },
			cache: false,
			success: function(response) {
				if(response.error == 1) {
					$("#section-2-error").html(response.message).show();
					$("#section-2-button").removeClass('button-inactive').attr('data-in-progress', 0);
				}
				else {
					$("#section-2").hide();
					$("#section-3").show();
					if(response.cron_setup == 0)
						$("#section-3").find('.section-caption').append('<br><br><span style="font-size:14px;color:red">CRON Jobs have not been setup. Please refer documentation to setup CRON Jobs manually</span>');
				}
			}
		});
	});
});

</script>

</body>
</html>