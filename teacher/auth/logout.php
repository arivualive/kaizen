<?php
session_start();

$_SESSION = array();
session_destroy();


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>newlms</title>
<!--[if IE]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

<![endif]-->
<style>
article, aside, dialog, figure, footer, header, hgroup, menu, nav,
	section {
	display: block;
}
</style>
</head>
<body>
<p>Logout</p>
<p>ログアウトしました。</p>
<p><a href="../auth/">ログイン</a></p>
</body>
</html>