<?php

	session_start();
	
	if(isset($_GET['id']) && is_numeric($_GET['id'])) {
	
		if(isset($_SESSION['username']) && $_SESSION['username'] == 'darthnuri') {
			
			require_once "./include/config.php";
			require_once "./include/inursql.php";
		
			$db = new inursql();
			$c = $db->connect($hostname, $username, $password, $database);
			
			$sql = "DELETE FROM posts WHERE id = " . $_GET['id'];
			$result = $db->query($sql);
			
			if($result) {
				
				header("Location: " . $_SERVER['HTTP_REFERER']);
				
			}
			
		} else {
			
			header("Location: " . $_SERVER['HTTP_REFERER']);
			
		}
	
	} else {
		
		header("Location: " . $_SERVER['HTTP_REFERER']);
		
	}

?>