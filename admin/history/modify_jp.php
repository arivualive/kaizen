<?php
require_once "../../config.php";

$grade_message = '';
$classroom_message = '';
$course_message = '';

$curl = new Curl($url);

print_r($_POST);
if (isset($_POST['subject_section_id'])) {

    $subject_section_id = $_POST['subject_section_id'];
    $obj = new SubjectSectionAccessSave($subject_section_id, $curl);

    // grade
    $grade_message = $obj->saveSectionGrade();

    // classroom
    $classroom_message = $obj->saveSectionClassroom();

    // course
    $course_message = $obj->saveSectionCourse();
}

$section_id = filter_input(INPUT_GET, "id");

$data = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'findSubjectSectionId'
    , 'params' => array('subject_section_id' => $section_id)
);

// セッションデータ
$section = $curl->send($data);

$access = new SubjectSectionAccess($section_id, $curl);

// grade
$grade_all = $access->checkedGrade();

// classroom
$classroom_all = $access->checkedClassroom();

// course
$course_all = $access->checkedCourse();

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
<h1><a href="https://<?php echo BASE_URL;?>/admin/menu/">Menu</a></h1>
<h2><?php echo $_SESSION['auth']['admin_name']; ?></h2>
<h2>科目アクセス権詳細</h2>
<form action="" method="post">
    <dl>
        <dt>講座ID</dt>
        <dd><?php echo $section['subject_section_id']; ?></dd>
        <dt>講座名</dt>
        <dd><?php echo $section['subject_section_name']; ?></dd>
        <dt>科目ジャンル</dt>
        <dd><?php echo $section['subject_genre_id']; ?></dd>
        <dt>科目グループ</dt>
        <dd><?php echo $section['subject_group_id']; ?></dd>
        <dt>grade<dt>
        <dd>
          <p><?php echo $grade_message; ?></p>
        <?php foreach ((array) $grade_all as $item): ?>
          <label><input type="checkbox" name="grade_id[]" value="<?php echo $item['grade_id']; ?>"<?php echo (isset($item['checked']))? $item['checked'] : '' ; ?> /><?php echo $item['grade_name']; ?></label><br />
        <?php endforeach; ?>
        </dd>
        <dt>classroom</dt>
        <dd>
          <p><?php echo $classroom_message; ?></p>
        <?php foreach ((array) $classroom_all as $item): ?>
           <label><input type="checkbox" name="classroom_id[]" value="<?php echo $item['classroom_id']; ?>"<?php echo (isset($item['checked']))? $item['checked'] : ''; ?> /><?php echo $item['classroom_name']; ?> </label><br />
        <?php endforeach; ?>
        </dd>
        <dt>course</dt>
        <dd>
          <p><?php echo $course_message; ?></p>
        <?php foreach ((array) $course_all as $item): ?>
           <label><input type="checkbox" name="course_id[]" value="<?php echo $item['course_id']; ?>"<?php echo (isset($item['checked']))? $item['checked'] : ''; ?>/><?php echo $item['course_name']; ?></label><br />
        <?php endforeach; ?>
        </dd>
    </dl>
    <input type="hidden" name="subject_section_id" value="<?php echo $section_id; ?>" />
    <button type="submit">保存</button>
</form>
<p><a href="./index.php">一覧へ戻る</a>
</body>
</html>
