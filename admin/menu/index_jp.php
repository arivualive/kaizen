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
<h2>コンテンツ管理</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/contents/index.php?id=1-0x4&op=1-0x2">コンテンツ一覧</a></p>
<h2>ユーザー管理</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/student/index.php">学生一覧</a></p>
<h2>アクセス権属性</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/grade/index.php">参加年度</a>(grade)</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/classroom/index.php">所属ユニット</a>(classroom)</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/course/index.php">コース</a>(course)</p>
<h2>アクセス権設定</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/student/index.php">学生情報</a></p>
<p><a href="https://<?php echo BASE_URL;?>/admin/access/subject/index.php">科目情報</a></p>
<h2>科目管理</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/subject/group/index.php">大区分 科目</a>(subject_group)</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/subject/genre/index.php">中区分 科目</a>(subject_genre)科目</p>
<p><a href="https://<?php echo BASE_URL;?>/admin/subject/section/index.php">小区分 科目</a>(subject_section)講義</p>
<h2>アンケート</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/questionnaire/index.php">アンケート一覧</a></p>
<h2>小テスト</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/quiz/index.php">小テスト一覧</a></p>
<h2>メッセージ</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/message/list/index.php">メッセージ一覧</a></p>
<h2>Report</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/report/index.php">View Density</a></p>
<h2>視聴履歴</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/viewing/index.php">視聴グラフ</a></p>
<h2>視聴履歴</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/dateWiseViewing/index.php">Date Wise 視聴グラフ</a></p>
<h2>受講履歴</h2>
<p><a href="https://<?php echo BASE_URL;?>/admin/history/index.php">受講状況確認</a></p>
<h2><a href="../auth/logout.php">ログアウト</a></h2>
</body>
</html>
