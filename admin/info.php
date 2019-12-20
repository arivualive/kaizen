<?php
require_once "../config.php";
require_once "../library/permission.php";
//login_check('/admin/auth/');

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;
//$test = isPermissionFlagOnArray($permission, "1-8", "1-10", "1-20", "1-40", "1-80", "1-0008");

$_POST["string"] = isset($_SESSION['auth']['bit_subject']) ? $_SESSION['auth']['bit_subject'] : 0;
$_POST['csvfile'] = '../library/category/csv/users.csv';
require_once('../library/category/catecalc.php');

$belongingNameTable = $csvRowD;
$parentBitTable = $csvRowC;

// 所属項目リストからフォルダを削除
foreach ($parentBitTable as $parentBit) {
    if ($parentBit == "") {
        continue;
    }

    unset($belongingNameTable[$parentBit]);
}

// 所属項目リストから所属グループビットが立っているものを抽出
$bitSubject = isset($_SESSION['auth']['bit_subject']) ? $_SESSION['auth']['bit_subject']: 0;
$belongingList = [];
if ($isManager) {
    $belongingList[] = "All groups";
} else {
    foreach ($belongingNameTable as $bit => $name)
    {
        if (isPermissionFlagOn($bitSubject, $bit)) {
            $belongingList[] = $name;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS Administrator</title>
    <!-- favicon -->
	<link rel="shortcut icon" href="images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <!-- js -->
    <!--<script src="https://code.jquery.com/jquery-3.1.1.js"></script>-->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>-->
    <script src="../js/jquery-3.1.1.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/bootstrap.js"></script>
</head>
<body>

<div id="wrap">

    <!-- ▼navgation -->
    <div id="nav-fixed">
        <!-- ▼h1 -->
        <div class="brand">
            <a href="info.php">
                <h1>
                    <div class="img_h1"><img src="images/logo.jpg" alt="ThinkBoard LMS"></div>
                    <p class="authority">For administrators</p>
                </h1>
            </a>
        </div>
        <!-- ▼scrol erea -->
        <div id="scrollerea">
            <nav id="mainnav">
                <ul id="accordion" class="accordion">
                    <li class="open">
                        <a href="info.php" class="active"><span class="icon-main-home"></span>HOME</a>
                    </li>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-1000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-user-add"></span>Student affiliation / ID setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/users.php">Affiliation group setting</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1000")) { ?>
                            <li><a href="user/student.php">Student ID setting</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>Content setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/contents.php">Content group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="contents/index.php">Content registration / editing</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li>
                        <a href="access/contents-control.php"><span class="icon-movie-set"></span>Target setting</a>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-graph"></span>Attendance status</a>
                        <ul class="togglemenu">
                            <li><a href="history/index.php">Confirmation from the student</a></li>
                            <!--<li><a href="dateWiseViewing/index.php">動画授業から確認</a></li>-->
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                    <li>
                        <a href="message/message_list.php"><span class="icon-main-message"></span>message</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>help</a>
                    </li>
                    <?php if ($isManager) { ?>
                    <li>
                        <a href="user/admin.php"><span class="icon-user-add"></span>Administrator ID, authority setting</a>
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
                <li class="active"><a>HOME</a></li>
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
                <li role="presentation"><a href="account/index.php"><span class="icon-lock"></span>Account Setting</a></li>
                <li role="presentation"><a href="auth/logout.php"><span class="icon-sign-out"></span>Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>HOME</h2>
        </div>
        <!-- ▲h2 -->
        <div id="status" class="clearfix">
            <!-- 利用者数 -->
            <div class="status-group clearfix">
                <p class="title users">Number of users</p>
                <dl>
                    <dd class="o">100<span> person</span></dd>
                    <dt>Lecturer</dt>
                </dl>
                <dl>
                    <dd class="o">100<span> person</span></dd>
                    <dt>Students</dt>
                </dl>
            </div>

            <!-- 所属グループ数 -->
            <div class="status-group clearfix">
                <p class="title user-group">Group number</p>
                <dl>
                    <dd>2</dd>
                    <dt>Group 1</dt>
                </dl>
                <dl>
                    <dd>5</dd>
                    <dt>Group 2</dt>
                </dl>
                <dl>
                    <dd>10</dd>
                    <dt>Group 3</dt>
                </dl>
                <dl>
                    <dd>30</dd>
                    <dt>Group 4</dt>
                </dl>
            </div>

            <!-- コンテンツ数 -->
            <div class="status-group clearfix">
                <p class="title contents">Number of contents</p>
                <dl>
                    <dd>100</dd>
                    <dt class="movie">Video class</dt>
                </dl>
                <dl>
                    <dd>50</dd>
                    <dt class="quiz">Quiz</dt>
                </dl>
                <dl>
                    <dd>25</dd>
                    <dt class="questionnaire">Questionnaire</dt>
                </dl>
                <dl>
                    <dd>10</dd>
                    <dt class="report">Report</dt>
                </dl>
                <dl>
                    <dd>50</dd>
                    <dt>Level 1</dt>
                </dl>
                <dl>
                    <dd>50</dd>
                    <dt>Level 2</dt>
                </dl>
            </div>
        </div>

        <!-- 機能一覧 -->
        <div id="mainmenu">

            <div class="clearfix">

                <!-- 受講者所属・ID設定 -->
                <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-1000")) { ?>
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_01.png"></p>
                        <p class="title">Student affiliation / ID setting</p>
                        <p class="method">Create and edit affiliation groups, and issue and edit student IDs.</p>
                        <ul class="clearfix">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/users.php">Affiliation group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1000")) { ?>
                            <li><a href="user/student.php">Student ID setting</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>


                <!-- Content setting -->
                <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_02.png"></p>
                        <p class="title">Content setting</p>
                        <p class="method">We will create content groups and register / edit video / test / question etc.</p>
                        <ul class="clearfix">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/contents.php">Content group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="contents/index.php">Content registration / editing</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>

                <!-- アクセス権設定 -->
                <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_03.png"></p>
                        <p class="title">Target setting</p>
                        <p class="method">For the content group, set the attendance target of viewable belonging group.</p>
                        <ul class="clearfix">
                            <li><a href="access/contents-control.php">Target setting</a></li>
                        </ul>
                    </div>
                </div>
                <?php } ?>

            </div>

            <div class="clearfix">

                <!-- 受講状況確認 -->
                <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_04.png"></p>
                        <p class="title">Attendance confirmation</p>
                        <p class="method">We can confirm the attendance situation for each student, contents with graph</p>
                        <ul class="clearfix">
                            <li><a href="history/index.php">Confirmation from the student</a></li>
                            <!-- <li class="w-50"><a href="dateWiseViewing/index.php">動画授業から確認</a></li> -->
                        </ul>
                    </div>
                </div>
                <?php } ?>

                <!-- メッセージ -->
                <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_05.png"></p>
                        <p class="title">message</p>
                        <p class="method">You can create talk group threads and exchange messages with instructors / students.</p>
                        <ul class="clearfix">
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                            <!--<li class="w-50 b-r"><a href="message/message_list.php">メッセージ一覧</a></li>-->
                            <li><a href="message/message_list.php">Message list</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-40")) { ?>
                            <!--<li class="w-50"><a href="message/message_create.php">新規作成</a></li>-->
                            <li><a href="message/message_create.php">Create New</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>

                <!-- 設定・その他 -->
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_06.png"></p>
                        <p class="title">help</p>
                        <p class="method">You can check the specifications and operation method of LMS.</p>
                        <ul class="clearfix">
                            <li><a href="help/TBLMS_Administrator.pdf" target="_blank">help</a></li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="clearfix">

                <!-- 所属 -->
                <div class="group">
                    <div class="in">
                        <p class="title2">Group</p>
                        <p class="method"><?= implode("<br>", $belongingList); ?></p>
                        <ul class="clearfix">
                        </ul>
                    </div>
                </div>

                <!-- 権限 -->
                <div class="group">
                    <div class="in">
                        <p class="title2">Authority</p>
                        <table>
                            <tr><td>Student ID setting (view)</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-1000")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Student ID setting (registration / editing)</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-2")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Student ID setting (deleted)</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-4")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Content setting（Browse）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-2000")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Content setting（Registration/Edit）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-8")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Content setting（Delete）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-10")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Message (view)</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-4000")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Message (Create New)</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-40")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Message (deleted)</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-80")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Group / content group setting<br>Target setting</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-1")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>View class status</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-20")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>Administrator ID, authority setting</td><td><?= $isManager ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                        </table>
                        <ul class="clearfix">
                        </ul>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
