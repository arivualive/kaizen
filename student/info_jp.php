<?php
require_once "../config.php";
//login_check('/student/auth/');

$result_grade = '';
$result_classroom = '';
$result_course = '';

$curl = new Curl($url);
//debug($_SESSION);

if (isset($_SESSION['auth']['student_id'])) {
    $student_id = $_SESSION['auth']['student_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}

// student idの情報取得
if (filter_input(INPUT_POST, "student_id") != '') {
    $student_id = filter_input(INPUT_GET, "id");
}

$studentInfo = new GetStudentInfo($student_id, $school_id, $curl);

// contents データ
$contents_data = $studentInfo->getContents();
//debug($contents_data);

// questionnaire データ
$questionnaire_data = $studentInfo->getQuestionnaire(0);
//debug($questionnaire_data);

// report データ
$report_data = $studentInfo->getQuestionnaire(1);
//debug($report_data);

// quiz データ
$quiz_data = $studentInfo->getQuiz();
//debug($quiz_data);

// message データ
$message_data = $studentInfo->getMessage();
//debug($message_data);

$info_array = array_merge($contents_data,$questionnaire_data,$report_data,$quiz_data,$message_data);
foreach ((array) $info_array as $key => $value) {
    $sort['register_datetime'][$key] = $value['register_datetime'];
    if($value['type'] == 4){
        $sort['message_level'][$key] = $value['message_level'];
    } else {
        $sort['message_level'][$key] = 0;
    }
}
if(isset($sort['register_datetime'])) {
//    array_multisort($sort['message_level'], SORT_ASC, $info_array,
//                    $sort['register_datetime'], SORT_DESC, $info_array);
}
//debug($info_array);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS 受講者</title>
	<meta name="Author" content=""/>
	<!-- viewport -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- favicon -->
	<link rel="shortcut icon" href="images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/top-information.css">
    <!-- js -->
    <script src="../js/jquery-3.1.1.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/script.js"></script>
</head>
<body>

<div id="wrap">
    <!-- header -->
    <div id="header-bar">
        <div id="header">
            <!-- left -->
            <div class="header-left">
                <!-- h1 -->
                <div class="h1">
                    <a href="#">
                        <h1><img src="images/logo.jpg" alt="ThinkBoard LMS"></h1>
                    </a>
                </div>
                <!-- sub menu -->
                <div class="header-submenu">
                    <div class="btn-userinfomation dropdown">
                        <a href="#" id="dropdownMenu-userinfo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <p class="erea-icon"><span class="icon-user-student"></span></p>
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p> <!-- ここにユーザーの名前が入ります -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-userinfo">
                            <li class="PW">
                                <a href="account.php">パスワード変更</a>
                            </li>
                            <li class="loguot">
                                <a href="auth/logout.php">ログアウト</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="help/TBLMS_Student.pdf" target="_blank"><span>ヘルプ</span></a>
                    </div>
                </div>
            </div>
            <!-- right -->
            <div class="header-right">
                <nav class="nav-mainmenu">
                    <ul>
                        <li class="active">
                            <a href="info.php"><span>TOP</span></a>
                        </li>
                        <li>
                            <a href="contentslist.php"><span>講義受講</span></a>
                        </li>
                        <li>
                            <a href="message/message_list.php?p=1"><span>メッセージ</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- main container -->
    <div id="container-maincontents" class="container-maincontents">
        <!-- information -->
        <div id="top-information" class="top-information">
            <div class="h2 clearfix">
                <h2>インフォメーション</h2>
                <div class="btn-notice">
                    <a href="message/notice_list.php?p=1">お知らせ一覧へ</a>
                </div>
            </div>
            <div class="information-listgroup scrol">
                <?php if(count($info_array) != 0) { ?>
                    <?php foreach ((array) $info_array as $item): ?>
                        <div class='information-list'>
                            <div class='head'>
                                <p class="day"><?php echo date('Y/m/d', strtotime($item['register_datetime'])); ?></p>
                                <?php
                                switch ($item['type']) {
                                    case 0:
                                        echo "<p class='destination contents'>コンテンツ</p>";
                                        break;
                                    case 1:
                                        echo "<p class='destination questionnaire'>アンケート</p>";
                                        break;
                                    case 2:
                                        echo "<p class='destination report'>レポート</p>";
                                        break;
                                    case 3:
                                        echo "<p class='destination test'>テスト</p>";
                                        break;
                                    case 4:
                                        if($item['message_type'] == 0) {
                                            echo "<p class='destination message'>お知らせ</p>";
                                        } else if($item['message_type'] == 1) {
                                            echo "<p class='destination message'>Gメッセージ</p>";
                                        } else if($item['message_type'] == 2) {
                                            echo "<p class='destination message'>Pメッセージ</p>";
                                        }
                                        break;
                                }
                                ?>
                            </div>
                            <div class='text'>
                                <?php
                                if(!$item['check_flag']){
                                    echo "<span class='new'> NEW </span>";
                                }
                                switch ($item['type']) {
                                    case 0:
                                        echo $item['title'];
                                        break;
                                    case 1:
                                        echo $item['title'];
                                        break;
                                    case 2:
                                        echo $item['title'];
                                        break;
                                    case 3:
                                        echo $item['title'];
                                        break;
                                    case 4:
                                        echo "<a href='/student/message/message_detail.php?id=" . $item['primary_key'] . "' >".$item['title']."</a>";
                                        break;
                                    if(!$item['check_flag']){
                                        echo "</span>";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <div class='information-list'>
                        <div class="no-item">新着はありません</div>
                    </div>
                <?php }; ?>
            </div>
        </div>
        <!-- btn group -->
        <div id="top-mainmenubtngroup" class="top-mainmenubtngroup clearfix">
            <div class="mainmenubtn study">
                <div class="h3">
                    <h3><img src="images/icon_mainmenu_study.png">講義を受講する</h3>
                </div>
                <p class="text">動画授業の視聴、テスト・アンケート・レポートの提出ができます。</p>
                <a href="contentslist.php">講義を受講する</a>
            </div>
            <div class="mainmenubtn message">
                <div class="h3">
                    <h3><img src="images/icon_mainmenu_message.png">メッセージを送る・見る</h3>
                </div>
                <p class="text">管理者・講師の先生とメッセージを交換できます。</p>
                <a href="message/message_list.php?p=1">メッセージを見る・送る</a>
            </div>
        </div>
    </div>
</div>


</body>
</html>
