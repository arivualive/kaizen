<?php
require_once "../config.php";
//login_check('/student/auth/');

$message = '';
$curl = new Curl($url);

if (filter_has_var(INPUT_POST, "student_id")) {
    $data['school_id'] = filter_input(INPUT_POST, "school_id");
    $data['student_name'] = filter_input(INPUT_POST, "student_name", FILTER_SANITIZE_SPECIAL_CHARS);
    $data['enable'] = filter_input(INPUT_POST, "enable");
    $data['display_order'] = filter_input(INPUT_POST, "display_order");
    $data['student_id'] = filter_input(INPUT_POST, "student_id");

    $curl_data = array('repository' => 'StudentRepository', 'method' => 'updateStudentId', 'params' => $data);
    $result = $curl->send($curl_data);

    $message = "保存できませんでした。";

    if (! is_null($result)) {
        $message = "保存しました。";
    }
}

$id = filter_input(INPUT_GET, "id");

$data = array(
    'repository' => 'StudentRepository',
    'method' => 'findStudentId',
    'params' => array('id' => $id)
);

$result = $curl->send($data);

// school データ
$curl_data = array(
    'repository' => 'SchoolRepository',
    'method' => 'findSchoolAll'
);

$school_data = $curl->send($curl_data);
#print_r($result);
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
article, aside, dialog, figure, footer, header,
hgroup, menu, nav, section { display: block; }
</style>
</head>
<body>
<h1><a href="https://<?php echo BASE_URL;?>/student/menu/">Menu</a></h1>
<h2><?php echo $_SESSION['auth']['student_name']; ?></h2>
<h2>学生詳細</h2>
<p><?php echo $message; ?></p>
<!--<form action="" method="post">-->
<form action=<?php echo $_SERVER['REQUEST_URI']; ?> method="POST">
    <dl>
        <dt>ID</dt>
        <dd><?php echo $result['student_id']; ?></dd>
        <dt>学校</dt>
		<dd>
		    <select id="school_id" name="school_id">
		    <?php foreach ((array) $school_data as $item): ?>
		        <?php $selected = ($item['school_id'] == $school_data['school_id'])? ' selected':''; ?>
				<option value="<?php echo $item['school_id']; ?>"<?php echo $selected; ?>>
				    <?php echo $item['school_id']; ?>：<?php echo $item['school_name']; ?></option>
			<?php endforeach; ?>
			</select>
		</dd>
        <dt>氏名</dt>
        <dd> <input type="text" name="student_name" value="<?php echo $result['student_name']; ?>" /> </dd>
        <dt>有効フラグ<dt>
        <dd>
            <input type="hidden" name="enable" value="0" />
            <input type="checkbox" name="enable" value="1"<?php echo ($result['enable'] == '1')? 'checked':''; ?> /> 有効
        </dd>
        <dt>表示順</dt>
        <dd>
            <input type="text" name="display_order" value="<?php echo $result['display_order']; ?>" />
        </dd>
    </dl>
    <input type="hidden" name="student_id" value="<?php echo $result['student_id']; ?>" />
    <button type="submit">保存</button>
</form>
<p><a href="../student/index.php">一覧へ戻る</a>
</body>
</html>
