<?php

	require_once "./include/config.php";
	require_once "./include/inursql.php";

	$db = new inursql();
	$c = $db->connect($hostname, $username, $password, $database);

	$id = sanitize($_GET['id']);

	$sql = "SELECT neighbr, type, source, title, note, timestamp FROM posts WHERE id = $id";
	$result = $db->query($sql);

	while($post = $db->grab($result)) {
		$timestamp = "<div style=\"float: right;\">" . date("M d Y", strtotime($post['timestamp'])) . "</div>";
		$pagetitle = $post['title'] . " / " . $post['neighbr'] . " / yoursite.com";
		$posttitle = $post['title'];
		if(!isset($_GET['title'])) {
			header('Location: /view/' . $id . '/' . str_replace(' ', '-', trim($post['title']))); exit;
		}
		$neighbr = $post['neighbr'];
		if($post['type'] != 'text') {
			$type = $post['type'];
			$link = $post['source'];
			$note = $post['note'];
		} else {
			$type = $post['type'];
			$note = $post['note'];
		}
	}

	$socialm  = "";
	$socialm .= "<div style=\"text-align: right; margin-top: 10px;\">";
	$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://www.facebook.com/sharer.php?u=http://yoursite.com/view/" . $id . "/" . str_replace(' ', '-', trim($posttitle)) . "&t=" . $posttitle . "\" title=\"Share on Facebook\" target=\"_blank\"><img src=\"./img/icons/facebook.png\" alt=\"Share on Facebook\" /></a></div>";
	$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://www.reddit.com/submit?url=http://yoursite.com/view/" . $id . "&title=" . $posttitle . "\" title=\"Share on Reddit\" target=\"_blank\"><img src=\"./img/icons/reddit.png\" alt=\"Share on Reddit\" /></a></div>";
	$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://www.stumbleupon.com/submit?url=http://yoursite.com/view/" . $id . "&title=" . $posttitle . "\" title=\"Share on StumbleUpon\" target=\"_blank\"><img src=\"./img/icons/stumble.png\" alt=\"Share on StumbleUpon\" /></a></div>";
	$socialm .= "<div style=\"margin-top: 5px;\"><a href=\"http://twitter.com/home/?status=" . $posttitle . " - http://yoursite.com/view/" . $id . "\" title=\"Share on Twitter\" target=\"_blank\"><img src=\"./img/icons/twitter.png\" alt=\"Share on Twitter\" /></a></div>";
	$socialm .= "</div>";

	$rssurl = "/$neighbr/feed";

	include "./templates/header.php";
?>

		<div style="padding: 10px;">
<?php

		if(file_exists("./img/avatars/$neighbr.jpg")) {
			$avatar = "<img src=\"./img/avatars/$neighbr.jpg\" alt=\"NEIGHBR: $neighbr\" style=\"width: 50px; height: 50px; border: 0px;\" />";
		} else {
			$avatar = "<img src=\"./img/avatar.jpg\" alt=\"NEIGHBR: $neighbr\" style=\"width: 50px; height: 50px; border: 0px;\" />";
		}
		if($type == 'image') {
			list($width, $height, $type, $attr) = getimagesize("./images/$posttitle");
			if($width > 490) {
				$div = "<div style=\"padding: 0px; text-align: center;\">";
				$image = "<a href=\"$link\" title=\"$posttitle\"><img src=\"$link\" alt=\"$posttitle\" style=\"width: 500px; border: 0px;\" /></a>";
			} else {
				$padding = ceil((500 - $width) / 2);
				$div = "<div style=\"padding: " . $padding . "px; text-align: center;\">";
				$image = "<img src=\"$link\" alt=\"$posttitle\" />";
			}

			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"http://yoursite.com/$neighbr/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\">$timestamp<a href=\"./view/$id/" . str_replace(' ', '-', trim($posttitle)) . "\" title=\"$posttitle\">$posttitle</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\">\r\n";
			echo "\t\t\t\t\t$div\r\n";
			echo "\t\t\t\t\t\t$image\r\n";
			echo "\t\t\t\t\t\t<div style=\"padding: 5px; text-align: left;\">$note</div>\r\n";
			echo "\t\t\t\t\t</div>\r\n";
			echo "\t\t\t\t</div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($type == 'video') {
			$text = nl2br($note);
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"./$neighbr/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\"><a href=\"./view/$id/" . str_replace(' ', '-', trim($posttitle)) . "\" title=\"$posttitle\">$posttitle</a></div></div>\r\n";
			// old YouTube embed
			// echo "\t\t\t\t<div class=\"post-content\"><object width=\"500\" height=\"303\"><param value=\"http://www.youtube.com/v/$link\" name=\"movie\" /><param value=\"window\" name=\"wmode\" /><param value=\"true\" name=\"allowFullScreen\" /><embed width=\"500\" height=\"303\" wmode=\"window\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" src=\"http://www.youtube.com/v/$link\"></embed></object>\r\n<div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><iframe title=\"YouTube Video Player\" width=\"500\" height=\"311\" src=\"http://www.youtube.com/embed/{$link}\" frameborder=\"0\" allowfullscreen></iframe><div style=\"padding: 5px;\">{$text}</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($type == 'code') {
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"http://yoursite.com/$neighbr/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\">$timestamp<a href=\"./view/$id/" . str_replace(' ', '-', trim($posttitle)) . "\" title=\"$posttitle\">$posttitle</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">\r\n";
			echo "<pre><code>$note</code></pre>\r\n";
			echo "\t\t\t\t</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} elseif($type == 'link') {
			$linkcolor = $colors[rand(0,4)];
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"http://yoursite.com/$post[neighbr]/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\">$timestamp<a href=\"./view/$id/" . str_replace(' ', '-', trim($posttitle)) . "\" title=\"$posttitle\">$posttitle</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\"><a href=\"$note\" target=\"_blank\" class=\"$linkcolor\">$note</a></div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		} else {
			$text = nl2br($note);
			echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"http://yoursite.com/$neighbr/\">$avatar</a>$socialm</div>\r\n";
			echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
			echo "\t\t\t<div>\r\n";
			echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\">$timestamp<a href=\"./view/$id/" . str_replace(' ', '-', trim($posttitle)) . "\" title=\"$posttitle\">$posttitle</a></div></div>\r\n";
			echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">$text</div></div>\r\n";
			echo "\t\t\t</div>\r\n";
			echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
		}

?>

<?php

		$sql = "SELECT * FROM comments WHERE post = $id";
		$result = $db->query($sql);
		if(mysql_num_rows($result) > 0) {
			while($comment = $db->grab($result)) {
				if(file_exists("./img/avatars/$comment[neighbr].jpg")) {
					$avatar = "<img src=\"./img/avatars/$comment[neighbr].jpg\" alt=\"NEIGHBR: $comment[neighbr]\" style=\"width: 50px; height: 50px; border: 0px;\" />";
				} else {
					$avatar = "<img src=\"./img/avatar.jpg\" alt=\"NEIGHBR: $comment[neighbr]\" style=\"width: 50px; height: 50px; border: 0px;\" />";
				}
				$timestamp = "<div style=\"float: right;\">" . date("M d Y", strtotime($comment['timestamp'])) . "</div>";
				$text = nl2br($note);
				echo "\t\t\t<div style=\"float: left; width: 50px;\"><a href=\"http://yoursite.com/$comment[neighbr]/\">$avatar</a></div>\r\n";
				echo "\t\t\t<div style=\"float: right; width: 500px;\">\r\n";
				echo "\t\t\t<div>\r\n";
				echo "\t\t\t\t<div class=\"post-header\"><div style=\"padding: 5px;\">$timestamp$comment[title]</div></div>\r\n";
				echo "\t\t\t\t<div class=\"post-content\"><div style=\"padding: 5px;\">$comment[note]</div></div>\r\n";
				echo "\t\t\t</div>\r\n";
				echo "\t\t\t</div><div style=\"clear: both;\"></div><br />\r\n";
			}
		}

?>
			<div id="share-comment">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="shareform-comment" name="shareform-comment" action="share/comment" method="post">
			<input type="hidden" id="postid" name="postid" value="<?php echo $id; ?>" />
			<tr>
				<td width="100%" colspan="2">
					<div style="padding: 5px;"><input type="text" id="commenttitle" name="commenttitle" value="<?php echo "re: $posttitle"; ?>" style="width: 530px;" /></div>
					<div style="padding: 5px 5px 0px 5px;"><textarea id="commenttext" name="commenttext" rows="3" style="width: 530px; height: 50px;"></textarea></div>
				</td>
			</tr>
			<tr>
				<td width="99%" valign="middle" style="font-weight: bold; font-size: 11px;"><div style="padding-left: 5px;"><a href="javascript:document.getElementById('shareform-comment').submit();">Share It!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><div style="padding-right: 5px;"><a href="javascript:hideContainer('share-comment');">[x]</a></div></td>
			</tr>
			</form>
			</table>
			</div>
			<div style="text-align: right;"><a href="javascript:showContainer('share-comment', 137);" class="grey">REPLY</a></div>
			</div>
			<div style="clear: both;"></div>
		</div>
<?php include "./templates/footer.php"; ?>