<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOnArray($permission, "1-8")) {
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
    $subject_section_name = $csvRowD[$subject_section_id];
    $subject_genre_id = $csvRowC[$subject_section_id];
    $subject_genre_name = $csvRowD[$csvRowC[$subject_section_id]];
} else {
    $subject_section_id = 0;
    $subject_section_name = 'カテゴリー中';
    $subject_genre_id = 0;
    $subject_genre_name = 'カテゴリー大';
}

list($string, $check) = array_to_string((array)$subject_section_id);
$bit_classroom = $string;
//debug($string);

//debug($lines);
//------CSV読込部分 ここまで------

$adminInfo = new AdminContentsListModel($school_id, $bit_classroom, $curl);

//フォルダ追加処理
if (filter_input(INPUT_POST, "create_folder_flag" )) {
    //tbl_function_group
    $data['function_group_name'] = $_POST['create_folder_name'];
    $adminInfo->setFunctionGroup($data, 'insert');

    //tbl_function_list
    $data['type'] = 4;
    $data['primary_id'] = $adminInfo->getFunctionGroupMaxId()['max_function_group_id'];
    $data['parent_function_group_id'] = 0;
    $adminInfo->setFunctionList($data, 'insert');
}

//フォルダ変更処理
if (filter_input(INPUT_POST, "edit_folder_flag" )) {
    //tbl_function_group
    $data['function_group_id'] = $_POST['edit_folder_id'];
    $data['function_group_name'] = $_POST['edit_folder_name'];
    $adminInfo->setFunctionGroup($data, 'edit');

    //tbl_function_list
    for($i = 1 ; $i <= count($_POST['edit_primary_key']) ; $i++) {
        if($_POST['edit_parent_function_group_id'][$i] != $_POST['edit_parent_function_group_id_change'][$i]) {
            $data['type'] = $_POST['edit_type'][$i];
            $data['primary_id'] = $_POST['edit_primary_key'][$i];
            $data['parent_function_group_id'] = $_POST['edit_parent_function_group_id_change'][$i];
            $adminInfo->setFunctionList($data, 'f_edit');
        }
    }
}

//フォルダ削除処理
if (filter_input(INPUT_POST, "delete_folder_flag" )) {
    //tbl_function_group
    $data['function_group_id'] = $_POST['delete_folder_id'];
    $adminInfo->setFunctionGroup($data, 'delete');

    //tbl_function_list
    $data['parent_function_group_id'] = $_POST['delete_folder_id'];
    $adminInfo->setFunctionList($data, 'delete');
}

// contents データ
$contents_data = $adminInfo->getContents();
//debug($contents_data);

// questionnaire データ
$questionnaire_data = $adminInfo->getQuestionnaire(0);
//debug($questionnaire_data);

// report データ
$report_data = $adminInfo->getQuestionnaire(1);
//debug($report_data);

// quiz データ
$quiz_data = $adminInfo->getQuiz();
//debug($quiz_data);

// function_group データ
$function_group_data = $adminInfo->getFunctionGroup();
//debug($quiz_data);

// message データ
//$message_data = $adminInfo->getMessage();
//debug($message_data);

// subject データ
$subject_genre_name = 'カテゴリー大';
$subject_section_name = 'カテゴリー中';

//$subject_data = $adminInfo->getSubject();
//$subject_parameter_value = count($subject_data[0]);
//for( $i = 0 ; $i < count($subject_data) ; $i++ ) {
//    $subject_data[$i] += $adminInfo->getSubjectSection($subject_data[$i]);
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

$info_array = array();
if(count($contents_data) != 0){
    $info_array = array_merge($info_array,$contents_data);
}
if(count($questionnaire_data) != 0){
    $info_array = array_merge($info_array,$questionnaire_data);
}
if(count($report_data) != 0){
    $info_array = array_merge($info_array,$report_data);
}
if(count($quiz_data) != 0){
    $info_array = array_merge($info_array,$quiz_data);
}
if(count($function_group_data) != 0){
    for($i = 0 ; $i < count($function_group_data) ; $i++) {
        $k = 0;
        for($j = 0 ; $j < count($info_array) ; $j++) {
            if($function_group_data[$i]['primary_key'] == $info_array[$j]['parent_function_group_id']) {
                $function_group_data[$i]['child_data'][$k] = $info_array[$j];
                array_splice($info_array, $j, 1);
                $k++;
                $j--;
            }
        }
        $function_group_data[$i]['child_item'] = $k;
        $function_group_data[$i]['array_number'] = $j + 1;
    }
    $info_array = array_merge($info_array, $function_group_data);
}
for ($i = 0 ; $i < count($info_array) ; $i++) {
    $sort[$i]['display_order'] = $info_array[$i]['display_order'];
    $sort[$i]['primary_key'] = $info_array[$i]['primary_key'];
    $sort[$i]['type'] = $info_array[$i]['type'];
    if($sort[$i]['type'] == 4) {
        for ($j = 0 ; $j < $info_array[$i]['child_item'] ; $j++) {
            $sort_child[$i][$j]['display_order'] = $info_array[$i]['child_data'][$j]['display_order'];
            $sort_child[$i][$j]['primary_key'] = $info_array[$i]['child_data'][$j]['primary_key'];
            $sort_child[$i][$j]['type'] = $info_array[$i]['child_data'][$j]['type'];
            $sort_child[$i][$j]['title'] = $info_array[$i]['child_data'][$j]['title'];
        }
        if(count($sort_child[$i]) != 0) {
            array_multisort($sort_child[$i], SORT_DESC, $sort_child[$i]);
        }
        for ($j = 0 ; $j < $info_array[$i]['child_item'] ; $j++) {
            $info_array[$i]['child_data'][$j]['display_order'] = $sort_child[$i][$j]['display_order'];
            $info_array[$i]['child_data'][$j]['primary_key'] = $sort_child[$i][$j]['primary_key'];
            $info_array[$i]['child_data'][$j]['type'] = $sort_child[$i][$j]['type'];
            $info_array[$i]['child_data'][$j]['title'] = $sort_child[$i][$j]['title'];
        }
    }
}
if(isset($sort)) {
    array_multisort($sort, SORT_DESC, $info_array);
}
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
    <link rel="stylesheet" type="text/css" href="../css/contents.css">
    <!-- js -->
    <!--<script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>-->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/alert.js"></script>
    <script src="js/folder.js"></script>
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
                <li class="active"><a>コンテンツ登録・編集</a></li>
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
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-out"></span>ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>コンテンツ登録・編集</h2>
            <?php if ($isManager || isPermissionFlagOn($permission, "1-8")) { ?>
            <ul class="btns-mode">
                <li><a href="index.php?bid=<?php echo $subject_section_id ?>">登録・編集</a></li>
                <li><a href="sorting.php?bid=<?php echo $subject_section_id ?>">並べ替え</a></li>
                <li class="active"><a>フォルダ</a></li>
            </ul>
            <?php } else if (isPermissionFlagOn($permission, "1-10")) { ?>
            <ul class="btns-mode">
                <li><a href="index.php?bid=<?php echo $subject_section_id ?>">削除</a></li>
            </ul>
            <?php } ?>
        </div>
        <!-- ▲h2 -->

        <div id="col-contentscontrol" class="clearfix">
            <!-- ▼科目・講義グループ -->
            <div id="subject-group" class="subject-group">
                <!------CSV読込部分 ここから------>
                <?php

                    foreach($lines as $line) {
                        $item = explode(',', $line);
                        if(preg_match('/^[1-2]$/', $item[0])) {
                    //      $csvMenu[$item[0]][$item[2]][] = $item[1]; //
                    //      $csvCategoryParent[$item[1]] = $item[0]; //
                            $csvParent[$item[1]] = $item[2];
                    //      $csvName[$item[1]] = $item[3]; //
                        }
                    }
                    $parent = $csvParent[$_GET['bid']];

                    echo '<ul class="accordion scrollerea">' . "\n";

                    foreach($lines as $line) {
                        $item = explode(',', $line);
                        $item[3] = str_replace('{c}', ',', $item[3]);

                        if($item[0] == 1) {
                            if($flag) { echo '</ul>' . "\n" . '</li>' . "\n"; }
                            echo '<li';
                            if($item[1] == $parent) { echo ' class="open"'; }
                            echo '>' . "\n";

                            echo '<a class="togglebtn">' . $item[3] . '</a>' . "\n";
                            echo '<ul class="togglemenu';
                            if($item[1] == $parent) { echo ' open'; }
                            echo '">' . "\n";
                        }

                        if($item[0] == 2) {
                            $flag = 1;
                            echo '<li';
                            if($_GET['bid'] == $item[1]) { echo ' class="active"'; }
                            echo '><a href="' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $item[1] . '">' . $item[3] . '</a></li>' . "\n";
                        }
                    }

                    echo '</ul>' . "\n" . '</li>' . "\n";
                ?>
                <!------CSV読込部分 ここまで------>
            </div>
            <!-- ▲科目・講義グループ -->

            <!-- ▼コンテンツ一覧 -->
            <div id="contentslist" class="contentslist">

                <!-- フォルダ新規作成：フォルダ未選択時に表示 -->
                <!--
                <div class="transbox">
                    <form class='create_folder_form' action="<?php echo $_SERVER['REQUEST_URI'] ?>" method='POST'>
                        <button class="new-folder"><img src="../images/ico_folder.png"> 新規フォルダを作成</button>
                        <input type='hidden' name='create_folder_flag' value='1'/>
                        <input type='hidden' name='create_folder_name' value=''/>
                    </form>
                </div>
                 -->

                <!-- フォルダ編集：フォルダ選択時に表示 -->
                <?php if ($isManager || isPermissionFlagOn($permission, "1-8")) { ?>
                <div class="transbox">
                    <div class="left">
                        <p class="text-top"><img src="../images/ico_folder_p.png"> 新規作成</p>
                        <div class="change-name">
                            <p class="text">フォルダ名</p>
                            <input type="text" class="input_name" maxlength="30">
                        </div>
                    </div>
                    <div class="right">
                        <!-- 作成フォーム -->
                        <form class='create_form' action="<?php echo $_SERVER['REQUEST_URI'] ?>" method='POST'>
                            <button type='button' class="create">新規作成</button>
                            <input type='hidden' name='create_folder_flag' value='1'/>
                            <input type='hidden' name='create_folder_name' value=''/>
                        </form>
                        <!-- 変更フォーム -->
                        <form class='edit_form' action="<?php echo $_SERVER['REQUEST_URI'] ?>" method='POST' style='display:none;'>
                            <button type='button' class="save">保 存</button>
                            <input type='hidden' name='edit_folder_flag' value='1'/>
                            <input type='hidden' name='edit_folder_id' value=''/>
                            <input type='hidden' name='edit_folder_name' value=''/>
                            <?php
                                if(count($info_array) != 0) {
                                    $array_number = 0;
                                    for($i = 0 ; $i < count($info_array) ; $i++) {
                                        $count++;
                                        echo "
                                            <input type='hidden' name='edit_type[" . $count . "]' value='" . $info_array[$i]['type'] . "'/>
                                            <input type='hidden' name='edit_primary_key[" . $count . "]' value='" . $info_array[$i]['primary_key'] . "'/>
                                            <input type='hidden' name='edit_parent_function_group_id[" . $count . "]' value='" . $info_array[$i]['parent_function_group_id'] . "'/>
                                            <input type='hidden' name='edit_parent_function_group_id_change[" . $count . "]' value='" . $info_array[$i]['parent_function_group_id'] . "'/>
                                        ";
                                        for($j = 0 ; $j < $info_array[$i]['child_item'] ; $j++) {
                                            $count++;
                                            echo "
                                                <input type='hidden' name='edit_type[" . $count . "]' value='" . $info_array[$i]['child_data'][$j]['type'] . "'/>
                                                <input type='hidden' name='edit_primary_key[" . $count . "]' value='" . $info_array[$i]['child_data'][$j]['primary_key'] . "'/>
                                                <input type='hidden' name='edit_parent_function_group_id[" . $count . "]' value='" . $info_array[$i]['child_data'][$j]['parent_function_group_id'] . "'/>
                                                <input type='hidden' name='edit_parent_function_group_id_change[" . $count . "]' value='" . $info_array[$i]['child_data'][$j]['parent_function_group_id'] . "'/>
                                            ";
                                        }
                                    }
                                }
                            ?>
                        </form>
                        <!-- 取消フォーム -->
                        <form class='cancel_form' action="<?php echo $_SERVER['REQUEST_URI'] ?>" method='POST' style='display:none;'>
                            <button type='button' class="cansel">キャンセル</button>
                        </form>
                        <!-- 削除フォーム -->
                        <form class='delete_form' action="<?php echo $_SERVER['REQUEST_URI'] ?>" method='POST' style='display:none;'>
                            <button type='button' class="delete">削除</button>
                            <input type='hidden' name='delete_folder_flag' value='1'/>
                            <input type='hidden' name='delete_folder_id' value=''/>
                        </form>
                    </div>
                </div>
                <?php } ?>

                <!-- 各コンテンツ -->
                <div id="item-list" class="item-list folder scrollerea">

                    <?php if(count($info_array) != 0) { ?>
                        <?php $i = 0 ?>
                        <?php $count = 0 ?>
                        <?php foreach ((array) $info_array as $item): ?>
                            <?php
                                if($item['bit_classroom'] == $bit_classroom && $item['parent_function_group_id'] == '0') {
                                    $count++;
                                    $i++;
                                    switch ($item['type']) {
                                    case 0:
                                        if ($item['contents_extension_id'] <= 6) {
                                            echo "
                                                <!-- ThinkBoard -->
                                                <div class='list-dummy-01 item tb'>
                                                    <div class='in'>
                                                        <label class='checkbox contents' style='display:none;'>
                                                            <input type='checkbox' name='list_check' class='list_check'>
                                                            <span class='icon'></span>
                                                        </label>
                                                        <p class='title'>" . $item['title'] . "</p>
                                                        <p class='type'>TB動画</p>
                                                        <input type='hidden' class='primary_id' value='" . $item['primary_key'] . "'>
                                                        <input type='hidden' class='check_number' value='" . $count . "'/>
                                                    </div>
                                                </div>
                                            ";
                                        } else {
                                            echo "
                                                <!-- MP4 -->
                                                <div class='list-dummy-01 item MP4'>
                                                    <div class='in'>
                                                        <label class='checkbox contents' style='display:none;'>
                                                            <input type='checkbox' name='list_check' class='list_check'>
                                                            <span class='icon'></span>
                                                        </label>
                                                        <p class='title'>" . $item['title'] . "</p>
                                                        <p class='type'>MP4動画</p>
                                                        <input type='hidden' class='primary_id' value='" . $item['primary_key'] . "'>
                                                        <input type='hidden' class='check_number' value='" . $count . "'/>
                                                    </div>
                                                </div>
                                            ";
                                        }
                                        break;
                                    case 1:
                                        echo "
                                            <!-- アンケート -->
                                            <div class='list-dummy-03 item questionnaire'>
                                                <div class='in'>
                                                    <label class='checkbox contents' style='display:none;'>
                                                        <input type='checkbox' name='list_check' class='list_check'>
                                                        <span class='icon'></span>
                                                    </label>
                                                    <p class='title'>" . $item['title'] . "</p>
                                                    <p class='type'>アンケート</p>
                                                    <input type='hidden' class='primary_id' value='" . $item['primary_key'] . "'>
                                                    <input type='hidden' class='check_number' value='" . $count . "'/>
                                                </div>
                                            </div>
                                        ";
                                        break;
                                    case 2:
                                        echo "
                                            <!-- レポート -->
                                            <div class='list-dummy-04 item report'>
                                                <div class='in'>
                                                    <label class='checkbox contents' style='display:none;'>
                                                        <input type='checkbox' name='list_check' class='list_check'>
                                                        <span class='icon'></span>
                                                    </label>
                                                    <p class='title'>" . $item['title'] . "</p>
                                                    <p class='type'>レポート</p>
                                                    <input type='hidden' class='primary_id' value='" . $item['primary_key'] . "'>
                                                    <input type='hidden' class='check_number' value='" . $count . "'/>
                                                </div>
                                            </div>
                                        ";
                                        break;
                                    case 3:
                                        echo "
                                            <!-- テスト -->
                                            <div class='list-dummy-02 item test'>
                                                <div class='in'>
                                                    <label class='checkbox contents' style='display:none;'>
                                                        <input type='checkbox' name='list_check' class='list_check'>
                                                        <span class='icon'></span>
                                                    </label>
                                                    <p class='title'>" . $item['title'] . "</p>
                                                    <p class='type'>テスト</p>
                                                    <input type='hidden' class='primary_id' value='" . $item['primary_key'] . "'>
                                                    <input type='hidden' class='check_number' value='" . $count . "'/>
                                                </div>
                                            </div>
                                        ";
                                        break;
                                    case 4:
                                        echo "
                                            <!-- フォルダー -->
                                            <div class='contents-folder'>

                                                <!-- フォルダータイトル -->
                                                <div class='folder-control clearfix'>
                                                    <p class='title'>" . $item['title'] . "
                                                        <label class='checkbox folder'>
                                                            <input type='checkbox' name='list_check' class='list_check'>
                                                            <span class='icon'></span>
                                                        </label>
                                                        <input type='hidden' class='folder_id' value='" . $item['primary_key'] . "'>
                                                        <input type='hidden' class='folder_name' value='" . $item['title'] . "'>
                                                        <input type='hidden' class='check_number' value='" . $count . "'/>
                                                    </p>
                                                </div>
                                        ";
                                                for($j = 0 ; $j < $item['child_item'] ; $j++) {
                                                    if($item['primary_key'] == $item['child_data'][$j]['parent_function_group_id']) {
                                                        $count++;
                                                        switch ($item['child_data'][$j]['type']) {
                                                        case 0:
                                                            if ($item['child_data'][$j]['contents_extension_id'] <= 6) {
                                                                echo "
                                                                    <!-- ThinkBoard -->
                                                                    <div class='list-dummy-01 item tb'>
                                                                        <div class='in'>
                                                                            <label class='checkbox contents' style='display:none;'>
                                                                                <input type='checkbox' name='list_check' class='list_check'>
                                                                                <span class='icon'></span>
                                                                            </label>
                                                                            <p class='title'>" . $item['child_data'][$j]['title'] . "</p>
                                                                            <p class='type'>TB動画</p>
                                                                            <input type='hidden' class='primary_id' value='" . $item['child_data'][$j]['primary_key'] . "'>
                                                                            <input type='hidden' class='check_number' value='" . $count . "'/>
                                                                        </div>
                                                                    </div>
                                                                ";
                                                            } else {
                                                                echo "
                                                                    <!-- MP4 -->
                                                                    <div class='list-dummy-01 item MP4'>
                                                                        <div class='in'>
                                                                            <label class='checkbox contents' style='display:none;'>
                                                                                <input type='checkbox' name='list_check' class='list_check'>
                                                                                <span class='icon'></span>
                                                                            </label>
                                                                            <p class='title'>" . $item['child_data'][$j]['title'] . "</p>
                                                                            <p class='type'>MP4動画</p>
                                                                            <input type='hidden' class='primary_id' value='" . $item['child_data'][$j]['primary_key'] . "'>
                                                                            <input type='hidden' class='check_number' value='" . $count . "'/>
                                                                        </div>
                                                                    </div>
                                                                ";
                                                            }
                                                            break;
                                                        case 1:
                                                            echo "
                                                                <!-- アンケート -->
                                                                <div class='list-dummy-03 item questionnaire'>
                                                                    <div class='in'>
                                                                        <label class='checkbox contents' style='display:none;'>
                                                                            <input type='checkbox' name='list_check' class='list_check'>
                                                                            <span class='icon'></span>
                                                                        </label>
                                                                        <p class='title'>" . $item['child_data'][$j]['title'] . "</p>
                                                                        <p class='type'>アンケート</p>
                                                                        <input type='hidden' class='primary_id' value='" . $item['child_data'][$j]['primary_key'] . "'>
                                                                        <input type='hidden' class='check_number' value='" . $count . "'/>
                                                                    </div>
                                                                </div>
                                                            ";
                                                            break;
                                                        case 2:
                                                            echo "
                                                                <!-- レポート -->
                                                                <div class='list-dummy-04 item report'>
                                                                    <div class='in'>
                                                                        <label class='checkbox contents' style='display:none;'>
                                                                            <input type='checkbox' name='list_check' class='list_check'>
                                                                            <span class='icon'></span>
                                                                        </label>
                                                                        <p class='title'>" . $item['child_data'][$j]['title'] . "</p>
                                                                        <p class='type'>レポート</p>
                                                                        <input type='hidden' class='primary_id' value='" . $item['child_data'][$j]['primary_key'] . "'>
                                                                        <input type='hidden' class='check_number' value='" . $count . "'/>
                                                                    </div>
                                                                </div>
                                                            ";
                                                            break;
                                                        case 3:
                                                            echo "
                                                                <!-- テスト -->
                                                                <div class='list-dummy-02 item test'>
                                                                    <div class='in'>
                                                                        <label class='checkbox contents' style='display:none;'>
                                                                            <input type='checkbox' name='list_check' class='list_check'>
                                                                            <span class='icon'></span>
                                                                        </label>
                                                                        <p class='title'>" . $item['child_data'][$j]['title'] . "</p>
                                                                        <p class='type'>テスト</p>
                                                                        <input type='hidden' class='primary_id' value='" . $item['child_data'][$j]['primary_key'] . "'>
                                                                        <input type='hidden' class='check_number' value='" . $count . "'/>
                                                                    </div>
                                                                </div>
                                                            ";
                                                            break;
                                                        }
                                                    }
                                                }
                                        echo "
                                            </div>
                                        ";
                                        break;
                                    }
                                } else if(count($item) == 0) {
                                    echo "
                                        <div class='no-item'>
                                            <p><span class='icon'></span>コンテンツは未登録です</p>
                                        </div>
                                    ";
                                    break;
                                }
                            ?>
                        <?php endforeach; ?>
                        <?php
                            if($i == 0 && isset($_GET['bid'])) {
                                echo "
                                    <div class='no-item'>
                                        <p><span class='icon'></span>コンテンツは未登録です</p>
                                    </div>
                                ";
                            } else if($i == 0 && !isset($_GET['bid'])) {
                                echo "
                                    <div class='select-group'>
                                        <p><span class='icon'></span>コンテンツグループを選択してください</p>
                                    </div>
                                ";
                            }
                        ?>
                    <?php } else { ?>
                    <?php
                        if($i == 0 && isset($_GET['bid'])) {
                            echo "
                                <div class='no-item'>
                                    <p><span class='icon'></span>コンテンツは未登録です</p>
                                </div>
                            ";
                        } else if($i == 0 && !isset($_GET['bid'])) {
                            echo "
                                <div class='select-group'>
                                    <p><span class='icon'></span>コンテンツグループを選択してください</p>
                                </div>
                            ";
                        }
                    ?>
                    <?php }; ?>

                </div>

            </div>
            <!-- ▲コンテンツ一覧 -->

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
