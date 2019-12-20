<?php

require_once "../config.php";

$base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
header('Location: ' . $base . '/student/auth/');
exit();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>newlms</title>
<!--[if IE]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

<![endif]-->
<style>
article, aside, dialog, figure, footer, header, hgroup, menu, nav,
	section {
	display: block;
}
</style>
</head>
<body>
<h1>Menu</h1>
<h2><?php echo $_SESSION['auth']['student_name']; ?></h2>
<h2>content</h2>
<p><a href="http://<?php echo BASE_URL;?>/student/contents/index.php">Content play page</a></p>
</body>
</html>
