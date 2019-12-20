<?php
require_once "../../config.php";
$call_sign = "kai";

// セッションがあればメニューへ
if (isset($_SESSION['auth']['student_id'])) {
    $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
    //header('Location: ' . $base . '/student/menu/');
    header('Location: ' . $base . '/student/info.php');
    exit();
}

// post データより
$id = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
$pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

if ($id != '' && $pw != '') {
    $curl = new Curl($url);
    $student = new StudentAuth($call_sign . $id, $pw);
    $data = $student->authCheck();
    $result = $curl->send($data);

    if ($result['enable'] == 1 && $result['joining'] == 1) {
        $curl->send($student->updateAccessDate($result["student_id"]));
        //debug($result);
        $_SESSION['auth'] = $result;
        //$_SESSION['auth']['level'] = 'student';
        $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
        //header('Location: ' . $base . '/student/menu/');
        header('Location: ' . $base . '/student/info.php');
        exit();
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
    <link rel="shortcut icon" href="../images/favicon.ico">
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
    <link rel="stylesheet" type="text/css" href="../css/icon-font.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/login.css">
    <!-- js -->
    <!--<script src="../../js/jquery-3.1.1.js"></script>-->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/alert.js"></script>
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
            <p class="authority">Students</p>
            <p style="color: red; margin-top:10px;">
              Cannot log in during server maintenance
            </p>
        </div>
    </div>

    <div class="main">
        <div class="in">
            <!--<form action="" method="post">-->
            <form action=<?php echo $_SERVER['REQUEST_URI'] ?> method="POST">
                <dl>
                    <dt>Student ID</dt>
                    <dd>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" />
                    </dd>
                </dl>
                <dl>
                    <dt>Password</dt>
                    <dd>
                        <input type="password" name="password" value="<?php echo htmlspecialchars($pw, ENT_QUOTES); ?>" />
                    </dd>
                </dl>
                <p class="submit">
                    <input type="submit" name="submit" value="login" />
                </p>
            </form>
        </div>
    </div>
</div>
<?php
    if ($id != '' && $pw != '' && ($result['enable'] == '' && $result['joining'] == '')) {
        print "<script language=javascript>alertMessage('I was unable to log in. Please confirm the student ID and password and re-enter.')</script>";
    } else if ($id != '' && $pw != '' && ($result['enable'] == 0 || $result['joining'] == 0)) {
        print "<script language=javascript>alertMessage('The student ID and password you entered are currently unavailable. Please contact the administrator.')</script>";
    } else if ($id != '' && $pw == '') {
        print "<script language=javascript>alertMessage('Password has not been entered')</script>";
    } else if ($id == '' && $pw != '') {
        print "<script language=javascript>alertMessage('The student ID has not been entered.')</script>";
    }
?>
</body>
</html>
