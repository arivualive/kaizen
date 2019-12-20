<?php
session_name('LMS_SuperAdmin');
session_start();

$_SESSION = array(); //全てのセッション変数を削除
setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
session_destroy(); //セッションを破棄

header('Location: ../index.php');
exit();
?>
