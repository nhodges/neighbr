<?php

	require_once "./include/config.php";
	require_once "./include/inursql.php";

	$db = new inursql();
	$c = $db->connect($hostname, $username, $password, $database);

	if(!isset($_GET['user'])) {
		$title = "Neighbrhood Report";
		$sql = "SELECT * FROM posts WHERE objectionable = 0 ORDER BY id DESC LIMIT 0,20";
		$url = "http://yoursite.com/feed";
	} else {
		$neighbr = sanitize($_GET['user']);
		$title = "Neighbrhood Report: $neighbr";
		$sql = "SELECT * FROM posts WHERE objectionable = 0 AND neighbr = '$neighbr' ORDER BY id DESC LIMIT 0,20";
		$url = "http://yoursite.com/$neighbr/feed";
	}

	$result = $db->query($sql);

	if(mysql_num_rows($result) == 0) { die("There seems to be some sort of force field."); }

	header("Content-type: text/xml");
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n";
	echo "<rss version=\"2.0\">\r\n";
	echo "<channel>\r\n";
	echo "<title>$title</title>\r\n";
	echo "<description>What have your neighbrs been up to?</description>\r\n";
	echo "<link>$url</link>\r\n";
	echo "<language>en-us</language>\r\n";

	while($post = $db->grab($result)) {
		$pubDate = date("D, d M Y H:i:s O", strtotime($post['timestamp']));
		echo "\t<item>\r\n";
		echo "\t\t<neighbr>$post[neighbr]</neighbr>\r\n";
		if(file_exists("./img/avatars/$post[neighbr].jpg")) {
			$avatar = "http://yoursite.com/img/avatars/$post[neighbr].jpg";
		} else {
			$avatar = "http://yoursite.com/img/avatars/neighbrhood.jpg";
		}
		echo "\t\t<avatar>$avatar</avatar>\r\n";
		echo "\t\t<title>" . str_replace("&", "&amp;", $post[title]) . "</title>\r\n";
		echo "\t\t<description>" . str_replace("&", "&amp;", $post[note]) . "</description>\r\n";
		switch($post['type']) {
			case "text";
			case "code";
				echo "";
				break;
			case "link";
				echo "\t\t<source>" . str_replace("&", "&amp;", $post[note]) . "</source>\r\n";
				break;
			default;
				echo "\t\t<source>" . str_replace("&", "&amp;", $post[source]) . "</source>\r\n";
				echo "";
		}
		echo "\t\t<pubDate>$pubDate</pubDate>\r\n";
		echo "\t\t<guid>http://yoursite.com/view/$post[id]</guid>\r\n";
		echo "\t\t<link>http://yoursite.com/view/$post[id]</link>\r\n";
		echo "\t</item>\r\n";
	}

	echo "</channel>\r\n";
	echo "</rss>\r\n";