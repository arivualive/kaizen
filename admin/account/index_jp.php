<?php
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

$check = filter_input(INPUT_POST, "check", FILTER_SANITIZE_SPECIAL_CHARS);
$now = "";
$new1 = "";
$new2 = "";

//if ($now != '' && $new1 != '' && $new2 != '') {
if ($check != '') {
    $now = filter_input(INPUT_POST, "now-password", FILTER_SANITIZE_SPECIAL_CHARS);
    $new1 = filter_input(INPUT_POST, "new-password1", FILTER_SANITIZE_SPECIAL_CHARS);
    $new2 = filter_input(INPUT_POST, "new-password2", FILTER_SANITIZE_SPECIAL_CHARS);
    if($now == '') {
        $now = "input_error";
    }

    if($new1 == $new2 && $new1 != '' && $new2 != '') {
        $new = $new1;
    } else {
        $new = "error";
    }

    if($now != "input_error") {
        $curl = new Curl($url);
        $admin_id = $_SESSION['auth']['admin_id'];
        $admin = new AdminAccount($admin_id, $now, $new, $curl);
        $data = $admin->getAdmin();
    }

    if($now == '' || $now == 'input_error'){
        $now = "input_error";
    } else if(isset($data[0]['admin_id']) == false) {
        $now = "value_error";
    } else if($data[0]['admin_id'] != $admin_id) {
        $now = "value_error";
    } else {
        $now = "true";
    }
    //debug("now = " . $now);

    if($new1 == ''){
        $new1 = "input_error";
    } else if(filter_input(INPUT_POST, "new-password1", FILTER_SANITIZE_SPECIAL_CHARS) != filter_input(INPUT_POST, "new-password2", FILTER_SANITIZE_SPECIAL_CHARS)) {
        $new1 = "value_error";
    } else {
        $new1 = "true";
    }
    //debug("new1 = " . $new1);

    if($new2 == ''){
        $new2 = "input_error";
    } else if(filter_input(INPUT_POST, "new-password1", FILTER_SANITIZE_SPECIAL_CHARS) != filter_input(INPUT_POST, "new-password2", FILTER_SANITIZE_SPECIAL_CHARS)) {
        $new2 = "value_error";
    } else {
        $new2 = "true";
    }
    //debug("new2 = " . $new2);

    if($now == "true" && $new1 == "true" && $new2 == "true") {
        if ($data[0]['admin_id'] == $admin_id) {
            $data = $admin->setAdmin();
            $result = $curl->send($data);
            //debug($data);
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
    <link rel="shortcut icon" href="../images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
	<link rel="stylesheet" type="text/css" href="../css/account.css">

    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>

    <!--<script src="https://code.jquery.com/jquery-3.1.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>-->
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
                    <li>
                        <a href="../message/message_list.php"><span class="icon-main-message"></span>メッセージ</a>
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
                <li class="active"><a>アカウント設定</a></li>
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
                <li role="presentation"><a href="#"><span class="icon-lock"></span>アカウント設定</a></li>
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-in"></span>ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>アカウント設定</h2>
        </div>
        <!-- ▲h2 -->

        <div class="col-account">
            <div class="h3 clearfix">
            	<h3>パスワード変更</h3>
            </div>
            <div class="body">
            <form action="" method="post">
                <dl class="input-group">
                    <?php if($now == "input_error") { ?>
                        <dt>現在のパスワード&nbsp;<span style="color:#FF0000; font-size:10px;">※現在のパスワードを入力してください</span></dt>
                    <?php } else if($now == "value_error") { ?>
                        <dt>現在のパスワード&nbsp;<span style="color:#FF0000; font-size:10px;">※現在のパスワードが間違っています</span></dt>
                    <?php } else { ?>
                        <dt>現在のパスワード</dt>
                    <?php } ?>
                    <dd><input type="password" name="now-password" value=""></dd>
                </dl>
                <dl class="input-group">
                    <?php if($new1 == "input_error") { ?>
                        <dt>新しいパスワード&nbsp;<span style="color:#FF0000; font-size:10px;">※新しいパスワードを入力してください</span></dt>
                    <?php } else if($new1 == "value_error") { ?>
                        <dt>新しいパスワード&nbsp;<span style="color:#FF0000; font-size:10px;">※新しいパスワード(再入力)と一致しません</span></dt>
                    <?php } else { ?>
                        <dt>新しいパスワード</dt>
                    <?php } ?>
                    <dd><input type="password" name="new-password1" value=""></dd>
                </dl>
                <dl class="input-group">
                    <?php if($new2 == "input_error") { ?>
                        <dt>新しいパスワード(再入力)&nbsp;<span style="color:#FF0000; font-size:10px;">※新しいパスワード(再入力)を入力してください</span></dt>
                    <?php } else if($new2 == "value_error") { ?>
                        <dt>新しいパスワード(再入力)&nbsp;<span style="color:#FF0000; font-size:10px;">※新しいパスワードと一致しません</span></dt>
                    <?php } else { ?>
                        <dt>新しいパスワード(再入力)</dt>
                    <?php } ?>
                    <dd><input type="password" name="new-password2" value=""></dd>
                </dl>
                <dl>
                    <?php if($now == "true" && $new1 == "true" && $new2 == "true") { ?>
                        <dt style="color:#FF0000; font-size:14px;">パスワードを変更しました。</dt>
                    <?php } ?>
                </dl>
                <p class="btn-submit">
                    <button type="submit" name="submit" value="change">変更する</button><!-- class="disabled" -->
                    <input type="hidden" name="check" value="1">
                </p>
            </form>
			</div>
        </div>

    </div>
    <!-- ▲main -->
</div>

</body>
</html>
