<?php
require_once "../config.php";

// メニューへ
$base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
header('Location: ' . $base . '/admin/info.php');
exit();
