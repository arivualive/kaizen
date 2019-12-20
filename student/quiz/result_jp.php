<?php
require_once "../../config.php";
#debug($_SESSION);
#debug($_POST);

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

if (! $quiz_id) {
    die ("クイズ番号が不明です。");
}

$answer_id = filter_input(INPUT_GET, 'an', FILTER_SANITIZE_SPECIAL_CHARS);

if (! $answer_id) {
    die ("アンサー番号が不明です。");
}

$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

if (! $bid) {
    die ("コンテンツ番号が不明です。");
}

$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

$Curl =  new Curl($url);
$Result = new QuizResult($quiz_id, $Curl);
$quiz_data = $Result->getQuiz();

$student_id = $_SESSION['auth']['student_id'];
$Result->setStudentId($student_id);
$Result->setAnswerId($answer_id);

$View = new QuizResultView($Result);

// 合格点
$success_point = $Result->qualifyingScore();

// 合否
$is_success = $View->isSuccess();

// 問題数
$query_count = $Result->countQuery();

// 回答時間
$answer_time = $Result->answerTime();

// 制限時間
$limit_time = $Result->limitTime();
$limit_time2 = $quiz_data['limit_time'];
#$seconds = round($limit_time / 60, 2);

#debug($limit_time);
#debug($limit_time2);

// 平均点(小数点第2を四捨五入して表示)
$average = $Result->answerAvg();

// 受験者数
$examinees = $Result->examineesNumber();

// 正解率
$correct_rate = $Result->correctRate();

// 全体の正解率
$rate_all = $Result->correctRateAll();

// 偏差値
$deviation = $Result->deviation();

// 標準偏差
$standard_deviation = $Result->standardDeviation();

// 合格者数
$success_count = $Result->answerSuccessful();

// 平均回答時間
$answer_time_average = $Result->answerTimeAvg();

// 受験者情報
$student_id = $_SESSION['auth']['student_id'];
$Student = new StudentAccess($student_id, $Curl);
$student_data = $Student->getStudent();

// 受験状況
$rank = $Result->answerRank();
$history_data = $Result->findQuizAnswerStudent();
$history = $View->quizHistory($bid);
$register_datetime = $View->answerRegistDate();
$total_score = $View->totalScore();

// 問題
$query_data = $Result->getQuery();
#debug($query_data);

// 問題の選択肢
$selection = $Result->getQueryOfSelection();
#$selection_data = $QueryObj->getQuerySelection();

// 選択肢
#$selection_data = $Result->getSelection();
$selection_data = $Result->getSelectionQueryId();
#debug($selection_data);

#$data = $View->choiceSelection();
#debug($data);

// あなたの回答
foreach ($query_data as $value) {
    $query_choice[] = $Result->queryChoice($value['query_id']);
}
#debug($query_choice);

// 無回答
$no_answer = $Result->sumNoAnswer();

// 問題毎の正解率
$query_correct_rate = $Result->correctRateQuery();

// 回答形式
$queryType = $Result->queryType();

// 正解 or 不正解(不使用)
#$data = $Result->answerQuery();
$correct_jp = $View->showCorrect($Result->answerQuery());
#debug($correct_jp);

// 正誤
$Answer = new QuizAnswer($quiz_id, $Curl);
$Answer->setAnswerId($answer_id);

/*
$quiz_data['answer_flg'] = 0;
$quiz_data['explain_flg'] = 0;
$quiz_data['average_flg'] = 0;
$quiz_data['rank_flg'] = 0;
$quiz_data['student_answer_flg'] = 0;
$quiz_data['answer_rate_flg'] = 0;
$quiz_data['success_flg'] = 0;
$quiz_data['deviation_flg'] = 0;
$quiz_data['correct_flg'] = 0;
 */

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
                        <h1><img src="../images/logo.jpg" alt="ThinkBoard LMS"></h1>
                    </a>
                </div>
                <!-- sub menu -->
                <div class="header-submenu">
                    <div class="btn-userinfomation dropdown">
                        <a href="#" id="dropdownMenu-userinfo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <p class="erea-icon"><span class="icon-user-student"></span></p>
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p>
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
                        <li>
                            <a href="../info.php"><span>TOP</span></a>
                        </li>
                        <li class="active">
                        <a href="../contentslist.php?bid=<?php echo $bid; ?>"><span>講義受講</span></a>
                        </li>
                        <li>
                            <a href="../message/message_list.php"><span>メッセージ</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="container-maincontents" class="container-maincontents clearfix">
        <div id="quiz">

            <!-- ページタイトル -->
            <div class="quiz-top clearfix">
                <p class="text">テスト結果</p>
                <ul class="btns">
                <li><a href="../contentslist.php?bid=<?php echo $bid; ?>">講義一覧へ戻る</a></li>
                </ul>
            </div>

            <!-- メインボックス -->
            <div class="quiz-box">

                <!-- head -->
                <div class="quiz-head">
                    <div class="quiz-title">
                        <p><?php echo $quiz_data['title']; ?>
                        <?php
                        if ($quiz_data['description'] != '') {
                            printf('| <span style="font-weight:normal">%s</span>', $quiz_data['description']);
                        }
                        ?>
                        </p>
                    </div>
                </div>
                <!-- body -->
                <div class="quiz-body">
                    <div class="quiz-body-result">
                        <!-- テストの各受講回数 -->
                        <div class="quiz-count-select navbar-expand-lg">
                            <p class="head">他の受講回</p>
                            <a class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span>他の受講回</span>
                            </a>
                            <ul class="scrol collapse navbar-collapse clearfix" id="navbarSupportedContent">
                                <?php foreach ((array) $history as $item): ?>
                                    <?php echo $item; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!--点数-->
                        <div class="quiz-detail-points">
                            <p class="study-count"></p>
                            <p class="study-day">受験日時：<?php echo $register_datetime; ?></p>
                            <ul class="box-l clearfix">
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">得点 / 合格点</p>
                                        <p class="value"><big><?php echo $total_score; ?></big>点/
<?php echo $success_point; ?></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">合否</p>
                                    <?php if ($quiz_data['success_flg'] == 1): ?>
                                        <p class="value"><big><?php echo $is_success; ?></big></p>
                                    <?php else: ?>
                                        <p class="value"><big>ー</big></p>
                                    <?php endif; ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">順位 / 受験回数</p>
                                    <?php if ($quiz_data['rank_flg'] == 1): ?>
                                        <p class="value"><big><?php echo $rank['rank']; ?></big> / <?php echo $examinees; ?>回</p>
                                    <?php else: ?>
                                        <p class="value"><big>ー</big></p>
                                    <?php endif; ?>
                                    </div>
                                </li>
                            </ul>
                            <ul class="box-m clearfix">
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">平均点</p>
                                    <?php if ($quiz_data['average_flg'] == 1): ?>
                                        <p class="value"><?php echo $average; ?>点</p>
                                    <?php else: ?>
                                        <p class="value">ー</p>
                                    <?php endif; ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">合格回数</p>
                                        <p class="value"><?php echo $success_count; ?>回</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">偏差値</p>
                                    <?php if ($quiz_data['deviation_flg'] == 1): ?>
                                        <p class="value"><?php echo $deviation; ?></p>
                                    <?php else: ?>
                                        <p class="value">ー</p>
                                    <?php endif; ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">標準偏差</p>
                                        <p class="value"><?php echo $standard_deviation; ?></p>
                                    </div>
                                </li>
                            </ul>
                            <ul class="box-m clearfix">
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">正解率</p>
                                    <?php if ($quiz_data['answer_rate_flg'] == 1): ?>
                                        <p class="value"><?php echo $correct_rate; ?>%</p>
                                    <?php else: ?>
                                        <p class="value">ー</p>
                                    <?php endif; ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">全体の正解率</p>
                                        <p class="value"><?php echo $rate_all; ?>%</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">解答時間/制限時間</p>
                                        <p class="value"><?php echo $answer_time; ?> / <small><?php echo $limit_time; ?></small></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="in">
                                        <p class="itemtitle">全体の平均解答時間</p>
                                        <p class="value"><?php echo $answer_time_average; ?></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
           <?php for($i = 0; $query_count > $i; $i++): ?>
               <?php $num = $i + 1; ?>
            <!-- 問題 -->
            <div class="answer-group">
                <!-- 各問題 -->
                <div class="answer-listitem">
                    <div class="head">
                        <div class="head-top clearfix">
                            <p class="number">問題<?php echo $num; ?>&emsp;(<?php echo $query_data[$i]['score']; ?>点)</p>
                            <?php $flg = $Answer->findFlgRightQueryId($query_data[$i]['query_id']); ?>
                           <?php if ($quiz_data['correct_flg'] == 1): ?>
                            <?php if ($flg['flg_right'] == 1): ?>
                              <p class="result correct">正解</p>
                            <?php else: ?>
                              <p class="result incorrect">不正解</p>
                            <?php endif; ?>
                           <?php endif; ?>
                        </div>
                        <div class="head-bottom">
                            <p style="white-space:pre-wrap;"><?php echo $query_data[$i]['query_text']; ?></p>
                            <?php #debug($query_data[$i]);
                            ?>
                        </div>
                    </div>

                    <?php $choice = $Result->queryChoice($query_data[$i]['query_id']); ?>
                    <?php #debug($choice); ?>
                    <div class="body">
                        <div class="clearfix">
                            <!-- 解答 -->
                            <div class="answer-selected">
                               <p class="type"><?php echo $queryType[$query_data[$i]['query_type']]['type_jp']; ?></p>
                               <table>
                                    <?php foreach ($selection_data[$i] as $key => $values): ?>
                                        <tr>
                                       <?php $flg = 0; ?>
                                       <?php if ($values['query_id'] == $query_data[$i]['query_id']): ?>
                                        <?php if ($choice): ?>
                                          <?php foreach ($choice as $item): ?>
                                            <?php if ($item['selection_id'] == $values['selection_id']) : ?>
                                              <?php if ($quiz_data['student_answer_flg'] == 1) : ?>
                                                <td class="user-answer"><span style="font-size:80%;">あなたの解答</span></td>
                                              <?php else: ?>
                                                <td></td>
                                              <?php endif; ?>
                                                <?php $flg = 1; ?>
                                            <?php endif; ?>
                                          <?php endforeach; ?>
                                        <?php endif; ?>
                                            <?php if ($flg == 0): ?>
                                                <td>&emsp;</td>
                                            <?php endif; ?>
                                        <?php if ($quiz_data['answer_flg'] == 1): ?>
                                            <?php if ($values['correct_flg'] == 1): ?>
                                                <td class="correct">◯</td>
                                            <?php else: ?>
                                                <td class="incorrect">×</td>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <td>&nbsp;</td>
                                        <?php endif; ?>
                                            <td><?php echo $values['text']; ?></td>
                                       <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                       <tr>
                                      <?php if (! $choice) : ?>
                                        <?php if ($quiz_data['student_answer_flg'] == 1) : ?>
                                        <td class="user-answer"><span style="font-size:80%;">あなたの解答</span></td>
                                        <?php else: ?>
                                        <td></td>
                                        <?php endif; ?>
                                      <?php else: ?>
                                        <td></td>
                                      <?php endif; ?>
                                        <td>―</td>
                                        <td>未解答</td>
                                    </tr>
                               </table>
                            </div>
                            <!-- グラフ -->
                            <!--
                            <div class="graph">
                                <p class="percent">全体の正解率<span><?php echo (isset($query_correct_rate[$i]['query_rate'])) ? $query_correct_rate[$i]['query_rate'] : ''; ?>%</span></p>
                                <div class="graph-image"><img src=""></div>
                            </div>
                             -->
                            <?php
                                $answer_count['count'] = 0;
                                $answer_count['correct'] = 0;
                                foreach ($selection[$i] as $key => $item):
                                    if ($item['correct_flg'] == 1) {
                                        $answer_count['count'] += $Result->selectionAnswerCount($item['selection_id'])['answer_count'];
                                        $answer_count['correct'] += $Result->selectionAnswerCount($item['selection_id'])['answer_count'];
                                    } else {
                                        $answer_count['count'] += $Result->selectionAnswerCount($item['selection_id'])['answer_count'];
                                    }
                                endforeach;
                                $answer_count['count'] += $no_answer[$i]['no_answer_sum'];
                            ?>
                            <div class="graph">
                                <p class="percent">全体の正解率<span><?php echo ($answer_count['count'] != 0 && $answer_count['correct'] != 0) ? round($answer_count['correct'] / $answer_count['count'] * 100, 1): '0'; ?>%</span></p>
                                <div class="graph-image"><img src=""></div>
                            </div>
                            <div class="graph">
                            <?php
                                $image = $value['image_file_name'];
                                $path = $value['quiz_id'] . '_' . $value['query_id'] . '.deploy';
                                $image_file_path = $url2 . 'file/image/' . $path;
                             ?>
                             <?php if (file_exists($image_file_path) || $image != ''): ?>
                                <img src="<?php echo $image_file_path; ?>" width="100%" alt="画像" />
                                <p><?php echo $value['image_file_name']; ?></p>
                             <?php else: ?>
                                <p>画像なし</p>
                             <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($quiz_data['explain_flg'] == 1): ?>
                        <div class="commentary">
                            <dl class="text">
                                <dt>解説</dt>
                                <dd style="white-space:pre-wrap;"><?php echo nl2br($query_data[$i]['description']); ?></dd>
                            </dl>
                            <!--
                            <dl class="movie">
                                <dt>解説動画</dt>
                                <dd><button><span class="contents-title">コンテンツ名</span><span class="subject-group">科目名&gt;講義名</span></button></dd>
                                <dd><button><span class="contents-title">コンテンツ名</span><span class="subject-group">科目名&gt;講義名</span></button></dd>
                            </dl>
                            -->
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

</body>
</html>
