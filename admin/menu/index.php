<?php
require_once "../../config.php";

// セッションが無ければログイン画面へ
#//login_check();

// adminのセッションをクリアして、メニューの表示
$_SESSION['admin'] = array();

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
<h2><?php echo $_SESSION['auth']['admin_name']; ?></h2>
<h2>Content Management</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/contents/index.php?id=1-0x4&op=1-0x2">Contents list</a></p>
<h2>User management</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/student/index.php">Student list</a></p>
<h2>Access right attribute</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/grade/index.php">Participation year</a>(grade)</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/classroom/index.php">Unit belonging</a>(classroom)</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/course/index.php">Course</a>(course)</p>
<h2>Access right setting</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/student/index.php">Student information</a></p>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/subject/index.php">Course information</a></p>
<h2>Course management</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/subject/group/index.php">Major categories</a>(subject_group)</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/subject/genre/index.php">Middle class subjects</a>(subject_genre)Subjects</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/subject/section/index.php">Subdivision</a>(subject_section)lecture</p>
<h2>Questionnaire</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/questionnaire/index.php">List of questionnaires</a></p>
<h2>Mini quiz</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/quiz/index.php">List of quizzes</a></p>
<h2>Message</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/message/list/index.php">Message list</a></p>
<h2>Report</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/report/index.php">View Density</a></p>
<h2>Viewing history</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/viewing/index.php">Viewing graph</a></p>
<h2>Viewing graph</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/dateWiseViewing/index.php">Viewing graph</a></p>
<h2>Attending history</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/history/index.php">Attendance confirmation</a></p>
<h2><a href="../auth/logout.php">Logout</a></h2>
</body>
</html>
