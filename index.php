<?php

	require_once "./include/config.php";
	require_once "./include/inursql.php";

	$db = new inursql();
	$c = $db->connect($hostname, $username, $password, $database);

	include "./templates/header.php";

	$sql = "SELECT title,note FROM posts WHERE neighbr = 'neighbrhood' ORDER BY timestamp DESC LIMIT 0,1";
	$result = $db->query($sql);

	echo "\t\t<div id=\"bulletin\"><div style=\"padding: 10px;\">\r\n";
	while($bulletin = $db->grab($result)) {
		echo "\t\t\t<div style=\"font-weight: bold; margin-bottom: 5px;\"><a href=\"/neighbrhood/\">$bulletin[title]</a></div>\r\n";
		echo "\t\t\t<div style=\"color: #979797;\">$bulletin[note]</div>\r\n";
	}
	echo "\t\t</div></div>\r\n";

	// mysql_free_result($result);
	
	if(isset($_SESSION['username'])) {
		$sql = "SELECT * FROM users WHERE username = '" . $_SESSION['username'] . "'";
		$result = $db->query($sql);
		while($neighbr = $db->grab($result)) {
			$permissions = $neighbr['permissions'];
		}
	} else {
		$permissions = "guest";
	}

?>
		<div style="padding: 10px;">
<?php

	if(isset($_GET['pg'])) {
		if(!is_numeric($_GET['pg'])) { header("Location: {$_SERVER['HTTP_HOST']}"); }
		$pg = $_GET['pg'];
	} else {
		$pg = 1;
	}



	$sql = "SELECT * FROM posts WHERE objectionable = 0";
	$result = $db->query($sql);
	$numrows = mysql_num_rows($result);

	// echo $numrows;

	$perPage = 15;
	$limit = ' LIMIT '.($pg - 1)*$perPage.', '.$perPage;
	$lastpage = ceil($numrows/$perPage);

	if($pg < 1) { $pg = 1; }
	if($pg > $lastpage) { $pg = $lastpage; }

	// echo $pg;

	if($pg==1) {
		$pages .= '<div class="pages">&laquo;</div><div class="pages">&lsaquo;</div>';
	} else {
		$pages .= "<div class=\"pages\"><a href=\"http://yoursite.com/\">&laquo;</a></div>";
		$prevpage=$pg-1;
		$pages .= "<div class=\"pages\"><a href=\"http://yoursite.com/$prevpage\">&lsaquo;</a></div>";
	}
	$pages .= '<div class="pages">' . $pg . '</div>';
	if($pg==$lastpage) {
		$pages .= '<div class="pages">&rsaquo;</div><div class="pages">&raquo;</div>';
	} else {
		$nextpage = $pg+1;
		$pages .= "<div class=\"pages\"><a href=\"http://yoursite.com/$nextpage\">&rsaquo;</a></div>";
		$pages .= "<div class=\"pages\"><a href=\"http://yoursite.com/$lastpage\">&raquo;</a></div>";
	}

	$sql = "SELECT id, neighbr, type, source, title, note, timestamp FROM posts WHERE objectionable = 0 ORDER BY timestamp DESC" . $limit;
	$result = $db->query($sql);

	while($post = $db->grab($result)) {
		$socialm  = "";
		$socialm .= "<div style=\"text-align: right; margin-top: 10px;\">";
		$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://www.facebook.com/sharer.php?u=http://yoursite.com/view/" . $post['id'] . "/" . str_replace(' ', '-', trim($post['title'])) . "&t=" . $post['title'] . "\" title=\"Share on Facebook\" target=\"_blank\"><img src=\"./img/icons/facebook.png\" alt=\"Share on Facebook\" /></a></div>";
		$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://www.reddit.com/submit?url=http://yoursite.com/view/" . $post['id'] . "&title=" . $post['title'] . "\" title=\"Share on Reddit\" target=\"_blank\"><img src=\"./img/icons/reddit.png\" alt=\"Share on Reddit\" /></a></div>";
		$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://www.stumbleupon.com/submit?url=http://yoursite.com/view/" . $post['id'] . "&title=" . $post['title'] . "\" title=\"Share on StumbleUpon\" target=\"_blank\"><img src=\"./img/icons/stumble.png\" alt=\"Share on StumbleUpon\" /></a></div>";
		$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://twitter.com/home/?status=" . $post['title'] . " - http://yoursite.com/view/" . $post['id'] . "\" title=\"Share on Twitter\" target=\"_blank\"><img src=\"./img/icons/twitter.png\" alt=\"Share on Twitter\" /></a></div>";
		$socialm .= "</div>";
		if(isset($_SESSION['username'])) {
			
			if($permissions == 'administrator') {
				
				$options = "<a href=\"delete.php?id=" . $post['id'] . "\" style=\"margin-left: 5px; background: #3a3a3a; width: 15px; display: block; float: right; text-align: center; line-height: 15px; font-size: 8px;\">X</a><a href=\"#\" style=\"margin-left: 5px; background: #3a3a3a; width: 15px; display: block; float: right; text-align: center; line-height: 15px; font-size: 8px;\">&hearts;</a>";
				
				// $options = "<a href=\"#\" style=\"margin-left: 5px; background: #3a3a3a; width: 15px; display: block; float: right; text-align: center; line-height: 15px; font-size: 8px;\">&hearts;</a><a href=\"#\" style=\"margin-left: 5px; background: #3a3a3a; width: 15px; display: block; float: right; text-align: center;\">&uarr;</a> <a href=\"#\" style=\"background: #3a3a3a; width: 15px; display: block; float: right; text-align: center;\">&darr;</a>";
				
			} else {
				
				$options = "<a href=\"#\" style=\"margin-left: 5px; background: #3a3a3a; width: 15px; display: block; float: right; text-align: center; line-height: 15px; font-size: 8px;\">&hearts;</a>";
				
			}
			
		} else {
			
			$options = "";
			
		}
		
		if(file_exists("./img/avatars/$post[neighbr].jpg")) {
			$avatar = "<img src=\"./img/avatars/$post[neighbr].jpg\" alt=\"neighbr: $post[neighbr]\" style=\"width: 50px; height: 50px; border: 0px;\" />";
		} else {
			$avatar = "<img src=\"./img/avatar.jpg\" alt=\"neighbr: $post[neighbr]\" style=\"width: 50px; height: 50px; border: 0px;\" />";
		}
		
		if($post['type'] == 'image') {
			list($width, $height, $type, $attr) = getimagesize("./images/$post[title]");
			if($width > 490) {
				$div = "<div style=\"padding: 0px; text-align: center;\">";
				$image = "<a href=\"$post[source]\" title=\"$post[title]\"><img src=\"$post[source]\" alt=\"$post[title]\" style=\"width: 500px; border: 0px;\" /></a>";
			} else {
				$padding = ceil((500 - $width) / 2);
				$div = "<div style=\"padding: " . $padding . "px; text-align: center;\">";
				$image = "<img src=\"$post[source]\" alt=\"$post[title]\" />";
			}

			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><div style=\"float: right;\">$options</div><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\" style=\"position: relative;\">\r\n";
			echo "\t\t\t\t\t$div\r\n";
			echo "\t\t\t\t\t\t$image\r\n";
			echo "\t\t\t\t\t\t<div style=\"padding: 5px; text-align: left;\">$post[note]</div>\r\n";
			echo "\t\t\t\t\t</div>\r\n";
			echo "\t\t\t\t</div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($post['type'] == 'video') {
			$text = nl2br($post['note']);
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><div style=\"float: right;\">$options</div><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			// old YouTube embed
			// echo "\t\t\t\t<div class=\"post-content\"><object width=\"500\" height=\"303\"><param value=\"http://www.youtube.com/v/$post[source]\" name=\"movie\" /><param value=\"window\" name=\"wmode\" /><param value=\"true\" name=\"allowFullScreen\" /><embed width=\"500\" height=\"303\" wmode=\"window\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" src=\"http://www.youtube.com/v/$post[source]\"></embed></object>\r\n<div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><iframe title=\"YouTube Video Player\" width=\"500\" height=\"311\" src=\"http://www.youtube.com/embed/{$post['source']}\" frameborder=\"0\" allowfullscreen></iframe><div style=\"padding: 5px;\">{$text}</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($post['type'] == 'code') {
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">\r\n";
			echo "<pre><code>$post[note]</code></pre>\r\n";
			echo "\t\t\t\t</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($post['type'] == 'link') {
			if(strlen($post[note]) > 65) {
				$note = substr($post[note], 0, 65) . " ...";
			} else {
				$note = $post[note];
			}
			$linkcolor = $colors[rand(0,4)];
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><div style=\"float: right;\">$options</div><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\"><a href=\"$post[note]\" target=\"_blank\" class=\"$linkcolor\">$note</a></div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} else {
			$text = nl2br($post['note']);
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]/" . str_replace(' ', '-', trim($post['title'])) . "\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		}
	}

	// mysql_free_result($result);

?>
				</div>
			</div>
			<div style="clear: both; text-align: right; padding-right: 15px; padding-bottom: 15px;"><?php echo $pages;?></div>
		</div>
<?php include "./templates/footer.php"; ?>