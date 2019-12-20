<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-1000")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄

    header('Location: ../auth/index.php');
    exit();
}

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
    $adminBitSubject = $_SESSION['auth']['bit_subject'];
    $isManager = $_SESSION['auth']['manage'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}

$curl = new Curl($url);
$studentInfo = new AdminStudentModel($school_id, $curl);

if (isset($_GET['id'])) {
    if($_GET['id'] != 0) {
        $data['student_id'] = $_GET['id'];
        $student_data = $studentInfo->getStudent($data, 'person')[0];
        $database = $student_data['bit_subject'];
        //debug($student_data);
    } else {
        $student_data['joining'] = 1;
    }
} else {
    $student_data['joining'] = 1;
}

if (isset($_GET['sc'])) {
    $scrool_value = $_GET['sc'];
} else {
    $scrool_value = 0;
}


//$current = filter_input(INPUT_GET, "p");
//$max_rows = 100;
//$limit = 10;

//$paginate = new Paginate($current, $max_rows, $limit);
//$offset = $paginate->getOffset();

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
//先頭の2行を削除して詰める
unset($lines[0], $lines[1]);

//------CSV読込部分 ここまで------

$list_data = $studentInfo->getStudent('', 'list');
if (!$isManager) {
    $count = removeStudentList($list_data, $adminBitSubject);
}
//$count = $studentInfo->getStudentCount($data);
//※DBからコールサインの取得するように変更予定※
$call_sign = $studentInfo->getCallSign()['call_sign'];
//debug($call_sign);

//debug($count);

$max_rows = $count['count'];
$limit = 20;

if (filter_input(INPUT_POST, "insert_flag" )) {
    $error = 0;

    if(filter_input(INPUT_POST, "student_code")) {
        $data['student_code'] = htmlspecialchars(filter_input(INPUT_POST, "student_code"));
    } else {
        $data['student_code'] = '';
    }

    if(filter_input(INPUT_POST, "student_name")) {
        $data['student_name'] = htmlspecialchars(filter_input(INPUT_POST, "student_name"));
    } else {
        $error = 1;
    }

    if(filter_input(INPUT_POST, "id")) {
        $data['id'] = htmlspecialchars($call_sign . filter_input(INPUT_POST, "id"));
        $check_flg = $studentInfo->checkStudentId($data);
        if($check_flg['check_flg'] && $check_flg['student_id'] != $data['student_id']) {
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

    //debug(filter_input(INPUT_POST, "joining"));
    if(filter_input(INPUT_POST, "joining")) {
        $data['joining'] = filter_input(INPUT_POST, "joining");
    } else {
        $data['joining'] = 0;
    }

    $database = array_to_string($_POST['category'])[0];

    if($database) {
        $data['bit_subject'] = $database;
    } else {
        $error = 1;
        $subject_error = 1;
    }

    $data['student_id'] = filter_input(INPUT_POST, "primary_id");
    if($error == 0 && $data['student_id'] == 0) {
        $studentInfo->setStudent($data, 'insert');
        $list_data = $studentInfo->getStudent('', 'list');
        if (!$isManager) {
            $count = removeStudentList($list_data, $adminBitSubject);
        }
        //debug($data['student_id']);
    } else if($error == 0 && $data['student_id'] != 0) {
        //debug($data);
        $studentInfo->setStudent($data, 'edit');
        $list_data = $studentInfo->getStudent('', 'list');
        if (!$isManager) {
            $count = removeStudentList($list_data, $adminBitSubject);
        }
        $student_data = $studentInfo->getStudent($data, 'person')[0];
        $database = $student_data['bit_subject'];
    } else if($error == 1) {
        //debug($data);
        $student_data = $data;
        $student_data['id'] = substr($student_data['id'], 3);
    }
} else if (filter_input(INPUT_POST, "delete_flag" )) {
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
        if (!$isManager) {
            $count = removeStudentList($list_data, $adminBitSubject);
        }
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
                //      $csv_input_error_column = "Membership number";
                //      break;
                    case 1:
                        $csv_input_error_column = "Name";
                        break;
                    case 2:
                        $csv_input_error_column = "Student ID";
                        break;
                    case 3:
                        $csv_input_error_column = "password";
                        break;
                    case 4:
                        $csv_input_error_column = "Attendance status";
                        break;
                    case 5:
                        $csv_input_error_column = "Group membership";
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
    if (!$isManager) {
        $count = removeStudentList($list_data, $adminBitSubject);
    }

    function studentFilter() {
        $student_data = $studentInfo->getStudent($data, 'person')[0];
    }

} else if (filter_input(INPUT_POST, "move_flag" )) {
    $flag = $_POST['move_type'];

    $studentInfo->sortStudentDisplayOrder($data, $flag);
    $list_data = $studentInfo->getStudent('', 'list');
    if (!$isManager) {
        $count = removeStudentList($list_data, $adminBitSubject);
    }
}

//debug($list_data);

//---CSV output(アンケート分布) ここから---
$csv_strings = array();

//CSV整形-アンケート項目[A1-F1]
$csv_strings[0] .= "Membership number,Name,Student ID,password,Attendance status,Group membership";

//CSV整形-アンケートデータ[A2-F*]
for($i = 0 ; $i < count($list_data) ; $i++) {
    // 2019/6/03 count関数対策
    $line_count = 0;
    if(is_countable($csv_strings)){
      $line_count = count($csv_strings);
    }
    //$line_count = count($csv_strings);                                                                                      //CSVのインサート行数設定
    $csv_strings[$line_count] .= check_string_replace($list_data[$i]['student_code']) . ",";                                //Membership number
    $csv_strings[$line_count] .= check_string_replace($list_data[$i]['student_name']) . ",";                                //Name
    $csv_strings[$line_count] .= check_string_replace($list_data[$i]['id']) . ",";                                          //受講者ID
    $csv_strings[$line_count] .= check_string_replace($list_data[$i]['password']) . ",";                                    //パスワード
    $csv_strings[$line_count] .= check_string_replace($list_data[$i]['joining']) . ",";                                     //Attendance status
    $csv_strings[$line_count] .= check_string_replace($list_data[$i]['bit_subject']) . ",";                                 //所属グループ

    //ソート機能で使用
    if($i == 0) {
        //リスト一番上のユーザーIDを取得
        $top_user = $list_data[$i]['student_id'];
    } else if($i == count($list_data) - 1) {
        //リスト一番下のユーザーIDを取得
        $bottom_user = $list_data[$i]['student_id'];
    }
}

//各行最後尾に改行追加・文字列連結
for($i = 0 ; $i < count($csv_strings) ; $i++) {
    $csv_strings[$i] .= "\r\n";
    $csv_data .= $csv_strings[$i];
}
//debug($csv_data);
$csv_name = "Student List";
//---CSV output(アンケート分布) ここまで---

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

<?php
// 2つのビットサブジェクトのビットが同じ位置で両方とも立っているかを判定する
function checkBitSubject($bitSubject1, $bitSubject2)
{
    $bitsArray1 = explode( "-", $bitSubject1 );
    $bitsArray2 = explode( "-", $bitSubject2 );

    foreach ( $bitsArray1 as $i => $bits1 ) {
        // もう一方の配列の要素がなければそこで終了
        if ( !isset($bitsArray2[$i]) ) {
            break;
        }

        // 16進数文字列から整数値に変換する
        $bits1 = hexdec( $bits1 );
        $bits2 = hexdec( $bitsArray2[$i] );

        // 両方のビット群で立っているビットがあればtrue
        if ( ($bits1 & $bits2) != 0) {
            return true;
        }
    }

    return false;
}

// 生徒リストから管理者の所属にないものを取り除く
function removeStudentList(&$studentList, $adminBitSubject)
{
    $adminBitSubject = removeParentBitsFromBitSubject($adminBitSubject, '../../library/category/csv/users.csv');

    foreach( $studentList as $i => $studentData ) {
        if ( !checkBitSubject($adminBitSubject, $studentData["bit_subject"])) {
            unset( $studentList[$i] );
        }
    }

    $studentList = array_values( $studentList );
    // 2019/6/03 count関数対策
    if(is_countable($studentList)){
      return count( $studentList );
    }
    //return count( $studentList );
}

function removeParentBitsFromBitSubject($bitSubject, $csvPath)
{
    // 文字列を数値の配列の変換
    $bitsArray = explode( "-", $bitSubject );
    foreach ($bitsArray as $i => $data) {
        $bitsArray[$i] = hexdec($data);
    }

    // 末端の階層以外のビットをオフにする
    $file = new SplFileObject( $csvPath );
    $file->setFlags( SplFileObject::READ_CSV );
    foreach ( $file as $i => $line ) {
        if ( $i <= 2 ) {
            continue;
        }

        if ( $line[0] == null ) {
            continue;
        }

        $parentBits = $line[2];

        if ( $parentBits == "" ) {
            continue;
        }

        list($arrayIndex, $bits) = explode("-", $parentBits);
        --$arrayIndex;
        $bits = hexdec($bits);

        $bitsArray[$arrayIndex] &= ~$bits;
    }

    // 数値配列を16進数文字列に変換
    foreach ($bitsArray as $i => $data) {
        $bitsArray[$i] = dechex($data);
    }
    $result = implode( "-", $bitsArray );

    return $result;
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
    <script src="js/student.js" id="js_value" data-param='<?php echo json_safe_encode($scrool_value);?>'></script>
    <script src="../js/alert.js"></script>
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
                    <li class="open">
                        <a class="togglebtn"><span class="icon-user-add"></span>Student affiliation / ID setting</a>
                        <ul class="togglemenu open">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/users.php">Affiliation group setting</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1000")) { ?>
                            <li><a href="../user/student.php" class="active">Student ID setting</a></li>
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
                <li>Student affiliation / ID setting</li>
                <li class="active"><a>Student ID setting</a></li>
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
            <h2>Student ID setting</h2>
        </div>
        <!-- ▲h2 -->

        <div id="col-userscontrol" class="clearfix">

            <!-- ▼Student List -->
            <div id="userlist">

                <div class="table-top clearfix">
                    <h3>Student List</h3>

                    <!-- 表示数 -->
                    <div class="control-erea">
                        <p class="number-desplay">
                            Displayed number <span class="real">100</span>person / <span class="default">100</span> person <!-- 現在の表示数/対象者 -->
                        </p>

                        <!-- 上下移動 ->
                        <ul class="btns-movement clearfix">
                            <?php //debug($top_user); ?>
                            <?php //debug($bottom_user); ?>
                            <?php if ($data['student_id'] == $top_user || $data['student_id'] == 0) { ?>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="top" />
                                        <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                    </form>
                                </li>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="up" />
                                        <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                    </form>
                                </li>
                            <?php } else { ?>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button class="active"></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="top" />
                                        <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                    </form>
                                </li>
                                <li>
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                        <button class="active"></button>
                                        <input type="hidden" name="move_flag" value="1" />
                                        <input type="hidden" name="move_type" value="up" />
                                        <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                    </form>
                                </li>
                            <?php } ?>
                            <?php if ($data['student_id'] == $bottom_user || $data['student_id'] == 0) { ?>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="down" />
                                    <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                </form>
                            </li>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="bottom" />
                                    <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                </form>
                            </li>
                            <?php } else { ?>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button class="active"></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="down" />
                                    <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                </form>
                            </li>
                            <li>
                                <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
                                    <button class="active"></button>
                                    <input type="hidden" name="move_flag" value="1" />
                                    <input type="hidden" name="move_type" value="bottom" />
                                    <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
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
                            <th class="id">Membership number</th>
                            <th class="name">Name</th>
                            <th class="lecture">Attending</th>
                            <th class="delete"></th>
                        </tr>
                        <tr>
                            <!-- Membership number -->
                            <td class="id">
                                <input type="text" id="filter_id">
                            </td>
                            <!-- Name -->
                            <td class="name">
                                <input type="text" id="filter_name">
                            </td>
                            <!-- 受講　可or不可 -->
                            <td class="lecture" id="filter_lecture">
                                <select>
                                    <option></option>
                                    <option>〇</option>
                                    <option>×</option>
                                </select>
                            </td>
                            <td class="delete">
                                <span><?php /*// 2019/6/03 count関数対策*/if(is_countable($list_data)){
                                    echo("all" .count($list_data) . "person");}/*echo("全" .count($list_data) . "名");*/ ?></span>
                            </td>
                        </tr>
                        <!--  <tr> 新規登録 -->
                            <tr class='listvalue new-user'><!-- 選択時 class="active" -->
                                <!-- 主キー -->
                                <td class="key" style="display:none;"><?php echo 0 ?></td>
                                <!-- Name -->
                                <td class="new" colspan="4">
                                    <?php if ($isManager || isPermissionFlagOn($permission, "1-2")) { ?>
                                    <p>sign up</p>
                                    <?php } ?>
                                </td>
                            </tr>
                    </table>

                    <!-- table(list) -->
                    <div class="scrollerea userlist">
                        <input type="hidden" id="canRegister" value="<?php echo ($isManager || isPermissionFlagOn($permission, "1-2")) ? 1 : 0; ?>">
                        <table class="userlist">
                        <?php foreach ((array)$list_data as $items): ?>
                            <?php if($items['student_id'] == $data['student_id']) { ?>
                                <tr class='listvalue active'><!-- 選択時 class="active" -->
                            <?php } else { ?>
                                <tr class='listvalue'><!-- 選択時 class="active" -->
                            <?php } ?>
                                <!-- 主キー -->
                                <td class="key" style="display:none;"><?php echo $items['student_id']; ?></td>
                                <!-- Membership number -->
                                <td class="id" filter="<?php echo $items['student_code']; ?>"><?php echo $items['student_code']; ?></td>
                                <!-- Name -->
                                <td class="name" filter="<?php echo $items['student_name']; ?>"><?php echo $items['student_name']; ?></td>
                                <!-- 受講可or不可 -->
                                <td class="lecture" filter="<?php if($items['joining'] == 1) { echo "〇"; } else { echo "×"; } ?>">
                                    <?php if($items['joining'] == 1) { echo "<span class='t'></span>"; } else { echo "<span class='f'></span>"; } ?>
                                </td>
                                <?php if ($isManager || isPermissionFlagOn($permission, "1-4")) { ?>
                                <td class="delete">
                                    <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST" onsubmit="return submitCheck('Delete student information. Is it OK?')">
                                        <button>Delete</button>
                                        <input type="hidden" name="delete_flag" value="1" />
                                        <input type="hidden" name="student_id" value="<?php echo $items['student_id']; ?>" />
                                    </form>
                                </td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                        </table>
                    </div>

                </div>
            </div>

            <!-- ▼詳細情報 -->
            <?php if ($isManager || isPermissionFlagOn($permission, "1-2")) { ?>
            <div id="controlerea">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <?php
                            if($data['student_id'] != 0) {
                                echo "<a class='nav-link active' data-toggle='tab' href='#new-regist' role='tab' aria-selected='true'>Edit</a>";
                            } else {
                                echo "<a class='nav-link active' data-toggle='tab' href='#new-regist' role='tab' aria-selected='true'>sign up</a>";
                            }
                        ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#csv" role="tab" aria-selected="false">CSV</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">

                    <!-- 新規登録 -->
                    <div class="tab-pane fade show active" id="new-regist" role="tabpanel">
                        <?php
                            if($data['student_id'] != 0) {
                                echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('Change student information. Is it OK?')\">";
                            } else {
                                echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('Register student information. Is it OK?')\">";
                            }
                        ?>
                            <div class="clearfix">
                                <!-- Membership number -->
                                <dl class="input-group w-50">
                                    <dt>Membership number</dt>
                                    <dd>
                                        <input type="text" maxlength="100" name="student_code" value="<?php echo $student_data['student_code'] ?>">
                                    </dd>
                                </dl>
                                <!-- 生徒ID -->
                                <dl class="input-group w-50">
                                    <dt>Name</dt>
                                    <dd>
                                        <input type="text" maxlength="100" name="student_name" value="<?php echo $student_data['student_name'] ?>">
                                    </dd>
                                    <?php
                                        if(!isset($data['id']) && filter_input(INPUT_POST, "insert_flag")) {
                                            echo "<p class='attention' id='not_student_name'>Your name has not been entered</p>";
                                        }
                                    ?>
                                </dl>
                            </div>
                            <div class="clearfix">
                                <!-- Name -->
                                <dl class="input-group w-50">
                                    <dt>Student ID</dt>
                                    <dd class="set-btn">
                                        <input type="text" maxlength="100" name="id" value="<?php echo $student_data['id'] ?>"><input type="button" id="auto_student_id" value="auto"></input>
                                    </dd>
                                    <?php
                                        if(!isset($data['id']) && filter_input(INPUT_POST, "insert_flag")) {
                                            echo "<p class='attention' id='not_id'>Student ID not entered</p>";
                                        } else if($id_error) {
                                            echo "<p class='attention' id='overlaped_id'>There is a duplicate in the student ID. Please enter another value.</p>";
                                        }
                                    ?>
                                </dl>
                                <!-- パスワード -->
                                <dl class="input-group w-50">
                                    <dt>password</dt>
                                    <?php
                                        if($data['student_id'] == 0) {
                                            echo "
                                                <dd class='set-btn'>
                                                    <input type='text' maxlength='100' name='password' value='" . $student_data['password'] . "'><input type='button' id='auto_student_password' value='auto'></input>
                                                </dd>
                                            ";
                                        } else {
                                            echo "
                                                <dd class='set-btn'>
                                                    <input type='password' maxlength='100' name='password' value='" . $student_data['password'] . "'>
                                                    <input type='text' maxlength='100' name='hide_password' value=''>
                                                    <input type='button' id='auto_student_password2' value='auto'></input>
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
                            <!-- 受講 -->
                            <dl class="input-group clearfix">
                                <dt>Attending</dt>
                                <dd>
                                    <label class="checkbox"><input type="radio" name="joining" value="1" value="<?php if( $student_data['joining'] == 1) { echo '"checked="true'; } ?>"><span class="icon"></span><span class="t"></span>Attendable</label>
                                    <label class="checkbox"><input type="radio" name="joining" value="0" value="<?php if( $student_data['joining'] == 0) { echo '"checked="true'; } ?>"><span class="icon"></span><span class="f"></span>Not accepted</label>
                                </dd>
                            </dl>
                            <!-- 所属情報 -->
                            <dl class="input-group border-top">
                                <dt>Group membership</dt>
                                <dd>
                                    <?php require_once(dirname(__FILE__) . '/' . $path . 'catetree.php'); //カテゴリーツリー表示用ファイルを読込み ?>
                                    <?php
                                        if($subject_error) {
                                            echo "<p class='attention'>※Subject-Lecture is not selected</p>";
                                        }
                                    ?>
                                </dd>
                            </dl>
                            <!-- ボタン -->
                            <dl class="input-group border-top">
                                <ul class="clearfix btngroup">
                                    <li class="save" name="submit"><button>Preservation</button></li>
                                    <input type="hidden" name="insert_flag" value="1" />
                                    <input type="hidden" name="primary_id" value="<?php if(isset($student_data['student_id'])) { echo $student_data['student_id']; } else { echo'0'; } ?>" />
                                </ul>
                            </dl>

                        </form>

                    </div>

                    <!-- CSV -->
                    <div class="tab-pane fade" id="csv" role="tabpanel">
                        <!-- CSV登録 -->
                        <dl class="input-group">
                            <form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST" onsubmit="return submitCheck('Register and change student information in CSV form. Is it OK?')" enctype="multipart/form-data">
                                <dt>CSV registration</dt>
                                <dd>
                                    <input type="file" name="csv_input_file" accept=".csv">
                                </dd>
                                <ul class="clearfix btngroup">
                                    <li><a href="../../../library/category/treecalc.php" target="_blank" class="usergroup-acq">Acquisition of affiliation group characters</a></li>
                                    <li class="save" name="submit"><button>Registration</button></li>
                                    <input type="hidden" name="csv_input_flag" value="1" />
                                    <?php
                                        if(isset($csv_input_error) && filter_input(INPUT_POST, "csv_input_flag")) {
                                            echo "<p class='attention' id='not_id'>There is a problem with the format of the CSV file(" . ($csv_input_error_line + 1) . "Line：「" . $csv_input_error_column . "」item)</p>";
                                        } else if($id_error) {
                                            echo "<p class='attention' id='overlaped_id'>There is a duplicate in the student ID. Please enter another value.</p>";
                                        }
                                    ?>
                                </ul>
                            </form>
                        </dl>
                        <dl class="input-group border-top">
                            <dt>CSV output</dt>
                            <ul class="clearfix btngroup">
                                <li class="save">
                                <form
                                      action=<?php echo $url . 'download_csv.php'; ?>
                                      method='post'
                                      target='hidden-iframe'
                                      class='csv'
                                      >
                                    <input type='hidden' name='data' value="<?php echo $csv_data; ?>">
                                    <input type='hidden' name='name' value="<?php echo $csv_name; ?>">
                                    <input type='submit' value=''>
                                    CSV output
                                </form>
                                </li>
                            </ul>
                        </dl>
                    </div>
                </div>

            </div>
            <?php } ?>

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
