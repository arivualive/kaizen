<?php
require dirname(filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . '/config.php';

// セッションがあればメニューへ
if (isset($_SESSION['auth']['teacher_id'])) {
    $base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
    header('Location: ' . $base . '/teacher/menu');
    exit();
}

// post データより
$id = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
$pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

if ($id != '') {
    $curl = new Curl($url);
    $teacher = new TeacherAuth($id, $pw);
    $data = $teacher->authCheck();

    $result = $curl->send($data);

    if ($result['teacher_name'] != '') {
        $_SESSION['auth'] = $result;
        $_SESSION['auth']['level'] = 'teacher';
        $base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
        header('Location: ' . $base . '/teacher/menu');
        exit();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>newlms</title>
<!--[if IE]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

<![endif]-->
<style>
article, aside, dialog, figure, footer, header, hgroup, menu, nav,
	section {
	display: block;
}
</style>
</head>
<body>
	<form action="" method="post">
		<p>
			ID: <input type="text" name="username" value="COCyano" />
		</p>
		<p>
			PW:<input type="test" name="password" value="yano" />
		</p>
		<p>
			<input type="submit" name="submit" value="送信" />
		</p>
	</form>
</body>
</html>
