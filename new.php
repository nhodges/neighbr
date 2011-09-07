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

?>
		<div style="padding: 10px;">
<?php

	if(isset($_GET['pg'])) {
		if(!is_numeric($_GET['pg'])) { header("Location: http://neighbr.net/"); }
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
		$pages .= "<div class=\"pages\"><a href=\"http://neighbr.net/\">&laquo;</a></div>";
		$prevpage=$pg-1;
		$pages .= "<div class=\"pages\"><a href=\"http://neighbr.net/$prevpage\">&lsaquo;</a></div>";
	}
	$pages .= '<div class="pages">' . $pg . '</div>';
	if($pg==$lastpage) {
		$pages .= '<div class="pages">&rsaquo;</div><div class="pages">&raquo;</div>';
	} else {
		$nextpage = $pg+1;
		$pages .= "<div class=\"pages\"><a href=\"http://neighbr.net/$nextpage\">&rsaquo;</a></div>";
		$pages .= "<div class=\"pages\"><a href=\"http://neighbr.net/$lastpage\">&raquo;</a></div>";
	}

	$sql = "SELECT id, neighbr, type, source, title, note FROM posts WHERE objectionable = 0 ORDER BY id DESC" . $limit;
	$result = $db->query($sql);

	while($post = $db->grab($result)) {
		if(file_exists("./img/avatars/$post[neighbr].jpg")) {
			$avatar = "<img src=\"./img/avatars/$post[neighbr].jpg\" alt=\"neighbr: $post[neighbr]\" style=\"width: 50px; height: 50px; border: 0px;\" />";
		} else {
			$avatar = "<img src=\"./img/avatar.jpg\" alt=\"neighbr: $post[neighbr]\" style=\"width: 50px; height: 50px; border: 0px;\" />";
		}
		if($post['type'] == 'image') {
			list($width, $height, $type, $attr) = getimagesize("./images/$post[title]");
			if($width > 490) {
				$div = "<div style=\"padding: 5px; text-align: center;\">";
				$image = "<a href=\"$post[source]\" title=\"$post[title]\"><img src=\"$post[source]\" alt=\"$post[title]\" style=\"width: 490px; border: 0px;\" /></a>";
			} else {
				$padding = ceil((500 - $width) / 2);
				$div = "<div style=\"padding: " . $padding . "px; text-align: center;\">";
				$image = "<img src=\"$post[source]\" alt=\"$post[title]\" />";
			}

			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a></div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\">\r\n";
			echo "\t\t\t\t\t$div\r\n";
			echo "\t\t\t\t\t\t$image\r\n";
			echo "\t\t\t\t\t\t<div>$post[note]</div>\r\n";
			echo "\t\t\t\t\t</div>\r\n";
			echo "\t\t\t\t</div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($post['type'] == 'video') {
			$text = nl2br($post['note']);
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a></div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><object width=\"500\" height=\"303\"><param value=\"http://www.youtube.com/v/$post[source]\" name=\"movie\" /><param value=\"window\" name=\"wmode\" /><param value=\"true\" name=\"allowFullScreen\" /><embed width=\"500\" height=\"303\" wmode=\"window\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" src=\"http://www.youtube.com/v/$post[source]\"></embed></object>\r\n<div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($post['type'] == 'code') {
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a></div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
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
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a></div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\"><a href=\"$post[note]\" target=\"_blank\" class=\"$linkcolor\">$note</a></div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} else {
			$text = nl2br($post['note']);
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$post[neighbr]/\">$avatar</a></div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$post[id]\" title=\"$post[title]\">$post[title]</a></div></div>\r\n";
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