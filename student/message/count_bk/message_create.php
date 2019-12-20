<?php
require_once "../../config.php";
//login_check('/student/auth/');

$curl = new Curl($url);

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$student_id = $_SESSION['auth']['student_id'];
$school_id = $_SESSION['auth']['school_id'];
$message_create_model = new GetStudentMessageCreate($student_id, $school_id, $curl);

//リストの取得（メッセージ）
//$student_data = $message_create_model->getMessageCreateStudentList();
$teacher_data = $message_create_model->getMessageCreateTeacherList();
$admin_data = $message_create_model->getMessageCreateAdminList();
//$grade_data = $message_create_model->getMessageCreateGradeList();
//$course_data = $message_create_model->getMessageCreateCourseList();
//$classroom_data = $message_create_model->getMessageCreateClassroomList();
$userLevel = 'NULL';

//debug($teacher_data);
//debug($admin_data);

//$student_data = new GetStudentMessage($student_id, $school_id, $curl);
//$test = $student_data->getStudentDataId();
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
    $data['type'] = filter_input(INPUT_POST, "type");
    $data['message_level'] = 2;
    $data['limit_date'] = date('Y-m-d', strtotime('+1 week'));

    //INSERT : tbl_message
    $message_id = $message_create_model->setMessageCreateInsertMessage($data);
    $data['message_id'] = $message_id['message_id'];
    //debug($data['message_id']);

    //INSERT : tbl_message_detail
    $message_detail_id = $message_create_model->setMessageCreateInsertMessageDetail($data);
    $data['message_detail_id'] = $message_detail_id['message_detail_id'];
    //debug($data['message_detail_id']);

    //INSERT : tbl_message_target
    //送信先は複数人の場合もあるため、送信先の人数分だけ繰り返し
    for( $i = 0 ; $i < count($receive_user_id) ; $i++) {
        $data['receive_user_id'] = $receive_user_id[$i];
        $message_target_id = $message_create_model->setMessageCreateInsertMessageTarget($data);
        $data['message_target_id'] = $message_target_id['message_target_id'];
        //debug($data['message_target_id']);
    }

    if(count($data['message_target_id'])) {
        header('Location: /student/message/message_list.php?p=1');
    } else {
        //debug("メッセージの作成に失敗しました");
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
                            <span class="modal-receive"></span>
                        </dd>
                    </dl>
                    <!-- タイトル(新規作成時のみ) -->
                    <dl class="message-title">
                        <dt>タイトル</dt>
                        <dd class="modal-title"></dd>
                    </dl>
                    <!-- 作成したメッセージ内容 -->
                    <dl class="message-contents">
                        <dt>内容</dt>
                        <dd class="modal-message" style="white-space:pre-wrap;"></dd>
                    </dl>
                    <ul class="btns">
                        <li class="cancel"><button data-dismiss="modal" aria-label="Close"><span aria-hidden="true">× キャンセル</span></button></li>
                        <!-- 送信ボタン -->
                        <form action='' method='post' name='send_form' id='send_form'>
                            <li class="submit" name="submit"><button form='send_form' id="submit">この内容で送信する</button></li>
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
                <p class="text">メッセージ作成</p>
                <ul class="btns">
                    <li><a href="message_list.php?p=1">一覧へ戻る</a></li>
                    <!-- <li><a>詳細へ戻る</a></li> -->
                </ul>
            </div>
            <div class="message-detail-box">
                <!-- head -->

                <!-- 返信時のみ表示 公開(public)or非公開(private) -->
                <!--<div class="message-detail-head type-public">
                    <p class="message-type">公開メッセージ</p>
                    <p class="message-title">ここにメッセージタイトルが入ります</p>
                </div>-->

                <!-- 新規メッセージ作成のみ表示 -->
                <div class="message-detail-head newmessage">
                    <p>新規メッセージ作成</p>
                </div>

                <!-- body -->
                <div class="message-create-body">
                    <!-- 返信先(destination user) -->
                    <div id="destination-user" class="item">
                        <div class="head">
                            <p>宛先</p>
                        </div>
                        <div class="body">
                            <div id="select-user" class="select-category">
                                <!-- 管理者・教員 -->
                                <p class="category">管理者・教員から選択</p>
                                <div class="clearfix">
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">管理者</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $admin_data as $item): ?>
                                                    <label><li><input type="checkbox" class="input-admin" value="<?php echo $item['admin_id']?>|<?php echo $item['admin_name']?>"><p><?php echo $item['admin_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">教員</p>
                                            <ul class="scrol">
                                                <?php foreach ((array) $teacher_data as $item): ?>
                                                    <label><li><input type="checkbox" class="input-teacher" value="<?php echo $item['teacher_id']?>|<?php echo $item['teacher_name']?>"><p><?php echo $item['teacher_name']?></p></li></label>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 学年・コース・ユニット(管理者・講師が新規作成時のみ表示) -->
                            <!--
                            <div id="select-coursegroup" class="select-category">
                                <p class="category">コース・講座</p>
                                <div class="clearfix">
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">年度</p>
                                            <ul class="scrol">
                                                <label><li><input type="checkbox"><p>年度1</p></li></label>
                                                <label><li><input type="checkbox"><p>年度2</p></li></label>
                                                <label><li><input type="checkbox"><p>年度3</p></li></label>
                                                <label><li><input type="checkbox"><p>年度4</p></li></label>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">コース</p>
                                            <ul class="scrol">
                                                <label><li><input type="checkbox"><p>コース1</p></li></label>
                                                <label><li><input type="checkbox"><p>コース2</p></li></label>
                                                <label><li><input type="checkbox"><p>コース3</p></li></label>
                                                <label><li><input type="checkbox"><p>コース4</p></li></label>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <p class="top">ユニット</p>
                                            <ul class="scrol">
                                                <label><li><input type="checkbox"><p>ユニット1</p></li></label>
                                                <label><li><input type="checkbox"><p>ユニット2</p></li></label>
                                                <label><li><input type="checkbox"><p>ユニット3</p></li></label>
                                                <label><li><input type="checkbox"><p>ユニット4</p></li></label>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <!-- 内容作成(text create) -->
                    <div id="message-create" class="item">
                        <div class="head">
                            <p>内容作成</p>
                        </div>
                        <div class="body">
                            <!-- メッセージタイトル(新規作成時のみ表示) -->
                            <div id="message-title" class="input-category">
                                <p class="title">タイトル<small>3文字以上255文字以内</small></p>
                                <div class="input-erea">
                                    <input type="text" name="title" id="input-title">
                                </div>
                            </div>
                            <!-- メッセージ内容 -->
                            <div id="message-contents" class="input-category">
                                <p class="title">メッセージ内容<small>3文字以上2000文字以内</small></p>
                                <div class="input-erea">
                                    <textarea name="message" id="input-message"></textarea>
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
                                <button data-toggle="modal" data-target="#Modal-messagecheck" id="modal-check">内容を確認する</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/message_create.js"></script>

</body>
</html>
