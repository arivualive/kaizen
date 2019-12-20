<?php
require dirname(filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . '/config.php';
//login_check('/teacher/auth/');

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
<h1>Menu</h1>
<h2><?php echo $_SESSION['auth']['teacher_name']; ?></h2>
</body>
</html>
