<?php
require_once "../../config.php";

$_SESSION['student'] = array();

//debug($_SESSION);

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
article, aside, dialog, figure, footer, header,
hgroup, menu, nav, section { display: block; }
</style>
</head>
<body>
<h1><a href="https://<?php echo BASE_URL; ?>/admin/menu/">Menu</a></h1>
<h2><?php echo $_SESSION['auth']['student_name']; ?></h2>
<h2>mini quiz(List of subjects)</h2>
  <p><a href="start.php?id=58&list_id=2">To answer(First time)</a></p>
  <p><a href="start.php?id=58&an=98&list_id=5">To answer</a></p>
  <p><a href="https://<?php echo BASE_URL;?>/student/quiz/result.php?id=58&an=98&list_id=5">Quiz results</a></p>
</body>
</html>
