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

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$admin_id = $_SESSION['auth']['admin_id'];
$school_id = $_SESSION['auth']['school_id'];
$message_create_model = new GetAdminMessageCreate($admin_id, $school_id, $curl);

//リストの取得（メッセージ）
$student_data = $message_create_model->getMessageCreateStudentList();
$teacher_data = $message_create_model->getMessageCreateTeacherList();
//$admin_data = $message_create_model->getMessageCreateAdminList();
$grade_data = $message_create_model->getMessageCreateGradeList();
$course_data = $message_create_model->getMessageCreateCourseList();
$classroom_data = $message_create_model->getMessageCreateClassroomList();
$userLevel = 'NULL';

//debug($teacher_data);
//debug($admin_data);

//$admin_data = new GetAdminMessage($admin_id, $school_id, $curl);
//$test = $admin_data->getStudentDataId();
//debug($test);

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


foreach($lines as $key => $line) {
    $item = explode(',', $line);

    $usersData[$key]['value'] = $item[1];
    switch ($item[0]) {
        case 1:
            $usersData[$key]['name'] = '▼' . $item[3];
            break;
        case 2:
            $usersData[$key]['name'] = '　▼' . $item[3];
            break;
        case 3:
            $usersData[$key]['name'] = '　　▼' . $item[3];
            break;
        case 4:
            $usersData[$key]['name'] = '　　　・' . $item[3];
            break;
        default:
            break;
    }
}

//print_r($list);
//------CSV読込部分 ここまで------

if (filter_input(INPUT_POST, "sendFlag") == true) {
    //フォームで入力された内容
    $data['receive_user_level_id'] = filter_input(INPUT_POST, "receive_user_level_id");
    $receive_user_id = explode(",",filter_input(INPUT_POST, "receive_user_id"));
    $data['grade_id'] = filter_input(INPUT_POST, "grade_id");           //学生側では'NULL'のみ
    $data['grade_id'] = 0;
    $data['course_id'] = filter_input(INPUT_POST, "course_id");         //学生側では'NULL'のみ
    $data['course_id'] = 0;
    $data['classroom_id'] = filter_input(INPUT_POST, "classroom_id");   //学生側では'NULL'のみ
    $data['classroom_id'] = 0;
    //debug($data['classroom_id']);
    $data['title'] = htmlspecialchars(filter_input(INPUT_POST, "title"));
    $data['message'] = htmlspecialchars(filter_input(INPUT_POST, "message"));
    $data['limit_date'] = filter_input(INPUT_POST, "limit_date");
    $data['message_level'] = filter_input(INPUT_POST, "message_level");
    $data['type'] = filter_input(INPUT_POST, "type");

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
        header('Location: /admin/message/message_list.php?p=1');
    } else {
        //debug("メッセージの作成に失敗しました");
    }
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
	<link rel="stylesheet" type="text/css" href="../css/datepicker.min.css">
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
    <script src="../js/datepicker.min.js"></script>
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
                    <!-- 学生への通知期限 -->
                    <dl class="message-limit">
                        <dt>通知期限</dt>
                        <dd class="modal-limit-date"></dd>
                    </dl>
                    <!-- 重要度 -->
                    <dl class="message-level">
                        <dt>重要度</dt>
                        <dd class="modal-message-level"></dd>
                    </dl>
                    <!-- 形式 -->
                    <dl class="message-type">
                        <dt>メッセージ形式</dt>
                        <dd class="modal-type"></dd>
                    </dl>
                    <ul class="btns">
                        <li class="cancel"><button data-dismiss="modal" aria-label="Close"><span aria-hidden="true">× キャンセル</span></button></li>
                        <!-- 送信ボタン -->
                        <form action='' method='post' name='send_form' id='send_form'>
                            <li class="submit" name="submit"><button form='send_form' id="submit">この内容で送信する</button></li>
                            <input form='send_form' type="hidden" name="sendFlag">
                            <input form='send_form' type="hidden" name="title">
                            <input form='send_form' type="hidden" name="message">
                            <input form='send_form' type="hidden" name="limit_date">
                            <input form='send_form' type="hidden" name="message_level">
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
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>コンテンツ設定</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">コンテンツグループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php">コンテンツ登録・編集</a></li>
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
                    <li class="open">
                        <a href="../message/message_list.php" class="active"><span class="icon-main-message"></span>メッセージ</a>
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
                <li><a href="message_list.php">メッセージ</a></li>
                <li class="active"><a>メッセージ新規作成</a></li>
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
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-in"></span>ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>メッセージ新規作成</h2>
        </div>
        <!-- ▲h2 -->

        <div id="message-detail">
            <div class="message-detail-box">

                <!-- body -->
                <div class="message-create-body">
                    <!-- 返信先(destination user) -->
                    <div id="destination-user" class="item">
                        <div class="head">
                            <p>宛先</p>
                        </div>
                        <div class="body">
                            <div id="select-user" class="select-category">
                                <!-- 教員・生徒 -->
                                <div class="clearfix">
                                    <div class="select-group">
                                        <div class="in">
                                            <div class="top">
												<div class="user-category">教員</div>
                                                <div class="sort-group">
                                                	<div class="all-user">
														<span class="h">全選択</span>
	                                                	<span class="b">
   	                                                		<label class="checkbox" for="allteacher">
    	                                                	<input type="checkbox" id="allteacher"><span class="icon"></span></label>
                                                		</span>
													</div>
                                                <?php if(true) {
                                                    echo "
														<div class='user-group'>
															<span class='h'>所属グループ</span>
                                                        	<span class='b pulldown'>
                                                                <select id='teacher_users_filter'>
                                                    ";
                                                                    echo "<option value=''>全て</option>";
                                                                    foreach($usersData as $key => $value) {
                                                                        echo "<option value=" . $usersData[$key]['value'] . ">" . $usersData[$key]['name'] . "</option>";
                                                                    }
                                                    echo "
                                                                </select>
                                                        	</span>
														</div>
													";
                                                    echo "
														<div class='user-name'>
															<span class='h'>氏名</span>
                                                        	<span class='b text'>
                                                            	<input type='text' id='teacher_filter' style='width:100%;'>
                                                        </div>
                                                    ";
                                                } ?>
												</div>
                                            </div>
                                            <ul class="scrol">
                                                <?php foreach ((array) $teacher_data as $key => $item): ?>
                                                <?php if($key == 0) {
                                                    echo "
                                                        <li>
                                                            <label class='text'>
                                                                <input type='text' id='teacher_filter' style='width:100%;'>
                                                            </label>
                                                        </li>
                                                    ";
                                                } ?>
                                                <li>
                                                    <label class="checkbox">
                                                        <input type="checkbox" class="input-teacher" value="<?php echo $item['teacher_id']?>|<?php echo $item['teacher_name']?>">
                                                        <span class="icon"></span>
                                                        <p class="teacher" filter="<?php echo $item['teacher_name']; ?>"><?php echo $item['teacher_name']?></p>
                                                    </label>
                                                </li>
                                                <?php endforeach;?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="select-group">
                                        <div class="in">
                                            <div class="top">
												<div class="user-category">生徒</div>
                                                <div class="sort-group">
                                                	<div class="all-user">
														<span class="h">全選択</span>
	                                                	<span class="b">
   	                                                		<label class="checkbox" for="allstudent">
    	                                                	<input type="checkbox" id="allstudent"><span class="icon"></span></label>
                                                		</span>
													</div>
                                                <?php if(true) {
                                                    echo "
														<div class='user-group'>
															<span class='h'>所属グループ</span>
                                                        	<span class='b pulldown'>
                                                                <select id='student_users_filter'>
                                                    ";
                                                                    echo "<option value=''>全て</option>";
                                                                    foreach($usersData as $key => $value) {
                                                                        echo "<option value=" . $usersData[$key]['value'] . ">" . $usersData[$key]['name'] . "</option>";
                                                                    }
                                                    echo "
                                                                </select>
                                                        	</span>
														</div>
													";
                                                    echo "
														<div class='user-name'>
															<span class='h'>氏名</span>
                                                        	<span class='b text'>
                                                            	<input type='text' id='student_filter' style='width:100%;'>
                                                        </div>
                                                    ";
                                                } ?>
												</div>
                                            </div>
                                            <ul class="scrol">
                                                <?php foreach ((array) $student_data as $key => $item): ?>
                                                <li class="listvalue">
                                                    <label class="checkbox">
                                                        <input type="checkbox" class="input-student" value="<?php echo $item['student_id']?>|<?php echo $item['student_name']?>">
                                                        <span class="icon"></span>
                                                        <p class="student" filter="<?php echo $item['student_name']; ?>" value="<?php echo $item['bit_subject']; ?>" ><?php echo $item['student_name']?></p>
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
                            <p>内容作成</p>
                        </div>
                        <div class="body">
                            <!-- メッセージタイトル(新規作成時のみ表示) -->
                            <div id="message-title" class="input-category">
                                <p class="title">タイトル<small>100文字以内</small></p>
                                <div class="input-erea">
                                    <input type="text" name="title" id="input-title" maxlength="100">
                                </div>
                            </div>
                            <!-- メッセージ内容 -->
                            <div id="message-contents" class="input-category">
                                <p class="title">メッセージ内容<small>2000文字以内</small></p>
                                <div class="input-erea">
                                    <textarea name="message" id="input-message" maxlength="2000"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- メッセージ通知期限 -->
                    <div id="message-notice" class="item">
                        <div class="head">
                            <p>通知期限</p>
                        </div>
                        <div class="body">
                            <dl class="input-group">
                                <dd>
                                <input type="text" class="datepicker" id="input-limit"  name="limit_date" value="<?php echo date('Y-m-d', strtotime('+1 month')); ?>">
								</dd>
                            	<p class="attention">未入力の場合、投稿から一ヶ月後までの期間で設定されます。</p>
                            </dl>
                        </div>
                    </div>

                    <!-- メッセージレベル -->
                    <div id="message-importance" class="item">
                        <div class="head">
                            <p>重要度</p>
                        </div>
                        <div class="body">
                            <div>
                                <ul class="clearfix">
                                    <li><label class="checkbox"><input type="radio" name="message_level" class="input-level" value=0 ?><span class="icon"></span>最重要</label></li>
                                    <li><label class="checkbox"><input type="radio" name="message_level" class="input-level" value=1 ?><span class="icon"></span>重要</label></li>
                                    <li><label class="checkbox"><input type="radio" name="message_level" class="input-level" value=2 checked="checked" ?><span class="icon"></span>普通</label></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- メッセージタイプ -->
                    <div id="message-type" class="item">
                        <div class="head">
                            <p>メッセージ形式</p>
                        </div>
                        <div class="body">
                            <div>
                                <ul class="clearfix">
                                    <li><label class="checkbox"><input type="radio" name="type" class="input-type" value=0 checked="checked" ?><span class="icon"></span>お知らせ</label></li>
                                    <li><label class="checkbox"><input type="radio" name="type" class="input-type" value=1 ?><span class="icon"></span>グループメッセージ</label></li>
                                    <li><label class="checkbox"><input type="radio" name="type" class="input-type" value=2 ?><span class="icon"></span>プライベートメッセージ</label></li>
                                </ul>
                            </div>
                        </div>
                    </div>

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
    <!-- ▲main -->
</div>
<script src="js/message_create.js"></script>

</body>
</html>
