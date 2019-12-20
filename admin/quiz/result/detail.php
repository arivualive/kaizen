<?php
require_once "../../../config.php";
//debug($_SESSION);
//debug($_POST);

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
if (! $quiz_id) {
    die ("I do not know the quiz number");
}

$answer_id = filter_input(INPUT_GET, 'an', FILTER_SANITIZE_SPECIAL_CHARS);
if (! $answer_id) {
    die ("The answer number is unknown.");
}

$student_id = filter_input(INPUT_GET, 'st', FILTER_SANITIZE_SPECIAL_CHARS);
if (! $student_id) {
    die ("I do not know my student number.");
}

$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

if (! $bid) {
    die ("The content number is unknown.");
}

$Curl =  new Curl($url);
$Result = new QuizResult($quiz_id, $Curl);
$quiz_data = $Result->getQuiz();

// 合格点
$success_point = $Result->qualifyingScore();

// 問題数
$query_count = $Result->countQuery();

// 制限時間
$limit_time = $Result->limitTime();

// 平均点(小数点第2を四捨五入して表示)
$average = $Result->answerAvg();

// 受験者数
$examinees = $Result->examineesNumber();

// 正解率
$correct_rate = $Result->correctRateAll();

// 標準偏差
$standard_deviation = $Result->standardDeviation();

// 合格者数
$success_count = $Result->answerSuccessful();

// 平均回答時間
$answer_time_average = $Result->answerTimeAvg();

// 受験者情報
$Student = new Student($Curl);
$Student->setStudentId($student_id);
$student_data = $Student->findStudentId();

// 受験状況
$Result->setAnswerId($answer_id);
#$rank = $Result->answerRank();
#$rank = $Result->findQuizAnswerQuizIdFirst();
#$history = $Result->findQuizAnswerStudent($student_id);

$QuizAnswer = new QuizAnswer($quiz_id, $Curl);
$QuizAnswer->setAnswerId($answer_id);
$answer = $QuizAnswer->findAnswerQueryAll();
$answer_data = $QuizAnswer->findQuizAnswerStudent($student_id);
// 2019/6/03 count関数対策
$cnt = 0;
if(is_countable($answer_data)){
  $cnt = count($answer_data);
}
//$cnt = count($answer_data);
$rank = $QuizAnswer->findQuizAnswerRankStudent($student_id);

// 問題
$query = $Result->getQuery();

// 問題の選択肢
$selection = $Result->getSelectionQueryId();
#$selection = $Result->getSelection();

// あなたの回答
#$query_id = 297;
#$choice = $Result->choice($answer_id, $query_id);

// 無回答
$no_answer = $Result->sumNoAnswer();

// 問題毎の正解率
$query_correct_rate = $Result->correctRateQuery();

//debug($quiz_data);
//debug($student_data);
//debug($answer_data);
//debug($rank);
//debug($cnt);
//debug($query);
//debug($selection);
//debug($answer);
//debug($no_answer);
//debug($query_correct_rate);

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS Administrator</title>
    <!-- favicon -->
    <link rel="shortcut icon" href="../images/favicon.ico">
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap-reboot.css">
    <link rel="stylesheet" type="text/css" href="../../css/icon-font.css">
    <link rel="stylesheet" type="text/css" href="../../css/common.css">
    <link rel="stylesheet" type="text/css" href="../../css/quiz.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.js"></script>
    <script src="../../js/script.js"></script>
</head>
<body>

<div id="wrap">

    <!-- ▼navgation -->
    <div id="nav-fixed">
        <!-- ▼h1 -->
        <div class="brand">
            <a href="#">
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
                        <a href="../../info.php"><span class="icon-main-home"></span>HOME</a>
                    </li>
                    <li>
                        <a class="togglebtn"><span class="icon-user-add"></span>Affiliation / ID setting</a>
                        <ul class="togglemenu">
                            <li><a href="../../access/users.php">Affiliation group setting</a></li>
                            <li><a href="#">Instructor ID setting</a></li>
                            <li><a href="../../user/student.php">Student ID setting</a></li>
                        </ul>
                    </li>
                    <li class="open">
                        <a class="togglebtn"><span class="icon-movie-manage"></span>Content setting</a>
                        <ul class="togglemenu open">
                            <li><a href="../../access/contents.php">Content group setting</a></li>
                            <li><a href="../../contents/index.php" class="active">Content registration / editing</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="../../access/contents-control.php"><span class="icon-movie-set"></span>Target setting</a>
                    </li>
                    <li>
                        <a class="togglebtn"><span class="icon-graph"></span>Attendance status</a>
                        <ul class="togglemenu">
                            <li><a href="../../history/index.php">Confirmation from the student</a></li>
                            <li><a href="../../dateWiseViewing/index.php">Check from video class</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="../../message/message_list.php" class="active"><span class="icon-main-message"></span>message</a>
                    </li>
                    <li>
                        <a href="../../help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>help</a>
                    </li>
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
                <li><a>HOME</a></li>
                <li>Content setting</li>
                <li><a href='../../contents/index.php?bid=<?php echo $bid ?>'>Content registration / editing</a></li>
                <li><a href="index.php?id=<?php echo $quiz_id; ?>&bid=<?php echo $bid; ?>">Test summary</a></li>
                <li class="active"><a>Test summary(Students)</a></li>
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
                <li role="presentation"><a href="../../account/index.php"><span class="icon-lock"></span>Account Setting</a></li>
                <li role="presentation"><a href="../../auth/logout.php"><span class="icon-sign-out"></span>Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>Test summary(Students)</h2>
        </div>
        <!-- ▲h2 -->

        <!-- title -->
        <div id="box-title">
            <dl class="title">
                <dt>Title</dt>
                <dd><?php echo $quiz_data['title']; ?></dd>
            </dl>
            <dl class="btns">
                <a class="total"  href="index.php?id=<?php echo $quiz_id; ?>&bid=<?php echo $bid; ?>">Total count result</a>
            </dl>
        </div>

        <!-- 受講者情報 -->
        <div class="box-userinfo clearfix">
            <dl>
                <dt>ID</dt>
                <dd><?php echo $student_data['student_id']; ?></dd>
            </dl>
            <dl>
                <dt>name</dt>
                <dd><?php echo $student_data['student_name']; ?></dd>
            </dl>
            <dl>
                <dt>Number of attendance</dt>
                <dd><?php echo $cnt; ?></dd>
            </dl>
        </div>

        <div class="answeruser-detail">
                    <table class="answerusers-list detail">
                        <tr>
                            <th class="count">No.</th>
                            <th class="points">Score</th>
                            <th class="rank">Rank</th>
                            <th class="sf">Pass or fail</th>
                            <th class="rate">Correct answer rate(%)</th>
                            <th class="time">Answer time</th>
                            <th class="day">Examination date</th>
                            <th class="detail"></th>
                        </tr>
                        <?php foreach ($rank as $key => $value): ?>
                        <?php if ($answer_id == $value['answer_id']): ?>
                        <tr class="active">
                        <?php else: ?>
                        <tr>
                        <?php endif; ?>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $value['total_score']; ?></td>
                            <td><?php echo $rank[$key]['rank']; ?></td>
                            <?php if($Result->isSuccess($value['total_score']) == '―'): ?>
                            <td>ー</td>
                            <?php else: ?>
                            <?php if ($Result->isSuccess($value['total_score']) == 'Pass'): ?>
                            <td><span class="s">Pass</span></td>
                            <?php else: ?>
                            <td><span class="f">Failure</span></td>
                            <?php endif; ?>
                            <?php endif; ?>
<?php
    $QuizAnswer->setAnswerId($value['answer_id']);
    $answer_data = $QuizAnswer->findQuizAnswer();
?>
                            <td><?php echo $answer_data['correct_answer_rate']; ?></td>
                            <td><?php echo $QuizAnswer->timeFormat($answer_data['answer_time']); ?></td>
                            <td><?php echo $answer_data['register_datetime']; ?></td>
                            <td><a class="detail" href="detail.php?id=<?php echo $quiz_id; ?>&an=<?php echo $value['answer_id']; ?>&st=<?php echo $value['student_id']; ?>&bid=<?php echo $bid; ?>">Details</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
        </div>
        <!-- 各問題 -->

        <div id="question">

            <!-- 問題 -->
            <?php foreach ($query as $key => $value): ?>
            <div class="each-question">
                <div class="number-erea">
                    <div class="in">
                        <p class="number"><span>Q</span><span><?php echo $key + 1; ?></span></p>
                    </div>
                </div>
                <div class="info-erea report">
                    <div class="in">
                        <!-- 問題文 -->
                        <dl class="sentence">
                            <dt>problem
                                <p class="points">〔<?php echo $value['score']; ?>point〕</p><!-- 配点 -->
                            </dt>
                            <dd><?php echo $value['query_text']; ?></dd>
                        </dl>
                        <!-- 正誤 -->
                        <dl class="tf">
                            <dt>Right and wrong</dt>
<?php
    $QuizAnswer->setAnswerId($answer_id);
    $flag = $QuizAnswer->findFlgRightQueryId($value['query_id']);
?>
                            <?php if ($flag['flg_right'] == 1): ?>
                            <dd><span class="t"></span></dd>
                            <?php else: ?>
                            <dd><span class="f"></span></dd>
                            <?php endif; ?>
                        </dl>
                        <!-- 正解率 -->
                        <!-- 正解率の計算がおかしい為、いったん非表示化(各回答数の取得数字が計算式に応用できない。ロジックの変更が必要。)
                        <dl class="rate">
                            <dt>全体正解率</dt>
                            <dd><span><?php echo (isset($query_correct_rate[$key]['query_rate'])) ? $query_correct_rate[$key]['query_rate']: '0'; ?></span> %</dd>
                        </dl>
                         -->
                        <dl class="rate">
                            <dt>Overall accuracy rate</dt>
                            <?php
                                $answer_count['count'] = 0;
                                $answer_count['correct'] = 0;
                                foreach ($selection[$key] as $i => $item):
                                    if ($item['correct_flg'] == 1) {
                                        $answer_count['count'] += $Result->selectionAnswerCount($item['selection_id'])['answer_count'];
                                        $answer_count['correct'] += $Result->selectionAnswerCount($item['selection_id'])['answer_count'];
                                    } else {
                                        $answer_count['count'] += $Result->selectionAnswerCount($item['selection_id'])['answer_count'];
                                    }
                                endforeach;
                                $answer_count['count'] += $no_answer[$key]['no_answer_sum'];
                            ?>
                            <dd><span><?php echo ($answer_count['count'] != 0 && $answer_count['correct'] != 0) ? round($answer_count['correct'] / $answer_count['count'] * 100, 1): '0'; ?></span> %</dd>
                        </dl>
                    </div>

                    <div class="navbar-collapse collapse" id="quiz-report<?php echo $key; ?>">
                        <div class="result clearfix">
                           <div class="w-50">
                           <?php $choice = $Result->queryChoice($value['query_id']); ?>
                                <table class="select-answer">
                                    <tr>
                                        <th class="correct">Correct answer</th>
                                        <th>item name</th>
                                        <th class="user-select">answer</th>
                                    </tr>
                                   <?php foreach ((array) $selection[$key] as $i => $item): ?>
                                    <tr>
                                    <?php if ($item['correct_flg'] == 1): ?>
                                        <td><span class="correct"></span></td>
                                    <?php else: ?>
                                        <td></td>
                                    <?php endif; ?>
                                        <td><?php echo $item['text']; ?></td>
                                        <td>
                                    <?php foreach ($choice as $select): ?>
                                    <?php if ($select['selection_id'] == $item['selection_id']) : ?>
                                        <span class="check"></span>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                        </td>
                                    </tr>
                                 <?php endforeach; ?>
                                    <tr>
                                        <td>―</td>
                                        <td>No answer</td>
                                        <?php $noAnswer = $QuizAnswer->findNoAnswer($value['query_id']); ?>
                                    <?php if ($noAnswer['flg_no_answer'] == 1): ?>
                                        <td><span class="check"></span></td>
                                    <?php else:?>
                                        <td></td>
                                    <?php endif; ?>
                                    </tr>
                                </table>
                            </div>
                            <div class="w-50 graph">

                            </div>
                        </div>
                        <div class="reference clearfix">
                            <div class="w-50 img">
                                <dt>Reference image</dt>
                                <dd>
                                <?php
                                   $image = $value['image_file_name'];
                                   $path = $value['quiz_id'] . '_' . $value['query_id'] . '.deploy';
                                   $image_file_path = $url2 . 'file/image/' . $path;
                                ?>
                                <?php if (file_exists($image_file_path) || $image != ''): ?>
                                   <img src="<?php echo $image_file_path; ?>" alt="image" />
                                   <p><?php echo $value['image_file_name']; ?></p>
                                <?php else: ?>
                                   <p>No picture</p>
                                <?php endif; ?>
                                </dd>
                            </div>
                            <!--
                            <div class="w-50 voice">
                                <dt>参考音声</dt>
                                <dd> ここに音声を入れてください </dd>
                            </div>
                            -->
                        </div>
                        <div class="commentary">
                            <dl>
                                <dt>Commentary on incorrect answer
                                <!--    <a class="movie">解説動画</a> -->
                                </dt>
                                <dd><?php echo $value['description']; ?></dd>
                            </dl>
                        </div>
                    </div>
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#quiz-report<?php echo $key; ?>" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">Details</button>
                </div>
            </div>
            <?php endforeach; ?>

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
