<?php
require_once "../../config.php";
require_once "../../library/permission.php";

//debug($_SESSION);
//debug($_POST);

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-8")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄

    header('Location: ../auth/index.php');
    exit();
}

//------CSV読込部分 ここから------
error_reporting(~E_NOTICE);
$path = '../../library/category/'; //カテゴリーPHPライブラリの場所（※必須）
$csvpath = $path . 'csv/'; //CSVファイルの場所
$_POST['csvfile'] = $csvpath . 'contents.csv';

//カテゴリー計算用ファイルを読込み
require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');

//CSVファイルを読み込み「UTF-8」に変換
$lines = @file($_POST['csvfile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$lines) { $lines = array(); }
mb_convert_variables('UTF-8', 'SJIS-win', $lines);
//先頭の2行を削除して詰める
unset($lines[0], $lines[1]);

if(filter_input(INPUT_GET, "bid")) {
    $subject_section_id = $_GET['bid'];
}

list($string, $check) = array_to_string((array)$subject_section_id);
$bit_classroom = $string;
//debug($string);

//debug($check);
//------CSV読込部分 ここまで------

$error_message = array();

$quiz_id = filter_input(INPUT_GET, "id");
if ($quiz_id == '') die ('I do not know the quiz number');

$bid = filter_input(INPUT_GET, "bid");
if ($bid == '') die ('The category number is unknown.');

$Curl = new Curl($url);
$Quiz = new Quiz($quiz_id, $Curl);

if ($quiz_id) {
    $quiz = $Quiz->getQuiz();
    $data = $quiz;
    $repeat_challenge_flg = ($quiz['repeat_challenge'] > 0) ? 1 : 0;
    $limit_time_flg = ($quiz['limit_time'] > 0) ? 1 : 0;
    $qualifying_score_flg = ($quiz['qualifying_score'] > 0) ? 1 : 0;
}

if (filter_input(INPUT_POST, "submit") == "save") {
    // タイトル
    $data['title'] = filter_input(INPUT_POST, "title", FILTER_SANITIZE_SPECIAL_CHARS);

    // バリデーションチェック
    if ($data['title'] == '') {
        $error_message['title'] = 'Please enter a title.';
    }

    // 概要
    $data['description'] = filter_input(INPUT_POST, "description", FILTER_SANITIZE_SPECIAL_CHARS);

    // 受験期間
    $dt = new DateTime();
    $dt->setTimeZone(new DateTimeZone('Asia/Tokyo'));

    if(filter_input(INPUT_POST, "start_day")) {
        $data['start_day'] = filter_input(INPUT_POST, "start_day");
    } else {
        $data['start_day'] = '0000-01-01';
    }

    if(filter_input(INPUT_POST, "last_day")) {
        $data['last_day'] = filter_input(INPUT_POST, "last_day");
    } else {
        $data['last_day'] = '9999-12-31';
    }

    // 受験回数
    $options = array('options'=>array('default' => 0));
    $repeat_challenge_flg = filter_input(INPUT_POST, "repeat_challenge_flg", FILTER_VALIDATE_INT, $options);

    $options = array('options'=>array('default' => 3, 'min_range' => 1, 'max_range' => 10));
    $repeat_challenge = filter_input(INPUT_POST, "repeat_challenge", FILTER_VALIDATE_INT, $options);

    $data['repeat_challenge'] = ($repeat_challenge_flg == 0) ? 0 : $repeat_challenge;

    // 制限時間
    $options = array('options'=>array('default' => 0));
    $limit_time_flg = filter_input(INPUT_POST, "limit_time_flg", FILTER_VALIDATE_INT, $options);

    $options = array('options'=>array('default' => 10, 'min_range' => 1, 'max_range' => 120));
    $limit_time = filter_input(INPUT_POST, "limit_time");

    $data['limit_time'] = ($limit_time_flg == 0) ? 0 : $limit_time;

    // 合格点
    $options = array('options'=>array('default' => 0));
    $qualifying_score_flg = filter_input(INPUT_POST, "qualifying_score_flg", FILTER_VALIDATE_INT, $options);

    $options = array('options'=>array('default' => 70, 'min_range' => 10, 'max_range' => 100));
    $qualifying_score = filter_input(INPUT_POST, "qualifying_score");

    $data['qualifying_score'] = ($qualifying_score_flg == 0) ? 0 : $qualifying_score;

    // 合否判定の表示
    $success_flg = filter_input(INPUT_POST, "success_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));
    $data['success_flg'] = ($qualifying_score_flg == 0) ? 0 : $success_flg;

    // 平均点の表示
    $data['average_flg'] = filter_input(INPUT_POST, "average_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 順位の表示
    $data['rank_flg'] = filter_input(INPUT_POST, "rank_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 正解率の表示
    $data['answer_rate_flg'] = filter_input(INPUT_POST, "answer_rate_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 偏差値の表示
    $data['deviation_flg'] = filter_input(INPUT_POST, "deviation_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 利用者の答えを表示
    $data['student_answer_flg'] = filter_input(INPUT_POST, "student_answer_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 回答結果の表示
    $data['answer_flg'] = filter_input(INPUT_POST, "answer_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 解説の表示
    $data['explain_flg'] = filter_input(INPUT_POST, "explain_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // 正誤の表示
    $data['correct_flg'] = filter_input(INPUT_POST, "correct_flg", FILTER_VALIDATE_INT, array('options'=>array('default' => 1)));

    // テスト終了のメッセージ
    $finished_message = filter_input(INPUT_POST, "finished_message", FILTER_SANITIZE_SPECIAL_CHARS);
    $data['finished_message'] = (! $finished_message) ? "Thank you very much." : $finished_message;

    $data['bit_classroom'] = $bit_classroom;

    $data['quiz_id'] = filter_input(INPUT_POST, "quiz_id");
    $bid = filter_input(INPUT_POST, "bid");

    if (filter_input(INPUT_POST, 'quiz_id')) {
        $result = $Quiz->updateQuiz($data);
    }

    if (! $error_message) {
        $Quiz->redirect("query.php?id=$quiz_id&p=0&bid=$bid");
    }
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
    <link rel="stylesheet" type="text/css" href="../css/datepicker.min.css">
    <link rel="stylesheet" type="text/css" href="../css/icon-font.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/quiz.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script type="text/javascript" src="./css/sweetalert-master/dist/sweetalert.min.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/datepicker.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
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
                    <li class="open">
                        <a class="togglebtn"><span class="icon-movie-manage"></span>Content setting</a>
                        <ul class="togglemenu open">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">Content group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php" class="active">Content registration / editing</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li>
                        <a href="../access/contents-control.php"><span class="icon-movie-set"></span>Target setting</a>
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
                <li>Content setting</li>
                <li><a href='../contents/index.php?bid=<?php echo $bid ?>'>Content registration / editing</a></li>
                <li class="active"><a>Test creation</a></li>
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
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-out"></span>Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">
    <form action="" method="post">
        <!-- ▼h2 -->
        <div class="h2">
            <h2>Test creation</h2>
        </div>
        <!-- ▲h2 -->

        <!-- progress -->
        <div id="progress">
            <ul class="clearfix">
                <li class="active">
                    <span class="text">Basic setting</span>
                    <span class="circle"></span>
                </li>
                <li>
                    <span class="text">Quiz creation</span>
                    <span class="circle"></span>
                </li>
                <li>
                    <span class="text">Detail confirmation</span>
                    <span class="circle"></span>
                </li>
            </ul>
        </div>

        <div id="col-quiz-control" class="clearfix">

            <!-- ▼コンテンツグループ -->
            <div id="control-box-contentsgroup">

                <div class="h3 clearfix">
                    <h3>Open range</h3>
                </div>

                <div class="body scrollerea">
                    <div id="subject-group" class="subject-group setting">
                            <!------CSV読込部分 ここから------>
                            <?php

                                echo '<ul class="accordion">' . "\n";

                                foreach($lines as $line) {
                                    $item = explode(',', $line);
                                    $item[3] = str_replace('{c}', ',', $item[3]);

                                    if($item[0] == 1) {
                                        if($flag) { echo '</ul>' . "\n" . '</li>' . "\n"; }
                                        echo '<li';
                                        if($csvRowC[$subject_section_id] == $item[1]) { echo ' class="open"'; }
                                        echo '>' . "\n";
                                        echo '<a class="togglebtn">' . $item[3] . '</a>' . "\n";
                                        echo '<ul class="togglemenu';
                                        if($csvRowC[$subject_section_id] == $item[1]) { echo ' open'; }
                                        echo '">' . "\n";
                                    }

                                    if($item[0] == 2) {
                                        $flag = 1;
                                        echo '<li';
                                        if($_GET['bid'] == $item[1]) { echo ' class="active"'; }
                                       # echo '><a href="' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $item[1] . '">' . $item[3] . '</a></li>' . "\n";
                                        echo '><a href="' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $item[1] . '&id=' . $quiz_id .'">' . $item[3] . '</a></li>' . "\n";
                                    }
                                }

                                echo '</ul>' . "\n" . '</li>' . "\n";
                            ?>
                            <!------CSV読込部分 ここまで------>
                    </div>
                </div>
            </div>
            <!-- ▲コンテンツグループ -->

            <!-- ▼詳細情報 -->
            <div id="control-box-formgroup">

                <div class="h3 clearfix">
                    <h3>Detailed information</h3>
                </div>
                <div class="body">
                    <!-- テストタイトル -->
                    <dl class="input-group">
                        <dt>Title<span class="text_limit">Within 250 characters</span></dt>
                        <dd>
                            <textarea maxlength="250" rows="2" class="movie-title" name="title"><?php echo $data['title']; ?></textarea>
                        </dd>
                        <p class="attention"><?php echo ($error_message['title']) ?: ''; ?> </p>
                    </dl>
                    <!-- 説明文 -->
                    <dl class="input-group">
                        <dt>Explanatory text<span class="text_limit">Within 250 characters</span></dt>
                        <dd>
                            <textarea maxlength="250" rows="4" name="description"><?php echo $data['description']; ?></textarea>
                        </dd>
                    </dl>
                    <!-- 終了後メッセージ -->
                    <dl class="input-group">
                        <dt>End message<span class="text_limit">Within 250 characters</span></dt>
                        <dd>
                        <textarea maxlength="250" rows="4" name="finished_message"><?php echo $data['finished_message']; ?></textarea>
                        </dd>
                    </dl>
                    <div class="clearfix">
                        <!-- 公開日 -->
                        <dl class="input-group day-start">
                            <dt>Release date</dt>
                            <dd>
                                <input type="text" class="datepicker" name="start_day" value="<?php $data['start_day'] != '0000-01-01' ? print str_replace("-", "/", $data['start_day']) : '';?>">
                            </dd>
                            <p class="attention"><?php echo ($error_message['start_day']) ?: ''; ?> </p>
                        </dl>
                        <!-- 期限日 -->
                        <dl class="input-group day-limit clearfix">
                            <dt>Deadline</dt>
                            <dd>
                                <input type="text" class="datepicker" name="last_day" value="<?php $data['last_day'] != '9999-12-31' ? print str_replace("-", "/", $data['last_day']) : '';?>">
                            </dd>
                            <p class="attention"><?php echo ($error_message['last_day']) ?: ''; ?> </p>
                        </dl>
                    </div>
                    <div class="clearfix">
                        <!-- 受講回数 -->
                        <dl class="input-group checkbox-number clearfix">
                            <dt>Number of attendance<span>※ Up to 10 times</span></dt>
                            <dd>
                                <label class="checkbox"><input type="radio" name="repeat_challenge_flg" value="0"<?php echo ($repeat_challenge_flg == 0) ? ' checked': ''; ?>><span class="icon"></span>Unlimited</label>
                                <label class="checkbox"><input type="radio" name="repeat_challenge_flg" value="1"<?php echo ($repeat_challenge_flg == 1) ? ' checked': ''; ?>><span class="icon"></span>Limited</label>
                                <input type="number" name="repeat_challenge" min="1" max="10" value="<?php echo ($data['repeat_challenge'] == 0)? 3 : $data['repeat_challenge']; ?>"> Times
                            </dd>
                        </dl>
                        <!-- 制限時間 -->
                        <dl class="input-group checkbox-number clearfix">
                            <dt>Time limit<span>※ Up to 120 minutes</span></dt>
                            <dd>
                                <label class="checkbox"><input type="radio" name="limit_time_flg" value="0"<?php echo ($limit_time_flg == 0) ? ' checked': ''; ?>><span class="icon"></span>Unlimited</label>
                                <label class="checkbox"><input type="radio" name="limit_time_flg" value="1"<?php echo ($limit_time_flg == 1) ? ' checked': ''; ?>><span class="icon"></span>Limited</label>
                                <input type="number" name="limit_time" min="1" max="120" value="<?php echo ($data['limit_time'] == 0)? 10 : $data['limit_time']; ?>"> 分
                            </dd>
                        </dl>
                        <!-- 合格点 -->
                        <dl class="input-group checkbox-number clearfix">
                            <dt>Passing score</dt>
                            <dd>
                                <label class="checkbox"><input type="radio" name="qualifying_score_flg" value="0"<?php echo ($qualifying_score_flg == 0) ? ' checked': ''; ?>><span class="icon"></span>None</label>
                                <label class="checkbox"><input type="radio" name="qualifying_score_flg" value="1"<?php echo ($qualifying_score_flg == 1) ? ' checked': ''; ?>><span class="icon"></span>Yes</label>
                                <input type="number" name="qualifying_score" min="10" max="100" value="<?php echo ($data['qualifying_score'] == 0)? 70 : $data['qualifying_score']; ?>"> point
                            </dd>
                        </dl>
                    </div>
                    <!-- 答案結果の表示 -->
                    <dl class="input-group checkbox-group">
                        <dt>Display of answer results</dt>
                        <dd>
<input type="hidden" name="average_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="average_flg" value="1"<?php echo ($data['average_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Average score</label>
<input type="hidden" name="rank_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="rank_flg" value="1"<?php echo ($data['rank_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Rank</label>
<input type="hidden" name="answer_rate_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="answer_rate_flg" value="1"<?php echo ($data['answer_rate_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Correct answer rate</label>
<input type="hidden" name="deviation_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="deviation_flg" value="1"<?php echo ($data['deviation_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Deviation value</label>
<input type="hidden" name="student_answer_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="student_answer_flg" value="1"<?php echo ($data['student_answer_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Student response</label>
<input type="hidden" name="answer_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="answer_flg" value="1"<?php echo ($data['answer_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Answer to the problem</label>
<input type="hidden" name="success_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="success_flg" value="1"<?php echo ($data['success_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Admission decision</label>
<input type="hidden" name="correct_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="correct_flg" value="1"<?php echo ($data['correct_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Correct or incorrect problem</label>
<input type="hidden" name="explain_flg" value="0">
                            <label class="checkbox"><input type="checkbox" name="explain_flg" value="1"<?php echo ($data['explain_flg'] == 1) ? ' checked': ''; ?>><span class="icon"></span>Commentary</label>
                        </dd>
                    </dl>
                </div>

            </div>
            <!-- ▲詳細情報 -->

        </div>

        <!-- 保存 -->
        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
        <input type="hidden" name="bid" value="<?php echo $bid; ?>">
        <div id="col-mainbtn" class="clearfix">
            <ul class="clearfix">
                <li class="save"><button type="submit" name="submit" value="save">Quiz creation</button></li>
            </ul>
        </div>
    </form>
    </div>
    <!-- ▲main -->
</div>
<?php echo "<pre>";print_r($data['finished_message']); echo "</pre>";?>
</body>
</html>
