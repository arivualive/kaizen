<?php
require_once "../config.php";
$call_sign = "kai";

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
        $student_id = $_SESSION['auth']['student_id'];
        $student = new StudentAccount($student_id, $now, $new, $curl);
        $data = $student->getStudent();
    }

    if($now == '' || $now == 'input_error'){
        $now = "input_error";
    } else if(isset($data[0]['student_id']) == false) {
        $now = "value_error";
    } else if($data[0]['student_id'] != $student_id) {
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
        if ($data[0]['student_id'] == $student_id) {
            $data = $student->setStudent();
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
    <title>Thinkboard LMS students</title>
	<meta name="Author" content=""/>
	<!-- viewport -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- favicon -->
	<link rel="shortcut icon" href="images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/account.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/script.js"></script>
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
                        <h1><img src="images/logo.jpg" alt="ThinkBoard LMS"></h1>
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
                                <a href="account.php">Change Password</a>
                            </li>
                            <li class="loguot">
                                <a href="auth/logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="#"><span>help</span></a>
                    </div>
                </div>
            </div>
            <!-- right -->
            <div class="header-right">
                <nav class="nav-mainmenu">
                    <ul>
                        <li>
                            <a href="info.php"><span>TOP</span></a>
                        </li>
                        <li>
                            <a href="contentslist.php"><span>Taking lectures</span></a>
                        </li>
                        <li>
                            <a href="message/message_list.php?p=1"><span>message</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- main container -->
    <div id="container-maincontents" class="container-maincontents">
        <div class="account-main">
            <h2>Account Setting</h2>
            <!--<form action="" method="post">-->
            <form action=<?php echo $_SERVER['REQUEST_URI']; ?> method="POST">
                <dl>
                    <?php if($now == "input_error") { ?>
                        <dt>Current password&nbsp;<span style="color:#FF0000; font-size:10px;">※ Please enter your current password</span></dt>
                    <?php } else if($now == "value_error") { ?>
                        <dt>Current password&nbsp;<span style="color:#FF0000; font-size:10px;">※ The current password is incorrect</span></dt>
                    <?php } else { ?>
                        <dt>Current password</dt>
                    <?php } ?>
                    <dd><input type="password" name="now-password" value=""></dd>
                </dl>
                <dl>
                    <?php if($new1 == "input_error") { ?>
                        <dt>New password&nbsp;<span style="color:#FF0000; font-size:10px;">※please enter a new password</span></dt>
                    <?php } else if($new1 == "value_error") { ?>
                        <dt>New password&nbsp;<span style="color:#FF0000; font-size:10px;">※ It does not match the new password (re-enter)</span></dt>
                    <?php } else { ?>
                        <dt>New password</dt>
                    <?php } ?>
                    <dd><input type="password" name="new-password1" value=""></dd>
                </dl>
                <dl>
                    <?php if($new2 == "input_error") { ?>
                        <dt>New password(Re-enter)&nbsp;<span style="color:#FF0000; font-size:10px;">※ Please enter a new password (re-enter)</span></dt>
                    <?php } else if($new2 == "value_error") { ?>
                        <dt>New password(Re-enter)&nbsp;<span style="color:#FF0000; font-size:10px;">※ It does not match the new password</span></dt>
                    <?php } else { ?>
                        <dt>New password(Re-enter)</dt>
                    <?php } ?>
                    <dd><input type="password" name="new-password2" value=""></dd>
                </dl>
                <dl>
                    <?php if($now == "true" && $new1 == "true" && $new2 == "true") { ?>
                        <dt style="color:#FF0000; font-size:14px;">Password changed</dt>
                    <?php } ?>
                </dl>
                <p class="btn-submit">
                    <button type="submit" name="submit" value="change">Change</button><!-- class="disabled" -->
                    <input type="hidden" name="check" value="1">
                </p>
            </form>
        </div>
    </div>
</div>


</body>
</html>
