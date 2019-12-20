<?php
//error_reporting(~E_NOTICE);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
//ini_set('memory_limit', -1); //確保する最大メモリ(無制限)
ini_set('memory_limit', '6144M');

$absolute = '/home/kaizen/web/'; //本番環境
//$absolute = '/home/test-kaizen2.net/web/'; //テスト環境
//$absolute = '/home/nishioka/newlms/web/'; //ローカル環境

//CRON処理とログ表示の分岐
if(isset($_GET['cnt']) && $_GET['cnt'] == 'log') {
	$logfile = $absolute . 'htdocs/admin/history/json/time.txt';
	$log = @file_get_contents($logfile);
	mb_convert_variables('UTF-8', 'SJIS-win', $log);

	echo "<pre>\n" . $log . "</pre>";
	exit();
}

//ロックファイルをチェック
$lockfile = $absolute . 'htdocs/admin/history/json/lock.txt';
$lock = @file_get_contents($lockfile);
if($lock) { echo $lock; exit; }

//require(__dir__ . '/../../class/Curl.php'); //絶対パスに変更
require($absolute . 'htdocs/class/Curl.php');

$url = 'https://newlms-db.develop.kjs/';
$school_id = 2;

/*
switch($_SERVER['SERVER_NAME']) {
	case 'newlms.dev':
		$url = 'https://newlms-db.dev/';
		break;
	case 'nishioka.develop.kjs':
		$url = 'https://nishioka-db.develop.kjs/';
		break;
	case 'yano.develop.kjs':
		$url = 'https://yano-db.develop.kjs/';
		break;
	case 'ookubo.develop.kjs':
		$url = 'https://ookubo-db.develop.kjs/';
		break;
	case 'kondo.develop.kjs':
		$url = 'https://kondo-db.develop.kjs/';
		break;
	case 'kaseda.develop.kjs':
		$url = 'https://kaseda-db.develop.kjs/';
		break;
	case 'kubozono.develop.kjs':
		$url = 'https://kubozono-db.develop.kjs/';
		break;
	case 'rafi.develop.kjs':
		$url = 'https://rafi-db.develop.kjs/';
		break;
	//早稲田テスト環境
	//default:
	case 'test-kaizen2.net':
		$url = 'https://test-newlms-core.tbshare.net/module/';
		break;
	//早稲田本番環境
	default:
		$url = 'https://newlms-core.tbshare.net/module/';
		$school_id = 1;
}
*/

$url = '/kaizen/core/module/';
$school_id = 1;

$curl = new Curl( $url );

//受講者数確認
require($absolute . 'htdocs/class/StringEncrypt.php');
require($absolute . 'htdocs/models/AdminStudentModel.php');
$studentInfo = new AdminStudentModel($school_id, $curl);
$detail = $studentInfo->getStudent('', 'list');
// 2019/5/31 count関数対策
$members = 0;
if(is_countable($detail)){
	$members = count($detail);
}
//$members = count($detail);

//CSVファイルを読み込み「UTF-8」に変換
//$csvfile = '../../library/category/csv/contents.csv'; //絶対パスに変更
$csvfile = $absolute . 'htdocs/library/category/csv/contents.csv';
$lines = @file($csvfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$lines) { $lines = array(); }
mb_convert_variables('UTF-8', 'SJIS-win', $lines);

//第1カテゴリを抽出
foreach($lines as $line) {
	$item = explode(',', $line);
	if($item[0] == '1' || $item[0] == '2') { $genre[] = $line; } //第2カテゴリー追加で修正
}

//JSONファイルを作成
//require_once('json_generate.php'); //絶対パスに変更
require_once($absolute . 'htdocs/admin/history/json_generate.php');
//foreach($genre01 as $genre) { TABLE_JSON($curl, $genre); echo $genre . '<br>'; }

//count.txtを読込
//$filename = 'json/count.txt'; //絶対パスに変更
$filename = $absolute . 'htdocs/admin/history/json/count.txt';
$count = @file_get_contents($filename);
if(!$count) { $count = 0; }

//GETの処理
if(isset($_GET['cnt']) && $_GET['cnt'] >= 1 && $_GET['cnt'] <= count($genre)) {
	$count = $_GET['cnt'] - 1;
} else {
	$count += 1;
	if($count >= count($genre)) { $count = 0; }
}

//JSONファイルを生成
file_put_contents($lockfile, 'be locked', LOCK_EX);
$from = microtime(true);

//list($loop01, $loop02) = TABLE_JSON($curl, $genre01[$count], $school_id);
$item = explode(',', $genre[$count]);
list($loop01, $loop02, $memory) = TABLE_JSON($curl, $item, $school_id); //第2カテゴリー追加で修正

$to = microtime(true);
unlink($lockfile);

//count.txtを更新
if(!$loop01) { $count -= 1; } //CRON空回りチェック
if(!isset($_GET['cnt'])) { file_put_contents($filename, $count, LOCK_EX); }
if(!$loop01) { $count += 1; } //CRON空回りチェック

//time.txtを更新
date_default_timezone_set('Asia/Dhaka');
$filename = $absolute . 'htdocs/admin/history/json/time.txt';
$lines = @file($filename);
mb_convert_variables('UTF-8', 'SJIS-win', $lines);

for($i = count($genre); $i >= 1; $i--) { if(!$lines[$i - 1]) { $lines[$i - 1] = "\r\n"; } }
ksort($lines);

$save = '';
foreach($lines as $key => $line) {
	if($key == $count) {
		$save .= ($item[0] == 1) ? '〓' : '　'; //第2カテゴリー追加で修正
		$save .= ($key + 1) . '.' . $item[3] . '|' . $item[1] . '|' . date('G:i:s') . '|' . $loop01 . '回|' . $loop02 . '回|' . $memory . 'KB|' . round($to - $from, 3) . '秒　　　◎最新' . "\r\n";
	} else {
		$save .= str_replace('　　　◎最新', '', $line);
	}
}
file_put_contents($filename, mb_convert_encoding($save, 'SJIS-win', 'UTF-8'), LOCK_EX);

//members.txtを更新
$valid = 0; //受講可カウント
foreach($detail as $value) {
	if($value['joining']) { $valid ++; }
}

$filename = $absolute . 'htdocs/admin/history/json/members.txt';
$log = @file($filename);
$last = explode(',', end($log));

if($last[0] != date('Y/m/d')) { //ファイル更新日と今日を比較
	$save = date('Y/m/d') . ',' . $members . ',' . $valid . ',' . ($members - $valid) . "\r\n";
	file_put_contents($filename, mb_convert_encoding($save, 'SJIS-win', 'UTF-8'), FILE_APPEND | LOCK_EX);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>JSON生成</title>
<meta charset="UTF-8">
<!--<meta http-equiv="refresh" content="180" >-->
<style>body{font-family:YuGothic,'Yu Gothic Medium',Meiryo,sans-serif}</style>
</head>
<body>
生成科目:<?php echo $item[3] . '（' . $item[1] . '）' . $item[0] . '層目' ?><br>
生成位置:<?php /*2019/5/31 count関数対策*/ $genre_count = 0; if(is_countable($genre)){$genre_count = count($genre);} echo ($count + 1) . '/' . $genre_count; ?> value:<?php echo $loop01 ?>回 multi:<?php echo $loop02 ?>回<br>
生成時間:<?php echo round($to - $from, 3) ?>秒<br>
使用メモリ:<?php echo $memory ?> KB<br>
<br>
登録受講者数:<?php echo $members . '名' ?><br>
受講可:<?php echo $valid . '名' ?><br>
受講不可:<?php echo ($members - $valid) . '名' ?><br>
<br>
<?php
$filename = $absolute . 'htdocs/admin/history/json/' . $item[1] . '.json';
echo $item[1] . '.json（$return_data）- ' . number_format(filesize($filename)) . " bytes<br>\n";
$filename = $absolute . 'htdocs/admin/history/json/' . $item[1] . 'a.json';
echo $item[1] . 'a.json（$access_code）- ' . number_format(filesize($filename)) . " bytes<br>\n";
$filename = $absolute . 'htdocs/admin/history/json/' . $item[1] . 't.json';
echo $item[1] . 't.json（$student_table_contents）- ' . number_format(filesize($filename)) . " bytes<br>\n";
$filename = $absolute . 'htdocs/admin/history/json/' . $item[1] . 'd.json';
echo $item[1] . 'd.json（$table_data）- ' . number_format(filesize($filename)) . " bytes<br>\n";

$filename = $absolute . 'htdocs/admin/history/json/' . $item[1] . '.csv';
echo $item[1] . '.csv（第1テーブルCSV）- ' . number_format(filesize($filename)) . " bytes<br>\n";
$filename = $absolute . 'htdocs/admin/history/json/' . $item[1] . 'd.csv';
echo $item[1] . 'd.csv（第2テーブルCSV）- ' . number_format(filesize($filename)) . " bytes<br>\n";

$filename = $absolute . 'htdocs/admin/history/json/members.txt';
echo 'members.txt（日次受講者数ログ）- ' . number_format(filesize($filename)) . " bytes<br>\n";

//echo '<pre>$genre<br>';
//print_r($genre);
//echo '<br>$item<br>';
//print_r($item);
//echo '</pre>';
?>
</body>
</html>
