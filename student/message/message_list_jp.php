<?php
require_once "../../config.php";
//login_check('/student/auth/');

$curl = new Curl($url);

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$student_id = $_SESSION['auth']['student_id'];
$school_id = $_SESSION['auth']['school_id'];
$message_model = new GetStudentMessageList($student_id, $school_id, $curl);

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
$limit = 10;

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

//$student_data = new GetStudentMessageList($student_id, $school_id, $curl);
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
    <!-- main container -->
    <div id="container-maincontents" class="container-maincontents clearfix">
        <div id="message-list">
            <!-- topicpath -->
            <div class="message-list-title clearfix">
                <p class="text">メッセージ一覧</p>
                <div class="btn-newmessage">
                    <a href="message_create.php">新規メッセージ作成</a>
                </div>
            </div>
            <!-- list -->
            <div class="message-list-group">
                <!-- head -->
                <div class="message-list-method">
                    <div class="title">タイトル</div>
                    <div class="last-user">作成者</div>
                    <div class="last-day">最終送信日</div>
                    <div class="message-count">メッセージ数</div>
                    <div class="btns"></div>
                </div>
                <?php foreach ((array) $result as $item): ?>
                <div class="message-list-item
                    <?php if($item['check_flag'] == 0 && $item['enable'] == 1) { echo "new"; }; ?>
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
                                            <a href='message_detail.php?id=" . $item['message_id'] . "'>詳細</a>
                                        </div>
                                    ";
                                } else {
                                    echo "<td></td>";
                                }
                            ?>
                            <?php
                                //スレッドの作成者＝ログインアカウント
                                if($item['enable'] == 1 && $item['auther_flag'] == 1) {
                                    echo "
                                        <!-- <a href='modify.php? id=" . $item['message_id'] . "'></a> -->
                                        <form class='delete' action=" . $_SERVER['REQUEST_URI'] . " method='POST'>
                                            <button></button>
                                            <input type='hidden' name='delete' value='" . $item['message_id'] . "'/>
                                        </form>
                                    ";
                                //スレッドが削除
                                } else if($item['enable'] == 0) {
                                    echo "
                                        <td><p class='deleted'>削除済み</p></td>
                                    ";
                                //その他（スレッドの作成者＝他アカウント）
                                } else {
                                    echo "<td></td>";
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
    </div>
</div>


</body>
</html>
