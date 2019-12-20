<?php
require dirname(filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . '/config.php';
//login_check('/admin/auth/');

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
    $receive_user_id = split(",",filter_input(INPUT_POST, "receive_user_id"));
    $data['grade_id'] = filter_input(INPUT_POST, "grade_id");           //学生側では'NULL'のみ
    $data['course_id'] = filter_input(INPUT_POST, "course_id");         //学生側では'NULL'のみ
    $data['classroom_id'] = filter_input(INPUT_POST, "classroom_id");   //学生側では'NULL'のみ
    //debug($data['classroom_id']);
    $data['title'] = filter_input(INPUT_POST, "title");
    $data['message'] = filter_input(INPUT_POST, "message");

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

    if(count($data['message_target_id'])) {
        header("Location: /admin/template/message/message_detail.php?id=$message_id");
    } else {
        //debug('メッセージの作成に失敗しました');
    }
}

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
	<link rel="shortcut icon" href="images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/message.css">
    <!-- js -->
    <script src="https://code.jquery.com/jquery-2.2.3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
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
                    <p class="text">メッセージ内容の確認</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- 宛先 -->
                    <dl class="destination-user">
                        <dt>宛先</dt>
                        <dd>
                            <span class="modal-receive-level"></span>
                            <span class="modal-receive">
                        </dd>
                    </dl>
                    <!-- タイトル(新規作成時のみ) -->
                        <!--
                        <dl class="message-title">
                            <dt>タイトル</dt>
                            <dd class="modal-title"></dd>
                        </dl>
                         -->
                    <!-- 作成したメッセージ内容 -->
                    <dl class="message-contents">
                        <dt>内容</dt>
                        <dd class="modal-message"></dd>
                    </dl>
                    <ul class="btns">
                        <li class="cancel"><button data-dismiss="modal" aria-label="Close"><span aria-hidden="true">× キャンセル</span></button></li>
                        <!-- 返信ボタン -->
                        <form action='' method='post' name='send_form' id='send_form'>
                            <li class="submit" name="submit"><button form='send_form' id="submit">この内容で返信する</button></li>
                            <input form='send_form' type="hidden" name="sendFlag">
                            <input form='send_form' type="hidden" name="title">
                            <input form='send_form' type="hidden" name="message">
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
                            <p class="erea-icon"><span class="icon-user-admin"></span></p>
                            <p class="erea-username"><?php echo $_SESSION['auth']['admin_name']; ?></p> <!-- ここにユーザーの名前が入ります -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-userinfo">
                            <li class="PW">
                                <a href="#">パスワード変更</a>
                            </li>
                            <li class="loguot">
                                <a href="#">ログアウト</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="#"><span>ヘルプ</span></a>
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
                            <a href=""><span>講義受講</span></a>
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
                <p class="text">メッセージ返信</p>
                <ul class="btns">
                    <li><a href="message_list.php?p=1">一覧へ戻る</a></li>
                    <li><a href="message_detail.php?id=<?php echo $message_id ?>">詳細へ戻る</a></li>
                    <!-- <li><a>詳細へ戻る</a></li> -->
                </ul>
            </div>
            <div class="message-detail-box">
                <!-- head -->
                
                <!-- 返信時のみ表示 公開(public)or非公開(private) -->
                    <?php
                        if($message_data[0]['type'] == 1) {
                            echo "
                                <div class='message-detail-head type-public'>
                                    <p class='message-type'>公開メッセージ</p>
                                    <p class='message-title'>" . $message_data[0]['title'] . "</p>
                                </div>
                            ";
                        } else if($message_data[0]['type'] == 2) {
                            echo "
                                <div class='message-detail-head type-private'>
                                    <p class='message-type'>非公開メッセージ</p>
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
                            <p>返信先</p>
                        </div>
                        <div class="body">
                            <div id="select-user" class="select-category">
                                <!-- 教員・生徒 -->
                                <p class="category">教員・生徒から選択</p>
                                <div class="clearfix">
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">教員</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $teacher_data as $item): ?>
                                                    <label><li><input type="checkbox" class="input-teacher" value=<?php echo $item['teacher_id']?>|<?php echo $item['teacher_name']?>><p><?php echo $item['teacher_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">生徒</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $student_data as $item): ?>
                                                    <label><li><input type="checkbox" class="input-student" value=<?php echo $item['student_id']?>|<?php echo $item['student_name']?>><p><?php echo $item['student_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 学年・コース・ユニット(管理者・講師が新規作成時のみ表示) -->

                            <div id="select-coursegroup" class="select-category">
                                <p class="category">コース・講座</p>
                                <div class="clearfix">
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">年度</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $grade_data as $item): ?>
                                                    <label><li><input type="radio" name="grade" class="input-grade" value=<?php echo $item['grade_id']?>|<?php echo $item['grade_name']?>><p><?php echo $item['grade_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">コース</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $course_data as $item): ?>
                                                    <label><li><input type="radio" name="course" class="input-course" value=<?php echo $item['course_id']?>|<?php echo $item['course_name']?>><p><?php echo $item['course_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">ユニット</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $classroom_data as $item): ?>
                                                    <label><li><input type="radio" name="classroom" class="input-classroom" value=<?php echo $item['classroom_id']?>|<?php echo $item['classroom_name']?>><p><?php echo $item['classroom_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 内容作成(text create) -->
                    <div id="message-create" class="item">
                        <div class="head">
                            <p>内容作成</p>
                        </div>
                        <div class="body">
                            <!-- メッセージタイトル(新規作成時のみ表示) -->
                                <!-- 
                                <div id="message-title" class="input-category">
                                    <p class="title">タイトル<small>3文字以上255文字以内</small></p>
                                    <div class="input-erea">
                                        <input type="text" name="title" id="input-title">
                                    </div>
                                </div>
                                 -->
                            <!-- メッセージ内容 -->
                            <div id="message-contents" class="input-category">
                                <p class="title">メッセージ内容<small>3文字以上2000文字以内</small></p>
                                <div class="input-erea">
                                    <input type="text" name="message" id="input-message">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 管理者・講師が新規作成するときのみここにメッセージタイプ選択が入る -->
                    
                    <!-- 内容確認 -->
                    <div id="last-check" class="item">
                        <div class="head">
                            <p>内容確認</p>
                        </div>
                        <div class="body">
                            <div>
                                <a data-toggle="modal" data-target="#Modal-messagecheck" id="modal-check">内容を確認する</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/message_reply.js"></script>

</body>
</html>
