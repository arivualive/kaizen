<?php
require_once "../../config.php";
//login_check('/student/auth/');

$curl = new Curl($url);

//メッセージIDの取得処理
if (filter_input(INPUT_GET, "id")) {
    $message_id = filter_input(INPUT_GET, "id");
    $_SESSION['message_id'] = $message_id;
}

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$student_id = $_SESSION['auth']['student_id'];
$school_id = $_SESSION['auth']['school_id'];
$message_detail_model = new GetStudentMessageDetail($student_id, $school_id, $message_id, $curl);

//削除ボタン判定および削除処理
if (filter_input(INPUT_POST, "delete") != '') {
    $message_detail_id = filter_input(INPUT_POST, "delete");
    $message_detail_model->setMessageDetailDelete($message_detail_id);
}

//メッセージのスレッドデータ(メッセージ種類・タイトル・作成者・作成日)の取得
$title_data = $message_detail_model->getMessageDetailTitle();

//アラートメッセージ関連(現時点では見れないものはリストへ遷移するのみ)
if(count($title_data) == 0){
    echo "<script>alert('このメッセージを見ることはできません。');</script>";
    header('Location: /student/message/message_list.php?p=1');
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

//$student_data = new GetStudentMessage($student_id, $school_id, $curl);
//$test = $student_data->getStudentDataId();
//debug($test);
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
	<link rel="shortcut icon" href="../images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/message.css">
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
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p> <!-- ここにユーザーの名前が入ります -->
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
                        <li>
                            <a href="../contentslist.php"><span>講義受講</span></a>
                        </li>
                        <li class="active">
                            <a href="message_list.php?p=1"><span>メッセージ</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="container-maincontents" class="container-maincontents clearfix">
        <div id="message-detail">
            <!-- page title -->
            <div class="message-detail-title clearfix">
                <p class="text">メッセージ詳細</p>
                <ul class="btns">
                    <li><a href="message_list.php?p=1">一覧へ戻る</a></li>

                </ul>
            </div>
            <div class="message-detail-box">
                <!-- head -->
                <?php foreach ((array) $title_data as $item): ?>
                    <?php if($item['type'] == 0) {
                        echo '
                        <div class="message-detail-head type-notice"><!-- お知らせ(notice)or公開(public)or非公開(private) -->
                            <p class="message-type">お知らせ</p>
                    '; }; ?>
                    <?php if($item['type'] == 1) {
                        echo '
                        <div class="message-detail-head type-public"><!-- お知らせ(notice)or公開(public)or非公開(private) -->
                            <p class="message-type">グループメッセージ</p>
                    '; }; ?>
                    <?php if($item['type'] == 2) {
                        echo '
                        <div class="message-detail-head type-private"><!-- お知らせ(notice)or公開(public)or非公開(private) -->
                            <p class="message-type">プライベートメッセージ</p>
                    '; }; ?>
                            <p class="message-title"><?php echo $item['title']; ?></p>
                        </div>
                <?php endforeach;?>

                <!-- body -->
                <div class="message-detail-body">
                    <div class="otherinformation navbar-expand-lg">
                        <a class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span>詳細情報</span>
                        </a>
                        <div class="collapse navbar-collapse in" id="navbarSupportedContent">
                            <?php foreach ((array) $title_data as $item): ?>
                                <!-- メッセージ作成者 -->
                                <dl class="created-user">
                                    <dt>作成者</dt>
                                    <dd><?php echo $item['auther']; ?></dd>
                                </dl>
                                <!-- メッセージ作成日 -->
                                <dl class="created-date">
                                    <dt>作成日</dt>
                                    <dd><?php echo $item['register_datetime']; ?></dd>
                                </dl>
                            <?php endforeach;?>
                            <!-- メッセージ公開範囲(受講者名orコース・ユニット名)　※公開メッセージの場合のみ表示 -->
                            <dl class="open-range">
                                <dt>公開範囲</dt>
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

	if(count($receiver_data[0]) > 10) {
		echo '<br>' . "\n";
		echo '<dd>... 他' . (count($receiver_data[0]) - 10) . '名</dd>' . "\n";
	}
?>
                            </dl>
                        </div>
                    </div>
                    <div class="talkerea">
                        <?php foreach ((array) $title_data as $item): ?>
                        <?php if($item['type'] != 0) {
                            echo "
                            <div class='btn-reply'>
                                <a href='message_reply.php?id=" . $message_id . "'>返信する</a>
                            </div>
                        "; }; ?>
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
                                            <p class="username">送信者：<?php echo $item['sender']; ?></p>
                                            <!-- 削除ボタン -->
                                            <?php
                                                if($item['send_flag'] == 1) {
                                                    echo "
                                                        <div class='btn-delete'>
                                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST'>
                                                                <button>削除</button>
                                                                <input type='hidden' name='delete' value='" . $item['message_detail_id'] . "'/>
                                                            </form>
                                                        </div>
                                                    ";
                                                };
                                            ?>
                                            <!-- 書込み日 -->
                                            <p class="last-day">送信日：<?php echo $item['register_datetime']; ?></p>
                                        </div>
                                        <div class="writing" style="white-space:pre-wrap;"><?php echo $item['message']; ?></div>
                                        <!-- 返信先 -->
                                        <div class="destination">
                                            受信者：
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

	if(count($receiver_data[0]) > 3) {
		echo '... 他' . (count($receiver_data[0]) - 3) . '名' . "\n";
	}
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
                                            <p class="username">送信者：<?php echo $item['sender']; ?></p>
                                            <!-- 書込み日 -->
                                            <p class="last-day">送信日：<?php echo $item['register_datetime']; ?></p>
                                        </div>
                                        <div class="writing">このメッセージは削除されました。</div>
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
</div>


</body>
</html>
