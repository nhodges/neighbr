<?php

	require_once "./include/config.php";
	require_once "./include/inursql.php";
	session_start();

	if(!isset($_SESSION['loggedin']) OR $_SESSION['loggedin'] == FALSE) { header("Location: http://yoursite.com"); }

	$db = new inursql();
	$c = $db->connect($hostname, $username, $password, $database);
	$_GET = sanitize($_GET);
	$_POST = sanitize($_POST);

	$insertid = mysql_insert_id();

	switch($_GET['type']) {
		case "text";
			$text = strip_tags($_POST[texttext]);
			if(!$_POST['texttitle'] OR !$_POST['texttext']) { die(header("Location: http://yoursite.com/")); }
			$sql = "INSERT INTO posts (neighbr, type, title, note) VALUES ('$_SESSION[username]', 'text', '$_POST[texttitle]', '$text')";
			$result = $db->query($sql);
			echo ($result) ? header("Location: http://yoursite.com/$_SESSION[username]/") : "We couldn't share your note! :(";
			break;
		case "image";
			if(!$_POST['imagetext'] or !$_FILES['imagefile']['name']) { die(header("Location: http://yoursite.com/")); }
			$name = $_FILES['imagefile']['name'];
			$source = "http://yoursite.com/images/$name";
			if(is_uploaded_file($_FILES['imagefile']['tmp_name'])) {
				$tmp = $_FILES['imagefile']['tmp_name'];
				$fp = fopen($tmp, 'r');				
				$imgdata = addslashes(fread($fp, filesize($tmp)));
				fclose($fp);
 
				// get the image info..
				$info = getimagesize($_FILES['imagefile']['tmp_name']);
				$mime = $info['mime'];

				if(file_exists("./images/$name")) { $name = $name . "-" . rand(0,1000) + rand(0,1000); }

				if(move_uploaded_file($tmp, "images/$name")) {
					$sql = "INSERT INTO posts (neighbr, type, source, title, note) VALUES ('$_SESSION[username]', 'image', 'http://yoursite.com/images/$name', '$name', '$_POST[imagetext]')";  
					$result = $db->query($sql);
					echo ($result) ? header("Location: http://yoursite.com/$_SESSION[username]/") : "We couldn't share your image! :(";
				} else {
					echo "We couldn't share your image! :(";
				}

				// our sql query
				/*
				$sql = "INSERT INTO images VALUES (NULL, '$_SESSION[username]', '$imgdata', '$name', '$mime')";
				if($db->query($sql)) {
					$sql = "INSERT INTO posts (neighbr, type, source, title, note) VALUES ('$_SESSION[username]', 'image', 'http://yoursite.com/images/$name', '$name', '$_POST[imagetext]')";  
					$result = $db->query($sql);
					echo ($result) ? header("Location: http://yoursite.com/$_SESSION[username]/") : "We couldn't share your image! :(";
				} else {
					echo "We couldn't share your image! :(";
				}
				*/			
			} else {
				die(header("Location: http://yoursite.com/"));
			}
			break;
		case "video";
			if(!$_POST['videosource']) { die(header("Location: http://yoursite.com/")); } else { require_once "./include/inurtube.php"; }
			$videotext = strip_tags($_POST[videotext]);
			$videosource = $_POST['videosource'];
			if(substr($videosource, 0, 4) == 'http') {
				if(preg_match('/youtube\.com\/(v\/|watch\?v=)([\w\-]+)/', $videosource, $match)) {
					$youtubeid = $match[2];
					$youtubeurl = "http://gdata.youtube.com/feeds/api/videos/$youtubeid";
				} else {
					die("We couldn't seem to get the YouTube ID from that link ... care to try again?");
				}
			} else {
				$youtubeid = $videosource;
				$youtubeurl = "http://gdata.youtube.com/feeds/api/videos/$youtubeid";
			}
			if(file_get_contents($youtubeurl) == 'Invalid id') { die("It seems you slipped us a curious YouTube ID? Why don't you try to copy and paste the full URL?"); }
			$youtubehandle = simplexml_load_file($youtubeurl);
			$youtubedata = parseVideoEntry($youtubehandle);
			$videotitle = sanitize($youtubedata->title);
			if(!$_POST['videotext']) { $videotext = sanitize($youtubedata->description); }
			$sql = "INSERT INTO posts (neighbr, type, title, note, source) VALUES ('$_SESSION[username]', 'video', '$videotitle', '$videotext', '$youtubeid')";
			$result = $db->query($sql);
			echo ($result) ? header("Location: http://yoursite.com/$_SESSION[username]/") : "We couldn't share your video! :(";
			break;
		case "link";
			$link = strip_tags($_POST[linktext]);
			if(substr($link, 0, 4) != 'http') { die(header("Location: http://yoursite.com/")); }
			if(!$_POST['linktitle'] OR !$_POST['linktext']) { die(header("Location: http://yoursite.com/")); }
			$sql = "INSERT INTO posts (neighbr, type, title, note) VALUES ('$_SESSION[username]', 'link', '$_POST[linktitle]', '$link')";
			$result = $db->query($sql);
			echo ($result) ? header("Location: http://yoursite.com/$_SESSION[username]/") : "We couldn't share your link! :(";
			break;
		case "code";
			$code = str_replace(array("<?php", "<?", "?>"), "", $_POST['codetext']);
			if(!$_POST['codetitle'] OR !$_POST['codetext']) { die(header("Location: http://yoursite.com/")); }
			$sql = "INSERT INTO posts (neighbr, type, title, note) VALUES ('$_SESSION[username]', 'code', '$_POST[codetitle]', '$code')";
			$result = $db->query($sql);
			echo ($result) ? header("Location: http://yoursite.com/$_SESSION[username]/") : "We couldn't share your code! :(";
			break;
		case "comment";
			$comment = strip_tags($_POST[commenttext]);
			if(!$_POST['commenttitle'] OR !$_POST['commenttext']) { die(header("Location: http://yoursite.com/view/$_POST[postid]")); }
			$sql = "INSERT INTO comments (neighbr, post, title, note) VALUES ('$_SESSION[username]', $_POST[postid], '$_POST[commenttitle]', '$comment')";
			$result = $db->query($sql);
			echo ($result) ? header("Location: http://yoursite.com/view/$_POST[postid]") : "We couldn't share your note! :(";
			break;
		default;
			header("Location: http://yoursite.com/");
	}

?>