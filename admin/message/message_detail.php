<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOnArray($permission, "1-4000")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄

    header('Location: ../auth/index.php');
    exit();
}

$curl = new Curl($url);

//メッセージIDの取得処理
if (filter_input(INPUT_GET, "id")) {
    $message_id = filter_input(INPUT_GET, "id");
    $_SESSION['message_id'] = $message_id;
}

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$admin_id = $_SESSION['auth']['admin_id'];
$school_id = $_SESSION['auth']['school_id'];
$message_detail_model = new GetAdminMessageDetail($admin_id, $school_id, $message_id, $curl);

//削除ボタン判定および削除処理
if (filter_input(INPUT_POST, "delete") != '') {
    $message_detail_id = filter_input(INPUT_POST, "delete");
    $message_detail_model->setMessageDetailDelete($message_detail_id);
}

//メッセージのスレッドデータ(メッセージ種類・タイトル・作成者・作成日)の取得
$title_data = $message_detail_model->getMessageDetailTitle();

//アラートメッセージ関連(現時点では見れないものはリストへ遷移するのみ)
if(count($title_data) == 0){
    echo "<script>alert('I can not see this message.');</script>";
    header('Location: /admin/message/message_list.php?p=1');
    exit();
} else {
    //メッセージの確認状況更新
    $message_detail_model->getMessageDetailDateCheck();
}

//表示されるリストの合計数を取得(offsetに利用)
$max_rows = $title_data[0]['count'];
//debug($max_rows);

//limit値の設定
$limit = 5;

//offset値の設定
$current = filter_input(INPUT_GET, 'p');
$paginate = new Paginate($current, $max_rows, $limit);
$offset = $paginate->getOffset();

//リストの取得（メッセージ）
$detail_data = $message_detail_model->getMessageDetailOffset($limit, $offset);
$order = filter_input(INPUT_GET, 'o');

$receiver_data = [];
$type = $title_data[0]['type'];
$i = 0;
foreach((array) $detail_data as $item) {
    $receiver_data[$i] = $message_detail_model->getMessageDetailReceiver($item['message_detail_id'], $type);
    $i++;
}
//debug($receiver_data);

//以下、プルダウン等によるソート処理の原案
//if ('id' == $order) {
//    $detail_data = array('repository' => 'MessageDetailRepository', 'method' => 'findMessageDetailOrderById','params' => array('limit' => $limit, 'offset' => $offset) );
//}

//if ('name' == $order) {
//    $detail_data = array('repository' => 'MessageDetailRepository', 'method' => 'findMessageDetailOrderByName','params' => array('limit' => $limit, 'offset' => $offset) );
//}

$result = $detail_data;

//$admin_data = new GetAdminMessage($admin_id, $school_id, $curl);
//$test = $admin_data->getStudentDataId();
//debug($test);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Thinkboard LMS students</title>
	<!-- favicon -->
	<link rel="shortcut icon" href="../images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/message.css">
    <!-- js -->
    <!--
    <script src="https://code.jquery.com/jquery-2.2.3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    -->
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
                    <li class="open">
                        <a href="../message/message_list.php" class="active"><span class="icon-main-message"></span>message</a>
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
                <li><a href="message_list.php">Message</a></li>
                <li class="active"><a>Message Details</a></li>
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
            <h2>Message Details</h2>
        </div>
        <!-- ▲h2 -->

        <div id="message-detail">
            <div class="message-detail-box">
                <!-- head -->
                <?php foreach ((array) $title_data as $item): ?>
                    <?php if($item['type'] == 0) {
                        echo '
                        <div class="message-detail-head type-notice"><!-- お知らせ(notice)or公開(public)or非公開(private) -->
                            <p class="message-type">Notice</p>
                    '; }; ?>
                    <?php if($item['type'] == 1) {
                        echo '
                        <div class="message-detail-head type-public"><!-- お知らせ(notice)or公開(public)or非公開(private) -->
                            <p class="message-type">Group message</p>
                    '; }; ?>
                    <?php if($item['type'] == 2) {
                        echo '
                        <div class="message-detail-head type-private"><!-- お知らせ(notice)or公開(public)or非公開(private) -->
                            <p class="message-type">Private message</p>
                    '; }; ?>
                            <p class="message-title"><?php echo $item['title']; ?></p>
                        </div>
                <?php endforeach;?>

                <!-- body -->
                <div class="message-detail-body">
                    <div class="otherinformation navbar-expand-lg">
                        <a class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span>Detailed information</span>
                        </a>
                        <div class="collapse navbar-collapse in" id="navbarSupportedContent">
                            <?php foreach ((array) $title_data as $item): ?>
                                <!-- メッセージ作成者 -->
                                <dl class="created-user">
                                    <dt>Author</dt>
                                    <dd><?php echo $item['auther']; ?></dd>
                                </dl>
                                <!-- メッセージ作成日 -->
                                <dl class="created-date">
                                    <dt>Created date</dt>
                                    <dd><?php echo $item['register_datetime']; ?></dd>
                                </dl>
                            <?php endforeach;?>
                            <!-- メッセージ公開範囲(受講者名orコース・ユニット名)　※公開メッセージの場合のみ表示 -->
                            <dl class="open-range">
                                <dt>Open range</dt>
                                <?php //foreach ((array) $receiver_data[0] as $item): ?>
                                    <?php //if($item['grade'] != "") { echo '<dd>' . $item['grade'] . '</dd>'; }; ?>
                                    <?php //if($item['course'] != "") { echo '<dd>' . $item['course'] . '</dd>'; }; ?>
                                    <?php //if($item['classroom'] != "") { echo '<dd>' . $item['classroom'] . '</dd>'; }; ?>
                                    <?php //if($item['receiver'] != "") { echo '<dd>' . $item['receiver'] . '</dd>'; }; ?>
                                <?php //endforeach;?>
<?php
	$count = 0;
	foreach((array)$receiver_data[0] as $item) {
		$count ++;
		if($count > 10) { break; }
		if($item['receiver'] != "") { echo '<dd>' . $item['receiver'] . '</dd>' . "\n"; }
	}
  // 2019/6/03 count関数対策
  if(is_countable($receiver_data[0])) {
    if(count($receiver_data[0]) > 10) {
  		echo '<br>' . "\n";
  		echo '<dd>... other' . (count($receiver_data[0]) - 10) . 'name</dd>' . "\n";
  	}
  }
  /*
	if(count($receiver_data[0]) > 10) {
		echo '<br>' . "\n";
		echo '<dd>... 他' . (count($receiver_data[0]) - 10) . '名</dd>' . "\n";
	}
  */
?>
                            </dl>
                        </div>
                    </div>
                    <div class="talkerea">
                        <?php foreach ((array) $title_data as $item): ?>
                        <?php if($item['type'] != 0) {
                          if ($isManager || isPermissionFlagOn($permission, "1-40")) {
                            echo "
                            <div class='btn-reply'>
                                <a href='message_reply.php?id=" . $message_id . "'>Send back</a>
                            </div>
                            ";
                          }
                        }; ?>
                        <?php endforeach;?>
                        <div class="thread-group">
                            <?php $i = 0; ?>
                            <?php foreach ((array) $detail_data as $item): ?>
                                <?php if($item['enable'] === '1') { ?>
                                    <div class="thread-item
                                        <?php if($item['send_user_level_id'] == 1) { echo 'admin'; }; ?>
                                        <?php if($item['send_user_level_id'] == 2) { echo 'teacher'; }; ?>
                                        <?php if($item['send_user_level_id'] == 3) { echo 'student'; }; ?>
                                    ">
                                        <div class="head clearfix">
                                            <!-- 書込み者名 -->
                                            <p class="username">sender:<?php echo $item['sender']; ?></p>
                                            <!-- 削除ボタン -->
                                            <?php
                                                if($item['send_flag']==1 && ($isManager || isPermissionFlagOn($permission, "1-80"))) {
                                                    echo "
                                                        <div class='btn-delete'>
                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST'>
                                                                <button>Delete</button>
                                                                <input type='hidden' name='delete' value='" . $item['message_detail_id'] . "'/>
                                                            </form>
                                                        </div>
                                                    ";
                                                };
                                            ?>
                                            <!-- 書込み日 -->
                                            <p class="last-day">Send date:<?php echo $item['register_datetime']; ?></p>
                                        </div>
                                        <div class="writing" style="white-space:pre-wrap;"><?php echo $item['message']; ?></div>
                                        <!-- 返信先 -->
                                        <div class="destination">
                                            Receiver:
                                            <?php //foreach ((array) $receiver_data[$i] as $item): ?>
                                                <?php //if($item['grade'] != "") { echo $item['grade'] . '&nbsp;&nbsp;&nbsp;&nbsp;'; }; ?>
                                                <?php //if($item['course'] != "") { echo $item['course'] . '&nbsp;&nbsp;&nbsp;&nbsp;'; }; ?>
                                                <?php //if($item['classroom'] != "") { echo $item['classroom'] . '&nbsp;&nbsp;&nbsp;&nbsp;'; }; ?>
                                                <?php //if($item['receiver'] != "") { echo $item['receiver'] . '&nbsp;&nbsp;&nbsp;&nbsp;'; }; ?>
                                            <?php //endforeach; ?>
<?php
	$count = 0;
	foreach((array)$receiver_data[$i] as $item) {
		$count ++;
		if($count > 3) { break; }

		if($item['receiver'] != "") {
			if($count != 1) { echo ', '; }
			echo $item['receiver'];
		}
	}
  // 2019/6/03 count関数対策
  if(is_countable($receiver_data[0])){
    if(count($receiver_data[0]) > 3) {
  		echo '... other' . (count($receiver_data[0]) - 3) . 'name' . "\n";
  	}
  }
  /*
	if(count($receiver_data[0]) > 3) {
		echo '... 他' . (count($receiver_data[0]) - 3) . '名' . "\n";
	}
  */
?>
                                        </div>
                                    </div>
                                <?php } else if($item['enable'] === '0') { ?>
                                    <div class="thread-item
                                        <?php if($item['send_user_level_id'] == 1) { echo 'admin'; }; ?>
                                        <?php if($item['send_user_level_id'] == 2) { echo 'teacher'; }; ?>
                                        <?php if($item['send_user_level_id'] == 3) { echo 'student'; }; ?>
                                    ">
                                        <div class="head clearfix">
                                            <!-- 書込み者名 -->
                                            <p class="username">sender:<?php echo $item['sender']; ?></p>
                                            <!-- 書込み日 -->
                                            <p class="last-day">Send date:<?php echo $item['register_datetime']; ?></p>
                                            <!-- 削除済の表示 -->
                                            <!-- <p>削除済&nbsp;&nbsp;&nbsp;&nbsp;</p> -->
                                        </div>
                                        <div class="writing">This message has been deleted.</div>
                                        <!-- <div class="writing"><?php echo $item['message']; ?></div> -->
                                        <!-- 返信先 -->
                                    </div>
                                <?php }; ?>
                                <?php $i++ ?>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ▲main -->
</div>


</body>
</html>
