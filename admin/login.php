<?php
session_start();

require_once('../settings/settings-2.php');

if(isset($_SESSION['admin'])) {
	header('Location: ./');
	exit();
}
else {
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$check_login = (LOGIN_EMAIL == $_POST['username'] && LOGIN_PASSWORD_MD5 == md5($_POST['password']));
		if($check_login) {
			$_SESSION['admin'] = 1;
			header('Location: ./');
			exit();
		}
		else {
			header('Location: login.php?error');
			exit();
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Quizzio Admin Login - c-o-d-e-l-i-s-t-.-c-c</title>
<style type="text/css">

body {
	font-family: Arial;
	height: 100%;
	margin: 0;
	padding: 0;
	margin: 0 auto;
	position: relative;
	background-color: #f8f8f8;
}

#logo {
	display: block;
	margin: 0 0 20px 0;
}

#login-form {
	width: 400px;
	margin: 200px auto 0 auto;
}

#error {
	text-align: center;
	color: red;
	font-size: 14px;
	margin: 0 0 25px 0;
}

.form-element {
	overflow: hidden;
	margin: 0 0 15px 0;
	font-size: 14px;
}

label {
	width: 150px;
	float: left;
}

.form-element input {
	padding: 5px;
	font-size: 14px;
	font-family: inherit;
	border: 1px solid #cccccc;
	width: 250px;
	float: left;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
}

input[type="submit"] {
	width: 80px;
	background-color: #999999;
	color: white;
	padding: 3px;
	font-size: 14px;
	font-family: inherit;
	border: 1px solid #999999;
	cursor: pointer;
}

</style>
</head>

<body>

<form id="login-form" action="login.php" method="post">
	<div id="error"><?php echo (isset($_GET['error']) ? 'Error : Username / Password incorrect' : ''); ?></div>
	<div class="form-element">
		<label>Email</label>
		<input type="text" name="username" />
	</div>
	<div class="form-element">
		<label>Password</label>
		<input type="password" name="password" />
	</div>
	<input type="submit" value="Login" />
</form>

<script type="text/javascript">

document.getElementsByName("username")[0].focus();

</script>

</body>
</html>