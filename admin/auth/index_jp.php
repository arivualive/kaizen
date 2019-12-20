<?php
require_once "../../config.php";
$call_sign = 'kai';

// セッションがあればメニューへ
if (isset($_SESSION['auth']['admin_id'])) {
    $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
    //header('Location: ' . $base . '/admin/menu/');
    header('Location: ' . $base . '/admin/info.php');
    exit();
}

// post データより
$id = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
$pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

if ($id != '' && $pw != '') {
    $curl = new Curl($url);
    $admin = new AdminAuth($call_sign . $id, $pw);
    $data = $admin->authCheck();
    $result = $curl->send($data);

    if ($result['enable'] == 1) {
        //$curl->send($admin->updateAccessDate($result["admin_id"]));
        //debug($result);
        $_SESSION['auth'] = $result;
        $_SESSION['auth']['level'] = 'admin';
        $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"];
        //header('Location: ' . $base . '/admin/menu/');
        header('Location: ' . $base . '/admin/info.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS 管理者</title>
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
    <!--<script src="https://code.jquery.com/jquery-3.1.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>-->
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
            <p class="h1_btm">ThinkBoard 学習管理システム</p>
            <p class="authority">管理者</p>
        </div>
    </div>

    <div class="main">
        <div class="in">
            <form action="" method="post">
                <dl>
                    <dt>管理者ID</dt>
                    <dd>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" />
                    </dd>
                </dl>
                <dl>
                    <dt>パスワード</dt>
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
    if ($id != '' && $pw != '' && $result['enable'] == '') {
        print "<script language=javascript>alertMessage('ログインできませんでした。管理者IDとパスワードをご確認の上、再度入力してください。')</script>";
    } else if ($id != '' && $pw != '' && $result['enable'] == 0) {
        print "<script language=javascript>alertMessage('入力した管理者IDとパスワードは、利用できません。')</script>";
    } else if ($id != '' && $pw == '') {
        print "<script language=javascript>alertMessage('パスワードが入力されていません。')</script>";
    } else if ($id == '' && $pw != '') {
        print "<script language=javascript>alertMessage('管理者IDが入力されていません。')</script>";
    }
?>
</body>
</html>
