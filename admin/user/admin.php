<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager) {
    $_SESSION = array(); //全てのセッション変数をDelete
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーをDelete
    session_destroy(); //セッションを破棄

    header('Location: ../auth/index.php');
    exit();
}

// コアのmodelsにアクセスするため
$loader->registerDir(dirname(__FILE__) . '/../../core/models');

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
    $data["own_id"] = $admin_id;
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
    $data['school_id'] = $school_id;
}


$curl = new Curl($url);
$studentInfo = new AdminStudentModel($school_id, $curl);
$adminInfo = new AdminConfig($curl);

$data['call_sign'] = $studentInfo->getCallSign()['call_sign'];

$permissionString = 0;
if (isset($_GET['id'])) {
    $data['admin_id'] = $_GET['id'];
    if($_GET['id'] != 0) {
        $admin_data = $adminInfo->selectAdmin($data, 'person');
        $database = $admin_data['bit_subject'];
        $permissionString = $admin_data['permission'];
        //debug($admin_data);
    }
}

if (isset($_GET['sc'])) {
    $scrool_value = $_GET['sc'];
} else {
    $scrool_value = 0;
}

/*
$current = filter_input(INPUT_GET, "p");
$max_rows = 100;
$limit = 10;

$paginate = new Paginate($current, $max_rows, $limit);
$offset = $paginate->getOffset();
*/

//------CSV読込部分 ここから------
error_reporting(~E_NOTICE);
$path = '../../library/category/'; //カテゴリーPHPライブラリの場所（※必須）
$csvpath = $path . 'csv/'; //CSVファイルの場所
$_POST['csvfile'] = $csvpath . 'users.csv';
$_POST['mode'] = 1;
$_POST['noform'] = 1;

//カテゴリー計算用ファイルを読込み
require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');

//CSVファイルを読み込み「UTF-8」に変換
$lines = @file($_POST['csvfile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$lines) { $lines = array(); }
mb_convert_variables('UTF-8', 'SJIS-win', $lines);
//先頭の2行をDeleteして詰める
unset($lines[0], $lines[1]);

//------CSV読込部分 ここまで------

/*
$list_data = $studentInfo->getStudent('', 'list');
$count = $studentInfo->getStudentCount($data);
//※DBからコールサインの取得するように変更予定※
$call_sign = $studentInfo->getCallSign()['call_sign'];
//debug($call_sign);
//debug($count);
*/

$list_data = $adminInfo->selectAdmin($data, 'list');
$count = [];
$count['count'] = $adminInfo->selectAdmin($data, 'count');
$count['count_all'] = $adminInfo->selectAdmin($data, 'count_all');

$max_rows = $count['count'];
$limit = 20;

if (filter_input(INPUT_POST, "insert_flag" )) {
    $error = 0;

    $data['admin_id'] = filter_input(INPUT_POST, "primary_id");

    if(filter_input(INPUT_POST, "admin_name")) {
        $data['admin_name'] = htmlspecialchars(filter_input(INPUT_POST, "admin_name"));
    } else {
        $error = 1;
    }

    if(filter_input(INPUT_POST, "id")) {
        $data['id'] = htmlspecialchars(filter_input(INPUT_POST, "id"));
        $check_flg = $adminInfo->checkAdminLoginId($data);
        if($check_flg['check_flg'] && $check_flg['admin_id'] != $data['admin_id']) {
            $error = 1;
            $id_error = 1;
        }
    } else {
        $error = 1;
    }

    if(filter_input(INPUT_POST, "password")) {
        $data['password'] = htmlspecialchars(filter_input(INPUT_POST, "password"));
    } else {
        $error = 1;
    }

    if(filter_input(INPUT_POST, "enable")) {
        $data['enable'] = filter_input(INPUT_POST, "enable");
    } else {
        $data['enable'] = 0;
    }

    $database = isset($_POST['category']) ? array_to_string($_POST['category'])[0] : 0;
    $data['bit_subject'] = $database;
    /*
    if($database) {
        $data['bit_subject'] = $database;
    } else {
        $error = 1;
        $subject_error = 1;
    }
    */
    if ($data['admin_id'] == 0) {
        $data['display_order'] = $count['count_all'] + 1;
    } else {
        $data['display_order'] = $admin_data['display_order'];
    }

    $data['manage'] = filter_input(INPUT_POST, "manage") ? filter_input(INPUT_POST, "manage") : 0;

    $permissionString = isset($_POST['permission']) ? array_to_string($_POST['permission'])[0] : 0;
    $data['permission'] = $permissionString;
    /*if($permissionString) {
        $data['permission'] = $permissionString;
    } else {
        $error = 1;
        $permission_error = 1;
    }
    */

    if($error == 0 && $data['admin_id'] == 0) {
        $adminInfo->changeAdmin($data, 'insert');
        $list_data = $adminInfo->selectAdmin($data, 'list');
        $database = 0;
        $permissionString = 0;
        list($check, $temp) = string_to_array($database);
        //debug($data['student_id']);
    } else if($error == 0 && $data['admin_id'] != 0) {
        //debug($data);
        $adminInfo->changeAdmin($data, 'update');
        $list_data = $adminInfo->selectAdmin($data, 'list');
        $admin_data = $adminInfo->selectAdmin($data, 'person');
        $database = $admin_data['bit_subject'];
        $permissionString = $admin_data['permission'];
        list($check, $temp) = string_to_array($database);
    } else if($error == 1) {
        //debug($data);
        $admin_data = $data;
        //$admin_data['id'] = substr($admin_data['id'], 3);
    }
} else
/* if (filter_input(INPUT_POST, "delete_flag" )) {
    $error = 0;

    if(filter_input(INPUT_POST, "student_id")) {
        $data['student_id'] = filter_input(INPUT_POST, "student_id");
    } else {
        $error = 1;
    }

    //debug($data);
    //debug($_POST);
    if($error == 0) {
        $studentInfo->setStudent($data, 'delete');
        $list_data = $studentInfo->getStudent('', 'list');
    }
} else if (filter_input(INPUT_POST, "csv_input_flag" )) {
    $csv_data['csv_input_file'] = $_FILES['csv_input_file'];
    //debug($csv_data['csv_input_file']);

    $csv_data = file_get_contents($csv_data['csv_input_file']['tmp_name']);
    $csv_data = mb_convert_encoding($csv_data, 'UTF-8', 'SJIS-win');
    $temp = tmpfile();
    $meta = stream_get_meta_data($temp);
    fwrite($temp, $csv_data);
    rewind($temp);

    $file = new SplFileObject($meta['uri'], 'rb');
    $file->setFlags(SplFileObject::READ_CSV);
    $i = 0;
    foreach($file as $line) {
        //debug($line);
        if(count($line) == 1 && $line[0] == "") {
            break;
        }
        for($j = 0 ; $j < 6 ; $j++) {
            if($line[$j] == "" && $j != 0) {
                $csv_input_error = 1;
                $csv_input_error_line = $i;
                switch($j) {
                //  case 0:
                //      $csv_input_error_column = "会員番号";
                //      break;
                    case 1:
                        $csv_input_error_column = "氏名";
                        break;
                    case 2:
                        $csv_input_error_column = "受講者ID";
                        break;
                    case 3:
                        $csv_input_error_column = "パスワード";
                        break;
                    case 4:
                        $csv_input_error_column = "受講状況";
                        break;
                    case 5:
                        $csv_input_error_column = "所属グループ";
                        break;
                }
            }
        }
        if(isset($csv_input_error)) {
            break;
        }
        $records[$i]['student_code'] = $line[0];
        $records[$i]['student_name'] = $line[1];
        $records[$i]['id'] = $line[2];
        $records[$i]['password'] = $line[3];
        $records[$i]['joining'] = $line[4];
        $records[$i]['bit_subject'] = $line[5];
        $i++;
    }
    //debug($records);

    if(!isset($csv_input_error)) {
        for($i = 1 ; $i < count($records) ; $i++) {
            $data['student_code'] = $records[$i]['student_code'];
            $data['student_name'] = $records[$i]['student_name'];
            $data['id'] = $call_sign . $records[$i]['id'];
            $data['password'] = $records[$i]['password'];
            $data['joining'] = $records[$i]['joining'];
            $data['bit_subject'] = $records[$i]['bit_subject'];

            $student_already_check = $studentInfo->getStudent($data, 'already');

            if($student_already_check['count'] == 0) {

                $studentInfo->setStudent($data, 'insert');
            } else if($student_already_check['count'] != 0) {
                $data['student_id'] = $student_already_check['student_id'];
                $studentInfo->setStudent($data, 'edit');
            }
        }
    }
    $list_data = $studentInfo->getStudent('', 'list');

    function studentFilter() {
        $student_data = $studentInfo->getStudent($data, 'person')[0];
    }

} else */
if (filter_input(INPUT_POST, "move_flag" )) {
    $flag = $_POST['move_type'];

    $adminInfo->sortAdminDisplayOrder($data, $flag);
    $list_data = $adminInfo->selectAdmin($data, 'list');
}
//リスト一番上のユーザーIDを取得
$top_user = $list_data[0]['admin_id'];
//リスト一番下のユーザーIDを取得
// 2019/6/03 count関数対策
if(is_countable($list_data)){
  $bottom_user = $list_data[count($list_data)-1]['admin_id'];
}
//$bottom_user = $list_data[count($list_data)-1]['admin_id'];


//------CSV変換用関数 ここから------

function check_string_replace($data) {
    $check_string = array(",","\r\n");
    $replace_string = array("，","");

    return str_replace($check_string, $replace_string, $data);
}

//------CSV変換用関数 ここまで------

//------JSON_ENCODEの設定用関数 ここから------
function json_safe_encode($data){
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
//------JSON_ENCODEの設定用関数 ここまで------
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
    <link rel="stylesheet" type="text/css" href="../css/user.css">
    <link rel="stylesheet" href="<?php echo $path ?>items/awesome.min.css">
    <link rel="stylesheet" href="<?php echo $path ?>category.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
    <script src="<?php echo $path ?>category.min.js"></script>
    <script src="js/admin.js" id="js_value" data-param='<?php echo json_safe_encode($scrool_value);?>'></script>
    <script src="../js/alert.js"></script>
    <style type="text/css">
        .maru::before{
            font-family: icomoon;
            content: "\e70a";
            color: #318dd0;
        }
        .batu::before{
            font-family: icomoon;
            content: "\ea0f";
            color: #e33b3b;
        }
    </style>
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
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>Content setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">Content group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php">Content registration / editing</a></li>
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
                    <li class="open">
                        <a href="../user/admin.php" class="active"><span class="icon-user-add"></span>Administrator ID, authority setting</a>
                    </li>
                    <?php }; ?>
                </ul>
            </nav>

            <!-- デバッグメッセージ
            <div class="alert alert-info" role="alert" style="margin: 8px;">
                <strong>manage</strong>： <?php echo $_SESSION["auth"]["manage"]; ?>
                <br>
                <strong>permission</strong>： <?php echo $_SESSION["auth"]["permission"]; ?>
                <br>
                <strong>bit_subject</strong>： <?php echo $_SESSION["auth"]["bit_subject"]; ?>
            </div>
            -->

        </div>
    </div>
    <!-- ▲navgation -->

    <!-- ▼header -->
    <div id="header">
        <!-- ▼topicpath -->
        <div id="topicpath">
            <ol>
                <li><a href="../info.php">HOME</a></li>
                <li>Administrator affiliation · ID setting</li>
                <li class="active"><a>Administrator ID, authority setting</a></li>
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
            <h2>Administrator settings</h2>
        </div>
        <!-- ▲h2 -->

        <div id="col-userscontrol" class="clearfix">

            <!-- ▼受講者一覧 -->
            <div id="userlist">

                <div class="table-top clearfix">
                    <h3>Administrator List</h3>

                    <!-- 表示数 -->
                    <div class="control-erea">
                        <p class="number-desplay">
                            Displayed number <span class="real">100</span>person / <span class="default">100</span> person <!-- 現在の表示数/対象者 -->
                        </p>

                        <!-- 上下移動 ->
                        <ul class="btns-movement clearfix">
                            <?php //debug($top_user); ?>
                            <?php //debug($bottom_user); ?>
                            <?php if ($data['admin_id'] == $top_user || $data['admin_id'] == 0) { ?>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="top" />
                                        <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                    </form>
                                </li>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="up" />
                                        <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                    </form>
                                </li>
                            <?php } else { ?>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button class="active"></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="top" />
                                        <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                    </form>
                                </li>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button class="active"></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="up" />
                                        <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                    </form>
                                </li>
                            <?php } ?>
                            <?php if ($data['admin_id'] == $bottom_user || $data['admin_id'] == 0) { ?>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="down" />
                                    <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                </form>
                            </li>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="bottom" />
                                    <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                </form>
                            </li>
                            <?php } else { ?>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button class="active"></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="down" />
                                    <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                </form>
                            </li>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button class="active"></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="bottom" />
                                    <input type="hidden" name="admin_id" value="<?php echo $data['admin_id']; ?>" />
                                </form>
                            </li>
                            <?php } ?>
                        </ul>
                        -->

                        <!-- 更新 -->
                        <p class="btn-update"><button value="reload" onclick="location.reload()">update</button></p>

                    </div>
                </div>

                <div class="body">

                    <!-- table(sort) -->
                    <table class="sort">
                        <tr>
                            <th class="name">Name</th>
                            <th class="delete"></th>
                        </tr>
                        <tr>
                            <td class="name">
                                <input type="text" id="filter_name">
                            </td>
                            <td class="delete">
                                <span><?php /*// 2019/6/03 count関数対策*/
                                  if(is_countable($list_data)){
                                    echo("all" .count($list_data) . "person");
                                  }
                                /*echo("全" .count($list_data) . "名");*/ ?></span>
                            </td>
                        </tr>
                        <!--  <tr> 新規登録 -->
                            <tr class='listvalue new-user'><!-- 選択時 class="active" -->
                                <!-- 主キー -->
                                <td class="key" style="display:none;"><?php echo 0 ?></td>
                                <!-- 氏名 -->
                                <td class="new" colspan="2"><p>sign up</p></td>
                            </tr>
                    </table>

                    <!-- table(list) -->
                    <div class="scrollerea userlist">
                        <table class="userlist">
                        <?php foreach ((array)$list_data as $items): ?>
                            <?php if($items['admin_id'] == $data['admin_id']) { ?>
                                <tr class='listvalue active'><!-- 選択時 class="active" -->
                            <?php } else { ?>
                                <tr class='listvalue'><!-- 選択時 class="active" -->
                            <?php } ?>
                                <!-- 主キー -->
                                <td class="key" style="display:none;"><?php echo $items['admin_id']; ?></td>
                                <!-- 氏名 -->
                                <td class="name" filter="<?php echo $items['admin_name']; ?>"><?php echo $items['admin_name']; ?></td>
                                <!-- 稼働状態 -->
                                <td class="delete">
                                    <?php if ($items['enable'] == 1) { ?>
                                        <span class="maru">Effectiveness</span>
                                    <?php } else { ?>
                                        <span class="batu">Invalid</span>
                                    <?php } ?>
                                    <!--
                                    <form action="<?php //echo $_SERVER['REQUEST_URI'] ?>" method="POST" onsubmit="return submitCheck('受講者情報をDeleteします。よろしいですか？')">
                                        <button>Delete</button>
                                        <input type="hidden" name="delete_flag" value="1" />
                                        <input type="hidden" name="admin_id" value="<?php //echo $items['admin_id']; ?>" />
                                    </form>
                                    -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </table>
                    </div>

                </div>
            </div>

            <!-- ▼詳細情報 -->
            <div id="controlerea">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <?php
                            if($data['admin_id'] != 0) {
                                echo "<a class='nav-link active' data-toggle='tab' href='#new-regist' role='tab' aria-selected='true'>Edit</a>";
                            } else {
                                echo "<a class='nav-link active' data-toggle='tab' href='#new-regist' role='tab' aria-selected='true'>sign up</a>";
                            }
                        ?>
                    </li>
                    <!--
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#csv" role="tab" aria-selected="false">CSV</a>
                    </li>
                    -->
                </ul>

                <div class="tab-content" id="myTabContent">

                    <!-- 新規登録 -->
                    <div class="tab-pane fade show active" id="new-regist" role="tabpanel">
                        <?php
                            if($data['admin_id'] != 0) {
                                echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('Change administrator information. Is it OK?')\">";
                            } else {
                                echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('Register administrator information. Is it OK?')\">";
                            }
                        ?>
                            <div class="clearfix">
                                <!-- 管理者氏名 -->
                                <dl class="input-group w-50">
                                    <dt>Name</dt>
                                    <dd>
                                        <input type="text" maxlength="100" name="admin_name" value="<?php echo $admin_data['admin_name'] ?>">
                                    </dd>
                                    <?php
                                        if(!isset($data['id']) && filter_input(INPUT_POST, "insert_flag")) {
                                            echo "<p class='attention' id='not_student_name'>Your name has not been entered</p>";
                                        }
                                    ?>
                                </dl>
                            </div>
                            <div class="clearfix">
                                <!-- 管理者ID -->
                                <dl class="input-group w-50">
                                    <dt>Administrator ID</dt>
                                    <dd class="set-btn">
                                        <input type="text" maxlength="100" name="id" value="<?php echo $admin_data['id'] ?>"><input type="button" id="auto_student_id" value="Automatically created">
                                    </dd>
                                    <?php
                                        if(!isset($data['id']) && filter_input(INPUT_POST, "insert_flag")) {
                                            echo "<p class='attention' id='not_id'>The administrator ID has not been entered.</p>";
                                        } else if($id_error) {
                                            echo "<p class='attention' id='overlaped_id'>There is a duplicate administrator ID. Please enter another value.</p>";
                                        }
                                    ?>
                                </dl>
                                <!-- パスワード -->
                                <dl class="input-group w-50">
                                    <dt>password</dt>
                                    <?php
                                        if($data['admin_id'] == 0) {
                                            echo "
                                                <dd class='set-btn'>
                                                    <input type='text' maxlength='100' name='password' value='" . $admin_data['password'] . "'><input type='button' id='auto_student_password' value='Automatically created'></input>
                                                </dd>
                                            ";
                                        } else {
                                            echo "
                                                <dd class='set-btn'>
                                                    <input type='password' maxlength='100' name='password' value='" . $admin_data['password'] . "'>
                                                    <input type='text' maxlength='100' name='hide_password' value=''>
                                                    <input type='button' id='auto_student_password2' value='Automatically created'></input>
                                                </dd>
                                            ";
                                        }
                                    ?>
                                    <?php
                                        if(!isset($data['password']) && filter_input(INPUT_POST, "insert_flag")) {
                                            echo "<p class='attention' id='not_password'>Password has not been entered</p>";
                                        }
                                    ?>
                                </dl>
                            </div>
                            <!-- Effectiveness・無効 -->
                        <?php if (!isset($_GET["id"]) || $_GET["id"]==0 || $admin_data["manage"]==0) { ?>
                            <dl class="input-group clearfix">
                                <dt>Operating status</dt>
                                <dd>
                                    <label class="checkbox"><input type="radio" name="enable" value="1" value="<?php if( $admin_data['enable'] == 1) { echo '"checked="true'; } ?>"><span class="icon"></span><span class="t"></span>Effectiveness</label>
                                    <label class="checkbox"><input type="radio" name="enable" value="0" value="<?php if( $admin_data['enable'] == 0) { echo '"checked="true'; } ?>"><span class="icon"></span><span class="f"></span>Invalid</label>
                                </dd>
                            </dl>
                        <?php } else { ?>
                            <input type="hidden" name="enable" value="<?php echo isset($admin_data['enable']) ? $admin_data['enable'] : 1; ?>">
                        <?php } ?>
                            <!-- マネージ機能 -->
                            <input type="hidden" name="manage" value="<?php echo isset($admin_data['manage']) ? $admin_data['manage'] : 0; ?>">
                            <!--
                            <dl class="input-group clearfix">
                                <dt>Administrator ID, authority setting</dt>
                                <dd>
                                    <label class="checkbox"><input type="radio" name="manage" value="1" <?php //if( $admin_data['manage'] == 1) { echo '"checked="true'; } ?>><span class="icon"></span><span class="t"></span>Effectiveness</label>
                                    <label class="checkbox"><input type="radio" name="manage" value="0" <?php //if( $admin_data['manage'] == 0) { echo '"checked="true'; } ?>><span class="icon"></span><span class="f"></span>無効</label>
                                </dd>
                            </dl>
                            -->
                            <!-- 所属情報 -->
                        <?php if (!isset($_GET["id"]) || $_GET["id"]==0 || $admin_data["manage"]==0) { ?>
                            <dl class="input-group border-top">
                                <dt>Group membership</dt>
                                <dd>
                                    <?php require(dirname(__FILE__) . '/' . $path . 'catetree.php'); //カテゴリーツリー表示用ファイルを読込み ?>
                                    <?php
                                        if($subject_error) {
                                            echo "<p class='attention'>※ Subject-Lecture is not selected</p>";
                                        }
                                    ?>
                                </dd>
                            </dl>
                            <dl class="input-group border-top">
                                <dt>Operation authority</dt>
                                <dd>
                                    <div class="border" spellcheck="false" data-calc="" data-noalert="">
                                    <ul class="padding padding1">
                                        <span class="item2">
                                            <input type="checkbox" name="folder">
                                            <label class="mark angle">&#xf078;</label>
                                            <input type="hidden" name="save[]" value="1{}1-0x1000{}{}Student ID setting{}">
                                            <input type="checkbox" name="permission[]" value="1-0x1000" id="p000" <?php setPermissionCheck($permissionString, "1-1000"); ?>>
                                            <label class="label label1" for="p000">Student ID setting (view)</label>
                                        </span>
                                        <ul class="padding">
                                            <div class="padding2">
                                                <span class="item1">
                                                    <input type="hidden" name="save[]" value="2{}1-0x2{}1-0x1000{}Registration / Edit{}">
                                                    <input type="checkbox" name="permission[]" value="1-0x2" data-permission1="1-0x1000" id="p001" <?php setPermissionCheck($permissionString, "1-2"); ?>>
                                                    <label class="label label4" for="p001">Registration / Edit</label>
                                                </span>
                                                <span class="item1">
                                                    <input type="hidden" name="save[]" value="2{}1-0x4{}1-0x1000{}Delete{}">
                                                    <input type="checkbox" name="permission[]" value="1-0x4" data-permission1="1-0x1000" id="p002" <?php setPermissionCheck($permissionString, "1-4"); ?>>
                                                    <label class="label label4" for="p002">Delete</label>
                                                </span>
                                            </div>
                                        </ul>
                                        <span class="item2">
                                            <input type="checkbox" name="folder">
                                            <label class="mark angle">&#xf078;</label>
                                            <input type="hidden" name="save[]" value="1{}1-0x2000{}{}Content setting{}">
                                            <input type="checkbox" name="permission[]" value="1-0x2000" id="p003" <?php setPermissionCheck($permissionString, "1-2000"); ?>>
                                            <label class="label label1" for="p003">Content setting (view)</label>
                                        </span>
                                        <ul class="padding">
                                            <div class="padding2">
                                                <span class="item1">
                                                    <input type="hidden" name="save[]" value="2{}1-0x8{}1-0x2000{}Registration / Edit{}">
                                                    <input type="checkbox" name="permission[]" value="1-0x8" data-permission1="1-0x2000" id="p004" <?php setPermissionCheck($permissionString, "1-8"); ?>>
                                                    <label class="label label4" for="p004">Registration / Edit</label>
                                                </span>
                                                <span class="item1">
                                                    <input type="hidden" name="save[]" value="2{}1-0x10{}1-0x2000{}Delete{}">
                                                    <input type="checkbox" name="permission[]" value="1-0x10" data-permission1="1-0x2000" id="p005" <?php setPermissionCheck($permissionString, "1-10"); ?>>
                                                    <label class="label label4" for="p005">Delete</label>
                                                </span>
                                            </div>
                                        </ul>
                                        <span class="item2">
                                            <input type="checkbox" name="folder">
                                            <label class="mark angle">&#xf078;</label>
                                            <input type="hidden" name="save[]" value="1{}1-0x4000{}{}message{}">
                                            <input type="checkbox" name="permission[]" value="1-0x4000" id="p006" <?php setPermissionCheck($permissionString, "1-4000"); ?>>
                                            <label class="label label1" for="p006">Message (view)</label>
                                        </span>
                                        <ul class="padding">
                                            <div class="padding2">
                                                <span class="item1">
                                                    <input type="hidden" name="save[]" value="2{}1-0x40{}1-0x4000{}Create New{}">
                                                    <input type="checkbox" name="permission[]" value="1-0x40" data-permission1="1-0x4000" id="p007" <?php setPermissionCheck($permissionString, "1-40"); ?>>
                                                    <label class="label label4" for="p007">Create New</label>
                                                </span>
                                                <span class="item1">
                                                    <input type="hidden" name="save[]" value="2{}1-0x80{}1-0x4000{}Delete{}">
                                                    <input type="checkbox" name="permission[]" value="1-0x80" data-permission1="1-0x4000" id="p008" <?php setPermissionCheck($permissionString, "1-80"); ?>>
                                                    <label class="label label4" for="p008">Delete</label>
                                                </span>
                                            </div>
                                        </ul>
                                        <div class="padding2">
                                            <span class="item1">
                                                <input type="hidden" name="save[]" value="1{}1-0x1{}{}Affiliation / content group setting、Target setting{}">
                                                <input type="checkbox" name="permission[]" value="1-0x1" id="p009" <?php setPermissionCheck($permissionString, "1-1"); ?>>
                                                <label class="label label4" for="p009">Affiliation / content group setting、Target setting</label>
                                            </span>
                                            <span class="item1">
                                                <input type="hidden" name="save[]" value="1{}1-0x20{}{}Attendance status{}">
                                                <input type="checkbox" name="permission[]" value="1-0x20" id="p010" <?php setPermissionCheck($permissionString, "1-20"); ?>>
                                                <label class="label label4" for="p010">Attendance status</label>
                                            </span>
                                        </div>
                                    </ul>
                                    </div>
                                    <?php
                                        if($permission_error) {
                                            echo "<p class='attention'>※ permission is not selected</p>";
                                        }
                                    ?>
                                </dd>
                            </dl>
                        <?php } ?>

                            <!-- ボタン -->
                            <dl class="input-group border-top">
                                <ul class="clearfix btngroup">
                                    <li class="save" name="submit"><button>Preservation</button></li>
                                    <input type="hidden" name="insert_flag" value="1" />
                                    <input type="hidden" name="primary_id" value="<?php if(isset($admin_data['admin_id'])) { echo $admin_data['admin_id']; } else { echo '0'; } ?>" />
                                </ul>
                            </dl>

                        </form>

                    </div>

                    <!-- CSV -->
                    <!--
                    <div class="tab-pane fade" id="csv" role="tabpanel">
                        <!-- CSV登録 ->
                        <dl class="input-group">
                            <form action="<?php //echo $_SERVER['REQUEST_URI'] ?>" method="POST" onsubmit="return submitCheck('受講者情報をCSVで一括登録・変更します。よろしいですか？')" enctype="multipart/form-data">
                                <dt>CSV登録</dt>
                                <dd>
                                    <input type="file" name="csv_input_file" accept=".csv">
                                </dd>
                                <ul class="clearfix btngroup">
                                    <li><a href="../../../library/category/treecalc.php" target="_blank" class="usergroup-acq">所属グループ文字の取得</a></li>
                                    <li class="save" name="submit"><button>登 録</button></li>
                                    <input type="hidden" name="csv_input_flag" value="1" />
                                    <?php
                                        /*
                                        if(isset($csv_input_error) && filter_input(INPUT_POST, "csv_input_flag")) {
                                            echo "<p class='attention' id='not_id'>CSVファイルの形式に問題があります(" . ($csv_input_error_line + 1) . "行目：「" . $csv_input_error_column . "」項目)</p>";
                                        } else if($id_error) {
                                            echo "<p class='attention' id='overlaped_id'>生徒IDに重複があります。別の値を入力して下さい。</p>";
                                        }
                                        */
                                    ?>
                                </ul>
                            </form>
                        </dl>
                        <dl class="input-group border-top">
                            <dt>CSV出力</dt>
                            <ul class="clearfix btngroup">
                                <li class="save">
                                <form
                                      action=<?php //echo $url . 'download_csv.php'; ?>
                                      method='post'
                                      target='hidden-iframe'
                                      class='csv'
                                      >
                                    <input type='hidden' name='data' value="<?php //echo $csv_data; ?>">
                                    <input type='hidden' name='name' value="<?php //echo $csv_name; ?>">
                                    <input type='submit' value=''>
                                    CSV出力
                                </form>
                                </li>
                            </ul>
                        </dl>
                    </div>
                    -->
                </div>

            </div>

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
