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
    $belongingList[] = "すべてのグループ";
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
    <title>ThinkBoard LMS 管理者</title>
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
                    <p class="authority">管理者用</p>
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
                        <a class="togglebtn"><span class="icon-user-add"></span>受講者所属・ID設定</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/users.php">所属グループ設定</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1000")) { ?>
                            <li><a href="user/student.php">受講者ID設定</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>コンテンツ設定</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/contents.php">コンテンツグループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="contents/index.php">コンテンツ登録・編集</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li>
                        <a href="access/contents-control.php"><span class="icon-movie-set"></span>受講対象設定</a>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-graph"></span>受講状況</a>
                        <ul class="togglemenu">
                            <li><a href="history/index.php">受講者から確認</a></li>
                            <!--<li><a href="dateWiseViewing/index.php">動画授業から確認</a></li>-->
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                    <li>
                        <a href="message/message_list.php"><span class="icon-main-message"></span>メッセージ</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>ヘルプ</a>
                    </li>
                    <?php if ($isManager) { ?>
                    <li>
                        <a href="user/admin.php"><span class="icon-user-add"></span>管理者ID・権限設定</a>
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
                    <p class="authority">学校管理者</p>
                    <p class="username"><?php echo $_SESSION['auth']['admin_name']; ?></p>
                </div>
            </a>
            <ul class="submenu">
                <li role="presentation"><a href="account/index.php"><span class="icon-lock"></span>アカウント設定</a></li>
                <li role="presentation"><a href="auth/logout.php"><span class="icon-sign-out"></span>ログアウト</a></li>
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
                <p class="title users">利用者数</p>
                <dl>
                    <dd class="o">100<span> 人</span></dd>
                    <dt>講師</dt>
                </dl>
                <dl>
                    <dd class="o">100<span> 人</span></dd>
                    <dt>受講者</dt>
                </dl>
            </div>

            <!-- 所属グループ数 -->
            <div class="status-group clearfix">
                <p class="title user-group">所属グループ数</p>
                <dl>
                    <dd>2</dd>
                    <dt>所属その1</dt>
                </dl>
                <dl>
                    <dd>5</dd>
                    <dt>所属その2</dt>
                </dl>
                <dl>
                    <dd>10</dd>
                    <dt>所属その3</dt>
                </dl>
                <dl>
                    <dd>30</dd>
                    <dt>所属その4</dt>
                </dl>
            </div>

            <!-- コンテンツ数 -->
            <div class="status-group clearfix">
                <p class="title contents">コンテンツ数</p>
                <dl>
                    <dd>100</dd>
                    <dt class="movie">動画授業</dt>
                </dl>
                <dl>
                    <dd>50</dd>
                    <dt class="quiz">テスト</dt>
                </dl>
                <dl>
                    <dd>25</dd>
                    <dt class="questionnaire">アンケート</dt>
                </dl>
                <dl>
                    <dd>10</dd>
                    <dt class="report">レポート</dt>
                </dl>
                <dl>
                    <dd>50</dd>
                    <dt>階層その1</dt>
                </dl>
                <dl>
                    <dd>50</dd>
                    <dt>階層その2</dt>
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
                        <p class="title">受講者所属・ID設定</p>
                        <p class="method">所属グループの作成・編集や、受講者のIDの発行・編集を行います。</p>
                        <ul class="clearfix">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/users.php">所属グループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1000")) { ?>
                            <li><a href="user/student.php">受講者ID設定</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>


                <!-- コンテンツ設定 -->
                <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_02.png"></p>
                        <p class="title">コンテンツ設定</p>
                        <p class="method">コンテンツグループの作成や、動画・テスト・アンケート等の登録・編集等を行います。</p>
                        <ul class="clearfix">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="access/contents.php">コンテンツグループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="contents/index.php">コンテンツ登録・編集</a></li>
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
                        <p class="title">受講対象設定</p>
                        <p class="method">コンテンツグループに対して、視聴可能な所属グループの受講対象を設定します。</p>
                        <ul class="clearfix">
                            <li><a href="access/contents-control.php">受講対象設定</a></li>
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
                        <p class="title">受講状況確認</p>
                        <p class="method">各受講者、コンテンツごとの受講状況をグラフで確認できます</p>
                        <ul class="clearfix">
                            <li><a href="history/index.php">受講者から確認</a></li>
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
                        <p class="title">メッセージ</p>
                        <p class="method">講師・受講者とのトークグループ・スレッドの作成やメッセージの交換できます。</p>
                        <ul class="clearfix">
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                            <!--<li class="w-50 b-r"><a href="message/message_list.php">メッセージ一覧</a></li>-->
                            <li><a href="message/message_list.php">メッセージ一覧</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-40")) { ?>
                            <!--<li class="w-50"><a href="message/message_create.php">新規作成</a></li>-->
                            <li><a href="message/message_create.php">新規作成</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <?php } ?>

                <!-- 設定・その他 -->
                <div class="group">
                    <div class="in">
                        <p class="icon"><img src="images/ico_top_06.png"></p>
                        <p class="title">ヘルプ</p>
                        <p class="method">LMSの仕様や操作方法を確認できます。</p>
                        <ul class="clearfix">
                            <li><a href="help/TBLMS_Administrator.pdf" target="_blank">ヘルプ</a></li>
                        </ul>
                    </div>
                </div>

            </div>

            <div class="clearfix">

                <!-- 所属 -->
                <div class="group">
                    <div class="in">
                        <p class="title2">所属</p>
                        <p class="method"><?= implode("<br>", $belongingList); ?></p>
                        <ul class="clearfix">
                        </ul>
                    </div>
                </div>

                <!-- 権限 -->
                <div class="group">
                    <div class="in">
                        <p class="title2">権限</p>
                        <table>
                            <tr><td>受講者ID設定（閲覧）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-1000")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>受講者ID設定（登録・編集）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-2")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>受講者ID設定（削除）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-4")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>コンテンツ設定（閲覧）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-2000")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>コンテンツ設定（登録・編集）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-8")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>コンテンツ設定（削除）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-10")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>メッセージ（閲覧）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-4000")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>メッセージ（新規作成）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-40")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>メッセージ（削除）</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-80")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>所属・コンテンツグループ設定<br>受講対象設定</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-1")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>受講状況閲覧</td><td><?= ($isManager || isPermissionFlagOn($permission, "1-20")) ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
                            <tr><td>管理者ID・権限設定</td><td><?= $isManager ? '<span class="maru"></span>': '<span class="batu"></span>'; ?></td></tr>
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
