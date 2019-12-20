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

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$admin_id = $_SESSION['auth']['admin_id'];
$school_id = $_SESSION['auth']['school_id'];
$message_model = new GetAdminMessageList($admin_id, $school_id, $curl);

//削除ボタン判定および削除処理
if (filter_input(INPUT_POST, "delete") != '') {
    $message_id = filter_input(INPUT_POST, "delete");
    $message_model->setMessageListDelete($message_id);
}

//表示されるリストの合計数を取得(offsetに利用)
$data = $message_model->getMessageListCount();
$max_rows = $data['count'];
//debug($max_rows);

//limit値の設定
$limit = 20 ;

//offset値の設定
$current = filter_input(INPUT_GET, 'p');
$paginate = new Paginate($current, $max_rows, $limit);
$offset = $paginate->getOffset();

//リストの取得（メッセージ）
$data = $message_model->getMessageListOffset($limit, $offset);
$order = filter_input(INPUT_GET, 'o');

//以下、プルダウン等によるソート処理の原案
//if ('id' == $order) {
//    $data = array('repository' => 'MessageListRepository', 'method' => 'findMessageListOrderById','params' => array('limit' => $limit, 'offset' => $offset) );
//}

//if ('name' == $order) {
//    $data = array('repository' => 'MessageListRepository', 'method' => 'findMessageListOrderByName','params' => array('limit' => $limit, 'offset' => $offset) );
//}

$result = $data;

//$admin_data = new GetAdminMessageList($admin_id, $school_id, $curl);
//$test = $admin_data->getAdminDataId();
//debug($test);
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
                <li class="active"><a>message</a></li>
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
            <h2>Message</h2>
            <?php if ($isManager || isPermissionFlagOn($permission, "1-40")) { ?>
            <div class="btn-newmessage">
                <a href="message_create.php">Create New</a>
            </div>
            <?php } ?>
        </div>
        <!-- ▲h2 -->
            <!-- head -->
            <div class="message-list-method">
                <div class="title">Title</div>
                <div class="last-user">Author</div>
                <div class="last-day">Last sent date</div>
                <div class="message-count">Number of messages</div>
                <div class="btns"></div>
            </div>
            <!-- list -->
            <div class="message-list-group scrollerea">
                <?php foreach ((array) $result as $item): ?>
                <div class="message-list-item
                    <?php if($item['check_flag'] == 0) { echo "new"; }; ?>
                    <?php if($item['enable'] == 0) { echo "deleted"; }; ?>
                    <?php if($item['type'] == 0) { echo "type-notice"; }; ?>
                    <?php if($item['type'] == 1) { echo "type-public"; }; ?>
                    <?php if($item['type'] == 2) { echo "type-private"; }; ?>
                ">
                    <div class="in">
                        <div class="title"><?php echo $item['title']; ?></div>
                        <div class="other-detail">
                            <p class="last-user"><?php echo $item['last_sender']; ?></p>
                            <p class="last-day"><?php echo $item['last_sending_date']; ?></p>
                            <p class="message-count"><?php echo $item['total_message']; ?></p>
                        </div>
                        <div class="btns">
                            <?php
                                if($item['enable'] == 1) {
                                    echo "
                                        <div class='detail'>
                                            <a href='message_detail.php?id=" . $item['message_id'] . "'>Details</a>
                                        </div>
                                    ";
                                } else {
                                    echo "
                                        <div class='detail'>
                                            <a href='message_detail.php?id=" . $item['message_id'] . "'>Details</a>
                                        </div>
                                    ";
                                }
                            ?>
                            <?php
                            if ($isManager || isPermissionFlagOn($permission, "1-80")) {
                                //スレッドの作成者＝ログインアカウント
                                if($item['enable'] == 1 && $item['auther_flag'] == 1) {
                                    echo "
                                        <div class='delete'>
                                            <!-- <a href='modify.php? id=" . $item['message_id'] . "'></a> -->
                                            <form action=" . $_SERVER['REQUEST_URI'] . " method='POST'>
                                                <button></button>
                                                <input type='hidden' name='delete' value='" . $item['message_id'] . "'/>
                                            </form>
                                        </div>
                                    ";
                                //スレッドが削除
                                } else if($item['enable'] == 0) {
                                    echo "
                                        <td><p class='deleted'>deleted</p></td>
                                    ";
                                //その他（スレッドの作成者＝他アカウント）
                                } else {
                                    echo "<td></td>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            </div>

            <!-- pager -->
            <nav>
                <ul class="pagination">
                    <?php echo $paginate->pagenation(array('name' => 'o', 'value' => $order)); ?>
                    <!--
                    <li class="page-item">
                        <a class="page-link" href="#!" aria-label="Previous">«<span> 前へ</span></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#!">1</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#!">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#!">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#!" aria-label="Next"><span>次へ </span>»</a>
                    </li>
                    -->
                </ul>
            </nav>
    </div>
    <!-- ▲main -->
</div>

</body>
</html>
