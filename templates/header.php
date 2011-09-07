<?php session_start(); $colors = array("blue", "green", "orange", "purple", "cyan"); $randomcolor = $colors[rand(0,4)]; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php if(isset($pagetitle)) { echo $pagetitle; } else { echo "Won't you be my neighbr?"; } ?></title>
	<base href="http://yoursite.com/" />
	<meta property="og:title" content="<?php echo $posttitle; ?>" />
	<meta property="og:description" content="<?php echo $note; ?>" />
<?php if($type == 'image') { ?>
	<meta property="og:image" content="<?php echo $link; ?>" />
	<link rel="image_src" href="<?php echo $link; ?>" />
<?php } ?>
	<link rel="stylesheet" type="text/css" media="screen" href="neighbr.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="include/sunburst.css">
	<link rel="alternate" type="application/rss+xml" title="neighbrhood rss" href="<?php if(isset($rssurl)) { echo $rssurl; } else { echo "/feed"; } ?>" />
	<script type="text/javascript" src="http://arguments.callee.info/Jelo.js"></script>
	<script type="text/javascript" src="include/aesthetics.js"></script>
	<script type="text/javascript" src="include/highlight.js"></script>
	<script>hljs.initHighlightingOnLoad();</script>
</head>
<body>
<div id="wrapper">
	<div id="breadcrumbs">
<?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == TRUE) { ?>
<?php

	if(file_exists("./img/avatars/{$_SESSION['username']}.jpg")) {
		$avatar = "<img src=\"./img/avatars/{$_SESSION['username']}.jpg\" alt=\"neighbr: {$_SESSION['username']}\" style=\"width: 32px; height: 32px; border: 0px; float: right; margin-top: 2px;\" />";
	} else {
		$avatar = "<img src=\"./img/avatar.jpg\" alt=\"neighbr: {$_SESSION['username']}\" style=\"width: 32px; height: 32px; border: 0px; float: right; margin-top: 2px;\" />";
	}

?>
		<div style="padding: 20px; font-size: 12px;">
			<?php echo $avatar; ?>
			<div>Welcome back to the neighbrhood,</div>
			<div style="font-size: 18px; font-weight: bold;"><a href="http://yoursite.com/<?php echo $_SESSION['username']; ?>/"><?php echo $_SESSION['username']; ?></a></div>
		</div>
		<div id="share">
			<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td width="100%" valign="middle">
					<a href="javascript:hideContainer('share-image', 'share-code', 'share-link', 'share-video');showContainer('share-text', 137);" style="text-transform: uppercase;">Text</a> &bull;
					<a href="javascript:hideContainer('share-text', 'share-code', 'share-link', 'share-video');showContainer('share-image', 137);" style="text-transform: uppercase;">Image</a> &bull;
					<a href="javascript:hideContainer('share-text', 'share-image', 'share-code', 'share-link');showContainer('share-video', 137);" style="text-transform: uppercase;">Video</a> &bull;
					<a href="javascript:hideContainer('share-text', 'share-image', 'share-code', 'share-video');showContainer('share-link', 137);" style="text-transform: uppercase;">Link</a> &bull;
					<!-- <a href="#">File</a> &bull; -->
					<a href="javascript:hideContainer('share-text', 'share-image', 'share-link');showContainer('share-code', 137);" style="text-transform: uppercase;">Code</a>
				</td>
			</tr>
			</form>
			</table>
		</div>
		<div id="share-text">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="shareform-text" name="shareform-text" action="share/text" method="post">
			<tr>
				<td width="100%" colspan="2">
					<div style="padding: 5px;"><input type="text" id="texttitle" name="texttitle" value="Describe your title here, then share your note below!" onClick="this.value=''" /></div>
					<div style="padding: 5px 5px 0px 5px;"><textarea id="texttext" name="texttext" rows="3" style="width: 570px; height: 50px; font-style: italic;" onFocus="if(this.innerHTML='Click here and type or copy/paste your note.'){this.innerHTML='';this.style.fontStyle='none';}">Click here and type or copy/paste your note.</textarea></div>
				</td>
			</tr>
			<tr>
				<td width="99%" valign="middle" style="font-weight: bold; font-size: 11px;"><div style="padding-left: 5px;"><a href="javascript:document.getElementById('shareform-text').submit();">Share It!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><div style="padding-right: 5px;"><a href="javascript:hideContainer('share-text');">[x]</a></div></td>
			</tr>
			</form>
			</table>
		</div>
		<div id="share-image">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="shareform-image" name="shareform-image" action="share/image" method="post" enctype="multipart/form-data">
			<input type="hidden" name="MAX_FILE_SIZE" value="16777215" />
			<tr>
				<td width="100%" colspan="2">
					<div style="padding: 5px; position: relative;"><input type="file" id="imagefile" name="imagefile" size="123" style="color: #979797;" /></div>
					<div style="padding: 5px 5px 0px 5px;"><textarea id="imagetext" name="imagetext" rows="3" style="width: 570px; height: 50px; font-style: italic;" onFocus="if(this.innerHTML='Click here and enter a description about this image.'){this.innerHTML='';this.style.fontStyle='none';}">Click here and enter a description about this image.</textarea></div>
				</td>
			</tr>
			<tr>
				<td width="99%" valign="middle" style="font-weight: bold; font-size: 11px;"><div style="padding-left: 5px;"><a href="javascript:document.getElementById('shareform-image').submit();">Share It!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><div style="padding-right: 5px;"><a href="javascript:hideContainer('share-image');">[x]</a></div></td>
			</tr>
			</form>
			</table>
		</div>
		<div id="share-video">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="shareform-video" name="shareform-video" action="share/video" method="post">
			<tr>
				<td width="100%" colspan="2">
					<div style="padding: 5px;"><input type="text" id="videosource" name="videosource" value="Paste the YouTube URL or ID here, and describe it below." onClick="this.value=''" /></div>
					<div style="padding: 5px 5px 0px 5px;"><textarea id="videotext" name="videotext" rows="3" style="width: 570px; height: 50px; font-style: italic;"></textarea></div>
				</td>
			</tr>
			<tr>
				<td width="99%" valign="middle" style="font-weight: bold; font-size: 11px;"><div style="padding-left: 5px;"><a href="javascript:document.getElementById('shareform-video').submit();">Share It!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><div style="padding-right: 5px;"><a href="javascript:hideContainer('share-text');">[x]</a></div></td>
			</tr>
			</form>
			</table>
		</div>
		<div id="share-link">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="shareform-link" name="shareform-link" action="share/link" method="post">
			<tr>
				<td width="100%" colspan="2">
					<div style="padding: 5px;"><input type="text" id="linktitle" name="linktitle" value="Describe your link briefly, and paste the link below." onClick="this.value=''" /></div>
					<div style="padding: 5px 5px 0px 5px;"><textarea id="linktext" name="linktext" rows="3" style="width: 570px; height: 50px; font-style: italic;" onFocus="if(this.innerHTML='Click here and enter or copy/paste the URL.'){this.innerHTML='';this.style.fontStyle='none';}">Click here and enter or copy/paste the URL.</textarea></div>
				</td>
			</tr>
			<tr>
				<td width="99%" valign="middle" style="font-weight: bold; font-size: 11px;"><div style="padding-left: 5px;"><a href="javascript:document.getElementById('shareform-link').submit();">Share It!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><div style="padding-right: 5px;"><a href="javascript:hideContainer('share-link');">[x]</a></div></td>
			</tr>
			</form>
			</table>
		</div>
		<div id="share-code">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="shareform-code" name="shareform-code" action="share/code" method="post">
			<tr>
				<td width="100%" colspan="2">
					<div style="padding: 5px;"><input type="text" id="codetitle" name="codetitle" value="Label your snippet, and share your code below." onClick="this.value=''" /></div>
					<div style="padding: 5px 5px 0px 5px;"><textarea id="codetext" name="codetext" rows="3" style="width: 570px; height: 50px; font-style: italic;" onFocus="if(this.innerHTML='Click here and enter or copy/paste the code you wish to post.'){this.innerHTML='';this.style.fontStyle='none';}">Click here and enter or copy/paste the code you wish to post.</textarea></div>
				</td>
			</tr>
			<tr>
				<td width="99%" valign="middle" style="font-weight: bold; font-size: 11px;"><div style="padding-left: 5px;"><a href="javascript:document.getElementById('shareform-code').submit();">Share It!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><div style="padding-right: 5px;"><a href="javascript:hideContainer('share-code');">[x]</a></div></td>
			</tr>
			</form>
			</table>
		</div>
<?php } else { ?>
		<div id="login">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="userlogin" name="userlogin" action="user.php?do=login" method="post">
			<tr>
				<td width="23%" valign="middle"><input type="text" id="username" name="username" value="username" style="width: 100%;" onClick="this.value=''" /></td>
				<td width="23%" valign="middle"><input type="password" id="password" name="password" value="password" style="width: 100%;" onClick="this.value=''" /></td>
				<td width="53%" valign="middle"><div style="font-size: 10px; font-weight: bold; padding: 0px 2px;"><a href="javascript:document.getElementById('userlogin').submit();">Go!</a></div></td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><a href="javascript:hideContainer('login');">[x]</a></td>
			</tr>
			<input type="submit" style="display: none;" id="submitlogin" name="submitlogin" /></form>
			</table>
		</div>
		<div id="register">
			<table width="100%" cellspacing="0" cellpadding="5" border="0">
			<form id="newuser" name="newuser" action="user.php?do=register" method="post">
			<tr>
				<td width="23%" valign="middle"><input type="text" id="r_username" name="r_username" value="username" style="width: 100%;" onClick="this.value=''" /></td>
				<td width="76%" valign="middle"><div style="font-size: 10px; padding: 0px 2px;">Please enter a unique username to use in the neighbrhood.</td>
				<td width="1%" valign="middle" style="font-weight: bold; font-size: 11px; text-align: right;"><a href="javascript:hideContainer('register');">[x]</a></td>
			</tr>
			<tr>
				<td width="23%" valign="middle"><input type="password" id="r_password" name="r_password" value="password" style="width: 100%;" onClick="this.value=''" /></td>
				<td width="76%" valign="middle"><div style="font-size: 10px; padding: 0px 2px;">Please enter a unique password to authenticate your account.</td>
				<td width="1%" valign="middle" style="font-weight: bold;"></td>
			</tr>
			<tr>
				<td width="23%" valign="middle"><input type="text" id="email" name="email" value="you@email.com" style="width: 100%;" onClick="this.value=''" /></td>
				<td width="76%" valign="middle"><div style="font-size: 10px; padding: 0px 2px;">Please enter a valid e-mail address.</td>
				<td width="1%" valign="middle" style="font-weight: bold;"><div style="font-size: 10px; font-weight: bold; padding: 0px 2px;"><a href="javascript:document.getElementById('newuser').submit();">Go!</a></div></td>
			</tr>
			<input type="submit" style="display: none;" id="submituser" name="submituser" />
			</form>
			</table>
		</div>
		<div style="float: left; width: 50%; text-align: left;">
			<div style="padding: 5px;">Howdy, neighbr!</div>
		</div>
		<div style="float: right; width: 50%; text-align: right;">
			<div style="padding: 5px;"><a href="javascript:hideContainer('register');showContainer('login', 27);">Log In</a> &bull; <a href="javascript:showContainer('register', 84);hideContainer('login');">Register</a> &bull; <a href="http://yoursite.com/">NEIGHBR</a></div>
		</div>
		<div style="clear: both;"></div>
<?php } ?>
	</div>
	<div style="padding: 10px;">