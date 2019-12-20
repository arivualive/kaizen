<?php
require_once "../../config.php";

// studentのセッションをクリアして、メニューの表示
$_SESSION[ 'student' ] = array();

print_r( $_SESSION );

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
<h2><?php echo $_SESSION[ 'auth' ][ 'student_name' ]; ?></h2>
<h2>Content Management</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/contents/index.php?p=1">Contents list</a></p>
<h2>Questionnaire</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/questionnaire/index.php?p=1">List of questionnaires</a></p>
<h2>Report</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/report/index.php?p=1">Report list</a></p>
<h2>Quiz</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/quiz/index.php">Quiz list</a></p>
<h2>Course history</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/history/index.php">Course history</a></p>
<h2>Help</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/help/index.php">Help</a></p>
<h2>Account</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/account/index.php">account</a></p>
<h2>Message</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/message/list/index.php?p=1">Message list</a></p>
<h2><a href="../auth/logout.php">Logout</a></h2>
</body>
</html>
