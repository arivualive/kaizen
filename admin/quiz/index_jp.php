<?php
require_once "../../config.php";

$curl = new Curl($url);

/*
$data = array(
      'repository' => 'QuizRepository'
    , 'method' => 'findQuizAll'
);

$quiz = $curl->send($data);
*/

$quiz_id = '';
$QuizObj = new Quiz($quiz_id, $curl);
$quiz = $QuizObj->findQuiz();
#$quiz = $QuizObj->findQuizOrderByRegisted()
//debug($quiz);


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
<link rel="stylesheet" href="../../css/Paginate.css">
</head>
<body>
<h1><a href="https://<?php echo BASE_URL; ?>/admin/menu/">Menu</a></h1>
<h2><?php echo $_SESSION['auth']['admin_name']; ?></h2>
<h2>小テスト(科目一覧)</h2>
<p><a href="create.php">新規登録</a></p>
<table>
<tr>
  <th>テストID</th>
  <th>講義ID</th>
  <th>タイトル</th>
  <th>公開日</th>
  <th>終了日</th>
  <th>enable</th>
  <th>作成日</th>
  <th colspan="3">アクション</th>
</tr>
<?php foreach ((array)$quiz as $items): ?>
<tr>
  <td><?php echo $items['quiz_id']; ?></td>
  <td><?php echo $items['subject_section_id']; ?></td>
  <td><?php echo $items['title']; ?></td>
  <td><?php echo $items['start_day']; ?></td>
  <td><?php echo $items['last_day']; ?></td>
  <td align="center"><?php echo $items['enable']; ?></td>
  <td><?php echo $items['register_datetime']; ?></td>
  <td><a href="./result/index.php?id=<?php echo $items['quiz_id']; ?>">結果</a></td>
  <td><a href="base.php?id=<?php echo $items['quiz_id']; ?>">編集</a></td>
  <td><a href="delete.php?id=<?php echo $items['quiz_id']; ?>">削除</a></td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
