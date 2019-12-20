<?php
require_once "../../../config.php";

//debug($_SESSION);

//------CSV読込部分 ここから------
error_reporting(~E_NOTICE);
$path = '../../../library/category/'; //カテゴリーPHPライブラリの場所（※必須）
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

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

if (! $quiz_id) {
    die ("I do not know the quiz number");
}

$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

if (! $bid) {
    die ("The content number is unknown.");
}

$student_id = '';
$curl = new Curl($url);
$Result = new QuizResult($quiz_id, $curl);
$View = new QuizResultView($Result);
$Answer = new QuizAnswer($quiz_id, $curl);

// クイズ基本データ
$quiz_data = $Result->getQuiz();

if (! $quiz_data) {
    die ('Data is unknown');
}

// 登録者名
$Admin = new Admin($curl);
$Admin->setAdminId($quiz_data['register_id']);
$admin_data = $Admin->findAdminId();

// 受講者名
$Student = new Student($curl);

// 合格点
$success_point = $Result->qualifyingScore();

// 問題数
$query_count = $Result->countQuery();

// 制限時間
$limit_time = $Result->limitTime();

// 平均点(小数点第2を四捨五入して表示)
$average = $Result->answerAvg();

// 受験者数
$examinees = $Result->examineesNumberDist();
//debug($examinees);

// 正解率
$correct_rate = $Result->correctRateAll();
//debug($correct_rate);

// 標準偏差
$standard_deviation = $Result->standardDeviation();

// 合格者数
$success_count = $Result->answerSuccessful();

// 平均回答時間
$answer_time_average = $Result->answerTimeAvg();

// クイズ番号内のランキング
$answer_data = $Answer->findQuizAnswerQuizId();

$data['quiz_id'] = $quiz_id;
$student_data = array();
$student_data = $Answer->findQuizAnswerDistinctStudentId($data);

$rank = array();
foreach ($student_data as $value) {
    $rank[] = $Answer->findQuizAnswerMaxScore($value);
}

foreach ($rank as $key => $value) {
    $total_score[$key] = $value['total_score'];
    $answer_id[$key] = $value['answer_id'];
}

if ($rank) {
    array_multisort($total_score, SORT_DESC, $answer_id, SORT_ASC, $rank);
}

//debug($rank);

// 問題
$query = $Result->getQuery();

// 問題の選択肢
$selection = $Result->getQueryOfSelection();
#$selection_data = $QueryObj->getQuerySelection();

// 選択肢
#$selection = $Result->getSelection();

// 無回答
$no_answer = $Result->sumNoAnswer();

// 問題毎の正解率
$query_correct_rate = $Result->correctRateQuery();

// IDの復号
$crypt = new StringEncrypt;

#$selection_choice_count = selectionAnswerCount($selection_id);

//debug($admin_data);
//debug($quiz_data);
//debug($query);
//debug($selection);
//debug($no_answer);
//debug($query_correct_rate);
#$cnt = $QuizAnswer->findQuizAnswerStudent($student_id);
//debug( $quiz_id );
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
    <script src="../js/index.js"></script>
    <script type="text/javascript" src="../../../js/papaparse.min.js"></script>
</head>
<body>

<div id="wrap">

    <!-- ▼navgation -->
    <div id="nav-fixed">
        <!-- ▼h1 -->
        <div class="brand">
            <a href="../../info.php">
                <h1>
                    <div class="img_h1"><img src="../../images/logo.jpg" alt="ThinkBoard LMS"></div>
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
                <li><a href="../../info.php">HOME</a></li>
                <li>Content setting</li>
                <li><a href='../../contents/index.php?bid=<?php echo $bid ?>'>Content registration / editing</a></li>
                <li class="active"><a>Test summary</a></li>
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
            <h2>Test summary</h2>
        </div>
        <!-- ▲h2 -->

        <!-- title -->
        <div id="box-title">
            <dl class="title">
                <dt>Title</dt>
                <dd><?php echo $quiz_data['title']; ?><dd>
            </dl>
            <dl class="btns">
                <a class="edit">Edit</a>
                <button id="csv_results" class="csv" data-id=<?php echo $quiz_id; ?>>CSV output</button>
            </dl>
            <form id="post-form" method="post">
              <input type="hidden" name="csv_name" value="student_data">
              <input id="key_data" type="hidden" name="export_csv">
            </form>
        </div>

        <!-- 数字 -->
        <div id="box-value" class="clearfix">
            <dl>
                <dt>The number of questions</dt>
                <dd><span><?php echo $query_count; ?></span>Q</dd>
            </dl>
            <dl>
                <dt>Answerer</dt>
                <dd><span><?php echo $examinees['student_count']; ?></span>person</dd>
            </dl>
            <dl>
                <dt>Pass count</dt>
                <dd><span class="o"><?php echo $success_count; ?></span>Times</dd>
            </dl>
            <dl>
                <dt>Average answer time/Time limit</dt>
                <dd><span><?php echo $answer_time_average; ?></span> / <?php echo $limit_time; ?></dd>
            </dl>
            <!--
            <dl>
                <dt>総点数</dt>
                <dd><span>??</span>点</dd>
            </dl>
            -->
            <dl>
                <dt>Average score/Passing score</dt>
                <dd><span class="o"><?php echo $average; ?></span>point / <?php echo $success_point; ?></dd>
            </dl>
            <dl>
                <dt>Correct answer rate</dt>
                <dd><span class="o"><?php echo $correct_rate; ?></span>%</dd>
            </dl>
            <dl>
                <dt>standard deviation</dt>
                <dd><span><?php echo $standard_deviation; ?></span></dd>
            </dl>
        </div>

        <!-- 基本情報 -->
        <div id="check-baseinfo" class="clearfix">
        <!------CSV読込部分 ここから------>
        <!-- 公開範囲 -->
        <div class="col-contentsgroup-check">
            <dl>
                <dt class="clearfix"><p>Open range</p></dt>
                <dd>
<?php
    foreach($lines as $line) {
        $item = explode(',', $line);
        $item[3] = str_replace('{c}', ',', $item[3]);

        if($item[0] == 1) {
            if($csvRowC[$bid] == $item[1]) { echo '<span>' . $item[3] . '</span>' . "\n"; }
        }
        if($item[0] == 2) {
            if($bid == $item[1]) { echo '<span>' . $item[3] . '</span>' . "\n"; }
        }
    }
?>
                </dd>
            </dl>
        </div>
        <!------CSV読込部分 ここまで------>

            <!-- テーブル -->
            <div class="col-baseinfo-check">
                <table class="baseinfo">
                    <tr>
                        <th>Explanatory text</th>
                        <td><?php echo $quiz_data['description']; ?></td>
                    </tr>
                    <tr>
                        <th>End message</th>
                        <td><?php echo $quiz_data['finished_message']; ?></td>
                    </tr>
                    <tr>
                        <th>Release date setting</th>
                        <?php if ($quiz_data['start_day'] == '0000-01-01' && $quiz_data['last_day'] == '9999-12-31'): ?>
                        <td>Indefinite period</td>
                        <?php else: ?>
                        <td><?php echo $quiz_data['start_day']; ?>　～　<?php echo $quiz_data['last_day']; ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <th>Registration date</th>
                        <td><?php echo $quiz_data['register_datetime']; ?></td>
                    </tr>
                    <tr>
                        <th>Registered person</th>
                        <td><?php echo $admin_data['admin_name']; ?></td>
                    </tr>
                </table>
            </div>

        </div>

        <!-- 解答者・未解答者一覧 -->

        <div id="answerusers-list">

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#answerusers" role="tab" aria-controls="nav-home" aria-selected="true">Answerer</a>
<!--
                    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#notanswerusers" role="tab" aria-controls="nav-profile" aria-selected="false">未解答者</a>
-->
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">

                <!-- 解答者 -->
                <div class="tab-pane fade show active" id="answerusers" role="tabpanel" aria-labelledby="nav-home-tab">
                    <table class="answerusers-list">
                        <tr>
                            <th class="id">ID</th>
                            <th>Name</th>
                            <th>Score</th>
                       <!-- <th class="rank">順位</th>
                       -->
                            <th class="sf">Pass or fail</th>
                            <th class="count">Number of attendance</th>
                            <th class="rate">Correct answer rate(%)</th>
                            <th class="time">Answer time</th>
                            <th class="day">Examination date</th>
                            <th class="detail"></th>
                        </tr>
                        <?php foreach ($rank as $value): ?>
                        <tr>
                            <?php
                               $Student->setStudentId($value['student_id']);
                               $student_data = $Student->findStudentId();
                               $login_id = $crypt->decrypt($student_data['id']);
                             ?>
                            <td class="id"><?php echo substr($login_id, 3); ?></td>
                            <td><?php echo $student_data['student_name']; ?></td>
                            <td><?php echo $value['total_score']; ?></td>
                         <!--   <td><?php echo $value['rank']; ?></td>
                         -->
                            <?php if ($Result->isSuccess($value['total_score']) == '―') :?>
                            <td><span>―</span></td>
                            <?php else: ?>
                            <?php if ($Result->isSuccess($value['total_score']) == 'Pass'): ?>
                            <td><span class="s">Pass</span></td>
                            <?php else: ?>
                            <td><span class="f">Failure</span></td>
                            <?php endif; ?>
                            <?php endif; ?>
                            <?php $cnt = $Answer->findQuizAnswerStudent($value['student_id']);?>
                            <td><?php /*// 2019/6/03 count関数対策*/if(is_countable($cnt)){
                              echo count($cnt);
                            }/*echo count($cnt); */?></td>
                            <td><?php echo $value['correct_answer_rate']; ?></td>
                            <td><?php echo $Answer->timeFormat($value['answer_time']); ?></td>
                         <!--   <td><?php echo round($value['answer_time'] / 60, 2); ?></td>
                         -->
                            <td><?php echo $value['register_datetime']; ?></td>
                            <td><a class="detail" href="detail.php?id=<?php echo $quiz_id; ?>&an=<?php echo $value['answer_id']; ?>&st=<?php echo $value['student_id']; ?>&bid=<?php echo $bid; ?>">Details</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="pager-userslist clearfix">
                        <a class="back"></a><!-- 前 -->
                        <p>1/3</p><!-- 現在のページ/全ページ数 -->
                        <a class="next"></a><!-- 次 -->
                    </div>

                </div>

                <!-- 未解答者
                <div class="tab-pane fade" id="notanswerusers" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <table class="answerusers-list">
                        <tr>
                            <th class="id">ID</th>
                            <th>名前</th>
                        </tr>
                        <tr>
                            <td>1234absd</td>
                            <td>窪薗かな</td>
                        </tr>
                    </table>
                    <div class="pager-userslist clearfix">
                        <a class="back"></a>
                        <p>1/3</p>
                        <a class="next"></a>
                    </div>
                </div>
                    -->
            </div>

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
                                <p class="points">〔<?php echo $value['score']; ?>point〕</p>
                            <dd style="white-space:pre-wrap;"><?php echo $value['query_text']; ?></dd>
                        </dl>
                        <!-- 正解率 -->
                        <!-- 正解率の計算がおかしい為、いったん非表示化(各回答数の取得数字が計算式に応用できない。ロジックの変更が必要。)
                            <dl class="rate">
                                <dt>正解率</dt>
                                <dd><span><?php echo (isset($query_correct_rate[$key]['query_rate'])) ? $query_correct_rate[$key]['query_rate']: '0'; ?></span> %</dd>
                            </dl>
                         -->
                        <!-- 正解率 -->
                        <dl class="rate">
                            <dt>Correct answer rate</dt>
                            <!-- <dd><span><?php echo (isset($query_correct_rate[$key]['query_rate'])) ? $query_correct_rate[$key]['query_rate']: '0'; ?></span> %</dd> -->
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
                                <table class="select-answer">
                                    <tr>
                                        <th class="correct">Correct answer</th>
                                        <th>item name</th>
                                        <th class="answer-value">Number of responses</th>
                                    </tr>
                                    <?php foreach ($selection[$key] as $i => $item): ?>
                                    <tr>
                                        <td><?php echo ($item['correct_flg']) ? '<span class="correct"></span>' : ''; ?></td>
                                        <td><?php echo $item['text']; ?></td>
                                            <?php $data = $Result->selectionAnswerCount($item['selection_id']); ?>
                                        <td><?php echo $data['answer_count']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td></td>
                                        <td>No answer</td>
                                        <td><?php echo $no_answer[$key]['no_answer_sum']; ?></td>
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
                              <!--      <a class="movie">解説動画</a> -->
                                </dt>
                                <dd style="white-space:pre-wrap;"><?php echo $value['description'];?></dd>
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
