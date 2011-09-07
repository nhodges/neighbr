<?php

	require_once "./include/config.php";
	require_once "./include/inursql.php";
	session_start();

	$db = new inursql();
	$c = $db->connect($hostname, $username, $password, $database);

	$_GET = sanitize($_GET);
	$_POST = sanitize($_POST);

	if(isset($_GET['do']) && $_GET['do'] == 'login') {
		if(empty($_POST['username']) OR empty($_POST['password'])) {
			header("Location: http://yoursite.com/");
		} else {
			$username = $_POST['username'];
			$pass = md5($_POST['password']);
			$sql = "SELECT password FROM users WHERE username = '$username'";
			$result = $db->query($sql);

			if(mysql_result($result, 0) != $pass) {
				header("Location: http://yoursite.com/");
			} else {
				$sql = "SELECT * FROM users WHERE username = '$username'";
				$result = $db->query($sql);
				while($user = $db->grab($result)) {
					$_SESSION['loggedin'] = TRUE;
					$_SESSION['username'] = $user['username'];
					$_SESSION['useridno'] = $user['id'];
				}
				header("Location: http://yoursite.com/");
			}
		}
	}

	if(isset($_GET['do']) && $_GET['do'] == 'logout') {
		unset($_SESSION['loggedin']);
		unset($_SESSION['username']);
		header("Location: http://yoursite.com/");
	}

	if(isset($_GET['do']) && $_GET['do'] == 'register') {
		$username = $_POST['r_username'];
		$password = md5($_POST['r_password']);
		$sql = "SELECT * FROM users WHERE username = '$username'";
		$result = $db->query($sql);

		if(mysql_num_rows($result) == 0) {
			$sql = "INSERT INTO users (email, username, password, permissions) VALUES ('$_POST[email]', '$username', '$password', 'guest')";
			$result = $db->query($sql);
			if($result) {
				$sql = "SELECT * FROM users WHERE username = '$username'";
				$result = $db->query($sql);
				while($user = $db->grab($result)) {
					$_SESSION['loggedin'] = TRUE;
					$_SESSION['username'] = $username;
					$_SESSION['useridno'] = $user['id'];
				}
				header("Location: http://yoursite.com/");
			} else {
				header("Location: http://yoursite.com/");
			}
		} else {
			header("Location: http://yoursite.com/");
		}
	}

	if(isset($_GET['befriend'])) {
		$sql = "SELECT friends FROM users WHERE username = '$_SESSION[username]'";
		$result = $db->query($sql);
		$friends = mysql_result($result, 0);
		if(in_array($_GET[befriend], explode(",", $friends))) {
			header("Location: http://yoursite.com/$_GET[befriend]/");
		} else {
			$friends = $friends . ",$_GET[befriend]";
			$sql = "UPDATE users SET friends='$friends' WHERE username = '$_SESSION[username]'";
			$result = $db->query($sql);
			echo ($result) ? header("Location: http://yoursite.com/$_GET[befriend]/") : "Unable to befriend this neighbr :(";
		}
	}

?>