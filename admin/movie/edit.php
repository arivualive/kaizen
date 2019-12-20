<?php
require_once "../../config.php";
require_once "../../library/permission.php";
require_once( '../class.player_modal.php' );
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
$modal = new modalCreate();

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}
//主キー情報の取得
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $data['contents_id'] = $id;
}

$movieInfo = new AdminMovieRegistModel($school_id, $curl);

//編集対象のデータを読み込む
$data = $movieInfo->getContents($data);

if ( $data[ 'contents_extension_id' ] < 7 ) {
  $data[ 'file_contents' ] = $data[ 'contents_id' ].'.deploy';
} else if ( $data[ 'contents_extension_id' ] == 7 ) {
  $data[ 'file_contents' ] = $data[ 'contents_id' ].'.mp4';
} else {
  $data[ 'file_contents' ] = $data[ 'contents_id' ].'.MP4';
}

$data[ 'file_path' ] = $_SERVER['SERVER_NAME'].'/file/contents/'.$data[ 'file_contents' ];

//debug($data);
$attachData = $movieInfo->getContentsAttachment($data);
//debug($attachData);

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
//            $subject_genre_na350me = $subject_data[$i]['subject_genre_name'];
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
  $testss = 'test555';
    if(filter_input(INPUT_POST, "contents_name")) {
        $data['contents_name'] = htmlspecialchars(filter_input(INPUT_POST, "contents_name"));
        $testss = 'test';
    }

    if(filter_input(INPUT_POST, "comment")) {
        $data['comment'] = htmlspecialchars(filter_input(INPUT_POST, "comment"));
    }

    if(filter_input(INPUT_POST, "first_day")) {
        $data['first_day'] = filter_input(INPUT_POST, "first_day");
    } else {
        $data['first_day'] = '0000-01-01';
    }

    if(filter_input(INPUT_POST, "last_day")) {
        $data['last_day'] = filter_input(INPUT_POST, "last_day");
    } else {
        $data['last_day'] = '9999-12-31';
    }

    if(isset($_FILES['contents_file'])) {
        $file_info = explode(".", $_FILES['contents_file']['name']);
        //ファイル名のみを格納
        $data['contents_file_name'] = $file_info[0];

        //値はDB準拠
        switch($file_info[1]) {
            case 'TBO': // type=1
                $data['contents_extension_id'] = 1;
                $data['extension'] = ".deploy";
                break;

            case 'TBON': // type=2
                $data['contents_extension_id'] = 2;
                $data['extension'] = ".deploy";
                break;

            case 'TBO-L': // type=3
                $data['contents_extension_id'] = 3;
                $data['extension'] = ".deploy";
                break;

            case 'TBO-LN': // type=4
                $data['contents_extension_id'] = 4;
                $data['extension'] = ".deploy";
                break;

            case 'TBO-M': // type=5
                $data['contents_extension_id'] = 5;
                $data['extension'] = ".deploy";
                break;

            case 'TBO-MN': // type=6
                $data['contents_extension_id'] = 6;
                $data['extension'] = ".deploy";
                break;

            case 'mp4': // type=7
                $data['contents_extension_id'] = 7;
                $data['extension'] = ".mp4";
                break;

            case 'MP4': // type=8
                $data['contents_extension_id'] = 8;
                $data['extension'] = ".MP4";
                break;
        }

        $data['size'] = $_FILES['contents_file']['size'];
        $data['contents_tmp_name'] = $_FILES['contents_file']['tmp_name'];
        $data['contents_error'] = $_FILES['contents_file']['error'];
    } else {
        $data['contents_file'] = 'not file';
    }

    if(isset($_FILES['attach_file'])) {
        $data['attach_file_name'] = htmlspecialchars($_FILES['attach_file']['name']);
        $data['attach_tmp_name'] = htmlspecialchars($_FILES['attach_file']['tmp_name']);
        $data['attach_error'] = $_FILES['attach_file']['error'];
    } else {
        $data['attach_file'] = 'not file';
    }

    //その他（インサートデータ）
    $data['contents_category_id'] = 1;
    $data['subject_section_id'] = 0;
    $data['user_level_id'] = 0;
    $data['register_id'] = $admin_id;
    $data['enable'] = 1;
    $data['type'] = 0;
    $data['bit_classroom'] = $bit_classroom;
    $data['function_group_id'] = 0;

    if($data['contents_error'] == 0) {
        $test = 'test';
        $movieInfo->setContents($data, 'edit');
    }

    if (filter_input(INPUT_POST, "attach_change_flag" ) == 1) {
        $data['contents_attachment_id'] = $attachData['contents_attachment_id'];
        $movieInfo->setContentsAttachment($data, 'delete');
    }

    if($data['attach_error'] == 0) {
        //一度、DBからファイル情報を削除
        //$movieInfo->setContentsAttachment($data, 'delete');

        $movieInfo->setContentsAttachment($data, 'insert');
        $data['attach_id'] = $movieInfo->getContentsAttachMaxId()['max_contents_attachment_id'];
        $movieInfo->moveUploadFileAttach($data);
    }
    //debug($data);
    header("Location: ../contents/index.php?bid=" . $_GET['bid']);
    exit();
} else if (filter_input(INPUT_POST, "send_flag" ) && $subject_section_id == 0) {
    $subject_error = 1;
}


$modal_display = $modal->modal_display( $data );
$data[ 'file_path' ] = json_encode($data[ 'file_path' ]);

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
    <link rel="stylesheet" type="text/css" href="../css/contents.css">
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
                            <dd><button class="submit">Save</button></dd>
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
                <li><a href='../contents/index.php?bid=<?php echo $subject_section_id ?>'>Content registration / editing</a></li>
                <li class="active"><a>Video class</a></li>
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
            <h2>Video class</h2>
        </div>
        <!-- ▲h2 -->
        <form action="" method="post" name="send_form" id="send_form" enctype="multipart/form-data">

            <div id="col-movie-control" class="clearfix">

                <!-- ▼コンテンツグループ -->
                <div id="control-box-contentsgroup">

                    <div class="h3 clearfix">
                        <h3>Content group</h3>
                    </div>

                    <div class="body scrollerea">
                        <div id="subject-group" class="subject-group setting">
                            <ul class="accordion">
                            <!------CSV読込部分 ここから------>
                            <?php

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
                <!-- ▲コンテンツグループ -->

                <!-- ▼詳細情報 -->
                <div id="control-box-formgroup">

                    <div class="h3 clearfix">
                        <h3>Detailed information</h3>
                    </div>

                    <div class="body">
                        <!-- コンテンツタイトル -->
                        <dl class="input-group">
                            <dt>title<span class="text_limit">within 100 characters</span></dt>
                            <dd>
                                <textarea maxlength="100" rows="2" class="movie-title" name="contents_name"><?php $data['contents_name'] != '' ? print $data['contents_name'] : '';?></textarea>
                                <?php
                                    if($data['contents_name'] == '' && filter_input(INPUT_POST, "attach_file")) {
                                        echo "<p class='attention' id='not_title'>Title has not been entered</p>";
                                    }
                                ?>
                            </dd>
                        </dl>
                        <!-- 説明文 -->
                        <dl class="input-group">
                            <dt>Explanatory text<span class="text_limit">Within 300 characters</span></dt>
                            <dd>
                                <textarea maxlength="300" rows="2" class="xplanatory" name="comment"><?php $data['comment'] != '' ? print $data['comment'] : '';?></textarea>
                            </dd>
                        </dl>
                        <!-- 視聴完了判定値 -->
                        <dl class="input-group">
                            <dt>Viewing completion judgment value</dt>
                            <dd>
                                <input type="number" value="<?php $data['proportion'] != '' ? print $data['proportion'] : '';?>" max="100" min="0"> %
                            </dd>
                        </dl>
                        <div class="clearfix">
                            <!-- 公開日 -->
                            <dl class="input-group day-start">
                                <dt>Release date</dt>
                                <dd>
                                    <input type="text" class="datepicker" name="first_day" value="<?php $data['first_day'] != '0000-01-01' ? print str_replace("-", "/", $data['first_day']) : '';?>">
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
                            <!-- コンテンツ再生 -->
                            <dl class="input-group day-limit clearfix">
                                <dt>Content play</dt>
                                <dd>
                                    <button type="button" id="play_button" data-filePath='<?php echo $data[ 'file_path' ]; ?>'>Play</button>
                                </dd>
                                <p class="attention">※ We will watch the registered video</p>
                            </dl>
                        </div>
                        <!-- 添付ファイル -->
                        <dl class="input-group border-top">
                            <?php
                                if(isset($attachData['file_name'])) {
                                    echo "
                                        <div id='attach_on'>
                                            <dt>Attached file<button class='cancel' type='button'>Delete</button></dt>

                                              <dd id='attach_on_file_function'>
                                                  <button form='attach_form' data-tooltip='Click to download' class='attachment_hidden' type='submit' name='id' value=".$attachData[ 'contents_attachment_id' ].">". $attachData['file_name'] ."</button>
                                                  <input type='hidden' form='attach_form' name='name'  value='".$attachData[ 'file_name' ]."'/>
                                              </dd>
                                        </div>

                                        <div id='attach_off' style='display:none;'>
                                            <dt>Attached file<button class='cancel' type='button'>cancel</button></dt>
                                            <dd id='attach_off_file_function'>
                                                <input type='file' accept='.doc, .docx, .pdf, .txt, .xls, .xlsx, .ppt, .pptx, .zip, .rar, .jpeg, .jpg, .gif, .bmp, .png' name='attach_file'>
                                                <br>
                                                <span id='attach_file_info'>
                                            </dd>
                                        </div>
                                    ";
                                } else {
                                    echo "
                                        <div id='attach_on' style='display:none;'>
                                            <dt>Attached file<button class='cancel' type='button'>Delete</button></dt>
                                            <dd id='attach_on_file_function'>
                                                <span> " . $attachData['file_name'] . " </span>
                                            </dd>
                                        </div>
                                        <div id='attach_off'>
                                            <dt>Attached file<button class='cancel' type='button'>cancel</button></dt>
                                            <dd id='attach_off_file_function'>
                                                <input type='file' accept='.doc, .docx, .pdf, .txt, .xls, .xlsx, .ppt, .pptx, .zip, .rar, .jpeg, .jpg, .gif, .bmp, .png' name='attach_file'>
                                                <br>
                                                <span id='attach_file_info'>
                                            </dd>
                                        </div>
                                    ";
                                }
                            ?>
                        </dl>
                      <!--</form>-->
                    </div>

                </div>
                <!-- ▲詳細情報 -->
                <!--<form action="https://newlms-core.tbshare.net/module/download_attachment.php" method="POST" id="attach_form">

                </form>-->
            </div>

            <!-- 保存 -->
            <div id="col-mainbtn" class="clearfix">
                <ul class="clearfix">
                    <?php
                        if($subject_error) {
                            echo "<p class='attention'>※Subject-Lecture has not been selected</p>";
                        }
                    ?>
                    <li class="save" name="submit"><button form="send_form" id="submit">Save</button></li>
                    <input type="hidden" name="send_flag" value="1"/>
                    <input type="hidden" name="attach_change_flag" value="0"/>
                    <li class="back"><a href='../contents/index.php?bid=<?php echo $subject_section_id ?>'>Back to index</a></li>
                </ul>
            </div>
        </form>

        <form action="../../core/module/download_attachment.php" method="POST" id="attach_form">

        </form>

    </div>
    <?php echo $modal_display; ?>
    <!-- ▲main -->
</div>
<script type="text/javascript" src="https://tbwp3.kaizen2.net/scripts/tbwp3"></script>
</body>
</html>
