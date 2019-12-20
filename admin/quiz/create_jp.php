<?php
require_once "../../config.php";

//debug($_SESSION);
$message = "";

$bit_classroom = filter_input(INPUT_GET, "bid");

$curl = new Curl($url);
$dt = new DateTime();
$dt->setTimeZone(new DateTimeZone('Asia/Tokyo'));

//------CSV読込部分 ここから------
error_reporting(~E_NOTICE);
$path = '../../library/category/'; //カテゴリーPHPライブラリの場所（※必須）
$csvpath = $path . 'csv/'; //CSVファイルの場所
$_POST['csvfile'] = $csvpath . 'contents.csv';

//カテゴリー計算用ファイルを読込み
require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');

//CSVファイルを読み込み「UTF-8」に変換
$lines = @file($_POST['csvfile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$lines) { $lines = array(); }
mb_convert_variables('UTF-8', 'SJIS-win', $lines);
//先頭の2行を削除して詰める
unset($lines[0], $lines[1]);

if(filter_input(INPUT_GET, "bid")) {
    $subject_section_id = $_GET['bid'];
}

list($string, $check) = array_to_string((array)$subject_section_id);
$bit_classroom = $string;
//debug($string);

//debug($check);
//------CSV読込部分 ここまで------

// 新しいクイズのレコードをinsert
if (isset($_SESSION['auth'])) {
    $data = array(
        'repository' => 'QuizRepository',
        'method' => 'insertQuiz',
        'params' => array(
            'school_id' => $_SESSION['auth']['school_id'],
            'start_day' => '0000-01-01',
            'last_day' => '9999-12-31',
            'user_level_id' => 0,
            'register_id' => $_SESSION['auth']['admin_id'],
            'bit_classroom' => $bit_classroom
        )
    );
    $quiz_id = $curl->send($data);

    if ( $quiz_id == '' ) {
      $quiz_id = 1;
    }

    //------ tbl_function_list ------//

        $Quiz = new Quiz($quiz_id, $curl);
        $data['type'] = 3;
        $Quiz->setFunctionList($data);

    //!!!!!! tbl_function_list !!!!!!//
    header("Location: base.php?id=$quiz_id&bid=$subject_section_id");
    exit();

} else {
    die ("問題作成に失敗しました。");
}

?>
