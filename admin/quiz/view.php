<?php
require_once "../../config.php";
require_once "../../library/permission.php";

//debug($_SESSION);

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

// request
$quiz_id = filter_input(INPUT_GET, "id");
$bid = filter_input(INPUT_GET, "bid");

$curl = new Curl($url);
$Quiz = new Quiz($quiz_id, $curl);

//debug($_POST);
$result = false;

if ('back' == filter_input(INPUT_POST, "submit")) {
    $Quiz->redirect("../contents/index.php?bid=$bid");
}


$quiz_data = $Quiz->getQuiz();

//debug($quiz_data);
$result_show = array();

// コンテンツカテゴリの種類を取得
$category_data = array();
/*
$data = array(
    'repository' => 'ContentsCategoryRepository',
    'method' => 'findContentsCategoryAll',
    'params' => array()
);
$category_data = $curl->send($data, $curl);

$category_name = array();
foreach ($category_data as $value) {
    $category_name[$value['contents_category_id']] = $value['display_name'];
}

//debug($category_name);
*/

// 平均点を表示するか
$result_show[0] = ($quiz_data['average_flg'] == 1) ? '<i class="fa fa-check"></i>Average score' : '<i class="fa fa-times"></i>Average score';

// 順位を表示するか
$result_show[1] = ($quiz_data['rank_flg'] == 1) ? '<i class="fa fa-check"></i>Rank' : '<i class="fa fa-times"></i>Rank';

// 正解率を表示するか
$result_show[2] = ($quiz_data['answer_rate_flg'] == 1) ? '<i class="fa fa-check"></i>Correct answer rate' : '<i class="fa fa-times"></i>Correct answer rate';

// 偏差値を表示するか
$result_show[3] = ($quiz_data['deviation_flg'] == 1) ? '<i class="fa fa-check"></i>Deviation value' : '<i class="fa fa-times"></i>Deviation value';

// 合否を表示するか
$result_show[4] = ($quiz_data['success_flg'] == 1) ? '<i class="fa fa-check"></i>Pass or fail' : '<i class="fa fa-times"></i>Pass or fail';

// 自身が答えた選択を表示するか
$result_show[5] = ($quiz_data['student_answer_flg'] == 1) ? '<i class="fa fa-check"></i>Student answers' : '<i class="fa fa-times"></i>Student answers';

// 生徒に答えを見せるかどうか
$result_show[6] = ($quiz_data['answer_flg'] == 1) ? '<i class="fa fa-check"></i>Answer to the problem' : '<i class="fa fa-times"></i>Answer to the problem';

// 生徒に解説を見せるかどうか
$result_show[7] = ($quiz_data['explain_flg'] == 1) ? '<i class="fa fa-check"></i>Commentary' : '<i class="fa fa-times"></i>Commentary';

// 問題の正誤を表示するか
$result_show[8] = ($quiz_data['correct_flg'] == 1) ? '<i class="fa fa-check"></i>Correctness of the problem' : '<i class="fa fa-times"></i>Correctness of the problem';

// 対象範囲の取得

$data = array(
    'repository' => 'SubjectSectionRepository',
    'method' => 'findSubjectSectionId',
    'params' => array('subject_section_id' => $quiz_data['subject_section_id'])
);
$subject_data = $curl->send($data, $curl);

//debug($subject_data);
// 講義idから科目名を得る
$data = array(
    'repository' => 'SubjectGenreRepository',
    'method' => 'findSubjectGenreId',
    'params' => array('subject_genre_id' => $subject_data['subject_genre_id'])
);

$genre_data = $curl->send($data, $curl);
//debug($genre_data);

// 問題の取得
$QueryObj = new Query($quiz_id, $curl);
$query_data = $QueryObj->getQuery();
/*
$data = array(
    'repository' => 'QuizQueryRepository',
    'method' => 'findQueryQuizId',
    'params' => array('quiz_id' => $quiz_id)
);
$query_data = $curl->send($data, $curl);
 */
//debug($query_data);

// 選択肢の取得

$default_cnt = 5;
$SelectObj = new QuerySelection($QueryObj, $curl);

foreach ($query_data as $key => $item) {
#    $selection_data[$key] = $QueryObj->getSelection($item['query_id']);
    $count_correct[$key] = $SelectObj->countCorrect($item['query_id']);
    #$FollowObj = new QueryFollow($item['query_id'], $QueryObj, $curl);
    $FollowObj = new QueryFollow($item['query_id'], $Quiz, $curl);
    $follow_contents[$key] = $FollowObj->followContents();
}

$selection_data = $QueryObj->getQuerySelection();
//debug($selection_data);
//debug($count_correct);
//debug($follow_contents);

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
    <link rel="stylesheet" type="text/css" href="../css/quiz.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
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
                <li><a href="../contents/index.php">Content registration / editing</a></li>
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

        <!-- ▼h2 -->
        <div class="h2">
            <h2>Test creation</h2>
        </div>
        <!-- ▲h2 -->

        <!-- progress
        <div id="progress">
            <ul class="clearfix">
                <li class="active">
                    <span class="text">基本設定</span>
                    <span class="circle"></span>
                </li>
                <li class="active">
                    <span class="text">問題作成</span>
                    <span class="circle"></span>
                </li>
                <li class="active">
                    <span class="text">内容確認</span>
                    <span class="circle"></span>
                </li>
            </ul>
        </div> -->

        <!-- 確認文言 -->
        <div class="text-check answered">
            <p>You can not re-edit a test that has passed its publication date</p>
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
                        <th>Title</th>
                        <td><?php echo $quiz_data['title']; ?></td>
                    </tr>
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
                        <th>Number of attendance</th>
                        <td><?php echo ($quiz_data['repeat_challenge'] == 0) ? 'Unlimited' : $quiz_data['repeat_challenge'] . 'Times'; ?></td>
                    </tr>
                    <tr>
                        <th>Time limit</th>
                        <td><?php echo ($quiz_data['limit_time'] == 0) ? 'Unlimited' : $quiz_data['limit_time'] . 'Minutes'; ?></td>
                    </tr>
                    <tr>
                        <th>Passing score</th>
                        <td><?php echo ($quiz_data['qualifying_score'] == 0) ? 'None' : $quiz_data['qualifying_score'] . 'point'; ?></td>
                    </tr>
                    <tr class="display">
                        <th>Display of answer results</th>
                        <td>
                            <span<?php echo ($quiz_data['average_flg'] == 1) ? ' class="active"' : ''; ?>>Average score</span>
                            <span<?php echo ($quiz_data['rank_flg'] == 1) ? ' class="active"' : ''; ?>>Rank</span>
                            <span<?php echo ($quiz_data['answer_rate_flg'] == 1) ? ' class="active"' : ''; ?>>Correct answer rate</span>
                            <span<?php echo ($quiz_data['deviation_flg'] == 1) ? ' class="active"' : ''; ?>>Deviation value</span>
                            <span<?php echo ($quiz_data['student_answer_flg'] == 1) ? ' class="active"' : ''; ?>>Student response</span>
                            <span<?php echo ($quiz_data['answer_flg'] == 1) ? ' class="active"' : ''; ?>>Answer to the problem</span>
                            <span<?php echo ($quiz_data['success_flg'] == 1) ? ' class="active"' : ''; ?>>Admission decision</span>
                            <span<?php echo ($quiz_data['correct_flg'] == 1) ? ' class="active"' : ''; ?>>Correct or incorrect problem</span>
                            <span<?php echo ($quiz_data['explain_flg'] == 1) ? ' class="active"' : ''; ?>>Commentary</span>
                        </td>
                    </tr>
                </table>
<!--                <p class="btn-re-edit"><a href="base.php?id=<?php echo $quiz_id; ?>&bid=<?php echo $bid; ?>">再編集</a></p>
-->
            </div>

        </div>

        <!-- 各問題 -->

        <div id="question">
            <!-- 問題 -->
<?php foreach ($query_data as $key => $item): ?>
            <div class="each-question">
                <div class="number-erea">
                    <div class="in">
                    <p class="number"><span>Q</span><span><?php echo $key + 1; ?></span></p>
                    </div>
                </div>
                <div class="info-erea check">
                    <div class="in">
                        <table class="each-info">
                            <tr>
                                <th>Question sentence</th>
                                <td><?php echo $item['query_text']; ?></td>
                            </tr>
                            <tr>
                                <th>Allocation of points</th>
                                <td><?php echo $item['score']; ?>point</td>
                            </tr>
<?php foreach ($selection_data[$key] as $keys => $select): ?>
                            <tr class="choice">
<?php if ($keys == 0): ?>
                            <th rowspan="<?php /*// 2019/6/03 count関数対策*/ if(is_countable($selection_data[$key])){
                              echo count($selection_data[$key]);}?>">Choice</th>
<?php endif; ?>
<td><span<?php echo ($select['correct_flg'] == 1) ? ' class="correct"' : ''; ?>></span><?php echo $select['text']; ?></td>
                            </tr>
<?php endforeach; ?>
                            <tr>
                                <th>Reference image file</th>
                                <td><?php echo ($item['image_file_name'] == '') ? 'No picture' : $item['image_file_name']; ?></td>
                            </tr>
<!--                       <tr>
                                <th>参考音声ファイル</th>
                                <td><?php echo ($item['sound_file_name'] == '') ? 'No sound' : $item['sound_file_name']; ?></td>
                            </tr>
-->
                            <tr>
                                <th>Commentary</th>
                                <td><?php echo ($item['description'] == '')? 'Not set' : $item['description']; ?></td>
                            </tr>
                            <!--
                            <tr>
                                <th>解説コンテンツ</th>
                                <td>コンテンツタイトル</td>
                            </tr>
                            -->
                        </table>
                        <!--
                        <p class="btn-re-edit"><a href="query.php?id=<?php echo $quiz_id; ?>&p=<?php echo $key; ?>&bid=<?php echo $bid; ?>">再編集</a></p> -->
                    </div>
                </div>
            </div>
<?php endforeach; ?>
            <!-- 問題 -->
        </div>

        <!-- 保存 -->
        <form action="" method="post">
<input type="hidden" name="bid" value="<?php echo $bid; ?>">
        <div id="col-mainbtn" class="clearfix">
            <ul class="clearfix">
                <li class="save"><button type="submit" name="submit" value="back">Back to index</button></li>
            </ul>
        </div>
        </form>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
