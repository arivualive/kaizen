<?php
//config.phpで使用するフラグ
$view_flag = 1;
require '../config.php';

//ログインチェック
if($_SESSION['auth']['organizer_id'] == ''){
    //$base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
    header('Location: ../index.php');
    exit();
} else {
    print_r('Login User Name : ' . $_SESSION['auth']['organizer_name'] . '<br/>');
    print_r('Login User ID   : ' . $_SESSION['auth']['organizer_id'] . '<br/>');
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>newlms</title>
<style>
    body {
        display: block;
        text-align:center;
    }
</style>
</head>
<body>
<h1>Menu</h1>
<p><a href="school_config.php">学校設定</a></p>
<p><a href="admin_config.php">管理者設定</a></p>
<p><a href="logout.php">ログアウト</a></p>
</body>
</html>
