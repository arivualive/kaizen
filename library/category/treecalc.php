<?php
//error_reporting(~E_NOTICE);
//初期設定
$path = './'; //カテゴリーPHPライブラリの場所（※必須）
$csvpath = $path . 'csv/'; //CSVファイルの場所
$_POST['csvfile'] = $csvpath . 'users.csv';
//オプション
$_POST['mode'] = 0;
$_POST['noform'] = $_POST['calc'] = $_POST['noalert'] = 1;

//カテゴリー計算用ファイルを読込み
require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>Affiliation group letter</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" href="<?php echo $path ?>items/awesome.min.css">
<link rel="stylesheet" href="<?php echo $path ?>category.css">
<link rel="stylesheet" href="<?php echo $path ?>Develop.css">
<script src="<?php echo $path ?>items/jquery.min.js"></script>
<script src="<?php echo $path ?>category.min.js" defer></script>
<script defer>$(function(){$('input[name="category[]"]').prop('disabled',false);$('.view1').css('cursor','pointer');});</script>
</head>
<body ontouchstart>
<span class="explain none"><i class="fas fa-exclamation-circle fa-fw"></i>Please select the group you want to affiliate, and input 「<i class="fas fa-qrcode fa-fw"></i>Affiliate group letter」at the <span class="true">at the bottom of the screen</span> into the CSV file</span>
<?php require_once(dirname(__FILE__) . '/' . $path . 'catetree.php'); //カテゴリーツリー表示用ファイルを読込み ?>
<div class="border2 none space">
<span><i class="fas fa-qrcode fa-fw"></i>Affiliation group letter</span><span id="alert">&nbsp;</span><br>
<input type="text" size="53" name="string" pattern="^[a-f\d\-]+$" value="<?php if(isset($_POST['string'])) echo $_POST['string'] ?>" placeholder="00000000-00000000-00000000-00000000-00000000-00000000" spellcheck="false" required>
<?php if(!preg_match('/iPhone|iPod|iPad|Android/ui', $_SERVER['HTTP_USER_AGENT'])) { echo '<button type="button" id="copy" class="button2 clip"><i class="fas fa-clipboard"></i></button>'; } ?>
</div>
</body>
</html>
