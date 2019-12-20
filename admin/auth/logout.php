<?php
session_name('LMS_Admin');
session_start();

$_SESSION = array(); //全てのセッション変数を削除
setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
session_destroy(); //セッションを破棄
?>
<!DOCTYPE html>
<html>
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
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <!-- js -->
    <script src="https://code.jquery.com/jquery-3.1.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
    <style>
    article, aside, dialog, figure, footer, header, hgroup, menu, nav,
    	section {
    	display: block;
    }
    </style>
</head>
<body>
<div id="login">

    <div class="head">
        <div class="in">
            <div class="h1">
                <h1><img src="../images/logo.jpg"></h1>
            </div>
            <p class="h1_btm">ThinkBoard Learning Management System</p>
            <p class="authority">Administrator</p>
        </div>
    </div>
    <div class="main logout">
        <div class="in">
            <p>logged out</p>
            <p class="link_loginpage"><a href="../auth/">To login page</a></p>
        </div>
    </div>
</div>
</body>
</html>
