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
    <title>Thinkboard LMS students</title>
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
                                <a href="account.php">Change Password</a>
                            </li>
                            <li class="loguot">
                                <a href="auth/logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="help/TBLMS_Student.pdf" target="_blank"><span>help</span></a>
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
                            <a href="contentslist.php"><span>Taking lectures</span></a>
                        </li>
                        <li>
                            <a href="message/message_list.php?p=1"><span>message</span></a>
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
                <h2>Information</h2>
                <div class="btn-notice">
                    <a href="message/notice_list.php?p=1">To the notice list</a>
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
                                        echo "<p class='destination contents'>content</p>";
                                        break;
                                    case 1:
                                        echo "<p class='destination questionnaire'>questionnaire</p>";
                                        break;
                                    case 2:
                                        echo "<p class='destination report'>report</p>";
                                        break;
                                    case 3:
                                        echo "<p class='destination test'>quiz</p>";
                                        break;
                                    case 4:
                                        if($item['message_type'] == 0) {
                                            echo "<p class='destination message'>Notice</p>";
                                        } else if($item['message_type'] == 1) {
                                            echo "<p class='destination message'>G message</p>";
                                        } else if($item['message_type'] == 2) {
                                            echo "<p class='destination message'>P message</p>";
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
                        <div class="no-item">There is no new arrival</div>
                    </div>
                <?php }; ?>
            </div>
        </div>
        <!-- btn group -->
        <div id="top-mainmenubtngroup" class="top-mainmenubtngroup clearfix">
            <div class="mainmenubtn study">
                <div class="h3">
                    <h3><img src="images/icon_mainmenu_study.png">Take a lecture</h3>
                </div>
                <p class="text">You can watch video lessons and submit quiz questionnaires.</p>
                <a href="contentslist.php">Take a lecture</a>
            </div>
            <div class="mainmenubtn message">
                <div class="h3">
                    <h3><img src="images/icon_mainmenu_message.png">Send and see messages</h3>
                </div>
                <p class="text">You can exchange messages with managers and instructors.</p>
                <a href="message/message_list.php?p=1">View and send messages</a>
            </div>
        </div>


        <div id="top-mainmenubtngroup" class="top-mainmenubtngroup clearfix">
            <div class="mainmenubtn study">
                <div class="h3">
                    <h3><img src="images/chart.png">BBS, Chat and Report</h3>
                </div>
                <p class="text">You can watch video lessons and submit quiz questionnaires.</p>
                <a href="https://procon-academy.cybozu.com">BBS, Chat and Report</a>
            </div>
            <div class="mainmenubtn message">
                <div class="h3">
                    <h3><img src="images/1066371.png">Frequently Asked Questions</h3>
                </div>
                <p class="text">You can clarify your douts and questions.</p>
                <a href="message/faq.php">FAQ</a>
            </div>
        </div>
    </div>
</div>


</body>
</html>
