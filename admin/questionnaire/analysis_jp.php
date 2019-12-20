<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOnArray($permission, "1-2000")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄

    header('Location: ../auth/index.php');
    exit();
}

$result_grade = '';
$result_classroom = '';
$result_course = '';

$curl = new Curl($url);
//debug($_SESSION);

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}
//主キー情報の取得
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $data['questionnaire_id'] = $id;
}

$questionnaireInfo = new AdminQuestionnaireModel($school_id, $curl);

//編集対象のデータを読み込む
$data = $questionnaireInfo->getQuestionnaire($data);
$answer_count = $questionnaireInfo->getQuestionnaire_AnswerCount($data);
if($answer_count != '') {
    $data += $answer_count;
} else {
    $data['answer_count'] = 0;
}


if (filter_input(INPUT_POST, "delete_flag" )) {
    $id = filter_input(INPUT_POST, "id");
    //debug($id);
    $questionnaireInfo->deleteQuestionnaireAnswer($id);
}

//debug($data);
$data['questionnaire_id'] = $id;
$queryData = $questionnaireInfo->getQuestionnaireQuery($data);
for($i = 0 ; $i < count($queryData) ; $i++) {
    if($queryData[$i]['query_type'] == 0) {
        if($answer_count != '') {
            $answer_count_query = $questionnaireInfo->getQuestionnaireQuery_AnswerCount($data)[$i];
            if($answer_count_query != '') {
                $queryData[$i] += $answer_count_query;
            } else {
                $queryData[$i]['answer_count'] = 0;
            }
        } else {
            $queryData[$i]['answer_count'] = 0;
        }
        $queryData[$i] += $questionnaireInfo->getQuestionnaireQuerySingleChoices_Analysis($queryData[$i]);
    } else if($queryData[$i]['query_type'] == 1) {
        if($answer_count != '') {
            $answer_count_query = $questionnaireInfo->getQuestionnaireQuery_AnswerCount($data)[$i];
            if($answer_count_query != '') {
                $queryData[$i] += $answer_count_query;
            } else {
                $queryData[$i]['answer_count'] = 0;
            }
        } else {
            $queryData[$i]['answer_count'] = 0;
        }
        $queryData[$i] += $questionnaireInfo->getQuestionnaireQueryMultipleChoices_Analysis($queryData[$i]);
    } else if($queryData[$i]['query_type'] == 2) {
        if($answer_count != '') {
            $answer_count_query = $questionnaireInfo->getQuestionnaireQuery_AnswerCount($data)[$i];
            if($answer_count_query != '') {
                $queryData[$i] += $answer_count_query;
            } else {
                $queryData[$i]['answer_count'] = 0;
            }
        } else {
            $queryData[$i]['answer_count'] = 0;
        }
        $queryData[$i] += $questionnaireInfo->getQuestionnaireQueryWord_Analysis($queryData[$i]);
    } else if($queryData[$i]['query_type'] == 3) {
        if($answer_count != '') {
            $answer_count_query = $questionnaireInfo->getQuestionnaireQuery_AnswerCount($data)[$i];
            if($answer_count_query != '') {
                $queryData[$i] += $answer_count_query;
            } else {
                $queryData[$i]['answer_count'] = 0;
            }
        } else {
            $queryData[$i]['answer_count'] = 0;
        }
        $queryData[$i] += $questionnaireInfo->getQuestionnaireQueryLength($queryData[$i]);
        $queryData[$i][0] += $questionnaireInfo->getQuestionnaireQueryLength_Analysis($queryData[$i]);
    }
}
//debug($queryData);

if($data['answer_count'] != 0) {
    $studentData = $questionnaireInfo->getStudent_Student($data);
    for($i = 0 ; $i < count($studentData) ; $i++) {
        $studentData[$i]['questionnaire_id'] = $id;
        $studentData[$i]['answer'] = $questionnaireInfo->getQuestionnaireAnswerQuery_Student($studentData[$i]);
        for($j = 0 ; $j < count($studentData[$i]['answer']) ; $j++) {
            if($studentData[$i]['answer'][$j]['answer_query_id'] == '') {
                $studentData[$i]['answer'][$j] = '-';
            } else if($studentData[$i]['answer'][$j]['query_type'] == 0) {
                $studentData[$i]['answer'][$j] = $questionnaireInfo->getQuestionnaireAnswerQuerySingleChoice_Student($studentData[$i]['answer'][$j])[0]['text'];
            } else if($studentData[$i]['answer'][$j]['query_type'] == 1) {
                $studentData[$i]['answer'][$j] = $questionnaireInfo->getQuestionnaireAnswerQueryMultipleChoice_Student($studentData[$i]['answer'][$j]);
                $studentChoiceString = '';
                for($k = 0 ; $k < count($studentData[$i]['answer'][$j]) - 1 ; $k++) {
                    $studentChoiceString .= $studentData[$i]['answer'][$j][$k]['text'] . "｜";
                }
                $studentChoiceString .= $studentData[$i]['answer'][$j][$k]['text'];
                $studentData[$i]['answer'][$j] = $studentChoiceString;
            } else if($studentData[$i]['answer'][$j]['query_type'] == 2) {
                $studentData[$i]['answer'][$j] = $questionnaireInfo->getQuestionnaireAnswerQueryWord_Student($studentData[$i]['answer'][$j])[0]['word'];
            } else if($studentData[$i]['answer'][$j]['query_type'] == 3) {
                $studentData[$i]['answer'][$j] = $questionnaireInfo->getQuestionnaireAnswerQueryLength_Student($studentData[$i]['answer'][$j])[0]['length'];
            }
        }
    }
    //debug($studentData);
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
    $bid = $_GET['bid'];
}

list($string, $check) = array_to_string((array)$bid);
$bit_subject = $string;
//debug($string);

//debug($check);

//------CSV読込部分 ここまで------

//debug($data);
//debug($queryData);

//---CSV出力(アンケート分布) ここから---
$csv_strings1 = array();

//CSV整形-アンケート項目[A1-F1]
$csv_strings1[0] .= "質問番号-質問文,回答形式,必須回答/任意回答,回答者数,項目(単一/複数)・値(数値),回答数(単一/複数/数値)";

//CSV整形-アンケートデータ[A2-F*]
for($i = 0 ; $i < count($queryData) ; $i++) {
    if($queryData[$i]['query_type'] == 0 || $queryData[$i]['query_type'] == 1) {
        for($j = 0 ; $j < count($queryData[$i]) - 6 ; $j++) {
            // 2019/6/03 count関数対策
            $line_count = 0;
            if(is_countable($csv_strings1)){
              $line_count = count($csv_strings1);
            }
            //$line_count = count($csv_strings1);                                                                                 //CSVのインサート行数設定
            if($j == 0) {
                $csv_strings1[$line_count] .= "Q" . ($i + 1) . "." . check_string_replace($queryData[$i]['query']) . ",";       //質問番号-質問文
                $csv_strings1[$line_count] .= $queryData[$i]['type_jp'] . ",";                                                  //回答形式
                if($queryData[$i]['query'] == 0) {                                                                              //必須回答/任意回答
                    $csv_strings1[$line_count] .= "任意回答,";
                } else if($queryData[$i]['query'] == 1) {
                    $csv_strings1[$line_count] .= "必須回答,";
                }
                $csv_strings1[$line_count] .= $queryData[$i]['answer_count'] . ",";                                             //回答者数
            } else {
                $csv_strings1[$line_count] .= "-,-,-,-,";                                                                       //先頭行以外は全てハイフン[-]で穴埋め
            }
            $csv_strings1[$line_count] .= check_string_replace($queryData[$i][$j]['text']) . ",";                               //項目(単一/複数)・値(数値)
            $csv_strings1[$line_count] .= $queryData[$i][$j]['answer_count'] . ",";                                             //回答数(単一/複数/数値)
        }
    } else if($queryData[$i]['query_type'] == 0 || $queryData[$i]['query_type'] == 3) {
        for($j = 0 ; $j < count($queryData[$i][0]) - 7 ; $j++) {
            // 2019/6/03 count関数対策
            $line_count = 0;
            if(is_countable($csv_strings1)){
              $line_count = count($csv_strings1);
            }
            //$line_count = count($csv_strings1);                                                                                 //CSVのインサート行数設定
            if($j == 0) {
                $csv_strings1[$line_count] .= "Q" . ($i + 1) . "." . check_string_replace($queryData[$i]['query']) . ",";       //質問番号-質問文
                $csv_strings1[$line_count] .= $queryData[$i]['type_jp'] . ",";                                                  //回答形式
                if($queryData[$i]['query'] == 0) {                                                                              //必須回答/任意回答
                    $csv_strings1[$line_count] .= "任意回答,";
                } else if($queryData[$i]['query'] == 1) {
                    $csv_strings1[$line_count] .= "必須回答,";
                }
                $csv_strings1[$line_count] .= $queryData[$i]['answer_count'] . ",";                                             //回答者数
            } else {
                $csv_strings1[$line_count] .= "-,-,-,-,";                                                                       //先頭行以外は全てハイフン[-]で穴埋め
            }
            $csv_strings1[$line_count] .= $queryData[$i][0][$j]['value'] . ",";                                                 //項目(単一/複数)・値(数値)
            $csv_strings1[$line_count] .= $queryData[$i][0][$j]['count'] . ",";                                                 //回答数(単一/複数/数値)
        }
    } else {
        // 2019/6/03 count関数対策
        $line_count = 0;
        if(is_countable($csv_strings1)){
          $line_count = count($csv_strings1);
        }
        //$line_count = count($csv_strings1);                                                                                     //CSVのインサート行数設定
        $csv_strings1[$line_count] .= "Q" . ($i + 1) . "." . check_string_replace($queryData[$i]['query']) . ",";               //質問番号-質問文
        $csv_strings1[$line_count] .= $queryData[$i]['type_jp'] . ",";                                                          //回答形式
        if($queryData[$i]['query'] == 0) {                                                                                      //必須回答/任意回答
            $csv_strings1[$line_count] .= "任意回答,";
        } else if($queryData[$i]['query'] == 1) {
            $csv_strings1[$line_count] .= "必須回答,";
        }
        $csv_strings1[$line_count] .= $queryData[$i]['answer_count'] . ",";                                                     //回答者数
        $csv_strings1[$line_count] .= "-,";                                                                                     //項目(単一/複数)・値(数値)
        $csv_strings1[$line_count] .= "-";                                                                                      //回答数(単一/複数/数値)
    }
}

//各行最後尾に改行追加・文字列連結
for($i = 0 ; $i < count($csv_strings1) ; $i++) {
    $csv_strings1[$i] .= "\r\n";
    $csv_data1 .= $csv_strings1[$i];
}
//debug($csv_data1);
$csv_name1 = $data['title'] . "(全体)";
//---CSV出力(アンケート分布) ここまで---

//---CSV出力(アンケート分布) ここから---
$csv_strings2 = array();

//CSV整形-アンケート項目[A1-C4]
$csv_strings2[0] .= "質問番号-質問文,-,-,";
$csv_strings2[1] .= "回答形式,-,-,";
$csv_strings2[2] .= "必須回答/任意回答,-,-,";
$csv_strings2[3] .= "選択肢(単一/複数)・最小値ラベル[値]～最大値ラベル[値](数値),-,-,";

//CSV整形-アンケートデータ[D1-*4]
for($i = 0 ; $i < count($queryData) ; $i++) {
    $csv_strings2[0] .= check_string_replace("Q" . ($i + 1) . "." . $queryData[$i]['query']) . ",";                             //質問番号-質問文
    $csv_strings2[1] .= $queryData[$i]['type_jp'] . ",";                                                                        //形式
    if($queryData[$i]['query'] == 0) {                                                                                          //必須回答/任意回答
        $csv_strings2[2] .= "任意回答,";
    } else if($queryData[$i]['query'] == 1) {
        $csv_strings2[2] .= "必須回答,";
    }
    if($queryData[$i]['query_type'] == 0 || $queryData[$i]['query_type'] == 1) {                                                //選択肢(単一/複数)
        for($j = 0 ; $j < count($queryData[$i]) - 7 ; $j++) {
            $csv_strings2[3] .= $queryData[$i][$j]['text'] . "｜";
        }
        $csv_strings2[3] .= $queryData[$i][$j]['text'] . ",";
    } else if($queryData[$i]['query_type'] == 3) {                                                                              //最小値ラベル[値]～最大値ラベル[値]
        $csv_strings2[3] .= check_string_replace($queryData[$i][0]['min_label']) . "[" . $queryData[$i][0]['min_limit'] . "]～" . check_string_replace($queryData[$i][0]['max_label']) . "[" . $queryData[$i][0]['max_limit'] . "]";
    } else {
        $csv_strings2[3] .= "-,";
    }
}

//CSV整形-空白行[E]
$csv_strings2[4] .= "";

//CSV整形-受講者項目[F1-F*]
$csv_strings2[5] .= "提出日,受講者ID,受講者名,";                                                                                //受講者項目-固定出力
for($i = 0 ; $i < count($queryData) ; $i++) {                                                                                   //受講者項目-反復出力
    $csv_strings2[5] .= "Q" . ($i + 1) . ".回答,";
}

//CSV整形-受講者項目[G1-**]
for($i = 0 ; $i < (is_countable($studentData) ? count($studentData) : 0) ; $i++) {
    // 2019/6/03 count関数対策
    $line = 0;
    if(is_countable($csv_strings2)){
      $line = count($csv_strings2);
    }
    //$line = count($csv_strings2);                                                                                               //追加対象の行を確定
    $csv_strings2[$line] .= $studentData[$i]['answer_datetime'] . ",";                                                          //提出日
    $csv_strings2[$line] .= $studentData[$i]['id'] . ",";                                                                       //受講者ID
    $csv_strings2[$line] .= check_string_replace($studentData[$i]['student_name']) . ",";                                       //受講者名
    for($j = 0 ; $j < count($studentData[$i]['answer']) ; $j++) {                                                               //回答
        $csv_strings2[$line] .= check_string_replace($studentData[$i]['answer'][$j]) . ",";
    }
}

//各行最後尾に改行追加・文字列連結
for($i = 0 ; $i < count($csv_strings2) ; $i++) {
    $csv_strings2[$i] .= "\r\n";
    $csv_data2 .= $csv_strings2[$i];
}
//debug($csv_data2);
$csv_name2 = $data['title'] . "(詳細)";
//---CSV出力(アンケート分布) ここまで---

//------CSV変換用関数 ここから------

function check_string_replace($data) {
    $check_string = array(",","\r\n");
    $replace_string = array("，","");

    return str_replace($check_string, $replace_string, $data);
}

//------CSV変換用関数 ここまで------

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS 管理者</title>
    <!-- favicon -->
    <link rel="shortcut icon" href="../images/favicon.ico">
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
    <link rel="stylesheet" type="text/css" href="../css/icon-font.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/questionnaire.css">
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
                    <p class="authority">管理者用</p>
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
                        <a class="togglebtn"><span class="icon-user-add"></span>受講者所属・ID設定</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/users.php">所属グループ設定</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1000")) { ?>
                            <li><a href="../user/student.php">受講者ID設定</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                    <li class="open">
                        <a class="togglebtn"><span class="icon-movie-manage"></span>コンテンツ設定</a>
                        <ul class="togglemenu open">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">コンテンツグループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php" class="active">コンテンツ登録・編集</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li>
                        <a href="../access/contents-control.php"><span class="icon-movie-set"></span>受講対象設定</a>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-graph"></span>受講状況</a>
                        <ul class="togglemenu">
                            <li><a href="../history/index.php">受講者から確認</a></li>
                            <!--<li><a href="dateWiseViewing/index.php">動画授業から確認</a></li>-->
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                    <li>
                        <a href="../message/message_list.php"><span class="icon-main-message"></span>メッセージ</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="../help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>ヘルプ</a>
                    </li>
                    <?php if ($isManager) { ?>
                    <li>
                        <a href="../user/admin.php"><span class="icon-user-add"></span>管理者ID・権限設定</a>
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
                <li>コンテンツ設定</li>
                <li><a href="../contents/index.php?bid=<?php echo $bid ?>">コンテンツ登録・管理</a></li>
                <li class="active"><a>アンケート分析</a></li>
            </ol>
        </div>
        <!-- ▼user information -->
        <div id="userinfo" class="button-dropdown">
            <a class="link" href="javascript:void(0)">
                <div class="erea-image"></div>
                <div class="erea-name">
                    <p class="authority">学校管理者</p>
                    <p class="username"><?php echo $_SESSION['auth']['admin_name']; ?></p>
                </div>
            </a>
            <ul class="submenu">
                <li role="presentation"><a href="../account/index.php"><span class="icon-lock"></span>アカウント設定</a></li>
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-in"></span>ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>アンケート分析</h2>
        </div>
        <!-- ▲h2 -->

        <div id="box-questionnaire-importantinfo">
            <dl class="title">
                <dt>タイトル</dt>
                <dd><?php echo $data['title']; ?></dd>
            </dl>
            <dl class="number-question">
                <dt>質問数</dt>
                <dd><span><?php // 2019/6/03 count関数対策
                  if(is_countable($queryData)){
                    echo count($queryData);
                  }
                 //echo count($queryData); ?></span> 問</dd>
            </dl>
            <dl class="number-user">
                <dt>回答者数</dt>
                <dd><span><?php echo $data['answer_count']; ?></span> 人</dd>
            </dl>
            <dl class="btns">
                <dd>
                    <form
                          action=<?php echo $url . 'download_csv.php'; ?>
                          method='post'
                          target='hidden-iframe'
                          class='csv'
                          >
                        <input type='hidden' name='data' value="<?php echo $csv_data1; ?>">
                        <input type='hidden' name='name' value="<?php echo $csv_name1; ?>">
                        <input type='submit' value=''>
                        CSV出力(全体)
                    </form>
                </dd>
                <dd>
                    <form
                          action=<?php echo $url . 'download_csv.php'; ?>
                          method='post'
                          target='hidden-iframe'
                          class='csv'
                          >
                        <input type='hidden' name='data' value="<?php echo $csv_data2; ?>">
                        <input type='hidden' name='name' value="<?php echo $csv_name2; ?>">
                        <input type='submit' value=''>
                        CSV出力(詳細)
                    </form>
                </dd>
            </dl>
        </div>

        <!-- メイン情報 -->
        <div id="box-questionnaire-maininfo" class="clearfix under">

            <!-- 公開範囲 -->
            <div class="col-contentsgroup-check">
                <dl>
                    <dt class="clearfix"><p>公開範囲</p></dt>
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

            <!-- テーブル -->
            <div class="col-maininfo-check">
                <table class="questionnaire-maininfo">
                    <tr>
                        <th>説明文</th>
                        <td style="white-space:pre-wrap;"><?php echo $data['description']; ?></td>
                    </tr>
                    <tr>
                        <th>終了後メッセージ</th>
                        <?php
                            if(isset($data['finished_message'])) {
                                echo "<td style='white-space:pre-wrap;'>" . $data['finished_message'] . "</td>";
                            } else {
                                echo "<span class='error'>未記入です</span>";
                            }
                        ?>
                    </tr>
                    <tr>
                        <th>公開日設定</th>
                        <td><?php echo $data['start_day']; ?>　～　<?php echo $data['last_day']; ?></td>
                    </tr>
                    <tr>
                        <th>登録者</th>
                        <td><?php echo $data['register_user_name']; ?></td>
                    </tr>
                    <tr>
                        <th>登録日</th>
                        <td><?php echo $data['register_datetime']; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- 各質問 -->

        <div id="question">

            <?php

                //質問番号用変数
                $i = 0;
                //各質問内容のパラメータ数を定義()
                $parameter_count = 6;

                foreach($queryData as $item) {
                    $i++;
                    if($item['query_type'] == 0) {
                        echo "
                            <!-- 単一選択形式 -->
                            <div class='each-question'>
                                <div class='number-erea'>
                                    <div class='in'>
                                        <p class='number'><span>質問</span><span>" . $i . "</span></p>
                                    </div>
                                </div>
                                <div class='info-erea report'>
                                    <div class='in'>
                                        <table class='each-info'>
                                            <tr>
                                                <th>質問形式</th>
                                                <td>" . $item['type_jp'] . "</td>
                                            </tr>
                                            <tr>
                                                <th>質問文</th>
                                                <td style='white-space:pre-wrap;'>" . $item['query'] . "</td>
                                            </tr>
                                            <tr class='slect-answer'>
                                                <th>選択項目</th>
                                                <td>
                        ";
                                                    for($count = 0 ; $count < (count($item) - $parameter_count) ; $count++) {
                                                        echo "<span class='slect-answer'>" . $item[$count]['text'] . "</span>";
                                                    }
                        echo "
                                                </td>
                                            </tr>
                                            <tr class='any-required'>
                                                <th>任意・必須</th>
                                                <td>
                        ";
                                                    if ($item['flg_query_must']) {
                                                        echo "<span class='passive'>任意回答</span>";
                                                        echo "<span class='active'>必須回答</span>";
                                                    } else {
                                                        echo "<span class='active'>任意回答</span>";
                                                        echo "<span class='passive'>必須回答</span>";
                                                    }
                        echo "
                                                </td>
                                            </tr>
                                            <tr class='number-user'>
                                                <th>回答者数</th>
                                                <td><span>" . $item['answer_count'] . "</span>人</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class='question-report graph clearfix navbar-collapse collapse' id='question-report" . $i . "'>
                                        <div class='w-50'>
                                            <table class='select answer'>
                                                <tr>
                                                    <th>項目</th>
                                                    <th>回答数</th>
                                                </tr>
                        ";
                                                for($count = 0 ; $count < (count($item) - $parameter_count) ; $count++) {
                                                    echo "
                                                        <tr>
                                                            <td>" . $item[$count]['text'] . "</td>
                                                            <td>" . $item[$count]['answer_count'] . "</td>
                                                        </tr>
                                                    ";
                                                }
                        echo "
                                            </table>
                                        </div>
                                        <div class='w-50'>

                                        </div>
                                    </div>
                                    <button class='navbar-toggler collapsed' type='button' data-toggle='collapse' data-target='#question-report" . $i . "' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>回答を見る</button>
                                </div>
                            </div>
                        ";
                    } else if($item['query_type'] == 1) {
                        echo "
                            <!-- 複数選択形式 -->
                            <div class='each-question'>
                                <div class='number-erea'>
                                    <div class='in'>
                                        <p class='number'><span>質問</span><span>" . $i . "</span></p>
                                    </div>
                                </div>
                                <div class='info-erea report'>
                                    <div class='in'>
                                        <table class='each-info'>
                                            <tr>
                                                <th>質問形式</th>
                                                <td>" . $item['type_jp'] . "</td>
                                            </tr>
                                            <tr>
                                                <th>質問文</th>
                                                <td style='white-space:pre-wrap;'>" . $item['query'] . "</td>

                                            </tr>
                                            <tr class='slect-answer'>
                                                <th>選択項目</th>
                                                <td>
                        ";
                                                    for($count = 0 ; $count < (count($item) - $parameter_count) ; $count++) {
                                                        echo "<span class='slect-answer'>" . $item[$count]['text'] . "</span>";
                                                    }
                        echo "
                                                </td>
                                            </tr>
                                            <tr class='any-required'>
                                                <th>任意・必須</th>
                                                <td>
                        ";
                                                    if ($item['flg_query_must']) {
                                                        echo "<span class='passive'>任意回答</span>";
                                                        echo "<span class='active'>必須回答</span>";
                                                    } else {
                                                        echo "<span class='active'>任意回答</span>";
                                                        echo "<span class='passive'>必須回答</span>";
                                                    }
                        echo "
                                                </td>
                                            </tr>
                                            <tr class='number-user'>
                                                <th>回答者数</th>
                                                <td><span>" . $item['answer_count'] . "</span>人</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class='question-report graph clearfix navbar-collapse collapse' id='question-report" . $i . "'>
                                        <div class='w-50'>
                                            <table class='select answer'>
                                                <tr>
                                                    <th>項目</th>
                                                    <th>回答数</th>
                                                </tr>
                        ";
                                                for($count = 0 ; $count < (count($item) - $parameter_count) ; $count++) {
                                                    echo "
                                                        <tr>
                                                            <td>" . $item[$count]['text'] . "</td>
                                                            <td>" . $item[$count]['answer_count'] . "</td>
                                                        </tr>
                                                    ";
                                                }
                        echo "
                                            </table>
                                        </div>
                                        <div class='w-50'>

                                        </div>
                                    </div>
                                    <button class='navbar-toggler collapsed' type='button' data-toggle='collapse' data-target='#question-report" . $i . "' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>回答を見る</button>
                                </div>
                            </div>
                        ";
                    } else if($item['query_type'] == 2) {
                        echo "
                            <!-- 自由答案形式 -->
                            <div class='each-question'>
                                <div class='number-erea'>
                                    <div class='in'>
                                        <p class='number'><span>質問</span><span>" . $i . "</span></p>
                                    </div>
                                </div>
                                <div class='info-erea report'>
                                    <div class='in'>
                                        <table class='each-info'>
                                            <tr>
                                                <th>質問形式</th>
                                                <td>" . $item['type_jp'] . "</td>
                                            </tr>
                                            <tr>
                                                <th>質問文</th>
                                                <td style='white-space:pre-wrap;'>" . $item['query'] . "</td>
                                            </tr>
                                            <tr class='any-required'>
                                                <th>任意・必須</th>
                                                <td>
                        ";
                                                    if ($item['flg_query_must']) {
                                                        echo "<span class='passive'>任意回答</span>";
                                                        echo "<span class='active'>必須回答</span>";
                                                    } else {
                                                        echo "<span class='active'>任意回答</span>";
                                                        echo "<span class='passive'>必須回答</span>";
                                                    }
                        echo "
                                                </td>
                                            </tr>
                                            <tr class='number-user'>
                                                <th>回答者数</th>
                                                <td><span>" . $item['answer_count'] . "</span>人</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class='question-report free-word navbar-collapse collapse' id='question-report" . $i . "'>
                                        <div class='w-100'>
                                            <table>
                                                <tr>
                                                    <th>回答順</th>
                                                    <th>受講者ID</th>
                                                    <th>受講者名</th>
                                                    <th>回答内容</th>
                                                    <th>再提出</th>
                                                </tr>
                        ";
                                                for($count = 0 ; $count < (count($item) - $parameter_count) ; $count++) {
                                                    echo "
                                                        <tr>
                                                            <td>" . ($count + 1) . "</td>
                                                            <td>" . $item[$count]['id'] . "</td>
                                                            <td>" . $item[$count]['student_name'] . "</td>
                                                            <td>" . $item[$count]['answer'] . "</td>
                                                            <td>
                                                                <form action='" . $_SERVER['REQUEST_URI'] . "' method='POST'>
                                                                    <button>再提出</button>
                                                                    <input type='hidden' name='delete_flag' value='1' />
                                                                    <input type='hidden' name='id' value='" . $item[$count]['answer_id'] . "' />
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    ";
                                                }
                        echo "
                                            </table>
                                        </div>
                                    </div>
                                    <button class='navbar-toggler collapsed' type='button' data-toggle='collapse' data-target='#question-report" . $i . "' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>回答を見る</button>
                                </div>
                            </div>
                        ";
                    } else if($item['query_type'] == 3) {
                        echo "
                            <!-- 数値答案形式 -->
                            <div class='each-question'>
                                <div class='number-erea'>
                                    <div class='in'>
                                        <p class='number'><span>質問</span><span>" . $i . "</span></p>
                                    </div>
                                </div>
                                <div class='info-erea report'>
                                    <div class='in'>
                                        <table class='each-info'>
                                            <tr>
                                                <th>質問形式</th>
                                                <td>" . $item['type_jp'] . "</td>
                                            </tr>
                                            <tr>
                                                <th>質問文</th>
                                                <td style='white-space:pre-wrap;'>" . $item['query'] . "</td>
                                            </tr>
                                            <tr class='value-answer'>
                                                <th>数値入力</th>
                                                <td>
                                                    <span class='label'>" . $item[0]['min_label'] . "</span>
                                                    <span class='value'>" . $item[0]['min_limit'] . "</span>
                                                    <span class='label'>～</span>
                                                    <span class='label'>" . $item[0]['max_label'] . "</span>
                                                    <span class='value'>" . $item[0]['max_limit'] . "</span>
                                                    <span class='label'>ステップ数</span>
                                                    <span class='value'>" . $item[0]['step'] . "</span>
                                                </td>
                                            </tr>
                                            <tr class='any-required'>
                                                <th>任意・必須</th>
                                                <td>
                        ";
                                                    if ($item['flg_query_must']) {
                                                        echo "<span class='passive'>任意回答</span>";
                                                        echo "<span class='active'>必須回答</span>";
                                                    } else {
                                                        echo "<span class='active'>任意回答</span>";
                                                        echo "<span class='passive'>必須回答</span>";
                                                    }
                        echo "
                                                </td>
                                            </tr>
                                            <tr class='number-user'>
                                                <th>回答者数</th>
                                                <td><span>" . $item['answer_count'] . "</span>人</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class='question-report graph clearfix navbar-collapse collapse' id='question-report" . $i . "'>
                                        <div class='w-50'>
                                            <table class='select value'>
                                                <tr>
                                                    <th>ステップ</th>
                                                    <th>回答数</th>
                                                </tr>
                        ";
                                                for($count = 0 ; $count < (count($item[0]) - $parameter_count) ; $count++) {
                                                    echo "
                                                        <tr>
                                                            <td>" . $item[0][$count]['value'] . "</td>
                                                            <td>" . $item[0][$count]['count'] . "</td>
                                                        </tr>
                                                    ";
                                                }
                        echo "
                                            </table>
                                        </div>
                                        <div class='w-50'>

                                        </div>
                                    </div>
                                    <button class='navbar-toggler collapsed' type='button' data-toggle='collapse' data-target='#question-report" . $i . "' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>回答を見る</button>
                                </div>
                            </div>
                        ";
                    }
                }
            ?>

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
