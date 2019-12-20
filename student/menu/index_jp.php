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
<h2>コンテンツ管理</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/contents/index.php?p=1">コンテンツ一覧</a></p>
<h2>アンケート</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/questionnaire/index.php?p=1">アンケート一覧</a></p>
<h2>レポート</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/report/index.php?p=1">レポート一覧</a></p>
<h2>テスト</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/quiz/index.php">テスト一覧</a></p>
<h2>受講履歴</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/history/index.php">受講履歴</a></p>
<h2>ヘルプ</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/help/index.php">ヘルプ</a></p>
<h2>アカウント</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/account/index.php">アカウント</a></p>
<h2>メッセージ</h2>
<p><a href="https://<?php echo BASE_URL;?>/student/message/list/index.php?p=1">メッセージ一覧</a></p>
<h2><a href="../auth/logout.php">ログアウト</a></h2>
</body>
</html>
