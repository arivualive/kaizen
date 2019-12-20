<?php
require_once "../../config.php";

$error_message = '';
$error = 0;

$_SESSION['student']['start_time'] = '';

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

if (is_null($quiz_id)) {
    $error_message = "クイズ番号が不明です";
    $error = 1;
}

$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

$Curl = new Curl($url);
$Quiz = new Quiz($quiz_id, $Curl);

// クイズの抽出
$quiz_data = $Quiz->getQuiz();
//debug($quiz_data);

// studentの登録
$Student = new Student($Curl);
$Student->setStudentId($_SESSION['auth']['student_id']);

// 問題の抽出
$Query = new Query($quiz_id, $Curl);
$query_data = $Query->getQuery();
$cnt = $Query->countQuery();

$Answer = new QuizAnswer($quiz_id, $Curl);
$Answer->setStudent($Student);

// Query_idを調べる
#$query_data = $Answer->findAnswerQueryAll();

// カウントダウンを秒に変換
$LimitTime = $quiz_data['limit_time'] * 60;

if (filter_input(INPUT_POST, 'submit') == 'start') {
    // スタート開始時間
    $now = new DateTime();
    $now->setTimeZone(new DateTimeZone('Asia/Tokyo'));
    $_SESSION['student']['start_time'] = $now->format('Y-m-d H:i:s');

    $answer_id = $Answer->quizAnswerInsert();
    $Answer->setAnswerId($answer_id);

    // answer_queryテーブルにflg_no_answer=1をセット
    if ($query_data) {

        foreach ($query_data as $value) {
            $data['query_id'] = $value['query_id'];
            $data['quiz_id'] = $quiz_id;
            $data['answer_id'] = $answer_id;

            $Answer->setNoAnswer($data);
        }
    }

    if ($answer_id) {
        $Quiz->redirect("answer.php?id=$quiz_id&an=$answer_id&t=$time_left&bid=$bid");
    }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<title>ThinkBoard LMS 受講者</title>
<meta name="Author" content="" />
<!-- viewport -->
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!-- favicon -->
<link rel="shortcut icon" href="images/favicon.ico">
<!-- css -->
<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
<link rel="stylesheet" type="text/css" href="../css/common.css">
<link rel="stylesheet" type="text/css" href="../css/quiz.css">
<!-- js -->
<script src="../../js/jquery-3.1.1.js"></script>
<script src="../../js/popper.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script src="../js/script.js"></script>
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
                        <h1>
                            <img src="../images/logo.jpg" alt="ThinkBoard LMS">
                        </h1>
                    </a>
                </div>
                <!-- sub menu -->
                <div class="header-submenu">
                    <div class="btn-userinfomation dropdown">
                        <a href="#" id="dropdownMenu-userinfo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <p class="erea-icon">
                                <span class="icon-user-student"></span>
                            </p>
                            <p class="erea-username">
                                <?php echo $_SESSION['auth']['student_name']; ?>
                            </p>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-userinfo">
                            <li class="PW">
                                <a href="../account.php">パスワード変更</a>
                            </li>
                            <li class="loguot">
                                <a href="../auth/logout.php">ログアウト</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="../help/TBLMS_Student.pdf" target="_blank"><span>ヘルプ</span></a>
                    </div>
                </div>
            </div>
            <!-- right -->
            <div class="header-right">
                <nav class="nav-mainmenu">
                    <ul>
                        <li><a href="../info.php"><span>TOP</span></a></li>
                        <li class="active"><a href="../contentslist.php"><span>講義受講</span></a>
                        </li>
                        <li><a href="message_list.php?p=1"><span>メッセージ</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="container-maincontents" class="container-maincontents clearfix">
        <div id="quiz-answer">

            <!-- ページタイトル -->
            <div class="quiz-top clearfix">
                <p>
                    <?php echo ($error_message != '') ? $error_message : ''; ?>
                    <p class="text">テスト</p>
                    <ul class="btns">
                        <li><a href="../contentslist.php?bid=<?php echo $bid; ?>">講義一覧へ戻る</a></li>
                    </ul>
            </div>

            <!-- メインボックス -->
            <div class="quiz-box">

                <!-- head -->
                <div class="quiz-head">
                    <div class="quiz-title">
                        <p>
                            <?php echo $quiz_data['title']; ?> | <span class=""><?php echo $quiz_data['description']; ?></span>
                        </p>
                    </div>
                    <div class="timelimit">
                        <div class="in">
                            <p class="text">制限時間</p>
                            <p class="time">
                                <?php printf("%02d:%02d:%02d", floor($LimitTime / 3600), floor(($LimitTime / 60) % 60), $LimitTime % 60); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- body -->
                <div class="quiz-body">

                    <!-- 問題内容 -->
                    <div class="question-main">
                        <!-- スタート画面 -->
                        <div class="question-start">
                            <p>テストを開始します。</p>
                            <p>
                                <small>全<?php echo $cnt; ?>問</small>
                            </p>
                        </div>
                        <!-- 前へ　次へ -->
                        <div class="answer-btns">
                            <!--<form action="" method="post">-->
                            <form action=<?php echo $_SERVER['REQUEST_URI'] ?> method="POST">
                                <ul>
                                    <!-- <li><button onclick="location.href='answer.php'">スタート</button></li> -->
                                    <input type="hidden" name="LimitTime" value="<?php echo $LimitTime; ?>" />
                                    <li><button type="submit" name="submit" value="start" <?php echo ($error)? 'disabled': '';?>>スタート</button></li>
                                </ul>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

</body>

</html>
