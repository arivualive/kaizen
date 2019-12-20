<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-1")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄

    header('Location: ../auth/index.php');
    exit();
}

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}


error_reporting(~E_NOTICE);
date_default_timezone_set('Asia/Dhaka');

$path = '../../library/category/'; //カテゴリーPHPライブラリの場所（※必須）
$csvpath = $path . 'csv/'; //CSVファイルの場所
$backpath = '../info.php'; //「戻る」ボタンのリンク先

if(empty($_POST['csvfile'])) { $_POST['csvfile'] = $csvpath . 'users.csv'; }
$_POST['mode'] = ($_GET['bid']) ? 1 : 0;

$csvfile = $csvpath . 'contents.csv';
//CSVファイルを読み込み「UTF-8」に変換
$lines = @file($csvfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$lines) { $lines = array(); }
mb_convert_variables('UTF-8', 'SJIS-win', $lines);
//先頭の2行を削除して詰める
unset($lines[0], $lines[1]);
$lines = array_values($lines);

foreach($lines as $line) {
	$item = explode(',', $line);
	if($_GET['bid'] == $item[1]) { $_POST['string'] = $item[4]; }
}

require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');

foreach($lines as $line) {
	$item = explode(',', $line);

	if($_POST['bid'] == $item[1]) {
		$save[] .= $item[0] . ',' . $item[1] . ',' . $item[2] . ',' . $item[3] . ',' . $string;
	} else {
		$save[] .= $line;
	}
}

//ツリーに変更があった場合は保存
if($_POST['flag'] && $save != $lines) {
	//$temp = explode('/', $csvfile);
	//$name = $temp[count($temp) - 1];
	//$backup = substr($csvfile, 0, strpos($csvfile, $name)) . 'BK_' .  substr($name, 0, strpos($name, '.')); //CSVバックアップフォルダ

	//ヘッダーを追加
	$category = '32-bit extended version（31bit 0x40000000 Up to）' . "\r\n\r\n";
	$category .= 'Category,value,Parent category,item name,Association' . "\r\n";
	//POSTされた配列を「Shift-JIS」で保存
	foreach((array)$save as $value) { $category .= $value . "\r\n"; }
	//バックアップフォルダの存在確認
	//if(!file_exists($backup)) { mkdir($backup, 0777); }
	//バックアップしてファイルを保存
	//if(file_exists($csvfile)) { rename($csvfile, $backup . '/' . date('Ymd-His') . '.txt'); }
	if(file_exists($csvfile)) { rename($csvfile, $csvfile . '.bak'); }
	file_put_contents($csvfile, mb_convert_encoding($category, 'SJIS-win', 'UTF-8'), LOCK_EX);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS Administrator</title>
    <!-- favicon -->
	<link rel="shortcut icon" href="../images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/contents.css">
<link rel="stylesheet" href="<?php echo $path ?>items/awesome.min.css">
<link rel="stylesheet" href="<?php echo $path ?>category.css">
<!-- js -->
<script src="<?php echo $path ?>items/jquery.min.js"></script>
<script src="../../js/popper.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="../js/script.js"></script>
<script src="<?php echo $path ?>category.min.js"></script>




<!--源ノ角ゴシック版
<link href="https://fonts.googleapis.com/earlyaccess/notosansjapanese.css" rel="stylesheet" />
<style>
body,label,button{font-family:'Noto Sans Japanese',sans-serif}
input[name='category[]']:checked + .label1{font-weight:normal}
input[name='category[]']:checked + .label2{font-weight:normal}
input[name='category[]']:checked + .label3{font-weight:normal}
input[name='category[]']:checked + .label4{font-weight:normal}
.label{padding:9px 4px;font-size:13px}
.mark{padding:9px 0px}
.view1{padding:5px 4px}
.view3{padding:5px 0px}
</style>
-->
<!--游ゴシック WEBフォント版
<style>
@font-face {font-family:'YuGothicWEB';src:url('../../library/category/YuGothic.woff2')format('woff2'),url('../../library/category/YuGothic.woff')format('woff')}
body{font-family:'YuGothicWEB',sans-serif}
</style>
-->




</head>
<body>

<div id="wrap">

    <!-- ▼navgation -->
    <div id="nav-fixed">
        <!-- ▼h1 -->
        <div class="brand">
            <a href="../info.php">
                <h1>
                    <div class="img_h1"><img src="../images/logo.jpg" alt="ThinkBoard LMS"></div>
                    <p class="authority">For administrators</p>
                </h1>
            </a>
        </div>
        <!-- ▼scrol erea -->
        <div id="scrollerea">
            <nav id="mainnav">
                <ul id="accordion" class="accordion">
                    <li>
                        <a href="../info.php"><span class="icon-main-home"></span>HOME</a>
                    </li>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-1000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-user-add"></span>Student affiliation / ID setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/users.php">Affiliation group setting</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1000")) { ?>
                            <li><a href="../user/student.php">Student ID setting</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>Content setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">Content group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php">Content registration / editing</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li class="open">
                        <a href="../access/contents-control.php" class="active"><span class="icon-movie-set"></span>Target setting</a>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-graph"></span>Attendance status</a>
                        <ul class="togglemenu">
                            <li><a href="../history/index.php">Confirmation from the student</a></li>
                            <!--<li><a href="dateWiseViewing/index.php">動画授業から確認</a></li>-->
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                    <li>
                        <a href="../message/message_list.php"><span class="icon-main-message"></span>message</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="../help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>help</a>
                    </li>
                    <?php if ($isManager) { ?>
                    <li>
                        <a href="../user/admin.php"><span class="icon-user-add"></span>Administrator ID, authority setting</a>
                    </li>
                    <?php }; ?>
                </ul>
            </nav>
        </div>
    </div>
    <!-- ▲navgation -->

    <!-- ▼header -->
    <div id="header">
        <!-- ▼topicpath -->
        <div id="topicpath">
            <ol>
                <li><a href="../info.php">HOME</a></li>
                <li class="active"><a>Target setting</a></li>
            </ol>
        </div>
        <!-- ▼user information -->
        <div id="userinfo" class="button-dropdown">
            <a class="link" href="javascript:void(0)">
                <div class="erea-image"></div>
                <div class="erea-name">
                    <p class="authority">School Admin</p>
                    <p class="username"><?php echo $_SESSION['auth']['admin_name']; ?></p>
                </div>
            </a>
            <ul class="submenu">
                <li role="presentation"><a href="../account/index.php"><span class="icon-lock"></span>Account Setting</a></li>
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-in"></span>Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>Target setting</h2>
        </div>
        <!-- ▲h2 -->

        <div id="col-contentscontrol" class="clearfix">
            <!-- ▼科目・講義グループ -->
            <div id="subject-group" class="subject-group">
<?php
/*
$database = '82014d';
if($database) { list($check) = string_to_array($database); }
//users.csv の load_csv必須
for($current = count($csvTree); $current >= 1; $current--) {
	foreach($csvTree[$current] as $value) {
		foreach($value as $line) {
			$temp = explode('-', $line);
			if($check[$temp[0]][log(hexdec($temp[1]), 2)]) {
				$selectU .= $csvRowD[$line] . '（' . $line . '）' . "\n";
			}
		}
	}
}
*/

foreach($lines as $line) {
	$item = explode(',', $line);
	$item[3] = str_replace('{c}', ',', $item[3]);

	if(preg_match('/^[1-2]$/', $item[0])) {
		$csvMenu[$item[0]][$item[2]][] = $item[1]; //
		$csvParent[$item[1]] = $item[2];
		$csvName[$item[1]] = $item[3]; //
	}
}

for($current = count($csvMenu); $current >= 1; $current --) {
	foreach($csvMenu[$current] as $key => $value) {
		${'menu' . $current}[$key] .= '<ul class="';
		${'menu' . $current}[$key] .= ($current == 1) ? 'accordion' : 'togglemenu';
		if($key == $csvParent[$_GET['bid']]) { ${'menu' . $current}[$key] .= ' open'; }
		${'menu' . $current}[$key] .= '">' . "\n";

		foreach($value as $line) {
			if($csvMenu[$current + 1][$line]) {
				//if(${'menu' . ($current + 1)}[$line] != '<ul class="togglemenu">' . "\n" . '</ul>' . "\n") { //空項目を無視
					${'menu' . $current}[$key] .= '<li';
					if($line == $csvParent[$_GET['bid']]) { ${'menu' . $current}[$key] .= ' class="open"'; }
					${'menu' . $current}[$key] .= '>' . "\n" .'<a class="togglebtn">' . $csvName[$line] . '</a>' . "\n";
					${'menu' . $current}[$key] .= ${'menu' . ($current + 1)}[$line] . '</li>' . "\n"; //子項目を挿入
				//}
			} else {
				//if(search_multi($csvpath . 'contents.csv', $line, $csvTree, $selectU)) { //CSV照合
					${'menu' . $current}[$key] .= '<li';
					if($line == $_GET['bid']) { ${'menu' . $current}[$key] .= ' class="active"'; }
					${'menu' . $current}[$key] .= '><a href="' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $line . '">' . $csvName[$line] . '</a></li>' . "\n";
				//}
			}
		}
		${'menu' . $current}[$key] .= '</ul>' . "\n";
	}
}
echo $menu1[''];

/*
echo '<ul class="accordion">' . "\n";
foreach($lines as $line) {
	$item = explode(',', $line);
	$item[3] = str_replace('{c}', ',', $item[3]);

	if($item[0] == 1) {
		if($flag) { echo '</ul>' . "\n" . '</li>' . "\n"; }
		echo '<li';
		if($item[1] == $csvParent[$_GET['bid']]) { echo ' class="open"'; }
		echo '>' . "\n";

		echo '<a class="togglebtn">' . $item[3] . '</a>' . "\n";
		echo '<ul class="togglemenu';
		if($item[1] == $csvParent[$_GET['bid']]) { echo ' open'; }
		echo '">' . "\n";
	}

	if($item[0] == 2) {
		$flag = 1;
		echo '<li';
		if($_GET['bid'] == $item[1]) { echo ' class="active"'; }
		echo '><a href="' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $item[1] . '">' . $item[3] . '</a></li>' . "\n";
	}
}
echo '</ul>' . "\n" . '</li>' . "\n" . '</ul>' . "\n";
*/
?>
            </div>
            <!-- ▲科目・講義グループ -->

            <!-- ▼コンテンツ一覧 -->
            <div id="contentslist">
<?php require_once(dirname(__FILE__) . '/' . $path . 'catetree.php'); ?>
            </div>
            <!-- ▲コンテンツ一覧 -->

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
