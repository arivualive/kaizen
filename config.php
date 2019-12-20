<?php
//URLから admin と student の判定
if(strpos($_SERVER['REQUEST_URI'], 'admin') !== false) {
	session_name('LMS_Admin'); //セッション名指定（Cookie名）
	$user = 'admin';
} else {
	session_name('LMS_Student'); //セッション名指定（Cookie名）
	$user = 'student';
}

//セッションIDの自動変更 2018/10/23 サーバー負荷軽減の為、session有効期限を1時間から1日へ変更
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime ', 86400);
session_start();

if(mt_rand(1, 6) == 1) { // 実行確率
	$sess_file = 'sess_' . session_id();
	$sess_path = ini_get('session.save_path');
	$timestamp = filemtime($sess_path . '/' . $sess_file);
	$span = 300; // 経過時間（秒）

	if(($timestamp + $span) < time()) {
		session_regenerate_id();
	}
}

// ログインのチェック
if(strpos($_SERVER['REQUEST_URI'], 'auth') === false) {
	if(!isset($_SESSION['auth'][$user . '_id'])) {
		$base = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
		header('Location: ' . $base . '/' . $user . '/auth/');
		exit();
	}
}

//以降、DB関連の謎クラス
require __dir__ . '/class/ClassLoader.php';

$loader = new ClassLoader();
$loader->registerDir(dirname(__FILE__) . '/class');
$loader->registerDir(dirname(__FILE__) . '/models');
$loader->register();

$url = 'localhost/kaizen/core/module/';
$url2 = 'localhost/kaizen/';

define ("BASE_URL" ,$_SERVER["HTTP_HOST"]);
?>
