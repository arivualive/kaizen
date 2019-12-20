DeletePlease enter a title.<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-8")) {
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
//debug($data);
$queryData = $questionnaireInfo->getQuestionnaireQuery($data);
// 2019/6/03 count関数対策
$query_parameter_value = 0;
if(is_countable($queryData[0])){
  $query_parameter_value = count($queryData[0]);
}
//$query_parameter_value = count($queryData[0]);
for($i = 0 ; $i < count($queryData) ; $i++) {
    if($queryData[$i]['query_type'] == 0 || $queryData[$i]['query_type'] == 1) {
        $queryData[$i] += $questionnaireInfo->getQuestionnaireQueryChoices($queryData[$i]);
    } else if($queryData[$i]['query_type'] == 3) {
        $queryData[$i] += $questionnaireInfo->getQuestionnaireQueryLength($queryData[$i]);
    }
}
//debug($queryData);

// subject データ
$subject_genre_name = 'Category large';
$subject_section_name = 'In category';

//$subject_data = $questionnaireInfo->getSubject();
//$subject_parameter_value = count($subject_data[0]);
//for( $i = 0 ; $i < count($subject_data) ; $i++ ) {
//    $subject_data[$i] += $questionnaireInfo->getSubjectSection($subject_data[$i]);
//    for( $j = 0 ; $j < count($subject_data[$i]) - $subject_parameter_value ; $j++ ) {
//        if($subject_data[$i][$j]['subject_section_id'] == filter_input(INPUT_GET, "id")) {
//            //debug($subject_data[$i][$j]['subject_section_name']);
//            //debug($subject_data[$i]['subject_genre_name']);
//            $subject_genre_id = $subject_data[$i]['subject_genre_id'];
//            $subject_genre_name = $subject_data[$i]['subject_genre_name'];
//            $subject_section_id = $subject_data[$i][$j]['subject_section_id'];
//            $subject_section_name = $subject_data[$i][$j]['subject_section_name'];
//        }
//    }
//}
//debug($subject_data);

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

if (filter_input(INPUT_POST, "send_flag" ) && $subject_section_id != 0) {
    $error = 0;
    $error_string .= "";

    if(filter_input(INPUT_POST, "title")) {
        $data['title'] = htmlspecialchars(filter_input(INPUT_POST, "title"));
    } else {
        $error = 1;
        $error_string .= "<span class='icon'></span>タイトルを入力してください。<br>";
    }

    if(filter_input(INPUT_POST, "description")) {
        $data['description'] = htmlspecialchars(filter_input(INPUT_POST, "description"));
    } else {
        $error = 1;
        $error_string .= "<span class='icon'></span>Please enter a descriptive text.<br>";
    }

    if(filter_input(INPUT_POST, "finished_message")) {
        $data['finished_message'] = htmlspecialchars(filter_input(INPUT_POST, "finished_message"));
    }

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

    //その他（インサートデータ）
    //$data['subject_section_id'] = 0;
    //$data['user_level_id'] = 0;
    //$data['register_user_id'] = $admin_id;
    //$data['enable'] = 1;
    //$data['type'] = 0;
    $data['bit_classroom'] = $bit_classroom;
    //$data['function_group_id'] = 0;

    if (isset($_POST['query_type'])) {
        $data['query_type'] = $_POST['query_type'];
    } else {
        $error = 1;
        $error_string .= "<span class='icon'></span>Please set the question contents.";
    }

    $data['flg_query_must'] = $_POST['flg_query_must'];
    $data['query'] = $_POST['query'];
    if(isset($_POST['text'])){
        $data['text'] = $_POST['text'];
    }

    //回答者の有無(回答があるなら処理しない)
    if($data['answer_flag'] == 0) {
        for($i = 0 ; $i < count($_POST['query_type']) ; $i++) {
            if($_POST['query_type'][$i] == 0) {
                if(count($_POST['text'][$i]) < 2) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Single response format - Set two or more options.<br>";
                }
                for($j = 0 ; $j < count($_POST['text'][$i]) ; $j++) {
                    if($_POST['text'][$i][$j] == "") {
                        $error = 1;
                        $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Single response format - There is a blank option.<br>(" . ($j + 1) . "choice)<br>Set a value or delete a choice.<br>";
                    }
                }
            } else if($_POST['query_type'][$i] == 1) {
                if(count($_POST['text'][$i]) < 2) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Multiple response format - Set two or more options.<br>";
                }
                for($j = 0 ; $j < count($_POST['text'][$i]) ; $j++) {
                    if($_POST['text'][$i][$j] == "") {
                        $error = 1;
                        $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Multiple response format - There is a blank option.<br>(" . ($j + 1) . "choice)<br>Set a value or delete a choice.<br>";
                    }
                }
            } else if($_POST['query_type'][$i] == 3) {
                $data[$i]['max_limit'] = $_POST['max_limit'][$i];
                $data[$i]['min_limit'] = $_POST['min_limit'][$i];
                $data[$i]['step'] = $_POST['step'][$i];
                if($data[$i]['max_limit'] <= $data[$i]['min_limit']) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Numeric response format - The minimum value is greater than or equal to the maximum value.<br>";
                } else if ($data[$i]['max_limit'] <= $data[$i]['step']  ) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Numeric response format - step is above the maximum value.<br>";
                } else if ($data[$i]['min_limit'] < 0) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Numeric response format - Set the minimum value as a positive value of 0 or more.<br>";
                } else if ($data[$i]['max_limit'] < 1) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Numeric response format - Set the maximum value as a positive value of 1 or more.<br>";
                } else if ($data[$i]['step'] < 1) {
                    $error = 1;
                    $error_string .= "<span class='icon'></span>Q" . ($i + 1) . ".Numeric response format - Set step with a positive value of 1 or more.<br>";
                }
            }
        }
    }

    if(!$error) {
        //下記の三テーブルに登録されている既存のデータは物理削除を行う
        //tbl_questionnaire_query
        //tbl_questionnaire_query_choices
        //tbl_questionnaire_query_length
        //削除後は新規作成と同一
        //回答者の有無(回答があるなら処理しない)
        if($data['answer_flag'] == 0) {
            for($i = 0 ; $i < count($queryData) ; $i++) {
                if($queryData[$i]['query_type'] == 0 || $queryData[$i]['query_type'] == 1 ){
                    $questionnaireInfo->deleteQuestionnaireQueryChoices($queryData[$i]);
            //  } else if($_POST['query_type'][$i] == 2) {
                } else if($queryData[$i]['query_type'] == 3) {
                    $questionnaireInfo->deleteQuestionnaireQueryLength($queryData[$i]);
                }
            }
            $questionnaireInfo->deleteQuestionnaireQuery($data);
        }

        //以下、新規作成とほぼ同一処理

        //テーブル登録処理：tbl_questionnaire
        $questionnaireInfo->setQuestionnaire($data, 'edit');
        $data['questionnaire_id'] = $id;
        //$function_list_data = $questionnaireInfo->setFunctionList($data);

        //テーブル登録処理：tbl_questionnaire_query
        //回答者の有無(回答があるなら処理しない)
        if($data['answer_flag'] == 0) {
            for($i = 0 ; $i < count($_POST['query_type']) ; $i++) {
                $data[$i]['questionnaire_id'] = $data['questionnaire_id'];
                $data[$i]['query'] = htmlspecialchars($_POST['query'][$i]);
                $data[$i]['query_type'] = $_POST['query_type'][$i];
                $data[$i]['flg_query_must'] = $_POST['flg_query_must'][$i];
                $data[$i]['enable'] = $data['enable'];

                $questionnaireInfo->setQuestionnaireQuery($data[$i]);
                $data[$i]['query_id'] = $questionnaireInfo->getQuestionnaireQueryMaxId()['max_query_id'];

                //テーブル登録処理：query_type：0 or 1 -> tbl_questionnaire_query_choices
                //テーブル登録処理：query_type：2 -> 追加処理なし
                //テーブル登録処理：query_type：3 -> tbl_questionnaire_query_length
                if($_POST['query_type'][$i] == 0 || $_POST['query_type'][$i] == 1 ){
                    for($j = 0 ; $j < count($_POST['text'][$i]) ; $j++) {
                        $data[$i][$j]['query_id'] = $data[$i]['query_id'];
                        $data[$i][$j]['text'] = htmlspecialchars($_POST['text'][$i][$j]);

                        $questionnaireInfo->setQuestionnaireQueryChoices($data[$i][$j]);
                    }
            //  } else if($_POST['query_type'][$i] == 2) {
                } else if($_POST['query_type'][$i] == 3) {
                    $data[$i]['max_label'] = htmlspecialchars($_POST['max_label'][$i]);
                    $data[$i]['max_limit'] = $_POST['max_limit'][$i];
                    $data[$i]['min_label'] = htmlspecialchars($_POST['min_label'][$i]);
                    $data[$i]['min_limit'] = $_POST['min_limit'][$i];
                    $data[$i]['step'] = $_POST['step'][$i];

                    $questionnaireInfo->setQuestionnaireQueryLength($data[$i]);
                }

                //debug($i);
            }
        }

        header("Location: ../contents/index.php?bid=" . $_GET['bid']);
        exit();
    }

    //debug($data);
    //debug($_POST);
} else if (filter_input(INPUT_POST, "send_flag" ) && $subject_section_id == 0) {
    $error = 1;
    $error_string .= "<span class='icon'></span>Content group has not been selected.<br>";
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
    <link rel="stylesheet" type="text/css" href="../css/questionnaire.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/datepicker.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
    <script src="js/edit.js"></script>
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
                <li><a href='../contents/index.php?bid=<?php echo $subject_section_id ?>'>Content registration / editing</a></li>
                <li class="active"><a>Questionnaire editing</a></li>
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
            <h2>Questionnaire editing</h2>
        </div>
        <!-- ▲h2 -->

        <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">

            <div id="col-questionnaire-control" class="clearfix">

                <!-- ▼Content group -->
                <div id="control-box-contentsgroup">

                    <div class="h3 clearfix">
                        <h3>Content group</h3>
                    </div>

                    <div class="body scrollerea">
                        <div id="subject-group" class="subject-group setting">
                            <ul class="accordion">
                            <!------CSV読込部分 ここから------>
                            <?php
                                //debug($subject_section_id);

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
                                        echo '><a href="' . $_SERVER['SCRIPT_NAME'] . '?id=' . $id . '&bid=' . $item[1] . '">' . $item[3] . '</a></li>' . "\n";
                                    }
                                }

                                echo '</ul>' . "\n" . '</li>' . "\n";
                            ?>
                            <!------CSV読込部分 ここまで------>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ▲Content group -->

                <!-- ▼Detailed information -->
                <div id="control-box-formgroup">

                    <div class="h3 clearfix">
                        <h3>Detailed information</h3>
                    </div>

                    <div class="body">
                        <!-- アンケートタイトル -->
                        <dl class="input-group">
                            <dt>Title<span class="text_limit">Within 100 characters</span></dt>
                            <dd>
                                <textarea maxlength="100" rows="2" class="questionnaire-title" name="title"><?php $data['title'] != '' ? print $data['title'] : '';?></textarea>
                                <?php
                                    if($data['title'] == '' && filter_input(INPUT_POST, "attach_file")) {
                                        echo "<p class='attention' id='not_title'>Title has not been entered</p>";
                                    }
                                ?>
                            </dd>
                        </dl>
                        <!-- Explanatory text -->
                        <dl class="input-group">
                            <dt>Explanatory text<span class="text_limit">Within 300 characters</span></dt>
                            <dd>
                                <textarea maxlength="300" rows="4" name="description"><?php $data['description'] != '' ? print $data['description'] : '';?></textarea>
                            </dd>
                        </dl>
                        <!-- 終了後メッセージ -->
                        <dl class="input-group">
                            <dt>End message<span class="text_limit">Within 100 characters</span></dt>
                            <dd>
                                <textarea maxlength="100" rows="4" name="finished_message"><?php $data['finished_message'] != '' ? print $data['finished_message'] : '';?></textarea>
                            </dd>
                        </dl>
                        <div class="clearfix">
                            <!-- 公開日 -->
                            <dl class="input-group day-start">
                                <dt>release date</dt>
                                <dd>
                                    <input type="text" class="datepicker" name="start_day" value="<?php $data['start_day'] != '0000-01-01' ? print str_replace("-", "/", $data['start_day']) : '';?>">
                                </dd>
                                <p class="attention">※ If you do not enter, it will be published from the posting date</p>
                            </dl>
                            <!-- 期限日 -->
                            <dl class="input-group day-limit clearfix">
                                <dt>deadline</dt>
                                <dd>
                                    <input type="text" class="datepicker" name="last_day" value="<?php $data['last_day'] != '9999-12-31' ? print str_replace("-", "/", $data['last_day']) : '';?>">
                                </dd>
                                <p class="attention">※ If you do not enter it, it will be published indefinitely</p>
                            </dl>
                        </div>
                    </div>

                </div>
                <!-- ▲Detailed information -->
            </div>

            <!-- 各質問 -->
            <div id="question">
                <?php
                    //アンケートの回答者が一人もいない
                    if($data['answer_flag'] == 0) {
                        for($i = 0 ; $i < (count($queryData)) ; $i++) {
                            if($queryData[$i]['query_type'] == 0) {
                                echo "
                                    <!--単一 -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                                <ul class='btns'>
                                ";
                                                    if((count($queryData) - 1) == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i != 0) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) == $i) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    }
                                echo "
                                                </ul>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]'>
                                                            <option value='0' selected='selected'>Single selection format</option>
                                                            <option value='1'>Multiple selection format</option>
                                                            <option value='2'>Free answer form</option>
                                                            <option value='3'>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1'><span class='icon'></span>Required answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0'><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                                <dl class='input-group select-answer'>
                                                    <dt>Selection item<span class='text_limit'>Set two or more</span></dt>
                                ";
                                                    for($j = 0 ; $j < (count($queryData[$i]) - $query_parameter_value) ; $j++) {
                                echo "
                                                        <dd class='item'><input type='text' maxlength='100' name='text[" . $i . "][" . $j . "]' value='" . $queryData[$i][$j]['text'] . "'><button class='delete item'>Delete</button></dd>
                                ";
                                                    }
                                echo "
                                                    <dd class='add'><button class='insert item'>Add item</button></dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else if($queryData[$i]['query_type'] == 1) {
                                echo "
                                    <!--複数 -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                                <ul class='btns'>
                                ";
                                                    if((count($queryData) - 1) == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i != 0) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) == $i) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    }
                                echo "
                                                </ul>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]'>
                                                            <option value='0'>Single selection format</option>
                                                            <option value='1' selected='selected'>Multiple selection format</option>
                                                            <option value='2'>Free answer form</option>
                                                            <option value='3'>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1'><span class='icon'></span>Required answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0'><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                                <dl class='input-group select-answer'>
                                                    <dt>Selection item<span class='text_limit'>Set two or more</span></dt>
                                ";
                                                    for($j = 0 ; $j < (count($queryData[$i]) - $query_parameter_value) ; $j++) {
                                echo "
                                                        <dd class='item'><input type='text' maxlength='500' name='text[" . $i . "][" . $j . "]' value='" . $queryData[$i][$j]['text'] . "'><button class='delete item'>Delete</button></dd>
                                ";
                                                    }
                                echo "
                                                    <dd class='add'><button class='insert item'>Add item</button></dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else if($queryData[$i]['query_type'] == 2) {
                                echo "
                                    <!-- Free answer form -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                                <ul class='btns'>
                                ";
                                                    if((count($queryData) - 1) == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i != 0) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) == $i) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    }
                                echo "
                                                </ul>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]'>
                                                            <option value='0'>Single selection format</option>
                                                            <option value='1'>Multiple selection format</option>
                                                            <option value='2' selected='selected'>Free answer form</option>
                                                            <option value='3'>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1'><span class='icon'></span>Required answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0'><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else if($queryData[$i]['query_type'] == 3) {
                                echo "
                                    <!--数値答案 -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                                <ul class='btns'>
                                ";
                                                    if((count($queryData) - 1) == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i != 0) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) > $i && $i == 0) {
                                echo "
                                                        <li class='top'><button type='button'></button></li>
                                                        <li class='bottom'><button class='active' type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    } else if((count($queryData) - 1) == $i) {
                                echo "
                                                        <li class='top'><button class='active' type='button'></button></li>
                                                        <li class='bottom'><button type='button'></button></li>
                                                        <li class='delete form'><button type='button'></button></li>
                                ";
                                                    }
                                echo "
                                                </ul>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]'>
                                                            <option value='0'>Single selection format</option>
                                                            <option value='1'>Multiple selection format</option>
                                                            <option value='2'>Free answer form</option>
                                                            <option value='3' selected='selected'>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1'><span class='icon'></span>Required answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0'><span class='icon'></span>Optional answer</label></p>
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                                <div class='clearfix'>
                                                    <dl class='input-group value-answer'>
                                                        <dt>Numeric input</dt>
                                                        <dd>
                                                            <input type='text' maxlength='20' placeholder='Label of the minimum value' class='label' name='min_label[" . $i . "]' value='" . $queryData[$i][0]['min_label'] . "'>
                                                            <input type='number' name='min_limit[" . $i . "]' value='" . $queryData[$i][0]['min_limit'] . "'>
                                                            <p>～</p>
                                                            <input type='text' maxlength='20' placeholder='Label for maximum value' class='label' name='max_label[" . $i . "]' value='" . $queryData[$i][0]['max_label'] . "'>
                                                            <input type='number' name='max_limit[" . $i . "]' value='" . $queryData[$i][0]['max_limit'] . "'>
                                                        </dd>
                                                    </dl>
                                                    <dl class='input-group step'>
                                                        <dt>number of steps</dt>
                                                        <dd>
                                                            <input type='number' value='" . $queryData[$i][0]['step'] . "' name='step[" . $i . "]'>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            }
                        }
                    //既にアンケートの回答者がいる
                    } else if($data['answer_flag'] == 1) {
                        for($i = 0 ; $i < (count($queryData)) ; $i++) {
                            if($queryData[$i]['query_type'] == 0) {
                                echo "
                                    <!--単一 -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]' style='background-color:#ddd;'>
                                                            <option value='0' selected>Single selection format</option>
                                                            <option value='1' disabled>Multiple selection format</option>
                                                            <option value='2' disabled>Free answer form</option>
                                                            <option value='3' disabled>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]' readonly style='background-color:#ddd;'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                                <dl class='input-group select-answer'>
                                                    <dt>Selection item<span class='text_limit'>Set two or more</span></dt>
                                ";
                                                    for($j = 0 ; $j < (count($queryData[$i]) - $query_parameter_value) ; $j++) {
                                echo "
                                                        <dd class='item'><input type='text' maxlength='100' name='text[" . $i . "][" . $j . "]' value='" . $queryData[$i][$j]['text'] . "' readonly style='background-color:#ddd;'></dd>
                                ";
                                                    }
                                echo "
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else if($queryData[$i]['query_type'] == 1) {
                                echo "
                                    <!--複数 -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]' style='background-color:#ddd;'>
                                                            <option value='0' disabled>Single selection format</option>
                                                            <option value='1' selected>Multiple selection format</option>
                                                            <option value='2' disabled>Free answer form</option>
                                                            <option value='3' disabled>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]' readonly style='background-color:#ddd;'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                                <dl class='input-group select-answer'>
                                                    <dt>Selection item<span class='text_limit'>Set two or more</span></dt>
                                ";
                                                    for($j = 0 ; $j < (count($queryData[$i]) - $query_parameter_value) ; $j++) {
                                echo "
                                                        <dd class='item'><input type='text' maxlength='500' name='text[" . $i . "][" . $j . "]' value='" . $queryData[$i][$j]['text'] . "' readonly style='background-color:#ddd;'></dd>
                                ";
                                                    }
                                echo "
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else if($queryData[$i]['query_type'] == 2) {
                                echo "
                                    <!-- Free answer form -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]' style='background-color:#ddd;'>
                                                            <option value='0' disabled>Single selection format</option>
                                                            <option value='1' disabled>Multiple selection format</option>
                                                            <option value='2' selected>Free answer form</option>
                                                            <option value='3' disabled>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]' readonly style='background-color:#ddd;'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else if($queryData[$i]['query_type'] == 3) {
                                echo "
                                    <!--数値答案 -->
                                    <div class='each-question' data-number=" . ($i + 1) . ">
                                        <div class='number-erea'>
                                            <div class='in'>
                                                <p class='number'><span>Question</span><span class='form-number'>" . ($i + 1) . "</span></p>
                                            </div>
                                        </div>
                                        <div class='info-erea create'>
                                            <div class='in'>
                                                <dl class='input-group type-questionnaire'>
                                                    <dt>Format selection</dt>
                                                    <dd class='clearfix'>
                                                        <select class='form-list' name='query_type[" . $i . "]' style='background-color:#ddd;'>
                                                            <option value='0' disabled>Single selection format</option>
                                                            <option value='1' disabled>Multiple selection format</option>
                                                            <option value='2' disabled>Free answer form</option>
                                                            <option value='3' selected>Numeric response format</option>
                                                        </select>
                                ";
                                                        if($queryData[$i]['flg_query_must'] == 0) {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='0' checked><span class='icon'></span>Optional answer</label></p>
                                ";
                                                        } else {
                                echo "
                                                            <p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" . $i . "]' value='1' checked><span class='icon'></span>Required answer</label></p>
                                ";
                                                        }
                                echo "
                                                    </dd>
                                                </dl>
                                                <dl class='input-group'>
                                                    <dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>
                                                    <dd><textarea maxlength='500' rows='4' name='query[" . $i . "]' readonly style='background-color:#ddd;'>" . $queryData[$i]['query'] . "</textarea></dd>
                                                </dl>
                                                <div class='clearfix'>
                                                    <dl class='input-group value-answer'>
                                                        <dt>Numeric input</dt>
                                                        <dd>
                                                            <input type='text' maxlength='20' placeholder='Label of the minimum value' class='label' name='min_label[" . $i . "]' value='" . $queryData[$i][0]['min_label'] . "' readonly style='background-color:#ddd;'>
                                                            <input type='number' name='min_limit[" . $i . "]' value='" . $queryData[$i][0]['min_limit'] . "' readonly style='background-color:#ddd;'>
                                                            <p>～</p>
                                                            <input type='text' maxlength='20' placeholder='Label for maximum value' class='label' name='max_label[" . $i . "]' value='" . $queryData[$i][0]['max_label'] . "' readonly style='background-color:#ddd;'>
                                                            <input type='number' name='max_limit[" . $i . "]' value='" . $queryData[$i][0]['max_limit'] . "' readonly style='background-color:#ddd;'>
                                                        </dd>
                                                    </dl>
                                                    <dl class='input-group step'>
                                                        <dt>number of steps</dt>
                                                        <dd>
                                                            <input type='number' value='" . $queryData[$i][0]['step'] . "' name='step[" . $i . "]' readonly style='background-color:#ddd;'>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            }
                        }
                    }
                ?>

            </div>

            <!-- 新規質問作成 -->
            <div class="btn-newquestion">
                <?php if($data['answer_flag'] == 0) { ?>
                    <button id="add-form" type="button">Create New</button>
                <?php } ?>
            </div>

            <!-- エラーメッセージ -->
            <?php
                if($error) {
                    echo "<p class='error-message'>" . $error_string . "</p>";
                }
            ?>

            <!-- 保存 -->
            <div id="col-mainbtn" class="clearfix">
                <ul class="clearfix">
                    <li class="save" name="submit"><button id="submit">Save</button></li>
                    <input type="hidden" name="send_flag" value="1" />
                    <li class="back"><a href='../contents/index.php?bid=<?php echo $subject_section_id ?>'>back to index</a></li>
                </ul>
            </div>
        </form>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
