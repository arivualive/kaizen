<?php
//config.phpで使用するフラグ
$view_flag = 1;
require './config.php';

// セッションがあればメニューへ
if (isset($_SESSION['auth']['organizer_id'])) {
    //$base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
    //header('Location: ' . $base . '/admin/menu/');
    //header('Location: ' . $base . '/core/php/index.php');
    header('Location: ./php/index.php');
    exit();
}

// post データより
$id = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
$pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

if ($id != '' && $pw != '') {
    $curl = new Curl($url);
    $organizer = new OrganizerAuth($id, $pw, $curl);
    $data = $organizer->authCheck();

    if ($data['organizer_id'] != '') {
        $_SESSION['auth'] = $data;
        //$base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
        //header('Location: ' . $base . '/php/index.php');
        header('Location: ./php/index.php');
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS SuperAdmin</title>
    <meta name="Author" content=""/>
    <!-- viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- js -->
    <!--<script src="https://code.jquery.com/jquery-3.1.1.js"></script>-->
    <script src="script/jquery-3.1.1.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>-->
    <script src="script/popper.min.js"></script>
    <style>
        body {
            display: block;
            text-align:center;
        }
    </style>
    <script>
    function alertMessage(message) {
        value = alert(message);
        return value
    }
    </script>
</head>
<body>
<div id="login">
    <div class="main">
        <div class="in">
            <form action="" method="post">
                <p>管理者ID：<input type="text" name="username" value="<?php echo htmlspecialchars($id, ENT_QUOTES); ?>" /></p>
                <p>管理者PW：<input type="password" name="password" value="<?php echo htmlspecialchars($pw, ENT_QUOTES); ?>" /></p>
                <p class="submit"><input type="submit" name="submit" value="login" /></p>
            </form>
        </div>
    </div>
</div>
<?php
    if ($id != '' && $pw != '') {
        print "<script language=javascript>alertMessage('ログインできませんでした。管理者IDとパスワードをご確認の上、再度入力してください。')</script>";
    } else if ($id != '' && $pw == '') {
        print "<script language=javascript>alertMessage('パスワードが入力されていません。')</script>";
    } else if ($id == '' && $pw != '') {
        print "<script language=javascript>alertMessage('管理者IDが入力されていません。')</script>";
    }
?>
</body>
</html>
