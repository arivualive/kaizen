<?php
error_reporting(~E_NOTICE);
date_default_timezone_set('Asia/Tokyo');

$path = './'; //カテゴリーPHPライブラリの場所（※必須）
$csvpath = $path . 'csv/'; //CSVファイルの場所
$backpath = './'; //「戻る」ボタンのリンク先

//ツリー情報CSVファイル名がない場合
if(empty($_POST['csvfile'])) { $_POST['csvfile'] = $csvpath . 'category.csv'; }

//識別文字を読込み（※データベースに変更予定）
$dbfile = str_replace('.csv', '.txt', $_POST['csvfile']); //識別文字保存ファイル
$database = @file_get_contents($dbfile);

//カテゴリー計算用ファイルを読込み
require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');
//識別文字生成の場合（$_POST['category']を評価。変数 $string と $check を返す。）
//識別文字変換の場合（$_POST['string']を評価。変数 $check と $temp を返す。）

//カテゴリーを照合（引数:照合文字, 照合条件, CSVファイル名, チェック用配列）
if(search_value($_POST['search'], $_POST['verify'], $select)) {
	$result = '<span class="true">照合結果:TRUE（選択カテゴリーは含まれています）</span>';
} else {
	$result = '<span class="false">照合結果:FALSE（選択カテゴリーは含まれていません）</span>';
}

//識別文字に変更があった場合は保存（※データベースに変更予定）
if($_POST['flag'] && $database != $string) { file_put_contents($dbfile, $string, LOCK_EX); }
?>
<!DOCTYPE html>
<html>
<head>
<title>カテゴリーGUI</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<meta name="format-detection" content="telephone=no">
<!--<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">-->
<link rel="stylesheet" href="<?php echo $path ?>items/awesome.min.css">
<link rel="stylesheet" href="<?php echo $path ?>category.css">
<!--<script src="https://use.fontawesome.com/releases/v5.0.6/js/all.js" defer></script>-->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
<script src="<?php echo $path ?>items/jquery.min.js"></script>
<script src="<?php echo $path ?>category.min.js"></script>
</head>
<body ontouchstart>
<?php //if($_POST['category']) { natsort($_POST['category']); } ?>
<?php require_once(dirname(__FILE__) . '/' . $path . 'catetree.php'); //カテゴリーツリー表示用ファイルを読込み ?>
<!--開発用ツール-->
<link rel="stylesheet" href="<?php echo $path ?>Develop.css">
<?php $parts = 'Input'; require($path . 'Develop.php'); //識別文字入力 ?>
<?php $parts = 'Mode'; require($path . 'Develop.php'); //表示モード切替用 ?>
<?php $parts = 'Result'; require($path . 'Develop.php'); //結果参照 ?>
<?php $parts = 'Source'; require($path . 'Develop.php'); //ソースコード表示 ?>
<?php $parts = 'QRCode'; require($path . 'Develop.php'); //QRコード＆ショートURL表示 ?>
</body>
</html>
