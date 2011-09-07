<?php

	require_once "./include/config.php";
	require_once "./include/inursql.php";

	$db = new inursql();
	$c = $db->connect($hostname, $username, $password, $database);

	$neighbr = $_GET['neighbr'];

	$pagetitle = "$neighbr / neighbr.net";
	$rssurl = "/$neighbr/feed";

	$sql = "SELECT friends FROM users WHERE username = '$neighbr'";
	$result = $db->query($sql);

	if(mysql_num_rows($result) != 0) {
		$friends = explode(",", mysql_result($result, 0));
	} else {
		header("Location: http://neighbr.net/");
	}

	include "./templates/header.php";
	echo "\r\n";

	if($_SESSION) {
		$sql = "SELECT friends FROM users WHERE username = '$_SESSION[username]'";
		$result = $db->query($sql);
		$neighbrs = explode(",", mysql_result($result, 0));
	} else {
		$neighbrs = array();
	}

	if(file_exists("./img/avatars/$neighbr.jpg")) {
		$avatar = "<img src=\"./img/avatars/$neighbr.jpg\" alt=\"neighbr: $neighbr\" style=\"width: 50px; height: 50px; border: 0px;\" />";
	} else {
		$avatar = "<img src=\"./img/avatar.jpg\" alt=\"neighbr: $neighbr\" style=\"width: 50px; height: 50px; border: 0px;\" />";
	}

?>
		<div id="bulletin"><div style="padding: 10px 10px 0px 10px;">
			<div style="width: 50px; float: left;"><?php echo $avatar; ?></div>
			<div style="width: 490px; float: right;">
				<div style="font-weight: bold;">
<?php

	if(in_array($neighbr, $neighbrs) == TRUE OR $_SESSION['username'] == $neighbr or !$_SESSION) {
		echo "";
	} else {
		echo "\t\t\t\t\t<div style=\"float: right;\"><a href=\"./add/$neighbr\" class=\"grey\">won't you be my neighbr?</a></div>\r\n";
	}

?>
				<?php echo $neighbr ?>
				</div>
				<div></div>
			</div>
			<div style="clear: both; position: relative; left: -5px; padding-top: 10px;">
				<table width="100%" cellspacing="5" cellpadding="0" border="0">
				<tr>
<?php

	if(count($friends) < 15) {
		foreach($friends as $friend) {
			if(file_exists("./img/avatars/$friend.jpg")) {
				$friendav = $friend;
			} else {
				$friendav = "neighbrhood";
			}
			echo "\t\t\t\t\t<td><a href=\"./$friend/\"><img src=\"./img/avatars/$friendav.jpg\" alt=\"neighbr: $friend\" style=\"width: 25px; height: 25px; border: 0px;\" /></a></td>\r\n";
		}
		echo "\t\t\t\t\t<td width=\"99%\" valign=\"top\" style=\"padding-left: 5px; font-size: 12pt; font-weight: bold;\">&raquo;</td>\r\n";
	} else {
		foreach($friends as $friend) {
			echo "\t\t\t\t\t<td><a href=\"./$friend/\"><img src=\"./img/avatars/$friend.jpg\" alt=\"neighbr: $friend\" style=\"width: 25px; height: 25px; border: 0px;\" /></a></td>\r\n";
		}
		echo "\t\t\t\t\t<td valign=\"top\" style=\"padding-left: 5px; font-size: 12pt; font-weight: bold;\">&raquo;</td>\r\n";
	}
?>
				</tr>
				</table>
			</div>
		</div></div>
		<div style="padding: 10px;">
<?php

	$sql = "SELECT * FROM posts WHERE neighbr = '$neighbr' ORDER BY id DESC LIMIT 0,10";
	$result = $db->query($sql);

	while($post = $db->grab($result)) {
		if($post['type'] == 'image') {
			list($width, $height, $type, $attr) = getimagesize("./images/$post[title]");
			if($width > 550) {
				$div = "<div style=\"padding: 5px; text-align: center;\">";
				$image = "<a href=\"$post[source]\" title=\"$post[title]\"><img src=\"$post[source]\" alt=\"$post[title]\" style=\"width: 550px; border: 0px;\" /></a>";
			} else {
				$padding = ceil((560 - $width) / 2);
				$div = "<div style=\"padding: " . $padding . "px; text-align: center;\">";
				$image = "<img src=\"$post[source]\" alt=\"$post[title]\" />";
			}

			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\">\r\n";
			echo "\t\t\t\t\t$div\r\n";
			echo "\t\t\t\t\t\t$image\r\n";
			echo "\t\t\t\t\t\t<div>$post[note]</div>\r\n";
			echo "\t\t\t\t\t</div>\r\n";
			echo "\t\t\t\t</div>\r\n";
			echo "\t\t\t</div><br />\r\n";
		} elseif($post['type'] == 'video') {
			$text = nl2br($post['note']);
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			// old YouTube embed
			// echo "\t\t\t\t<div class=\"post-content\"><object width=\"560\" height=\"339\"><param value=\"http://www.youtube.com/v/$post[source]\" name=\"movie\" /><param value=\"window\" name=\"wmode\" /><param value=\"true\" name=\"allowFullScreen\" /><embed width=\"560\" height=\"339\" wmode=\"window\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" src=\"http://www.youtube.com/v/$post[source]\"></embed></object>\r\n<div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><iframe title=\"YouTube video player\" width=\"560\" height=\"345\" src=\"http://www.youtube.com/embed/{$post['source']}\" frameborder=\"0\" allowfullscreen></iframe><div style=\"padding: 5px;\">{$text}</div></div>\r\n";
			echo "\t\t\t</div><br />\r\n";
		} elseif($post['type'] == 'code') {
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">\r\n";
			echo "<pre><code>$post[note]</code></pre>\r\n";
			echo "\t\t\t\t</div></div>\r\n";
			echo "\t\t\t</div><br />\r\n";
		} elseif($post['type'] == 'link') {
			$linkcolor = $colors[rand(0,4)];
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\"><a href=\"$post[note]\" target=\"_blank\" class=\"$linkcolor\">$post[note]</a></div></div>\r\n";
			echo "\t\t\t</div><br />\r\n";
		} else {
			$text = nl2br($post['note']);
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t</div><br />\r\n";
		}
	}

?>
		</div>
<?php include "./templates/footer.php"; ?>