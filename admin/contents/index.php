<?php
require_once "../../config.php";
require_once "../../library/permission.php";
require_once( '../class.player_modal.php' );
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

$curl  = new Curl($url);
$modal = new modalCreate();
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
    $subject_section_name = 'In category';
    $subject_genre_id = 0;
    $subject_genre_name = 'Category large';
}

list($string, $check) = array_to_string((array)$subject_section_id);
$bit_classroom = $string;
//debug($string);

//debug($lines);
//------CSV読込部分 ここまで------

$adminInfo = new AdminContentsListModel($school_id, $bit_classroom, $curl);

//削除処理
if (filter_input(INPUT_POST, "delete_flag" )) {
    $data['primary_id'] = filter_input(INPUT_POST, "primary_id");

    //コンテンツ (tbl_contents)
    if(filter_input(INPUT_POST, "type" ) == 0) {
        $adminInfo->setContents($data, 'delete');
    //アンケート (tbl_questionnaire)
    } else if(filter_input(INPUT_POST, "type" ) == 1) {
        $adminInfo->setQuestionnaire($data, 'delete');
    //レポート (tbl_questionnaire)
    } else if(filter_input(INPUT_POST, "type" ) == 2) {
        $adminInfo->setQuestionnaire($data, 'delete');
    //クイズ (tbl_quiz)
    } else if(filter_input(INPUT_POST, "type" ) == 3) {
        $adminInfo->setQuiz($data, 'delete');
    }
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
$subject_genre_name = 'Category large';
$subject_section_name = 'In category';

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

//$path = $_SERVER['SERVER_NAME'].'/file/contents/';
$path = '../../../file/contents/';

//$path = str_replace("'\'", '', $path);

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
    $info_array = array_merge($info_array,$function_group_data);
}
foreach ((array) $info_array as $key => $value) {
    $sort[$key]['display_order'] = $value['display_order'];
    $sort[$key]['primary_key'] = $value['primary_key'];
    $sort[$key]['type'] = $value['type'];

    if ( $value[ 'contents_extension_id' ] < 7 ) {
      //$info_array[ $key ][ 'contents_path' ] = json_encode($path.$value[ 'primary_key' ].'.deploy');
      $info_array[ $key ][ 'contents_path' ] = $path.$value[ 'primary_key' ].'.deploy';
    } else if ( $value[ 'contents_extension_id' ] == 7 ) {
      //$info_array[ $key ][ 'contents_path' ] = json_encode($path.$value[ 'primary_key' ].'.mp4');
      $info_array[ $key ][ 'contents_path' ] = $path.$value[ 'primary_key' ].'.mp4';
    } else {
      $info_array[ $key ][ 'contents_path' ] = $path.$value[ 'primary_key' ].'.MP4';
    }
}
//debug($info_array);

//ソート処理
if (isset($_POST['array_number']) && isset($_POST['sort_flag'])) {

    if(isset($sort)) {
        array_multisort($sort, SORT_DESC, $info_array);
    }

    $flag = $_POST['sort_flag'];
    $array_number = $_POST['array_number'];
    $sort_id = $sort[$array_number]['display_order'];

    //debug($array_number);
    //debug($sort_id);
    //debug($sort);
    if ($flag == 'top') {
        //ソート対象データの変更
        for($i = 0 ; $i < $array_number ; $i++) {
            $sort_id = $sort[$i]['display_order'];
            $sort[$i]['display_order'] = $sort[$i + 1]['display_order'];
            $sort[$i + 1]['display_order'] = $sort_id;
        }
    } else if($flag == 'up') {
        //ソート対象データの変更
        $sort[$array_number]['display_order'] = $sort[$array_number - 1]['display_order'];
        $sort[$array_number - 1]['display_order'] = $sort_id;
    } else if($flag == 'down') {
        //ソート対象データの変更
        $sort[$array_number]['display_order'] = $sort[$array_number + 1]['display_order'];
        $sort[$array_number + 1]['display_order'] = $sort_id;
    } else if($flag == 'bottom') {
        //ソート対象データの変更
        for($i = (count($sort) - 1) ; $i > $array_number ; $i--) {
            $sort_id = $sort[$i]['display_order'];
            $sort[$i]['display_order'] = $sort[$i - 1]['display_order'];
            $sort[$i - 1]['display_order'] = $sort_id;
        }
    }

    for($i = 0 ; $i < count($sort) ; $i++) {
        $data['display_order'] = $sort[$i]['display_order'];
        $data['primary_id'] = $sort[$i]['primary_key'];
        $data['type'] = $sort[$i]['type'];

        $adminInfo->setFunctionList($data);
        //debug($data);
    }
    //debug($sort);

    header("Location: index.php?bid=" . $_GET['bid']);
    exit();
}
if(isset($sort)) {
    array_multisort($sort, SORT_DESC, $info_array);
}

$modal_display = $modal->modal_display( $data );

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
    <link rel="stylesheet" type="text/css" href="../css/contents.css">
    <!-- js -->
    <!--<script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>-->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>

    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/alert.js"></script>
    <script src="js/index.js"></script>
</head>
<body>

<div id="wrap">

    <!-- ▼modal -->
    <div class="modal fade folder-edit" id="folder-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Edit folder</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="icon-cross"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <dl class="input-group">
                            <dt>Change of Name</dt>
                            <dd><input type="text"></dd>
                            <dd><button class="submit">Preservation</button></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <li class="active"><a>Content registration / editing</a></li>
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
            <h2>Content registration / editing</h2>
            <?php
                if(isset($_GET['bid'])) {
                    if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                echo "
            <ul class='btns-mode'>
                <li class='active'><a>登録・編集</a></li>
                <li><a href='sorting.php?bid=" . $subject_section_id . "'>sorting</a></li>
                <li><a href='folder.php?bid=" . $subject_section_id . "'>folder</a></li>
            </ul> ";
                    } else if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                echo "
            <ul class='btns-mode'>
                <li class='active'><a>delete</a></li>
            </ul> ";
                    }
                }
            ?>
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

               <?php
                    if(isset($_GET['bid'])) {
                    echo "
                <!-- コントロールボタン -->
                <div class='topbtngroup clearfix'>
                    <!-- 新規登録 -->";

                    if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                    echo "
                    <div class='btn-newregist'>

                        <p class='text'>Sign up</p>
                        <ul>
                            <li class='movie'><a href='../movie/regist.php?bid=" . $subject_section_id . "'>video class</a></li>
                            <li class='test'><a href='../quiz/create.php?bid=" . $subject_section_id . "'>quiz</a></li>
                            <li class='questionnaire'><a href='../questionnaire/regist.php?bid=" . $subject_section_id . "'>questionnaire</a></li>
                            <li class='report'><a href='../report/regist.php?bid=" . $subject_section_id . "'>report</a></li>
                        </ul>
                    </div>";
                    }

                    echo "
                    <!-- 並べ替え -->
                    <div class='btngroup-sortcontrol'>
                        <!-- ソート -->
                        <div class='button-dropdown item-sort'>
                            <p class='text'>refine</p>
                            <a class='link all-sort' href='javascript:void(0)'>all</a>
                            <ul class='submenu'>
                                <li class='sort-dummy-01 all-sort'><a>all</a></li>
                                <li class='sort-dummy-02 movie'><a>video class</a></li>
                                <li class='sort-dummy-03 test'><a>quiz</a></li>
                                <li class='sort-dummy-04 questionnaire'><a>questionnaire</a></li>
                                <li class='sort-dummy-05 report'><a>report</a></li>
                            </ul>
                        </div>
                    </div>
                </div> ";}
                ?>


                <!-- 各コンテンツ -->
                <div id="item-list" class="item-list default scrollerea">

                    <?php if(count($info_array) != 0) { ?>
                        <?php $i = 0 ?>
                        <?php foreach ((array) $info_array as $item): ?>
                            <?php
                                if($item['bit_classroom'] == $bit_classroom && $item['parent_function_group_id'] == '0') {
                                    $i++;
                                    switch ($item['type']) {
                                    case 0:
                                        if ($item['contents_extension_id'] <= 6) {
                                            echo "
                                                <!-- ThinkBoard -->
                                                <div class='list-dummy-01 item tb'>
                                                    <div class='in'>
                                                        <p class='title'>" . $item['title'] . "</p>
                                                        <p class='type'>TB video</p>
                                                        <ul class='btngroup'>";
                                            if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                echo "
                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Remove content Is it OK?')\">
                                                                <li class='delete'><button></button></li>
                                                                <input type='hidden' name='delete_flag' value='1' />
                                                                <input type='hidden' name='primary_id' value='" . $item['primary_key'] . "'/>
                                                                <input type='hidden' name='type' value='" . $item['type'] . "'/>
                                                            </form>";
                                            }
                                            if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                echo "
                                                            <li class='edit'><a href='../movie/edit.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                            }
                                            echo"
                                                            <li class='play'><button type='button' value=" . $item[ 'title' ] . " class='play_button' data-filePath=".$item[ 'contents_path' ]."></button></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            ";
                                        } else {
                                            echo "
                                                <!-- MP4 -->
                                                <div class='list-dummy-01 item MP4'>
                                                    <div class='in'>
                                                        <p class='title'>" . $item['title'] . "</p>
                                                        <p class='type'>MP4 video</p>
                                                        <ul class='btngroup'>";
                                            if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                echo "
                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Remove content Is it OK?')\">
                                                                <li class='delete'><button></button></li>
                                                                <input type='hidden' name='delete_flag' value='1' />
                                                                <input type='hidden' name='primary_id' value='" . $item['primary_key'] . "'/>
                                                                <input type='hidden' name='type' value='" . $item['type'] . "'/>
                                                            </form>";
                                            }
                                            if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                echo "
                                                            <li class='edit'><a href='../movie/edit.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                            }
                                            echo "
                                                            <li class='play'><button type='button' value=" . $item[ 'title' ] . " class='play_button' data-filePath=".$item[ 'contents_path' ]."></button></li>
                                                        </ul>
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
                                                    <p class='title'>" . $item['title'] . "</p>
                                                    <p class='type'>questionnaire</p>
                                                    <ul class='btngroup'>";
                                        if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                            echo "
                                                        <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Delete the questionnaire Is it OK?')\">
                                                            <li class='delete'><button></button></li>
                                                            <input type='hidden' name='delete_flag' value='1' />
                                                            <input type='hidden' name='primary_id' value='" . $item['primary_key'] . "'/>
                                                            <input type='hidden' name='type' value='" . $item['type'] . "'/>
                                                        </form>";
                                        }
                                        if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                            echo "
                                                        <li class='copy'><a href='../questionnaire/copy.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                        <li class='edit'><a href='../questionnaire/edit.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                        }
                                        echo "
                                                        <li class='graph'><a href='../questionnaire/analysis.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        ";
                                        break;
                                    case 2:
                                        echo "
                                            <!-- レポート -->
                                            <div class='list-dummy-04 item report'>
                                                <div class='in'>
                                                    <p class='title'>" . $item['title'] . "</p>
                                                    <p class='type'>report</p>
                                                    <ul class='btngroup'>";
                                        if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                            echo "
                                                        <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Delete a report Is it OK?')\">
                                                            <li class='delete'><button></button></li>
                                                            <input type='hidden' name='delete_flag' value='1' />
                                                            <input type='hidden' name='primary_id' value='" . $item['primary_key'] . "'/>
                                                            <input type='hidden' name='type' value='" . $item['type'] . "'/>
                                                        </form>";
                                        }
                                        if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                            echo "
                                                        <li class='copy'><a href='../report/copy.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                        <li class='edit'><a href='../report/edit.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                        }
                                        echo "
                                                        <li class='graph'><a href='../report/analysis.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        ";
                                        break;
                                    case 3:
                                        echo "
                                            <!-- テスト -->
                                            <div class='list-dummy-02 item test'>
                                                <div class='in'>
                                                    <p class='title'>" . $item['title'] . "</p>
                                                    <p class='type'>quiz</p>
                                                    <ul class='btngroup'>";
                                        if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                            echo "
                                                        <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Remove a test Is it OK?')\">
                                                            <li class='delete'><button></button></li>
                                                            <input type='hidden' name='delete_flag' value='1' />
                                                            <input type='hidden' name='primary_id' value='" . $item['primary_key'] . "'/>
                                                            <input type='hidden' name='type' value='" . $item['type'] . "'/>
                                                        </form>";
                                        }
                                        if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                            echo "
                                                        <li class='copy'><button></button></li>
                                                        <li class='edit'><a href='../quiz/confirm.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li>";
                                        }
                                        echo "
                                                        <li class='graph'><a href='../quiz/result/index.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li>
                                                    </ul>
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
                                                    <p class='title'>" . $item['title'] . "</p>
                                                </div>
                                        ";
                                                foreach ((array) $info_array as $child_item):
                                                    if($item['primary_key'] == $child_item['parent_function_group_id']) {
                                                        $i++;
                                                        switch ($child_item['type']) {
                                                        case 0:
                                                            if ($child_item['contents_extension_id'] <= 6) {
                                                                echo "
                                                                    <!-- ThinkBoard -->
                                                                    <div class='list-dummy-01 item tb'>
                                                                        <div class='in'>
                                                                            <p class='title'>" . $child_item['title'] . "</p>
                                                                            <p class='type'>TB video</p>
                                                                            <ul class='btngroup'>";
                                                                if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                                    echo "
                                                                                <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Remove content Is it OK?')\">
                                                                                    <li class='delete'><button></button></li>
                                                                                    <input type='hidden' name='delete_flag' value='1' />
                                                                                    <input type='hidden' name='primary_id' value='" . $child_item['primary_key'] . "'/>
                                                                                    <input type='hidden' name='type' value='" . $child_item['type'] . "'/>
                                                                                </form>";
                                                                }
                                                                if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                                    echo "
                                                                                <li class='edit'><a href='../movie/edit.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                                                }
                                                                echo "
                                                                                <li class='play'><button type='button' value=" . $child_item[ 'title' ] . " class='play_button' data-filePath='".$child_item[ 'contents_path' ]."'></button></li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                ";
                                                            } else {
                                                                echo "
                                                                    <!-- MP4 -->
                                                                    <div class='list-dummy-01 item MP4'>
                                                                        <div class='in'>
                                                                            <p class='title'>" . $child_item['title'] . "</p>
                                                                            <p class='type'>MP4 video</p>
                                                                            <ul class='btngroup'>";
                                                                if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                                    echo "
                                                                                <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Remove content Is it OK?')\">
                                                                                    <li class='delete'><button></button></li>
                                                                                    <input type='hidden' name='delete_flag' value='1' />
                                                                                    <input type='hidden' name='primary_id' value='" . $child_item['primary_key'] . "'/>
                                                                                    <input type='hidden' name='type' value='" . $child_item['type'] . "'/>
                                                                                </form>";
                                                                }
                                                                if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                                    echo "
                                                                                <li class='edit'><a href='../movie/edit.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                                                }
                                                                echo "
                                                                                <li class='play'><button type='button' value=" . $child_item[ 'title' ] . " class='play_button' data-filePath='".$child_item[ 'contents_path' ]."'></button></li>
                                                                            </ul>
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
                                                                        <p class='title'>" . $child_item['title'] . "</p>
                                                                        <p class='type'>questionnaire</p>
                                                                        <ul class='btngroup'>";
                                                            if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                                echo "
                                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Delete the questionnaire Is it OK?')\">
                                                                                <li class='delete'><button></button></li>
                                                                                <input type='hidden' name='delete_flag' value='1' />
                                                                                <input type='hidden' name='primary_id' value='" . $child_item['primary_key'] . "'/>
                                                                                <input type='hidden' name='type' value='" . $child_item['type'] . "'/>
                                                                            </form>";
                                                            }
                                                            if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                                echo "
                                                                            <li class='copy'><a href='../questionnaire/copy.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                                            <li class='edit'><a href='../questionnaire/edit.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                                            }
                                                            echo "
                                                                            <li class='graph'><a href='../questionnaire/analysis.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            ";
                                                            break;
                                                        case 2:
                                                            echo "
                                                                <!-- レポート -->
                                                                <div class='list-dummy-04 item report'>
                                                                    <div class='in'>
                                                                        <p class='title'>" . $child_item['title'] . "</p>
                                                                        <p class='type'>report</p>
                                                                        <ul class='btngroup'>";
                                                            if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                                echo "
                                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Delete a report Is it OK?')\">
                                                                                <li class='delete'><button></button></li>
                                                                                <input type='hidden' name='delete_flag' value='1' />
                                                                                <input type='hidden' name='primary_id' value='" . $child_item['primary_key'] . "'/>
                                                                                <input type='hidden' name='type' value='" . $child_item['type'] . "'/>
                                                                            </form>";
                                                            }
                                                            if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                                echo "
                                                                            <li class='copy'><a href='../report/copy.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                                            <li class='edit'><a href='../report/edit.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>";
                                                            }
                                                            echo "
                                                                            <li class='graph'><a href='../report/analysis.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            ";
                                                            break;
                                                        case 3:
                                                            echo "
                                                                <!-- テスト -->
                                                                <div class='list-dummy-02 item test'>
                                                                    <div class='in'>
                                                                        <p class='title'>" . $child_item['title'] . "</p>
                                                                        <p class='type'>quiz</p>
                                                                        <ul class='btngroup'>";
                                                            if ($isManager || isPermissionFlagOn($permission, "1-10")) {
                                                                echo "
                                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST' onsubmit=\"return submitCheck('Remove quiz Is it OK?')\">
                                                                                <li class='delete'><button></button></li>
                                                                                <input type='hidden' name='delete_flag' value='1' />
                                                                                <input type='hidden' name='primary_id' value='" . $child_item['primary_key'] . "'/>
                                                                                <input type='hidden' name='type' value='" . $child_item['type'] . "'/>
                                                                            </form>";
                                                            }
                                                            if ($isManager || isPermissionFlagOn($permission, "1-8")) {
                                                                echo "
                                                                            <li class='copy'><button></button></li>
                                                                            <li class='edit'><a href='../quiz/confirm.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li>";
                                                            }
                                                            echo "
                                                                            <li class='graph'><a href='../quiz/result/index.php?id=" . $child_item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            ";
                                                            break;
                                                        }
                                                    } else if(count($child_item) == 0) {
                                                        echo "
                                                            <div class='no-item'>
                                                                <p><span class='icon'></span>Content is not registered</p>
                                                            </div>
                                                        ";
                                                        break;
                                                    }
                                                endforeach;
                                        echo "
                                            </div>
                                        ";
                                        break;
                                    }
                                } else if(count($item) == 0) {
                                    echo "
                                        <div class='no-item'>
                                            <p><span class='icon'></span>Content is not registered</p>
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
                                        <p><span class='icon'></span>Content is not registered</p>
                                    </div>
                                ";
                            } else if($i == 0 && !isset($_GET['bid'])) {
                                echo "
                                    <div class='select-group'>
                                        <p><span class='icon'></span>Please select a content group</p>
                                    </div>
                                ";
                            }
                        ?>
                    <?php } else { ?>
                    <?php
                        if($i == 0 && isset($_GET['bid'])) {
                            echo "
                                <div class='no-item'>
                                    <p><span class='icon'></span>Content is not registered</p>
                                </div>
                            ";
                        } else if($i == 0 && !isset($_GET['bid'])) {
                            echo "
                                <div class='select-group'>
                                    <p><span class='icon'></span>Please select a content group</p>
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
    <?php echo $modal_display; ?>
    <!-- ▲main -->
    <?php echo "<pre>"; print_r(__DIR__ ); echo "</pre>"; ?>
</div>
<script type="text/javascript" src="https://tbwp3.kaizen2.net/scripts/tbwp3"></script>
</body>
</html>
