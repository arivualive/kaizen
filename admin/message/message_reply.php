<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-40")) {
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
$message_reply_model = new GetAdminMessageReply($admin_id, $school_id, $message_id, $curl);

//リストの取得（メッセージ）
$student_data = $message_reply_model->getMessageReplyStudentList();
$teacher_data = $message_reply_model->getMessageReplyTeacherList();
//$admin_data = $message_reply_model->getMessageReplyAdminList();
$grade_data = $message_reply_model->getMessageReplyGradeList();
$course_data = $message_reply_model->getMessageReplyCourseList();
$classroom_data = $message_reply_model->getMessageReplyClassroomList();
$message_data = $message_reply_model->getMessageReplyMessageData();
$userLevel = 'NULL';

//debug($teacher_data);
//debug($admin_data);

//$admin_data = new GetStudentMessage($admin_id, $school_id, $curl);
//$test = $admin_data->getStudentDataId();
//debug($test);

if (filter_input(INPUT_POST, "sendFlag") == true) {
    //フォームで入力された内容
    $data['receive_user_level_id'] = filter_input(INPUT_POST, "receive_user_level_id");
    $receive_user_id = explode(",",filter_input(INPUT_POST, "receive_user_id"));
    $data['grade_id'] = filter_input(INPUT_POST, "grade_id");           //学生側では'NULL'のみ
    $data['grade_id'] = 0;           //学生側では'NULL'のみ
    $data['course_id'] = filter_input(INPUT_POST, "course_id");         //学生側では'NULL'のみ
    $data['course_id'] = 0;         //学生側では'NULL'のみ
    $data['classroom_id'] = filter_input(INPUT_POST, "classroom_id");   //学生側では'NULL'のみ
    $data['classroom_id'] = 0;   //学生側では'NULL'のみ
    //debug($data['classroom_id']);
    $data['title'] = htmlspecialchars(filter_input(INPUT_POST, "title"));
    $data['message'] = htmlspecialchars(filter_input(INPUT_POST, "message"));

    //INSERT : tbl_message
    //返信画面ではtbl_messageには返信しない
    //$message_id = $message_reply_model->setMessageReplyInsertMessage($data);
    //$data['message_id'] = $message_id['message_id'];
    //debug($data['message_id']);

    //INSERT : tbl_message_detail
    $message_detail_id = $message_reply_model->setMessageReplyInsertMessageDetail($data);
    $data['message_detail_id'] = $message_detail_id['message_detail_id'];
    //debug($data['message_detail_id']);

    //INSERT : tbl_message_target
    //返信先は複数人の場合もあるため、返信先の人数分だけ繰り返し
    for( $i = 0 ; $i < count($receive_user_id) ; $i++) {
        $data['receive_user_id'] = $receive_user_id[$i];
        $message_target_id = $message_reply_model->setMessageReplyInsertMessageTarget($data);
        $data['message_target_id'] = $message_target_id['message_target_id'];
        //debug($data['message_target_id']);
    }

    //if(count($data['message_target_id'])) {
    if(!empty($data['message_target_id'])) {
        header("Location: /admin/message/message_detail.php?id=$message_id");
    } else {
        //debug('メッセージの作成に失敗しました');
    }
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
    <!-- modal(コンテンツ詳細) -->
    <div class="modal fade messagecheck" id="Modal-messagecheck" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="text">Confirmation of message content</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- 宛先 -->
                    <dl class="destination-user">
                        <dt>destination</dt>
                        <dd>
                            <span class="modal-receive-level"></span>
                            <span class="modal-receive"></span>
                        </dd>
                    </dl>
                    <!-- 作成したメッセージ内容 -->
                    <dl class="message-contents">
                        <dt>detail</dt>
                        <dd class="modal-message" style="white-space:pre-wrap;"></dd>
                    </dl>
                    <ul class="btns">
                        <li class="cancel"><button data-dismiss="modal" aria-label="Close"><span aria-hidden="true">× Cancel</span></button></li>
                        <!-- 送信ボタン -->
                        <form action='' method='post' name='send_form' id='send_form'>
                            <li class="submit" name="submit"><button form='send_form' id="submit">Send with this content</button></li>
                            <input form='send_form' type="hidden" name="sendFlag">
                            <input form='send_form' type="hidden" name="title">
                            <input form='send_form' type="hidden" name="message">
                            <input form='send_form' type="hidden" name="type">
                            <input form='send_form' type="hidden" name="receive_user_level_id">
                            <input form='send_form' type="hidden" name="receive_user_id">
                            <input form='send_form' type="hidden" name="grade_id">
                            <input form='send_form' type="hidden" name="course_id">
                            <input form='send_form' type="hidden" name="classroom_id">
                        </form>
                    </ul>
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
                    <p class="authority">Send with this content</p>
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
                <li><a href="message_detail.php?id=<?php echo $message_id ?>">Message Details</a></li>
                <li class="active"><a>Message reply</a></li>
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

    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>Message reply</h2>
        </div>
        <!-- ▲h2 -->

        <div id="message-detail">
            <div class="message-detail-box">
                <!-- head -->

                <!-- 返信時のみ表示 公開(public)or非公開(private) -->
                    <?php
                        if($message_data[0]['type'] == 1) {
                            echo "
                                <div class='message-detail-head type-public'>
                                    <p class='message-type'>Group message</p>
                                    <p class='message-title'>" . $message_data[0]['title'] . "</p>
                                </div>
                            ";
                        } else if($message_data[0]['type'] == 2) {
                            echo "
                                <div class='message-detail-head type-private'>
                                    <p class='message-type'>Private message</p>
                                    <p class='message-title'>" . $message_data[0]['title'] . "</p>
                                </div>
                            ";
                        };
                    ?>

                <!-- 新規メッセージ作成のみ表示 -->
                <!--
                <div class="message-detail-head newmessage">
                    <p>新規メッセージ作成</p>
                </div>
                 -->

                <!-- body -->
                <div class="message-create-body">
                    <!-- 返信先(destination user) -->
                    <div id="destination-user" class="item">
                        <div class="head">
                            <p>Destination</p>
                        </div>
                        <div class="body">
                            <div id="select-user" class="select-category">
                                <!-- 教員・生徒 -->
                                <p class="category">Choose from teachers and students</p>
                                <div class="clearfix">
                                    <div class="select-group">
                                        <div class="in">
                                            <div class="top">
												<div class="user-category">Teacher</div>
											</div>
                                            <ul class="scrol">
                                                <?php foreach ((array) $teacher_data as $item): ?>
                                                    <li>
                                                    <label class="checkbox">
                                                        <input type="checkbox" class="input-teacher" value="<?php echo $item['teacher_id']?>|<?php echo $item['teacher_name']?>" checked>
                                                        <span class="icon"></span>
                                                        <p><?php echo $item['teacher_name']?></p>
                                                    </label>
                                                </li>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <div class="top">
												<div class="user-category">Student</div>
											</div>
                                            <ul class="scrol">
                                                <?php foreach ((array) $student_data as $item): ?>
                                                    <li>
                                                    <label class="checkbox">
                                                        <input type="checkbox" class="input-student" value="<?php echo $item['student_id']?>|<?php echo $item['student_name']?>" checked>
                                                        <span class="icon"></span>
                                                        <p><?php echo $item['student_name']?></p>
                                                    </label>
                                                </li>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 学年・コース・ユニット(管理者・講師が新規作成時のみ表示) -->
                        </div>
                    </div>
                    <!-- 内容作成(text create) -->
                    <div id="message-create" class="item">
                        <div class="head">
                            <p>Detail creation</p>
                        </div>
                        <div class="body">
                            <!-- メッセージ内容 -->
                            <div id="message-contents" class="input-category">
                                <p class="title">Message content<small>Within 2000 characters</small></p>
                                <div class="input-erea">
                                    <textarea name="message" id="input-message" maxlength="2000"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 管理者・講師が新規作成するときのみここにメッセージタイプ選択が入る -->

                    <!-- 内容確認 -->
                    <div id="last-check" class="item">
                        <div class="head">
                            <p>Detail confirmation</p>
                        </div>
                        <div class="body">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-40")) { ?>
                            <div>
                                <a data-toggle="modal" data-target="#Modal-messagecheck" id="modal-check">Checking the detail</a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ▲main -->
</div>
<script src="js/message_reply.js"></script>

</body>
</html>
