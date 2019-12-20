<?php
require_once "../config.php";
require_once './contents/class.tbwp3_access.php';
//login_check('/admin/auth/');

$result_grade = '';
$result_classroom = '';
$result_course = '';

$curl  = new Curl($url);
$tbwp3 = new Tbwp3Access();
//debug($_SESSION);

if (isset($_SESSION['auth']['student_id'])) {
    $student_id = $_SESSION['auth']['student_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}

//------CSV読込部分 ここから------
error_reporting(~E_NOTICE);
$path = '../library/category/'; //カテゴリーPHPライブラリの場所（※必須）
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
    $subject_section_name = $temp02[$subject_section_id];
    $subject_genre_id = $temp05[$subject_section_id];
    $subject_genre_name = $temp02[$temp05[$subject_section_id]];
} else {
    $subject_section_id = 0;
//    $subject_section_name = 'カテゴリー中';
    $subject_genre_id = 0;
//    $subject_genre_name = 'カテゴリー大';
}
//debug($subject_section_id);

list($string, $check) = array_to_string((array)$subject_section_id);
$bit_classroom = $string;

//------CSV読込部分 ここまで------
$studentInfo = new StudentContentsListModel($student_id, $school_id, $bit_classroom, $curl);

//debug($studentInfo);

// contents データ
$contents_data = $studentInfo->getContents();
// 2019/6/03 count関数対策
$contents_parameter_value = 0;
if(is_countable($contents_data[0])){
  $contents_parameter_value = count($contents_data[0]);
}
//$contents_parameter_value = count($contents_data[0]);

for( $i = 0 ; $i < count($contents_data) ; $i++ ) {
    $contents_data[$i] += $studentInfo->getContentsAttachment($contents_data[$i]);
}

// questionnaire データ
$questionnaire_data = $studentInfo->getQuestionnaire(0);

// report データ
$report_data = $studentInfo->getQuestionnaire(1);

// quiz データ
$quiz_data = $studentInfo->getQuiz();

//debug( $quiz_data );

if(count($quiz_data) != 0) {
    // 2019/6/03 count関数対策
    $quiz_parameter_value = 0;
    if(is_countable($quiz_data[0])){
      $quiz_parameter_value = count($quiz_data[0]);
    }
    //$quiz_parameter_value = count($quiz_data[0]);
    //debug($quiz_parameter_value);
    for( $i = 0 ; $i < count($quiz_data) ; $i++ ) {
        $quiz_data[$i] += $studentInfo->getQuizAnswer($quiz_data[$i]);
        // 2019/6/03 count関数対策
        $index = 0;
        if(is_countable($quiz_data[$i])){
          $index = count($quiz_data[$i]) - ($quiz_parameter_value + 1);
        }

        //$index = count($quiz_data[$i]) - ($quiz_parameter_value + 1);
        if(isset($quiz_data[$i][$index])) {
            if($quiz_data[$i][$index]['end_flag'] == 0){
                $quiz_data[$i]['last_answer_id'] = $quiz_data[$i][$index]['answer_id'];
            } else {
                $quiz_data[$i]['last_answer_id'] = 0;
            }
        }
    }
}

//debug( $quiz_data );
// message データ
//$message_data = $studentInfo->getMessage();
//debug($message_data);

$student_id = $_SESSION[ 'auth' ][ 'student_id' ];

// subject データ
//$subject_genre_name = 'カテゴリー大';
//$subject_section_name = 'カテゴリー中';

//$subject_data = $questionnaireInfo->getSubject();
//$subject_parameter_value = count($subject_data[0]);
//for( $i = 0 ; $i < count($subject_data) ; $i++ ) {
//    $subject_data[$i] += $questionnaireInfo->getSubjectSection($subject_data[$i]);
//    for( $j = 0 ; $j < count($subject_data[$i]) - $subject_parameter_value ; $j++ ) {
//        if($subject_data[$i][$j]['subject_section_id'] == filter_input(INPUT_GET, "id")) {
//            //debug($subject_data[$i][$j]['subject_section_name']);
//            //debug($subject_data[$i]['subject_genre_name']);
//            $subject_genre_id = $subject_data[$i]['subject_genre_id'];
//            $subject_genre_name = $subject_data[$i]['subject_genre_name'];
//            $subject_section_id = $subject_data[$i][$j]['subject_section_id'];
//            $subject_section_name = $subject_data[$i][$j]['subject_section_name'];
//        }
//    }
//}

// function-group
$function_data = $studentInfo->getFunctionGroup();

$info_array = array();
if(count($contents_data) != 0){
    $info_array = array_merge($info_array,$contents_data);
}
if(count($questionnaire_data) != 0){
    $info_array = array_merge($info_array,$questionnaire_data);
}
if(count($report_data) != 0){
    $info_array = array_merge($info_array,$report_data);
}
if(count($quiz_data) != 0){
    $info_array = array_merge($info_array,$quiz_data);
}

$result_data = [];
$result_data = $info_array;

foreach ( $result_data as $key =>  $data ) {

  if ( $data[ 'type' ] == 3 ) {
    if ( $result_data[ $key ][ 'repeat_challenge' ] == 0 ) {
      // flg = 0 受けれる
      $result_data[ $key ][ 'next_flg' ] = 0;
    } else if ( $data[ 'repeat_challenge' ] > $data[ 'answer_count' ] ) {
      $result_data[ $key ][ 'next_flg' ] = 0;
    } else if ( $data[ 'repeat_challenge' ] == $data[ 'answer_count' ] ){
      $result_data[ $key ][ 'next_flg' ] = 1;
    }

  } else if ( $data[ 'type' ] == 2 ) {
    if ( $data[ 'answer_flg' ] == 0 ) {
      $result_data[ $key ][ 'next_flg' ] = 0;
    } else {
      $result_data[ $key ][ 'next_flg' ] = 1;
    }
  } else if ( $data[ 'type' ] == 1 ) {
    if ( $data[ 'answer_flg' ] == 0 ) {
      $result_data[ $key ][ 'next_flg' ] = 0;
    } else {
      $result_data[ $key ][ 'next_flg' ] = 1;
    }
  }
}

if ( count( $function_data ) != 0 ) {
  for ( $i = 0; $i <count( $function_data ); $i++ ) {
    $k = 0;
    for ( $j = 0; $j < count( $info_array ); $j++ ) {
      if ( $function_data[ $i ][ 'primary_key' ] == $info_array[ $j ][ 'parent_function_group_id' ] ) {
        $function_data[ $i ][ 'child_data' ][ $k ] = $info_array[ $j ];
        array_splice( $info_array, $j, 1 );
        $k++;
        $j--;
      }
    }
    $function_data[ $i ][ 'child_item' ] = $k;
    $function_data[ $i ][ 'array_number' ] = $j + 1;
  }
  $info_array = array_merge( $info_array, $function_data );
}

for ( $i = 0; $i < count( $info_array ); $i++ ) {
  $sort[ $i ][ 'display_order' ] = $info_array[ $i ][ 'display_order' ];
  $sort[ $i ][ 'primary_key' ] = $info_array[ $i ][ 'primary_key' ];
  $sort[ $i ][ 'type' ] = $info_array[ $i ][ 'type' ];

  if ( $sort[ $i ][ 'type' ] == 4 ) {
    for ( $j = 0; $j < $info_array[ $i ][ 'child_item' ]; $j++ ) {
      $sort_child[ $i ][ $j ][ 'display_order' ] = $info_array[ $i ][ 'child_data' ][ $j ][ 'display_order' ];
      $sort_child[ $i ][ $j ][ 'primary_key' ] = $info_array[ $i ][ 'child_data' ][ $j ][ 'primary_key' ];
      $sort_child[ $i ][ $j ][ 'type' ] = $info_array[ $i ][ 'child_data' ][ $j ][ 'type' ];
      $sort_child[ $i ][ $j ][ 'title' ] = $info_array[ $i ][ 'child_data' ][ $j ][ 'title' ];
    }

    if ( count( $sort_child[ $i ]) != 0 ) {
      array_multisort( $sort_child[ $i ], SORT_DESC, $sort_child[ $i ] );
    }

    for ( $j = 0; $j < $info_array[ $i ][ 'child_item' ]; $j++ ) {
      $info_array[ $i ][ 'child_data' ][ $j ][ 'display_order' ] = $sort_child[ $i ][ $j ][ 'display_order' ];
      $info_array[ $i ][ 'child_data' ][ $j ][ 'primary_key' ] = $sort_child[ $i ][ $j ][ 'primary_key' ];
      $info_array[ $i ][ 'child_data' ][ $j ][ 'type' ] = $sort_child[ $i ][ $j ][ 'type' ];
      $info_array[ $i ][ 'child_data' ][ $j ][ 'title' ] = $sort_child[ $i ][ $j ][ 'title' ];
    }
  }
}

if ( isset( $sort )) {
  array_multisort( $sort, SORT_DESC, $info_array );
}

foreach ( $info_array as $key =>  $info ) {

  foreach ( $result_data as $key2 => $result ) {

    if ( $info_array[ $key ][ 'type' ] != 4 ) {

      if ( $info_array[ $key ][ 'type' ] == $result[ 'type' ] && $info_array[ $key ][ 'primary_key'] == $result[ 'primary_key' ] ) {
        $info_array[ $key ] = $result_data[ $key2 ];
      }
    } else {
      // 2019/6/03 count関数対策
      $child_count = 0;
      if(is_countable($info_array[ $key ][ 'child_data' ])){
        $child_count = count( $info_array[ $key ][ 'child_data' ] );
      }
      //$child_count = count( $info_array[ $key ][ 'child_data' ] );
      for( $c = 0; $c < $child_count; $c++ ) {
        if ( $info_array[ $key ][ 'child_data' ][ $c ][ 'type' ] == $result_data[ $key2 ][ 'type' ]
          && $info_array[ $key ][ 'child_data' ][ $c ][ 'primary_key' ] == $result_data[ $key2 ][ 'primary_key' ] ) {

              $info_array[ $key ][ 'child_data' ][ $c ] = $result_data[ $key2 ];
              $info_array[ $key ][ 'child_data' ][ 'bid' ] = $subject_section_id;

        }


      }

    }

  }
}

// 2019/6/03 count関数対策
$i_count = 0;
if(is_countable($info_array)){
  $i_count = count( $info_array );
}
//$i_count = count( $info_array );
$is = 0;
for( $i = 0; $i < $i_count; $i++ ) {

  if ( isset( $info_array[ $i ][ 'child_data' ] )) {
    $_SESSION[ 'auth' ][ 'folder_data' ][ $is ] = $info_array[ $i ][ 'child_data' ];
    $is++;
  }
}


// 増えた配列分をプラス
$quiz_parameter_value = $quiz_parameter_value +1;

//ファイルサイズ表示用
function size_format($size) {
    //冗長性を持たせるため、HDDサイズレベル(TB)まで用意
    //実際は、150MB以上のファイルがあってはならない(仕様：コンテンツファイルサイズ < 150MB)
    $class = array("B","KB","MB","GB","TB");
    $i = 0;
    while ($size >= 1024) {
        $i++;
        $size = $size / 1024;
    }
    return number_format($size,($i ? 0 : 0),".",",").$class[$i];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS 受講者</title>
	<meta name="Author" content=""/>
	<!-- viewport -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- favicon -->
	<link rel="shortcut icon" href="images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="css/common.css">
  <!--<link rel="stylesheet" href="./css/sweetalert-master/dist/sweetalert.css">-->
    <link rel="stylesheet" type="text/css" href="css/contents.css">
    <!-- js -->
    <script src="../js/jquery-3.1.1.js"></script>
    <!--<script type="text/javascript" src="./css/sweetalert-master/dist/sweetalert.min.js"></script>-->
    <script src="../js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/script.js"></script>
    <!--<script type="text/javascript" src="contents/js/contents_play.js"></script>-->
</head>
<body>

<div id="wrap" data-studentID="<?php echo $_SESSION[ 'auth' ][ 'student_id' ];?>" data-schoolID="<?php echo $_SESSION[ 'auth' ][ 'school_id' ];?>">

    <!-- header -->
    <div id="header-bar">
        <div id="header">
            <!-- left -->
            <div class="header-left">
                <!-- h1 -->
                <div class="h1">
                    <a href="#">
                        <h1><img src="images/logo.jpg" alt="ThinkBoard LMS"></h1>
                    </a>
                </div>
                <!-- sub menu -->
                <div class="header-submenu">
                    <div class="btn-userinfomation dropdown">
                        <a href="#" id="dropdownMenu-userinfo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <p class="erea-icon"><span class="icon-user-student"></span></p>
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p> <!-- ここにユーザーの名前が入ります -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-userinfo">
                            <li class="PW">
                                <a href="account.php">パスワード変更</a>
                            </li>
                            <li class="loguot">
                                <a href="auth/logout.php">ログアウト</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="#"><span>ヘルプ</span></a>
                    </div>
                </div>
            </div>
            <!-- right -->
            <div class="header-right">
                <nav class="nav-mainmenu">
                    <ul>
                        <li>
                            <a href="info.php"><span>TOP</span></a>
                        </li>
                        <li class="active">
                            <a href="contentslist.php"><span>講義受講</span></a>
                        </li>
                        <li>
                            <a href="message/message_list.php?p=1"><span>メッセージ</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- main container -->
    <div id="container-maincontents" class="container-maincontents clearfix">
        <!-- subject-list -->
        <div id="player_wrap"></div>
        <div class="erea-subject-list">
            <div id="subject-list" class="subject-list navbar-expand-lg">
                <!-- head -->
                <div class="subject-list-head">
                    <p><span><img src="images/icon_subjectlist.png"></span>コンテンツグループを選択する</p>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- body -->
                <div class="subject-list-body collapse navbar-collapse" id="navbarSupportedContent">
                    <ul id="subject-list-accordion" class="subject-list-accordion">
<!------CSV読込部分 ここから------>
<?php
$database = $_SESSION['auth']['bit_subject'];
if($database) { list($check) = string_to_array($database); }

//users.csv の load_csv必須
list($csvTreeU,,, $csvRowDU) = load_csv($csvpath . 'users.csv', 0);

for($current = count($csvTreeU); $current >= 1; $current--) {
	foreach($csvTreeU[$current] as $value) {
		foreach($value as $line) {
			$temp = explode('-', $line);
			if($check[$temp[0]][log(hexdec($temp[1]), 2)]) {
				$selectU .= $csvRowDU[$line] . '（' . $line . '）' . "\n";
			}
		}
	}
}

foreach($lines as $line) {
	$item = explode(',', $line);
	$item[3] = str_replace('{c}', ',', $item[3]);

	if(preg_match('/^[1-2]$/', $item[0])) {
		$csvMenu[$item[0]][$item[2]][] = $item[1]; //
		$csvParent[$item[1]] = $item[2];
		$csvName[$item[1]] = $item[3]; //
	}
}

for($current = count($csvMenu); $current >= 1; $current --) {
	foreach($csvMenu[$current] as $key => $value) {
		${'menu' . $current}[$key] .= '<ul class="';
		${'menu' . $current}[$key] .= ($current == 1) ? 'accordion' : 'togglemenu';
		if($key == $csvParent[$_GET['bid']]) { ${'menu' . $current}[$key] .= ' open'; }
		${'menu' . $current}[$key] .= '">' . "\n";

		foreach($value as $line) {
			if($csvMenu[$current + 1][$line]) {
				if(${'menu' . ($current + 1)}[$line] != '<ul class="togglemenu">' . "\n" . '</ul>' . "\n") { //空項目を無視
					${'menu' . $current}[$key] .= '<li';
					if($line == $csvParent[$_GET['bid']]) {
						${'menu' . $current}[$key] .= ' class="open"';
						$subject_genre_name = $csvName[$line];
					}
					${'menu' . $current}[$key] .= '>' . "\n" .'<a class="togglebtn">' . $csvName[$line] . '</a>' . "\n";
					${'menu' . $current}[$key] .= ${'menu' . ($current + 1)}[$line] . '</li>' . "\n"; //子項目を挿入
				}
			} else {
				if(search_multi($csvpath . 'contents.csv', $line, $csvTreeU, $selectU)) { //CSV照合
					${'menu' . $current}[$key] .= '<li';
					if($line == $_GET['bid']) {
						${'menu' . $current}[$key] .= ' class="active"';
						$subject_section_name = $csvName[$line];
					}
					${'menu' . $current}[$key] .= '><a href="' . $_SERVER['SCRIPT_NAME'] . '?bid=' . $line . '">' . $csvName[$line] . '</a></li>' . "\n";
				}
			}
		}
		${'menu' . $current}[$key] .= '</ul>' . "\n";
	}
}
echo $menu1[''];
?>
<!------CSV読込部分 ここまで------>
                    </ul>
                </div>
            </div>
        </div>
        <!-- contents list -->
        <div id="contents-list" class="contents-list">
            <!-- subject not select(コンテンツグループ未選択時に表示) -->
            <!-- <div class="contents-list-notselect">
                <p>コンテンツグループを選んでください。</p>
            </div> -->
            <!-- title -->
            <div class="contents-list-head">
                <div class="subject-selecttitle">
<?php
	if($subject_genre_name && $subject_section_name) {
		echo '<span>' . $subject_genre_name . '</span>' . "\n";
		echo '<span>' . $subject_section_name . '</span>' . "\n";
	}
?>
                </div>
                <div class="btn-sort dropdown-contentssort">
                    <button id="dropdownMenu-contentssort" data-toggle="dropdown" class="dropdown">絞り込み</button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu-contentssort">
                        <ul>
                            <li class="sort-dummy-01 all-sort"><a>全て</a></li>
                            <li class="sort-dummy-02 contents"><a>動画授業</a></li>
                            <li class="sort-dummy-03 test"><a>テスト</a></li>
                            <li class="sort-dummy-04 questionnaire"><a>アンケート</a></li>
                            <li class="sort-dummy-05 report"><a>レポート</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- body -->
            <div class="contents-list-body">
                <div>
                    <!-- contents group title -->
                    <!--
                    <div class="contentsgroup-title">
                        <p>講義グループタイトル</p>
                    </div>
                     -->

                    <?php if(count($info_array) != 0) { ?>
                        <?php foreach ((array) $info_array as $item): ?>
                            <?php
                                if($item['bit_classroom'] == $bit_classroom) {
                                    switch ($item['type']) {
                                    case 0:
                                        if ($item['contents_extension_id'] <= 6) {
                                            echo "
                                                <!-- contents item = TB -->
                                                <div class='list-dummy-01 contents-item tb'>
                                                    <div class='in'>
                                                        <div class='title'>
                                                            <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'contents_extension_id' ] ."'
                                                                data-contentID=".$item[ 'primary_key' ]."
                                                                data-extensionID=".$item[ 'contents_extension_id' ]."
                                                                data-title=".$item[ 'title' ].">" . $item['title'] . "
                                                            </a>
                                                        </div>
                                                        <div class='detail-info'>
                                                            <div class='important-info'>
                                                                <span class='count'>" . $item['watch_count'] . "回</span>
                                            ";
                                                                if($item['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                    echo "<span class='contents-time'>未視聴</span>";
                                                                } else {
                                                                    echo "<span class='contents-time'>".$item['play_start_datetime']."</span>";
                                                                }
                                            echo "
                                                            </div>
                                                            <div class='others'>
                                            ";
                                                                if($item['proportion_flg'] == 0 && $item['proportion'] == '') {
                                                                    echo "<div class='progress incomplete'>未視聴</div>";
                                                                } else if($item['proportion_flg'] == 0 && $item['proportion'] != '') {
                                                                    echo "<div class='progress midstream'>" . $item['proportion'] . "%</div>";
                                                                } else if($item['proportion_flg'] == 1 && $item['proportion'] != '') {
                                                                    echo "<div class='progress complete'>" . $item['proportion'] . "%</div>";
                                                                }
                                            echo "
                                                                <ul class='btns'>
                                                                    <li class='play'><a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'contents_extension_id' ] ."'
                                                                    data-contentID=".$item[ 'primary_key' ]."
                                                                    data-extensionID=".$item[ 'contents_extension_id' ]."
                                                                    data-title=".$item[ 'title' ]."></data></a></data></li><!-- 再生 -->
                                                                    <li class='reacquire'><button id='reacquire_btn' class='reacquire_results' data-bid=".$_GET['bid']." data-studentID=".$student_id." data-contentID2=".$item[ 'primary_key' ]." data-extensionID2=".$item[ 'contents_extension_id' ]."'></button></li><!-- ログ再取得 -->
                                                                    <li class='info'><button id='motal_contents' class='contents_info' data-bid=".$_GET['bid']." data-studentID=".$student_id." data-contentID2=".$item[ 'primary_key' ]." data-extensionID2=".$item[ 'contents_extension_id' ]."  data-toggle='modal' data-target='#Modal-contentsinfo-tb-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                            ";
                                                                        for($i = 0; $i < count($item) - $contents_parameter_value ; $i++) {
                                                                            echo "
                                                                                <li class='file'>
                                                                                    <form
                                                                                          action='" . $url . "download_attachment.php'
                                                                                          method='post'
                                                                                          target='hidden-iframe'
                                                                                          >
                                                                                        <input type='hidden' name='id' value=" .$item[$i]['contents_attachment_id']. ">
                                                                                        <input type='hidden' name='name' value=" .$item[$i]['file_name']. ">
                                                                                        <input type='submit' value=''>
                                                                                    </form>
                                                                                </li>
                                                                            ";
                                                                        }
                                            echo "
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- modal(コンテンツ詳細-TB) -->
                                            <div class='modal fade contentsinfo tb' id='Modal-contentsinfo-tb-id=" . $item['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                                <div class='modal-dialog' role='document'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header'>
                                                            <p class='icon'></p>
                                                            <p class='contents-type'>動画授業(TB形式)</p>
                                                            <p class='contents-title'>" . $item['title'] . "</p>
                                                            <button type='button' id='contents_close' class='close' data-dismiss='modal' aria-label='Close'>
                                                                <span aria-hidden='true'>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class='modal-body contentsinfo'>
                                                            <div class='body-left'>
                                                                <div class='btngroup'>
                                                                    <ul>
                                                                        <li class='play'><a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'contents_extension_id' ] ."'
                                                                          data-contentID=".$item[ 'primary_key' ]."
                                                                          data-extensionID=".$item[ 'contents_extension_id' ]."
                                                                          data-title=".$item[ 'title' ].">再生する</a></li>
                                            ";
                                                                        for($i = 0; $i < count($item) - $contents_parameter_value ; $i++) {
                                                                            echo "
                                                                                <li class='file'>
                                                                                    <form
                                                                                          action='" . $url . "download_attachment.php'
                                                                                          method='post'
                                                                                          target='hidden-iframe'
                                                                                          >
                                                                                        <input type='hidden' name='id' value=" .$item[$i]['contents_attachment_id']. ">
                                                                                        <input type='hidden' name='name' value=" .$item[$i]['file_name']. ">
                                                                                        <input type='submit' value=''>
                                                                                        添付ファイル
                                                                                    </form>
                                                                                </li>
                                                                            ";
                                                                        }
                                            echo "
                                                                    </ul>
                                                                </div>
                                                                <div class='contents-position'>
                                                                    <div class='head'>カテゴリー・フォルダー</div>
                                                                    <div class='subject'>
                                                                        <ol>
                                                                           <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                           <li>" . $subject_section_name ."</li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class='contentsgroup'>
                                                                        <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                    </div>
                                                                </div>
                                                                <div class='contents-relation'>
                                                                    <div class='head'>関連コンテンツ</div>
                                                                    <ul>
                                                                        <li>TB</li>
                                                                        <li>MP4</li>
                                                                        <li>テスト</li>
                                                                        <li>アンケート</li>
                                                                        <li>レポート</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class='body-right'>
                                                                <div class='other-detail'>
                                                                    <table>
                                                                        <tr>
                                                                            <th>視聴状況</th>
                                            ";
                                                                            if($item['proportion'] == '') {
                                                                                echo "<td>未視聴</td>";
                                                                            } else {
                                                                                echo "<td>" . $item['proportion'] . "％</td>";
                                                                            }
                                            echo "
                                                                        </tr>
                                                                        <tr>
                                                                            <th>最終視聴履歴</th>
                                            ";
                                                                            if($item['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                                echo "<td>未視聴</td>";
                                                                            } else {
                                                                                echo "<td>" . $item['play_start_datetime'] . "</td>";
                                                                            }
                                            echo "
                                                                        </tr>
                                                                        <tr>
                                                                            <th>視聴期限</th>
                                            ";
                                                                            if($item['first_day'] == '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                                echo "<td>期限なし</td>";
                                                                            } else if($item['first_day'] != '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                                echo "<td>" . $item['first_day'] . " ～ </td>";
                                                                            } else if($item['first_day'] == '0000-01-01' && $item['last_day'] != '9999-12-31') {
                                                                                echo "<td> ～ " . $item['last_day'] . "</td>";
                                                                            } else {
                                                                                echo "<td>" . $item['first_day'] . " ～ " . $item['last_day'] . "</td>";
                                                                            }
                                            echo "
                                                                        </tr>
                                                                        <tr>
                                                                            <th>受講回数</th>
                                                                            <td>" . $item['watch_count'] . "回</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>容量</th>
                                                                            <td>" . size_format($item['size']) . "</td>
                                                                        </tr>
                                                                        <!--
                                                                        <tr class='message'>
                                                                            <th>投稿者</th>
                                                                            <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                        </tr>
                                                                        <tr class='message'>
                                                                            <th>関連スレッド</th>
                                                                            <td>
                                                                                <div class='linkbtn'><a>スレッド01</a></div>
                                                                                <div class='linkbtn'><a>スレッド02</a></div>
                                                                                <div class='linkbtn'><a>スレッド03</a></div>
                                                                            </td>
                                                                        </tr>
                                                                        -->
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            ";
                                        } else {
                                            echo "
                                                <!-- contents item = MP4 -->
                                                <div class='list-dummy-01 contents-item mp4'>
                                                    <div class='in'>
                                                        <div class='title'>
                                                            <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'contents_extension_id' ] ."'
                                                                data-contentID=".$item[ 'primary_key' ]."
                                                                data-extensionID=".$item[ 'contents_extension_id' ]."
                                                                data-title=".$item[ 'title' ].">" . $item['title'] . "
                                                            </a>
                                                        </div>
                                                        <div class='detail-info'>
                                                            <div class='important-info'>
                                                                <span class='count'>" . $item['watch_count'] . "回</span>
                                            ";
                                                                if($item['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                    echo "<span class='contents-time'>未視聴</span>";
                                                                } else {
                                                                    echo "<span class='contents-time'>".$item['play_start_datetime']."</span>";
                                                                }
                                            echo "
                                                            </div>
                                                            <div class='others'>
                                            ";
                                                                if($item['proportion_flg'] == 0 && $item['proportion'] == '') {
                                                                    echo "<div class='progress incomplete'>未視聴</div>";
                                                                } else if($item['proportion_flg'] == 0 && $item['proportion'] != '') {
                                                                    echo "<div class='progress midstream'>" . $item['proportion'] . "%</div>";
                                                                } else if($item['proportion_flg'] == 1 && $item['proportion'] != '') {
                                                                    echo "<div class='progress complete'>" . $item['proportion'] . "%</div>";
                                                                }
                                            echo "
                                                                <ul class='btns'>
                                                                    <li class='play'>
                                                                      <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'primary_key' ]. "&bid=" . $_GET['bid'] . "&s_id=" .$student_id . "&e_id=" . $item[ 'contents_extension_id' ] ."'
                                                                      data-contentID=".$item[ 'primary_key' ]."
                                                                      data-extensionID=".$item[ 'contents_extension_id' ]."
                                                                      data-title=".$item[ 'title' ]."></data></a></li><!-- 再生 -->
                                                                    <li class='reacquire'><button id='reacquire_mp4' class='reacquire_results' data-bid=".$_GET['bid']." data-studentID=".$student_id." data-contentID2=".$item[ 'primary_key' ]." data-extensionID2=".$item[ 'contents_extension_id' ]."'></button></li><!-- ログ再取得 -->
                                                                    <li class='info'><button data-toggle='modal' class='contents_info' data-bid=".$_GET['bid']." data-studentID=".$student_id." data-contentID2=".$item[ 'primary_key' ]." data-extensionID2=".$item[ 'contents_extension_id' ]." data-target='#Modal-contentsinfo-mp4-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                            ";
                                                                        for($i = 0; $i < count($item) - $contents_parameter_value ; $i++) {
                                                                            echo "
                                                                                <li class='file'>
                                                                                    <form
                                                                                          action='" . $url . "download_attachment.php'
                                                                                          method='post'
                                                                                          target='hidden-iframe'
                                                                                          >
                                                                                        <input type='hidden' name='id' value=" .$item[$i]['contents_attachment_id']. ">
                                                                                        <input type='hidden' name='name' value=" .$item[$i]['file_name']. ">
                                                                                        <input type='submit' value=''>
                                                                                    </form>
                                                                                </li>
                                                                            ";
                                                                        }
                                            echo "
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- modal(コンテンツ詳細-MP4) -->
                                            <div class='modal fade contentsinfo mp4' id='Modal-contentsinfo-mp4-id=" . $item['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                                <div class='modal-dialog' role='document'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header'>
                                                            <p class='icon'></p>
                                                            <p class='contents-type'>動画授業(MP4形式)</p>
                                                            <p class='contents-title'>" . $item['title'] . "</p>
                                                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                                <span aria-hidden='true'>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class='modal-body contentsinfo'>
                                                            <div class='body-left'>
                                                                <div class='btngroup'>
                                                                    <ul>
                                                                        <li class='play'><a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'primary_key' ]. "&bid=" . $_GET['bid'] . "&s_id=" .$student_id . "&e_id=" . $item[ 'contents_extension_id' ] ."'
                                                                          data-contentID=".$item[ 'primary_key' ]."
                                                                          data-extensionID=".$item[ 'contents_extension_id' ]."
                                                                          data-title=".$item[ 'title' ].">再生する</a></li>
                                            ";
                                                                        for($i = 0; $i < count($item) - $contents_parameter_value ; $i++) {
                                                                            echo "
                                                                                <li class='file'>
                                                                                    <form
                                                                                          action='" . $url . "download_attachment.php'
                                                                                          method='post'
                                                                                          target='hidden-iframe'
                                                                                          >
                                                                                        <input type='hidden' name='id' value=" .$item[$i]['contents_attachment_id']. ">
                                                                                        <input type='hidden' name='name' value=" .$item[$i]['file_name']. ">
                                                                                        <input type='submit' value=''>
                                                                                        添付ファイル
                                                                                    </form>
                                                                                </li>
                                                                            ";
                                                                        }
                                            echo "
                                                                    </ul>
                                                                </div>
                                                                <div class='contents-position'>
                                                                    <div class='head'>カテゴリー・フォルダー</div>
                                                                    <div class='subject'>
                                                                        <ol>
                                                                           <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                           <li>" . $subject_section_name ."</li>
                                                                        </ol>
                                                                    </div>
                                                                    <div class='contentsgroup'>
                                                                        <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                    </div>
                                                                </div>
                                                                <div class='contents-relation'>
                                                                    <div class='head'>関連コンテンツ</div>
                                                                    <ul>
                                                                        <li>TB</li>
                                                                        <li>MP4</li>
                                                                        <li>テスト</li>
                                                                        <li>アンケート</li>
                                                                        <li>レポート</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class='body-right'>
                                                                <div class='other-detail'>
                                                                    <table>
                                                                        <tr>
                                                                            <th>視聴状況</th>
                                            ";
                                                                            if($item['proportion'] == '') {
                                                                                echo "<td>未視聴</td>";
                                                                            } else {
                                                                                echo "<td>" . $item['proportion'] . "％</td>";
                                                                            }
                                            echo "
                                                                        </tr>
                                                                        <tr>
                                                                            <th>最終視聴履歴</th>
                                            ";
                                                                            if($item['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                                echo "<td>未視聴</td>";
                                                                            } else {
                                                                                echo "<td>" . $item['play_start_datetime'] . "</td>";
                                                                            }
                                            echo "
                                                                        </tr>
                                                                        <tr>
                                                                            <th>視聴期限</th>
                                            ";
                                                                            if($item['first_day'] == '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                                echo "<td>期限なし</td>";
                                                                            } else if($item['first_day'] != '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                                echo "<td>" . $item['first_day'] . " ～ </td>";
                                                                            } else if($item['first_day'] == '0000-01-01' && $item['last_day'] != '9999-12-31') {
                                                                                echo "<td> ～ " . $item['last_day'] . "</td>";
                                                                            } else {
                                                                                echo "<td>" . $item['first_day'] . " ～ " . $item['last_day'] . "</td>";
                                                                            }
                                            echo "
                                                                        </tr>
                                                                        <tr>
                                                                            <th>受講回数</th>
                                                                            <td>" . $item['watch_count'] . "回</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>容量</th>
                                                                            <td>" . size_format($item['size']) . "</td>
                                                                        </tr>
                                                                        <!--
                                                                        <tr class='message'>
                                                                            <th>投稿者</th>
                                                                            <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                        </tr>
                                                                        <tr class='message'>
                                                                            <th>関連スレッド</th>
                                                                            <td>
                                                                                <div class='linkbtn'><a>スレッド01</a></div>
                                                                                <div class='linkbtn'><a>スレッド02</a></div>
                                                                                <div class='linkbtn'><a>スレッド03</a></div>
                                                                            </td>
                                                                        </tr>
                                                                        -->
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            ";
                                        }
                                        break;
                                    case 1:
                                        echo "
                                            <!-- contents item = questionnaire -->
                                            <div class='list-dummy-02 contents-item questionnaire'>
                                                <div class='in'>
                                        ";
                                                if($item['answer_flg'] == 1) {
                                                    echo "
                                                        <div class='title'>" . $item['title'] . "</div>
                                                    ";
                                                } else {
                                                    echo "
                                                        <div class='title'><a href='questionnaire/questionnaire.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'>" . $item['title'] . "</a></div>
                                                    ";
                                                }
                                        echo "
                                                    <div class='detail-info'>
                                                        <div class='important-info'>
                                                            <span class='limit'>～" . $item['last_day'] . "</span>
                                                        </div>
                                                        <div class='others'>
                                        ";
                                                            if($item['answer_flg'] == 1) {
                                                                echo "
                                                                    <div class='progress complete'>提出済</div>
                                                                    <ul class='btns'>
                                                                        <li class='info'><button data-toggle='modal' data-target='#Modal-questionnaireinfo-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                                                    </ul>
                                                                ";
                                                            } else {
                                                                echo "
                                                                    <div class='progress incomplete'>未提出</div>
                                                                    <ul class='btns'>
                                                                        <li class='write'><a href='questionnaire/questionnaire.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li><!-- 書く -->
                                                                        <li class='info'><button data-toggle='modal' data-target='#Modal-questionnaireinfo-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                                                    </ul>
                                                                ";
                                                            }
                                        echo "
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- modal(アンケート詳細) -->
                                        <div class='modal fade contentsinfo questionnaire' id='Modal-questionnaireinfo-id=" . $item['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                            <div class='modal-dialog' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <p class='icon'></p>
                                                        <p class='contents-type'>アンケート</p>
                                                        <p class='contents-title'>" . $item['title'] . "</p>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body contentsinfo'>
                                                        <div class='body-left'>
                                                            <div class='btngroup'>
                                                                <ul>
                                        ";
                                                            if($item['answer_flg'] == 1) {
                                                            } else {
                                                                echo "<li class='play'><a href='questionnaire/questionnaire.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'>回答する</a></li>";
                                                            }
                                        echo "
                                                                </ul>
                                                            </div>
                                                            <div class='contents-position'>
                                                                <div class='head'>カテゴリー・フォルダー</div>
                                                                <div class='subject'>
                                                                    <ol>
                                                                       <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                       <li>" . $subject_section_name ."</li>
                                                                    </ol>
                                                                </div>
                                                                <div class='contentsgroup'>
                                                                    <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                </div>
                                                            </div>
                                                            <div class='contents-relation'>
                                                                <div class='head'>関連コンテンツ</div>
                                                                <ul>
                                                                    <li>TB</li>
                                                                    <li>MP4</li>
                                                                    <li>テスト</li>
                                                                    <li>アンケート</li>
                                                                    <li>レポート</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class='body-right'>
                                                            <div class='other-detail'>
                                                                <table>
                                                                    <tr>
                                                                        <th>回答状況</th>
                                        ";
                                                            if($item['answer_flg'] == 1) {
                                                                echo "<td>回答済<td>";
                                                            } else {
                                                                echo "<td>末回答<td>";
                                                            }
                                        echo "
                                                                    </tr>
                                                                    <tr>
                                                                        <th>回答期限</th>
                                        ";
                                                                        if($item['start_day'] == '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                            echo "<td>期限なし</td>";
                                                                        } else if($item['start_day'] != '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                            echo "<td>" . $item['start_day'] . " ～ </td>";
                                                                        } else if($item['start_day'] == '0000-01-01' && $item['last_day'] != '9999-12-31') {
                                                                            echo "<td> ～ " . $item['last_day'] . "</td>";
                                                                        } else {
                                                                            echo "<td>" . $item['start_day'] . " ～ " . $item['last_day'] . "</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <!--
                                                                    <tr class='message'>
                                                                        <th>投稿者</th>
                                                                        <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                    </tr>
                                                                    <tr class='message'>
                                                                        <th>関連スレッド</th>
                                                                        <td>
                                                                            <div class='linkbtn'><a>スレッド01</a></div>
                                                                            <div class='linkbtn'><a>スレッド02</a></div>
                                                                            <div class='linkbtn'><a>スレッド03</a></div>
                                                                        </td>
                                                                    </tr>
                                                                    -->
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                        break;
                                    case 2:
                                        echo "
                                            <!-- contents item = report -->
                                            <div class='list-dummy-03 contents-item report'>
                                                <div class='in'>
                                        ";
                                                if($item['answer_flg'] == 1) {
                                                    echo "
                                                        <div class='title'>" . $item['title'] . "</div>
                                                    ";
                                                } else {
                                                    echo "
                                                        <div class='title'><a href='report/report.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'>" . $item['title'] . "</a></div>
                                                    ";
                                                }
                                        echo "
                                                    <div class='detail-info'>
                                                        <div class='important-info'>
                                                            <span class='limit'>～" . $item['last_day'] . "</span>
                                                        </div>
                                                        <div class='others'>
                                        ";
                                                            if($item['answer_flg'] == 1) {
                                                                echo "
                                                                    <div class='progress complete'>提出済</div>
                                                                    <ul class='btns'>
                                                                        <li class='info'><button data-toggle='modal' data-target='#Modal-reportinfo-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                                                    </ul>
                                                                ";
                                                            } else {
                                                                echo "
                                                                    <div class='progress incomplete'>未提出</div>
                                                                    <ul class='btns'>
                                                                        <li class='write'><a href='report/report.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a></li><!-- 書く -->
                                                                        <li class='info'><button data-toggle='modal' data-target='#Modal-reportinfo-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                                                    </ul>
                                                                ";
                                                            }
                                        echo "
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- modal(レポート詳細) -->
                                        <div class='modal fade contentsinfo report' id='Modal-reportinfo-id=" . $item['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                            <div class='modal-dialog' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <p class='icon'></p>
                                                        <p class='contents-type'>レポート</p>
                                                        <p class='contents-title'>" . $item['title'] . "</p>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body contentsinfo'>
                                                        <div class='body-left'>
                                                            <div class='btngroup'>
                                                                <ul>
                                        ";
                                                            if($item['answer_flg'] == 1) {
                                                            } else {
                                                                echo "<li class='play'><a href='report/report.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'>作成する</a></li>";
                                                            }
                                        echo "
                                                                </ul>
                                                            </div>
                                                            <div class='contents-position'>
                                                                <div class='head'>カテゴリー・フォルダー</div>
                                                                <div class='subject'>
                                                                    <ol>
                                                                       <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                       <li>" . $subject_section_name ."</li>
                                                                    </ol>
                                                                </div>
                                                                <div class='contentsgroup'>
                                                                    <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                </div>
                                                            </div>
                                                            <div class='contents-relation'>
                                                                <div class='head'>関連コンテンツ</div>
                                                                <ul>
                                                                    <li>TB</li>
                                                                    <li>MP4</li>
                                                                    <li>テスト</li>
                                                                    <li>アンケート</li>
                                                                    <li>レポート</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class='body-right'>
                                                            <div class='other-detail'>
                                                                <table>
                                                                    <tr>
                                                                        <th>回答状況</th>
                                        ";
                                                            if($item['answer_flg'] == 1) {
                                                                echo "<td>回答済<td>";
                                                            } else {
                                                                echo "<td>末回答<td>";
                                                            }
                                        echo "
                                                                    </tr>
                                                                    <tr>
                                                                        <th>作成期限</th>
                                        ";
                                                                        if($item['start_day'] == '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                            echo "<td>期限なし</td>";
                                                                        } else if($item['start_day'] != '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                            echo "<td>" . $item['start_day'] . " ～ </td>";
                                                                        } else if($item['start_day'] == '0000-01-01' && $item['last_day'] != '9999-12-31') {
                                                                            echo "<td> ～ " . $item['last_day'] . "</td>";
                                                                        } else {
                                                                            echo "<td>" . $item['start_day'] . " ～ " . $item['last_day'] . "</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <!--
                                                                    <tr class='message'>
                                                                        <th>投稿者</th>
                                                                        <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                    </tr>
                                                                    <tr class='message'>
                                                                        <th>関連スレッド</th>
                                                                        <td>
                                                                            <div class='linkbtn'><a>スレッド01</a></div>
                                                                            <div class='linkbtn'><a>スレッド02</a></div>
                                                                            <div class='linkbtn'><a>スレッド03</a></div>
                                                                        </td>
                                                                    </tr>
                                                                    -->
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                        break;
                                    case 3:
                                        echo "
                                        <!-- contents item = test(合格) -->
                                        <div class='list-dummy-04 contents-item test'>
                                            <div class='in'>
                                        ";
                                                    if($item['repeat_challenge'] > 0 && $item['repeat_challenge'] <= count($item) - $quiz_parameter_value) {
                                                        echo "<div class='title'>" . $item['title'] . "</div>";
                                                    } else if($item['last_answer_id'] == 0) {
                                                        echo "<div class='title'><a href='quiz/start.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'>" . $item['title'] . "</a></div>";
                                                    } else {
                                                        echo "<div class='title'><a href='quiz/start.php?id=" . $item['primary_key'] . "&an=" . $item['last_answer_id'] . "&bid=" . $subject_section_id . "'>" . $item['title'] . "</a></div>";
                                                    }
                                        echo "
                                                <div class='detail-info'>
                                                    <div class='important-info'>
                                        ";
                                                        if($item['repeat_challenge'] != 0) {
                                                          // 2019/6/03 count関数対策
                                                          if(is_countable($item)){
                                                            echo "<span class='count'>あと" . ($item['repeat_challenge'] - (count($item) - $quiz_parameter_value)) . "回</span>";
                                                          }
                                                            //echo "<span class='count'>あと" . ($item['repeat_challenge'] - (count($item) - $quiz_parameter_value)) . "回</span>";
                                                        } else {
                                                            echo "<span class='count'>無制限</span>";
                                                        }
                                        echo "
                                                        <span class='limit'>～" . $item['last_day'] . "</span>
                                                    </div>
                                                    <div class='others'>
                                        ";          //debug( $item );
                                                    //debug( $item[ 'primary_key'] );
                                                    //debug( $subject_section_id );
                                                        if($item['answer_count'] > 0) {
                                                            echo "<div class='progress complete'>" . $item['max_score'] . "点</div>";
                                                        } else {
                                                            echo "<div class='progress failure'></div>";
                                                        }

                                                        if($item['repeat_challenge'] > 0 && $item['repeat_challenge'] <= count($item) - $quiz_parameter_value) {
                                                          //debug("1");
                                                          //debug( count($item) );
                                                          //debug( $quiz_parameter_value );
                                                            echo "
                                                                <ul class='btns'>
                                                                </li>
                                                            ";
                                                        } else if($item['last_answer_id'] == 0) {
                                                          //debug("2");
                                                            echo "
                                                                <ul class='btns'>
                                                                    <li class='write'><a href='quiz/start.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'></a><!-- テストを受ける -->
                                                                </li>
                                                            ";
                                                        } else {
                                                          //debug("3");
                                                            echo "
                                                                <ul class='btns'>
                                                                    <li class='write'><a href='quiz/start.php?id=" . $item['primary_key'] . "&an=" . $item['last_answer_id'] . "&bid=" . $subject_section_id . "'></a><!-- テストを受ける -->
                                                                </li>
                                                            ";
                                                        }
                                                        if(isset($item['last_register_datetime'])) {
                                                          //debug("4");
                                                            #echo "<li class='graph'><a href='quiz/result.php?id=" . $item['last_quiz_answer'] . "&bid=" . $subject_section_id . "'></a></li><!-- テスト結果 -->";
                                                            echo "<li class='graph'><a href='quiz/result.php?id=" . $item['primary_key'] . "&an=" . $item['last_quiz_answer'] . "&bid=" . $subject_section_id . "'></a></li><!-- テスト結果 -->";
                                                        }
                                        echo "
                                                            <li class='info'><button href='' data-toggle='modal' data-target='#Modal-testinfo-id=" . $item['primary_key'] . "'></button></li><!-- 詳細 -->
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                        echo "
                                        <!-- modal(テスト詳細) -->
                                        <div class='modal fade contentsinfo test' id='Modal-testinfo-id=" . $item['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                            <div class='modal-dialog' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <p class='icon'></p>
                                                        <p class='contents-type'>テスト</p>
                                                        <p class='contents-title'>" . $item['title'] . "</p>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body contentsinfo'>
                                                        <div class='body-left'>
                                                            <div class='btngroup'>
                                                                <ul>
                                            ";
                                                                if($item['repeat_challenge'] > 0 && $item['repeat_challenge'] <= count($item) - $quiz_parameter_value) {
                                                                    echo "
                                                                        <li class='write'>
                                                                        </li>
                                                                    ";
                                                                } else if($item['last_answer_id'] == 0) {
                                                                    echo "
                                                                        <li class='write'>
                                                                            <a href='quiz/start.php?id=" . $item['primary_key'] . "&bid=" . $subject_section_id . "'>テストを受ける</a>
                                                                        </li>
                                                                    ";
                                                                } else {
                                                                    echo "
                                                                        <li class='write'>
                                                                            <a href='quiz/start.php?id=" . $item['primary_key'] . "&an=" . $item['last_answer_id'] . "&bid=" . $subject_section_id . "'>テストを受ける</a>
                                                                        </li>
                                                                    ";
                                                                }
                                            echo "
                                                                </ul>
                                                            </div>
                                                            <div class='test-count'>
                                                                <div class='head'>詳細結果を見る</div>
                                                                <ul class='test-count-select'>

                                            ";
                                                                    for($i = 0, $j = 0; $i < count($item) - $quiz_parameter_value ; $i++) {
                                                                        if ($item[$i]['end_flag'] == 1) {
                                                                            $j++;
                                                                            if ($item[$i]['qualifying_score'] == 0) {
                                                                                echo "
                                                                                    <li>
                                                                                        <a href='quiz/result.php?id=" . $item[$i]['quiz_id'] . "&an=" . $item[$i]['answer_id'] . "&bid=" . $subject_section_id . "'>第" . ($j) . "回<span class='###判定無し###'>判定無し</span></a>
                                                                                    </li>
                                                                                ";
                                                                            } else if($item[$i]['qualifying_score'] <= $item[$i]['total_score']) {
                                                                                echo "
                                                                                    <li>
                                                                                        <a href='quiz/result.php?id=" . $item[$i]['quiz_id'] . "&an=" . $item[$i]['answer_id'] . "&bid=" . $subject_section_id . "'>第" . ($j) . "回<span class='t'>合格</span></a>
                                                                                    </li>
                                                                                ";
                                                                            } else if($item[$i]['qualifying_score'] > $item[$i]['total_score']) {
                                                                                echo "
                                                                                    <li>
                                                                                        <a href='quiz/result.php?id=" . $item[$i]['quiz_id'] . "&an=" . $item[$i]['answer_id'] . "&bid=" . $subject_section_id . "'>第" . ($j) . "回<span class='f'>不合格</span></a>
                                                                                    </li>
                                                                                ";
                                                                            }
                                                                        }
                                                                    }
                                            echo "
                                                                </ul>
                                                            </div>
                                                            <div class='contents-position'>
                                                                <div class='head'>カテゴリー・フォルダー</div>
                                                                <div class='subject'>
                                                                    <ol>
                                                                       <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                       <li>" . $subject_section_name ."</li>
                                                                    </ol>
                                                                </div>
                                                                <div class='contentsgroup'>
                                                                    <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                </div>
                                                            </div>
                                                            <div class='contents-relation'>
                                                                <div class='head'>関連コンテンツ</div>
                                                                <ul>
                                                                    <li>TB</li>
                                                                    <li>MP4</li>
                                                                    <li>テスト</li>
                                                                    <li>アンケート</li>
                                                                    <li>レポート</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class='body-right'>
                                                            <div class='other-detail'>
                                                                <table>
                                                                    <tr>
                                                                        <th>受験状況</th>
                                        ";
                                                                        if($item['answer_count'] == 0) {
                                                                            echo "<td>未受験</td>";
                                                                        } else {
                                                                            echo "<td>受験済</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <tr>
                                                                        <th>最終受験日</th>
                                        ";
                                                                        if($item['answer_count'] == 0) {
                                                                            echo "<td>-</td>";
                                                                        } else {
                                                                            echo "<td>～～～</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <tr>
                                                                        <th>受験期限</th>
                                        ";
                                                                        if($item['start_day'] == '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                            echo "<td>期限なし</td>";
                                                                        } else if($item['start_day'] != '0000-01-01' && $item['last_day'] == '9999-12-31') {
                                                                            echo "<td>" . $item['start_day'] . " ～ </td>";
                                                                        } else if($item['start_day'] == '0000-01-01' && $item['last_day'] != '9999-12-31') {
                                                                            echo "<td> ～ " . $item['last_day'] . "</td>";
                                                                        } else {
                                                                            echo "<td>" . $item['start_day'] . " ～ " . $item['last_day'] . "</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <tr>
                                                                        <th>受験回数</th>
                                                                        <td>" . $item['answer_count'] . "回</td>
                                                                    </tr>
                                                                        <th>回数制限</th>
                                        ";
                                                                        if($item['repeat_challenge'] == 0) {
                                                                            echo "<td>無制限</td>";
                                                                        } else {
                                                                            echo "<td>" . $item['repeat_challenge'] . "回</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <!--
                                                                    <tr>
                                                                        <th>容量</th>
                                                                        <td>20MB</td>
                                                                    </tr>
                                                                    <tr class='message'>
                                                                        <th>投稿者</th>
                                                                        <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                    </tr>
                                                                    <tr class='message'>
                                                                        <th>関連スレッド</th>
                                                                        <td>
                                                                            <div class='linkbtn'><a>スレッド01</a></div>
                                                                            <div class='linkbtn'><a>スレッド02</a></div>
                                                                            <div class='linkbtn'><a>スレッド03</a></div>
                                                                        </td>
                                                                    </tr>
                                                                    -->
                                                                    <tr>
                                                                        <th>合格点</th>
                                        ";
                                                                        if($item['qualifying_score'] == 0) {
                                                                            echo "<td>合格点無し</td>";
                                                                        } else {
                                                                            echo "<td>" . $item['qualifying_score'] . "点</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                    <tr>
                                                                        <th>最高得点</th>
                                        ";
                                                                        if($item['max_score'] == NULL) {
                                                                            echo "<td>未受験</td>";
                                                                        } else {
                                                                            echo "<td>" . $item['max_score'] . "点</td>";
                                                                        }
                                        echo "
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                        break;
                                  case 4:
                                    echo "
                                      <!-- フォルダー -->
                                      <div class='contentsgroup'>
                                      <!-- フォルダータイトル -->
                                        <div class='contentsgroup-title'>
                                          <p class='title'>" . $item['title'] . "</p>
                                        </div>";
                                    for ( $j = 0; $j < $item[ 'child_item' ]; $j++ ) {
                                      if ( $item['primary_key'] == $item['child_data'][$j]['parent_function_group_id'] ) {
                                        switch ( $item['child_data'][$j]['type'] ) {

                                          case 0:

                                          if ($item['child_data'][$j]['contents_extension_id'] <= 6) {
                                              echo "
                                                  <!-- contents item = TB -->
                                                  <div class='list-dummy-01 contents-item tb'>
                                                      <div class='in'>
                                                          <div class='title'>
                                                              <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'child_data' ][ $j ][ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'child_data' ][ $j ][ 'contents_extension_id' ] ."'
                                                                  data-contentID=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                  data-extensionID=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                  data-title=".$item[ 'child_data' ][ $j ][ 'title' ].">" . $item[ 'child_data' ][ $j ]['title'] . "
                                                              </a>
                                                          </div>
                                                          <div class='detail-info'>
                                                              <div class='important-info'>
                                                                  <span class='count'>" . $item[ 'child_data' ][ $j ]['watch_count'] . "回</span>
                                              ";
                                                                  if($item[ 'child_data' ][ $j ]['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                      echo "<span class='contents-time'>未視聴</span>";
                                                                  } else {
                                                                      echo "<span class='contents-time'>".$item[ 'child_data' ][ $j ]['play_start_datetime']."</span>";
                                                                  }
                                              echo "
                                                              </div>
                                                              <div class='others'>
                                              ";
                                                                  if($item[ 'child_data' ][ $j ]['proportion_flg'] == 0 && $item[ 'child_data' ][ $j ]['proportion'] == '') {
                                                                      echo "<div class='progress incomplete'>未視聴</div>";
                                                                  } else if($item[ 'child_data' ][ $j ]['proportion_flg'] == 0 && $item[ 'child_data' ][ $j ]['proportion'] != '') {
                                                                      echo "<div class='progress midstream'>" . $item[ 'child_data' ][ $j ]['proportion'] . "%</div>";
                                                                  } else if($item[ 'child_data' ][ $j ]['proportion_flg'] == 1 && $item[ 'child_data' ][ $j ]['proportion'] != '') {
                                                                      echo "<div class='progress complete'>" . $item[ 'child_data' ][ $j ]['proportion'] . "%</div>";
                                                                  }
                                              echo "
                                                                  <ul class='btns'>
                                                                      <li class='play'>
                                                                        <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'child_data' ][ $j ][ 'primary_key' ].
                                                                          "&bid=" . $_GET['bid'] ."&s_id=" .$student_id .
                                                                          "&e_id=" . $item[ 'child_data' ][ $j ][ 'contents_extension_id' ] ."'
                                                                          data-contentID=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                          data-extensionID=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                          data-title=".$item[ 'child_data' ][ $j ][ 'title' ].">
                                                                        </a>
                                                                      </li><!-- 再生 -->
                                                                      <li class='reacquire'><button id='reacquire_btn'
                                                                        class='reacquire_results' data-bid=".$_GET['bid']." data-studentID=".$student_id."
                                                                        data-contentID2=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                        data-extensionID2=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."'></button></li>
                                                                        <!-- ログ再取得 -->
                                                                      <li class='info'>
                                                                        <button id='motal_contents' class='contents_info' data-bid=".$_GET['bid']."
                                                                          data-studentID=".$student_id." data-contentID2=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                          data-extensionID2=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                          data-toggle='modal' data-target='#Modal-contentsinfo-tb-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'>
                                                                        </button>
                                                                      </li><!-- 詳細 -->
                                              ";
                                                                          for($i = 0; $i < count($item[ 'child_data' ][ $j ]) - $contents_parameter_value ; $i++) {
                                                                              echo "
                                                                                  <li class='file'>
                                                                                      <form
                                                                                            action='" . $url . "download_attachment.php'
                                                                                            method='post'
                                                                                            target='hidden-iframe'
                                                                                            >
                                                                                          <input type='hidden' name='id' value=" .$item[ 'child_data' ][ $j ][$i]['contents_attachment_id']. ">
                                                                                          <input type='hidden' name='name' value=" .$item[ 'child_data' ][ $j ][$i]['file_name']. ">
                                                                                          <input type='submit' value=''>
                                                                                      </form>
                                                                                  </li>
                                                                              ";
                                                                          }
                                              echo "
                                                                  </ul>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              <!-- modal(コンテンツ詳細-TB) -->
                                              <div class='modal fade contentsinfo tb' id='Modal-contentsinfo-tb-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                                  <div class='modal-dialog' role='document'>
                                                      <div class='modal-content'>
                                                          <div class='modal-header'>
                                                              <p class='icon'></p>
                                                              <p class='contents-type'>動画授業(TB形式)</p>
                                                              <p class='contents-title'>" . $item[ 'child_data' ][ $j ]['title'] . "</p>
                                                              <button type='button' id='contents_close' class='close' data-dismiss='modal' aria-label='Close'>
                                                                  <span aria-hidden='true'>&times;</span>
                                                              </button>
                                                          </div>
                                                          <div class='modal-body contentsinfo'>
                                                              <div class='body-left'>
                                                                  <div class='btngroup'>
                                                                      <ul>
                                                                          <li class='play'><a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'child_data' ][ $j ][ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'child_data' ][ $j ][ 'contents_extension_id' ] ."'
                                                                            data-contentID=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                            data-extensionID=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                            data-title=".$item[ 'child_data' ][ $j ][ 'title' ].">再生する</a></li>
                                              ";
                                                                          for($i = 0; $i < count($item[ 'child_data' ][ $j ]) - $contents_parameter_value ; $i++) {
                                                                              echo "
                                                                                  <li class='file'>
                                                                                      <form
                                                                                            action='" . $url . "download_attachment.php'
                                                                                            method='post'
                                                                                            target='hidden-iframe'
                                                                                            >
                                                                                          <input type='hidden' name='id' value=" .$item[ 'child_data' ][ $j ][$i]['contents_attachment_id']. ">
                                                                                          <input type='hidden' name='name' value=" .$item[ 'child_data' ][ $j ][$i]['file_name']. ">
                                                                                          <input type='submit' value=''>
                                                                                          添付ファイル
                                                                                      </form>
                                                                                  </li>
                                                                              ";
                                                                          }
                                              echo "
                                                                      </ul>
                                                                  </div>
                                                                  <div class='contents-position'>
                                                                      <div class='head'>カテゴリー・フォルダー</div>
                                                                      <div class='subject'>
                                                                          <ol>
                                                                             <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                             <li>" . $subject_section_name ."</li>
                                                                          </ol>
                                                                      </div>
                                                                      <div class='contentsgroup'>
                                                                          <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                      </div>
                                                                  </div>
                                                                  <div class='contents-relation'>
                                                                      <div class='head'>関連コンテンツ</div>
                                                                      <ul>
                                                                          <li>TB</li>
                                                                          <li>MP4</li>
                                                                          <li>テスト</li>
                                                                          <li>アンケート</li>
                                                                          <li>レポート</li>
                                                                      </ul>
                                                                  </div>
                                                              </div>
                                                              <div class='body-right'>
                                                                  <div class='other-detail'>
                                                                      <table>
                                                                          <tr>
                                                                              <th>視聴状況</th>
                                              ";
                                                                              if($item[ 'child_data' ][ $j ]['proportion'] == '') {
                                                                                  echo "<td>未視聴</td>";
                                                                              } else {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['proportion'] . "％</td>";
                                                                              }
                                              echo "
                                                                          </tr>
                                                                          <tr>
                                                                              <th>最終視聴履歴</th>
                                              ";
                                                                              if($item[ 'child_data' ][ $j ]['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                                  echo "<td>未視聴</td>";
                                                                              } else {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['play_start_datetime'] . "</td>";
                                                                              }
                                              echo "
                                                                          </tr>
                                                                          <tr>
                                                                              <th>視聴期限</th>
                                              ";
                                                                              if($item[ 'child_data' ][ $j ]['first_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                                  echo "<td>期限なし</td>";
                                                                              } else if($item[ 'child_data' ][ $j ]['first_day'] != '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                                  echo "<td>" . $item['first_day'] . " ～ </td>";
                                                                              } else if($item[ 'child_data' ][ $j ]['first_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] != '9999-12-31') {
                                                                                  echo "<td> ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                              } else {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['first_day'] . " ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                              }
                                              echo "
                                                                          </tr>
                                                                          <tr>
                                                                              <th>受講回数</th>
                                                                              <td>" . $item[ 'child_data' ][ $j ]['watch_count'] . "回</td>
                                                                          </tr>
                                                                          <tr>
                                                                              <th>容量</th>
                                                                              <td>" . size_format($item[ 'child_data' ][ $j ]['size']) . "</td>
                                                                          </tr>
                                                                          <!--
                                                                          <tr class='message'>
                                                                              <th>投稿者</th>
                                                                              <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                          </tr>
                                                                          <tr class='message'>
                                                                              <th>関連スレッド</th>
                                                                              <td>
                                                                                  <div class='linkbtn'><a>スレッド01</a></div>
                                                                                  <div class='linkbtn'><a>スレッド02</a></div>
                                                                                  <div class='linkbtn'><a>スレッド03</a></div>
                                                                              </td>
                                                                          </tr>
                                                                          -->
                                                                      </table>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              ";
                                          } else {
                                              echo "
                                                  <!-- contents item = MP4 -->
                                                  <div class='list-dummy-01 contents-item mp4'>
                                                      <div class='in'>
                                                          <div class='title'>
                                                              <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'child_data' ][ $j ][ 'primary_key' ]. "&bid=" . $_GET['bid'] ."&s_id=" .$student_id . "&e_id=" . $item[ 'child_data' ][ $j ][ 'contents_extension_id' ] ."'
                                                                  data-contentID=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                  data-extensionID=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                  data-title=".$item[ 'child_data' ][ $j ][ 'title' ].">" . $item[ 'child_data' ][ $j ]['title'] . "
                                                              </a>
                                                          </div>
                                                          <div class='detail-info'>
                                                              <div class='important-info'>
                                                                  <span class='count'>" . $item[ 'child_data' ][ $j ]['watch_count'] . "回</span>
                                              ";
                                                                  if($item[ 'child_data' ][ $j ]['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                      echo "<span class='contents-time'>未視聴</span>";
                                                                  } else {
                                                                      echo "<span class='contents-time'>".$item[ 'child_data' ][ $j ][ 'child_data' ][ $j ]['play_start_datetime']."</span>";
                                                                  }
                                              echo "
                                                              </div>
                                                              <div class='others'>
                                              ";
                                                                  if($item[ 'child_data' ][ $j ]['proportion_flg'] == 0 && $item[ 'child_data' ][ $j ]['proportion'] == '') {
                                                                      echo "<div class='progress incomplete'>未視聴</div>";
                                                                  } else if($item[ 'child_data' ][ $j ]['proportion_flg'] == 0 && $item[ 'child_data' ][ $j ]['proportion'] != '') {
                                                                      echo "<div class='progress midstream'>" . $item[ 'child_data' ][ $j ]['proportion'] . "%</div>";
                                                                  } else if($item[ 'child_data' ][ $j ]['proportion_flg'] == 1 && $item[ 'child_data' ][ $j ]['proportion'] != '') {
                                                                      echo "<div class='progress complete'>" . $item[ 'child_data' ][ $j ]['proportion'] . "%</div>";
                                                                  }
                                              echo "
                                                                  <ul class='btns'>
                                                                      <li class='play'>
                                                                        <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'child_data' ][ $j ][ 'primary_key' ].
                                                                          "&bid=" . $_GET['bid'] . "&s_id=" .$student_id . "&e_id=" . $item[ 'child_data' ][ $j ][ 'contents_extension_id' ] ."'
                                                                          data-contentID=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                          data-extensionID=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                          data-title=".$item[ 'child_data' ][ $j ][ 'title' ]."></data>
                                                                        </a>
                                                                      </li><!-- 再生 -->
                                                                      <li class='reacquire'>
                                                                        <button id='reacquire_mp4' class='reacquire_results' data-bid=".$_GET['bid']."
                                                                          data-studentID=".$student_id." data-contentID2=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                          data-extensionID2=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."'>
                                                                        </button>
                                                                      </li><!-- ログ再取得 -->
                                                                      <li class='info'>
                                                                        <button data-toggle='modal' class='contents_info' data-bid=".$_GET['bid']."
                                                                          data-studentID=".$student_id." data-contentID2=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                          data-extensionID2=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                          data-target='#Modal-contentsinfo-mp4-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'>
                                                                        </button>
                                                                      </li><!-- 詳細 -->
                                              ";
                                                                          for($i = 0; $i < count($item[ 'child_data' ][ $j ]) - $contents_parameter_value ; $i++) {
                                                                              echo "
                                                                                  <li class='file'>
                                                                                      <form
                                                                                            action='" . $url . "download_attachment.php'
                                                                                            method='post'
                                                                                            target='hidden-iframe'
                                                                                            >
                                                                                          <input type='hidden' name='id' value=" .$item[ 'child_data' ][ $j ][$i]['contents_attachment_id']. ">
                                                                                          <input type='hidden' name='name' value=" .$item[ 'child_data' ][ $j ][$i]['file_name']. ">
                                                                                          <input type='submit' value=''>
                                                                                      </form>
                                                                                  </li>
                                                                              ";
                                                                          }
                                              echo "
                                                                  </ul>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              <!-- modal(コンテンツ詳細-MP4) -->
                                              <div class='modal fade contentsinfo mp4' id='Modal-contentsinfo-mp4-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                                  <div class='modal-dialog' role='document'>
                                                      <div class='modal-content'>
                                                          <div class='modal-header'>
                                                              <p class='icon'></p>
                                                              <p class='contents-type'>動画授業(MP4形式)</p>
                                                              <p class='contents-title'>" . $item[ 'child_data' ][ $j ]['title'] . "</p>
                                                              <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                                  <span aria-hidden='true'>&times;</span>
                                                              </button>
                                                          </div>
                                                          <div class='modal-body contentsinfo'>
                                                              <div class='body-left'>
                                                                  <div class='btngroup'>
                                                                      <ul>
                                                                          <li class='play'>
                                                                            <a class='tb3_play' href='contents/contents_play.php?c_id=" .$item[ 'child_data' ][ $j ][ 'primary_key' ].
                                                                              "&bid=" . $_GET['bid'] . "&s_id=" .$student_id . "&e_id=" . $item[ 'child_data' ][ $j ][ 'contents_extension_id' ] ."'
                                                                              data-contentID=".$item[ 'child_data' ][ $j ][ 'primary_key' ]."
                                                                              data-extensionID=".$item[ 'child_data' ][ $j ][ 'contents_extension_id' ]."
                                                                              data-title=".$item[ 'child_data' ][ $j ][ 'title' ].">再生する
                                                                            </a>
                                                                          </li>
                                              ";
                                                                          for($i = 0; $i < count($item[ 'child_data' ][ $j ]) - $contents_parameter_value ; $i++) {
                                                                              echo "
                                                                                  <li class='file'>
                                                                                      <form
                                                                                            action='" . $url . "download_attachment.php'
                                                                                            method='post'
                                                                                            target='hidden-iframe'
                                                                                            >
                                                                                          <input type='hidden' name='id' value=" .$item[ 'child_data' ][ $j ][$i]['contents_attachment_id']. ">
                                                                                          <input type='hidden' name='name' value=" .$item[ 'child_data' ][ $j ][$i]['file_name']. ">
                                                                                          <input type='submit' value=''>
                                                                                          添付ファイル
                                                                                      </form>
                                                                                  </li>
                                                                              ";
                                                                          }
                                              echo "
                                                                      </ul>
                                                                  </div>
                                                                  <div class='contents-position'>
                                                                      <div class='head'>カテゴリー・フォルダー</div>
                                                                      <div class='subject'>
                                                                          <ol>
                                                                             <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                             <li>" . $subject_section_name ."</li>
                                                                          </ol>
                                                                      </div>
                                                                      <div class='contentsgroup'>
                                                                          <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                      </div>
                                                                  </div>
                                                                  <div class='contents-relation'>
                                                                      <div class='head'>関連コンテンツ</div>
                                                                      <ul>
                                                                          <li>TB</li>
                                                                          <li>MP4</li>
                                                                          <li>テスト</li>
                                                                          <li>アンケート</li>
                                                                          <li>レポート</li>
                                                                      </ul>
                                                                  </div>
                                                              </div>
                                                              <div class='body-right'>
                                                                  <div class='other-detail'>
                                                                      <table>
                                                                          <tr>
                                                                              <th>視聴状況</th>
                                              ";
                                                                              if($item[ 'child_data' ][ $j ]['proportion'] == '') {
                                                                                  echo "<td>未視聴</td>";
                                                                              } else {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['proportion'] . "％</td>";
                                                                              }
                                              echo "
                                                                          </tr>
                                                                          <tr>
                                                                              <th>最終視聴履歴</th>
                                              ";
                                                                              if($item[ 'child_data' ][ $j ]['play_start_datetime'] == '0000-00-00 00:00:00') {
                                                                                  echo "<td>未視聴</td>";
                                                                              } else {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['play_start_datetime'] . "</td>";
                                                                              }
                                              echo "
                                                                          </tr>
                                                                          <tr>
                                                                              <th>視聴期限</th>
                                              ";
                                                                              if($item[ 'child_data' ][ $j ]['first_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                                  echo "<td>期限なし</td>";
                                                                              } else if($item[ 'child_data' ][ $j ]['first_day'] != '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['first_day'] . " ～ </td>";
                                                                              } else if($item[ 'child_data' ][ $j ]['first_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] != '9999-12-31') {
                                                                                  echo "<td> ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                              } else {
                                                                                  echo "<td>" . $item[ 'child_data' ][ $j ]['first_day'] . " ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                              }
                                              echo "
                                                                          </tr>
                                                                          <tr>
                                                                              <th>受講回数</th>
                                                                              <td>" . $item[ 'child_data' ][ $j ]['watch_count'] . "回</td>
                                                                          </tr>
                                                                          <tr>
                                                                              <th>容量</th>
                                                                              <td>" . size_format($item[ 'child_data' ][ $j ]['size']) . "</td>
                                                                          </tr>
                                                                          <!--
                                                                          <tr class='message'>
                                                                              <th>投稿者</th>
                                                                              <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                          </tr>
                                                                          <tr class='message'>
                                                                              <th>関連スレッド</th>
                                                                              <td>
                                                                                  <div class='linkbtn'><a>スレッド01</a></div>
                                                                                  <div class='linkbtn'><a>スレッド02</a></div>
                                                                                  <div class='linkbtn'><a>スレッド03</a></div>
                                                                              </td>
                                                                          </tr>
                                                                          -->
                                                                      </table>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              ";
                                          }
                                          break;

                                          case 1:
                                          echo "
                                              <!-- contents item = questionnaire -->
                                              <div class='list-dummy-02 contents-item questionnaire'>
                                                  <div class='in'>
                                          ";
                                                if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                    echo "
                                                        <div class='title'>" . $item[ 'child_data' ][ $j ]['title'] . "</div>
                                                    ";
                                                } else {
                                                    echo "
                                                        <div class='title'><a href='questionnaire/questionnaire.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'>" . $item[ 'child_data' ][ $j ]['title'] . "</a></div>
                                                    ";
                                                }
                                          echo "
                                                      <div class='detail-info'>
                                                          <div class='important-info'>
                                                              <span class='limit'>～" . $item[ 'child_data' ][ $j ]['last_day'] . "</span>
                                                          </div>
                                                          <div class='others'>
                                          ";
                                                              if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                                  echo "
                                                                      <div class='progress complete'>提出済</div>
                                                                      <ul class='btns'>
                                                                          <li class='info'>
                                                                            <button data-toggle='modal' data-target='#Modal-questionnaireinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'>
                                                                            </button>
                                                                          </li><!-- 詳細 -->
                                                                      </ul>
                                                                  ";
                                                              } else {
                                                                  echo "
                                                                      <div class='progress incomplete'>未提出</div>
                                                                      <ul class='btns'>
                                                                          <li class='write'>
                                                                            <a href='questionnaire/questionnaire.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] .
                                                                              "&bid=" . $subject_section_id . "'>
                                                                            </a>
                                                                          </li><!-- 書く -->
                                                                          <li class='info'>
                                                                            <button data-toggle='modal' data-target='#Modal-questionnaireinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'></button>
                                                                          </li><!-- 詳細 -->
                                                                      </ul>
                                                                  ";
                                                              }
                                          echo "
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          <!-- modal(アンケート詳細) -->
                                          <div class='modal fade contentsinfo questionnaire' id='Modal-questionnaireinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                              <div class='modal-dialog' role='document'>
                                                  <div class='modal-content'>
                                                      <div class='modal-header'>
                                                          <p class='icon'></p>
                                                          <p class='contents-type'>アンケート</p>
                                                          <p class='contents-title'>" . $item[ 'child_data' ][ $j ]['title'] . "</p>
                                                          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                              <span aria-hidden='true'>&times;</span>
                                                          </button>
                                                      </div>
                                                      <div class='modal-body contentsinfo'>
                                                          <div class='body-left'>
                                                              <div class='btngroup'>
                                                                  <ul>
                                          ";
                                                              if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                              } else {
                                                                  echo "<li class='play'><a href='questionnaire/questionnaire.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'>回答する</a></li>";
                                                              }
                                          echo "
                                                                  </ul>
                                                              </div>
                                                              <div class='contents-position'>
                                                                  <div class='head'>カテゴリー・フォルダー</div>
                                                                  <div class='subject'>
                                                                      <ol>
                                                                         <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                         <li>" . $subject_section_name ."</li>
                                                                      </ol>
                                                                  </div>
                                                                  <div class='contentsgroup'>
                                                                      <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                  </div>
                                                              </div>
                                                              <div class='contents-relation'>
                                                                  <div class='head'>関連コンテンツ</div>
                                                                  <ul>
                                                                      <li>TB</li>
                                                                      <li>MP4</li>
                                                                      <li>テスト</li>
                                                                      <li>アンケート</li>
                                                                      <li>レポート</li>
                                                                  </ul>
                                                              </div>
                                                          </div>
                                                          <div class='body-right'>
                                                              <div class='other-detail'>
                                                                  <table>
                                                                      <tr>
                                                                          <th>回答状況</th>
                                          ";
                                                              if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                                  echo "<td>回答済<td>";
                                                              } else {
                                                                  echo "<td>末回答<td>";
                                                              }
                                          echo "
                                                                      </tr>
                                                                      <tr>
                                                                          <th>回答期限</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['start_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                              echo "<td>期限なし</td>";
                                                                          } else if($item[ 'child_data' ][ $j ]['start_day'] != '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['start_day'] . " ～ </td>";
                                                                          } else if($item[ 'child_data' ][ $j ]['start_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] != '9999-12-31') {
                                                                              echo "<td> ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                          } else {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['start_day'] . " ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <!--
                                                                      <tr class='message'>
                                                                          <th>投稿者</th>
                                                                          <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                      </tr>
                                                                      <tr class='message'>
                                                                          <th>関連スレッド</th>
                                                                          <td>
                                                                              <div class='linkbtn'><a>スレッド01</a></div>
                                                                              <div class='linkbtn'><a>スレッド02</a></div>
                                                                              <div class='linkbtn'><a>スレッド03</a></div>
                                                                          </td>
                                                                      </tr>
                                                                      -->
                                                                  </table>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          ";
                                          break;

                                          case 2:
                                          echo "
                                              <!-- contents item = report -->
                                              <div class='list-dummy-03 contents-item report'>
                                                  <div class='in'>
                                          ";
                                                if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                    echo "
                                                        <div class='title'>" . $item[ 'child_data' ][ $j ]['title'] . "</div>
                                                    ";
                                                } else {
                                                    echo "
                                                        <div class='title'><a href='report/report.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'>" . $item[ 'child_data' ][ $j ]['title'] . "</a></div>
                                                    ";
                                                }
                                          echo "
                                                      <div class='detail-info'>
                                                          <div class='important-info'>
                                                              <span class='limit'>～" . $item[ 'child_data' ][ $j ]['last_day'] . "</span>
                                                          </div>
                                                          <div class='others'>
                                          ";
                                                              if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                                  echo "
                                                                      <div class='progress complete'>提出済</div>
                                                                      <ul class='btns'>
                                                                          <li class='info'>
                                                                            <button data-toggle='modal' data-target='#Modal-reportinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'></button>
                                                                          </li><!-- 詳細 -->
                                                                      </ul>
                                                                  ";
                                                              } else {
                                                                  echo "
                                                                      <div class='progress incomplete'>未提出</div>
                                                                      <ul class='btns'>
                                                                          <li class='write'><a href='report/report.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'></a></li><!-- 書く -->
                                                                          <li class='info'><button data-toggle='modal' data-target='#Modal-reportinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'></button></li><!-- 詳細 -->
                                                                      </ul>
                                                                  ";
                                                              }
                                          echo "
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          <!-- modal(レポート詳細) -->
                                          <div class='modal fade contentsinfo report' id='Modal-reportinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                              <div class='modal-dialog' role='document'>
                                                  <div class='modal-content'>
                                                      <div class='modal-header'>
                                                          <p class='icon'></p>
                                                          <p class='contents-type'>レポート</p>
                                                          <p class='contents-title'>" . $item[ 'child_data' ][ $j ]['title'] . "</p>
                                                          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                              <span aria-hidden='true'>&times;</span>
                                                          </button>
                                                      </div>
                                                      <div class='modal-body contentsinfo'>
                                                          <div class='body-left'>
                                                              <div class='btngroup'>
                                                                  <ul>
                                          ";
                                                              if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                              } else {
                                                                  echo "<li class='play'><a href='report/report.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'>作成する</a></li>";
                                                              }
                                          echo "
                                                                  </ul>
                                                              </div>
                                                              <div class='contents-position'>
                                                                  <div class='head'>カテゴリー・フォルダー</div>
                                                                  <div class='subject'>
                                                                      <ol>
                                                                         <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                         <li>" . $subject_section_name ."</li>
                                                                      </ol>
                                                                  </div>
                                                                  <div class='contentsgroup'>
                                                                      <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                  </div>
                                                              </div>
                                                              <div class='contents-relation'>
                                                                  <div class='head'>関連コンテンツ</div>
                                                                  <ul>
                                                                      <li>TB</li>
                                                                      <li>MP4</li>
                                                                      <li>テスト</li>
                                                                      <li>アンケート</li>
                                                                      <li>レポート</li>
                                                                  </ul>
                                                              </div>
                                                          </div>
                                                          <div class='body-right'>
                                                              <div class='other-detail'>
                                                                  <table>
                                                                      <tr>
                                                                          <th>回答状況</th>
                                          ";
                                                              if($item[ 'child_data' ][ $j ]['answer_flg'] == 1) {
                                                                  echo "<td>回答済<td>";
                                                              } else {
                                                                  echo "<td>末回答<td>";
                                                              }
                                          echo "
                                                                      </tr>
                                                                      <tr>
                                                                          <th>作成期限</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['start_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                              echo "<td>期限なし</td>";
                                                                          } else if($item[ 'child_data' ][ $j ]['start_day'] != '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['start_day'] . " ～ </td>";
                                                                          } else if($item[ 'child_data' ][ $j ]['start_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] != '9999-12-31') {
                                                                              echo "<td> ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                          } else {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['start_day'] . " ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <!--
                                                                      <tr class='message'>
                                                                          <th>投稿者</th>
                                                                          <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                      </tr>
                                                                      <tr class='message'>
                                                                          <th>関連スレッド</th>
                                                                          <td>
                                                                              <div class='linkbtn'><a>スレッド01</a></div>
                                                                              <div class='linkbtn'><a>スレッド02</a></div>
                                                                              <div class='linkbtn'><a>スレッド03</a></div>
                                                                          </td>
                                                                      </tr>
                                                                      -->
                                                                  </table>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          ";
                                          break;

                                          case 3:
                                          echo "
                                          <!-- contents item = test(合格) -->
                                          <div class='list-dummy-04 contents-item test'>
                                              <div class='in'>
                                          ";
                                                    if($item[ 'child_data' ][ $j ]['repeat_challenge'] > 0 && $item[ 'child_data' ][ $j ]['repeat_challenge'] <= count($item[ 'child_data' ][ $j ]) - $quiz_parameter_value) {
                                                        echo "<div class='title'>" . $item[ 'child_data' ][ $j ]['title'] . "</div>";
                                                    } else if($item[ 'child_data' ][ $j ]['last_answer_id'] == 0) {
                                                        echo "<div class='title'><a href='quiz/start.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'>" . $item[ 'child_data' ][ $j ]['title'] . "</a></div>";
                                                    } else {
                                                        echo "<div class='title'><a href='quiz/start.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&an=" . $item[ 'child_data' ][ $j ]['last_answer_id'] . "&bid=" . $subject_section_id . "'>" . $item[ 'child_data' ][ $j ]['title'] . "</a></div>";
                                                    }
                                          echo "
                                                  <div class='detail-info'>
                                                      <div class='important-info'>
                                          ";
                                                          if($item[ 'child_data' ][ $j ]['repeat_challenge'] != 0) {
                                                            // 2019/6/03 count関数対策
                                                              if(is_countable($item[ 'child_data' ][ $j ])){
                                                                echo "<span class='count'>あと" . ($item[ 'child_data' ][ $j ]['repeat_challenge'] - (count($item[ 'child_data' ][ $j ]) - $quiz_parameter_value)) . "回</span>";
                                                              }
                                                              //echo "<span class='count'>あと" . ($item[ 'child_data' ][ $j ]['repeat_challenge'] - (count($item[ 'child_data' ][ $j ]) - $quiz_parameter_value)) . "回</span>";
                                                          } else {
                                                              echo "<span class='count'>無制限</span>";
                                                          }
                                          echo "
                                                          <span class='limit'>～" . $item[ 'child_data' ][ $j ]['last_day'] . "</span>
                                                      </div>
                                                      <div class='others'>
                                          ";
                                                          if($item[ 'child_data' ][ $j ]['answer_count'] > 0) {
                                                              echo "<div class='progress complete'>" . $item[ 'child_data' ][ $j ]['max_score'] . "点</div>";
                                                          } else {
                                                              echo "<div class='progress failure'></div>";
                                                          }

                                                          if($item[ 'child_data' ][ $j ]['repeat_challenge'] > 0 && $item[ 'child_data' ][ $j ]['repeat_challenge'] <= count($item[ 'child_data' ][ $j ]) - $quiz_parameter_value) {
                                                              echo "
                                                                  <ul class='btns'>
                                                                  </li>
                                                              ";
                                                          } else if($item[ 'child_data' ][ $j ]['last_answer_id'] == 0) {
                                                              echo "
                                                                  <ul class='btns'>
                                                                      <li class='write'><a href='quiz/start.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'></a><!-- テストを受ける -->
                                                                  </li>
                                                              ";
                                                          } else {
                                                              echo "
                                                                  <ul class='btns'>
                                                                      <li class='write'><a href='quiz/start.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&an=" . $item[ 'child_data' ][ $j ]['last_answer_id'] . "&bid=" . $subject_section_id . "'></a><!-- テストを受ける -->
                                                                  </li>
                                                              ";
                                                          }
                                                          if(isset($item[ 'child_data' ][ $j ]['last_register_datetime'])) {
                                                              #echo "<li class='graph'><a href='quiz/result.php?id=" . $item['last_quiz_answer'] . "&bid=" . $subject_section_id . "'></a></li><!-- テスト結果 -->";
                                                              echo "<li class='graph'><a href='quiz/result.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&an=" . $item[ 'child_data' ][ $j ]['last_quiz_answer'] . "&bid=" . $subject_section_id . "'></a></li><!-- テスト結果 -->";
                                                          }
                                          echo "
                                                              <li class='info'><button href='' data-toggle='modal' data-target='#Modal-testinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "'></button></li><!-- 詳細 -->
                                                          </ul>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          ";
                                          echo "
                                          <!-- modal(テスト詳細) -->
                                          <div class='modal fade contentsinfo test' id='Modal-testinfo-id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "' tabindex='-1' role='dialog' aria-hidden='true'>
                                              <div class='modal-dialog' role='document'>
                                                  <div class='modal-content'>
                                                      <div class='modal-header'>
                                                          <p class='icon'></p>
                                                          <p class='contents-type'>テスト</p>
                                                          <p class='contents-title'>" . $item[ 'child_data' ][ $j ]['title'] . "</p>
                                                          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                              <span aria-hidden='true'>&times;</span>
                                                          </button>
                                                      </div>
                                                      <div class='modal-body contentsinfo'>
                                                          <div class='body-left'>
                                                              <div class='btngroup'>
                                                                  <ul>
                                              ";
                                                                  if($item[ 'child_data' ][ $j ]['repeat_challenge'] > 0 && $item[ 'child_data' ][ $j ]['repeat_challenge'] <= count($item[ 'child_data' ][ $j ]) - $quiz_parameter_value) {
                                                                      echo "
                                                                          <li class='write'>
                                                                          </li>
                                                                      ";
                                                                  } else if($item[ 'child_data' ][ $j ]['last_answer_id'] == 0) {
                                                                      echo "
                                                                          <li class='write'>
                                                                              <a href='quiz/start.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&bid=" . $subject_section_id . "'>テストを受ける</a>
                                                                          </li>
                                                                      ";
                                                                  } else {
                                                                      echo "
                                                                          <li class='write'>
                                                                              <a href='quiz/start.php?id=" . $item[ 'child_data' ][ $j ]['primary_key'] . "&an=" . $item[ 'child_data' ][ $j ]['last_answer_id'] . "&bid=" . $subject_section_id . "'>テストを受ける</a>
                                                                          </li>
                                                                      ";
                                                                  }
                                              echo "
                                                                  </ul>
                                                              </div>
                                                              <div class='test-count'>
                                                                  <div class='head'>詳細結果を見る</div>
                                                                  <ul class='test-count-select'>

                                              ";
                                                                      for($i = 0, $an = 0; $i < count($item[ 'child_data' ][ $j ]) - $quiz_parameter_value ; $i++) {
                                                                          if ($item[ 'child_data' ][ $j ][$i]['end_flag'] == 1) {
                                                                              $an++;
                                                                              if ($item[ 'child_data' ][ $j ][$i]['qualifying_score'] == 0) {
                                                                                  echo "
                                                                                      <li>
                                                                                          <a href='quiz/result.php?id=" . $item[ 'child_data' ][ $j ][$i]['quiz_id'] . "&an=" . $item[ 'child_data' ][ $j ][$i]['answer_id'] . "&bid=" . $subject_section_id . "'>第" . ($an) . "回<span class='###判定無し###'>判定無し</span></a>
                                                                                      </li>
                                                                                  ";
                                                                              } else if($item[ 'child_data' ][ $j ][$i]['qualifying_score'] <= $item[ 'child_data' ][ $j ][$i]['total_score']) {
                                                                                  echo "
                                                                                      <li>
                                                                                          <a href='quiz/result.php?id=" . $item[ 'child_data' ][ $j ][$i]['quiz_id'] . "&an=" . $item[ 'child_data' ][ $j ][$i]['answer_id'] . "&bid=" . $subject_section_id . "'>第" . ($an) . "回<span class='t'>合格</span></a>
                                                                                      </li>
                                                                                  ";
                                                                              } else if($item[ 'child_data' ][ $j ][$i]['qualifying_score'] > $item[ 'child_data' ][ $j ][$i]['total_score']) {
                                                                                  echo "
                                                                                      <li>
                                                                                          <a href='quiz/result.php?id=" . $item[ 'child_data' ][ $j ][$i]['quiz_id'] . "&an=" . $item[ 'child_data' ][ $j ][$i]['answer_id'] . "&bid=" . $subject_section_id . "'>第" . ($an) . "回<span class='f'>不合格</span></a>
                                                                                      </li>
                                                                                  ";
                                                                              }
                                                                          }
                                                                      }
                                              echo "
                                                                  </ul>
                                                              </div>
                                                              <div class='contents-position'>
                                                                  <div class='head'>カテゴリー・フォルダー</div>
                                                                  <div class='subject'>
                                                                      <ol>
                                                                         <li><span><img src='images/icon_subjectlist.png'></span>" . $subject_genre_name ."</li>
                                                                         <li>" . $subject_section_name ."</li>
                                                                      </ol>
                                                                  </div>
                                                                  <div class='contentsgroup'>
                                                                      <p><span><img src='images/icon_contentsgroup.png'>講義グループ</span></p>
                                                                  </div>
                                                              </div>
                                                              <div class='contents-relation'>
                                                                  <div class='head'>関連コンテンツ</div>
                                                                  <ul>
                                                                      <li>TB</li>
                                                                      <li>MP4</li>
                                                                      <li>テスト</li>
                                                                      <li>アンケート</li>
                                                                      <li>レポート</li>
                                                                  </ul>
                                                              </div>
                                                          </div>
                                                          <div class='body-right'>
                                                              <div class='other-detail'>
                                                                  <table>
                                                                      <tr>
                                                                          <th>受験状況</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['answer_count'] == 0) {
                                                                              echo "<td>未受験</td>";
                                                                          } else {
                                                                              echo "<td>受験済</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <tr>
                                                                          <th>最終受験日</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['answer_count'] == 0) {
                                                                              echo "<td>-</td>";
                                                                          } else {
                                                                              echo "<td>～～～</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <tr>
                                                                          <th>受験期限</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['start_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                              echo "<td>期限なし</td>";
                                                                          } else if($item[ 'child_data' ][ $j ]['start_day'] != '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] == '9999-12-31') {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['start_day'] . " ～ </td>";
                                                                          } else if($item[ 'child_data' ][ $j ]['start_day'] == '0000-01-01' && $item[ 'child_data' ][ $j ]['last_day'] != '9999-12-31') {
                                                                              echo "<td> ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                          } else {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['start_day'] . " ～ " . $item[ 'child_data' ][ $j ]['last_day'] . "</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <tr>
                                                                          <th>受験回数</th>
                                                                          <td>" . $item[ 'child_data' ][ $j ]['answer_count'] . "回</td>
                                                                      </tr>
                                                                          <th>回数制限</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['repeat_challenge'] == 0) {
                                                                              echo "<td>無制限</td>";
                                                                          } else {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['repeat_challenge'] . "回</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <!--
                                                                      <tr>
                                                                          <th>容量</th>
                                                                          <td>20MB</td>
                                                                      </tr>
                                                                      <tr class='message'>
                                                                          <th>投稿者</th>
                                                                          <td><div class='linkbtn'><a>KJS先生</a></div></td>
                                                                      </tr>
                                                                      <tr class='message'>
                                                                          <th>関連スレッド</th>
                                                                          <td>
                                                                              <div class='linkbtn'><a>スレッド01</a></div>
                                                                              <div class='linkbtn'><a>スレッド02</a></div>
                                                                              <div class='linkbtn'><a>スレッド03</a></div>
                                                                          </td>
                                                                      </tr>
                                                                      -->
                                                                      <tr>
                                                                          <th>合格点</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['qualifying_score'] == 0) {
                                                                              echo "<td>合格点無し</td>";
                                                                          } else {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['qualifying_score'] . "点</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                      <tr>
                                                                          <th>最高得点</th>
                                          ";
                                                                          if($item[ 'child_data' ][ $j ]['max_score'] == NULL) {
                                                                              echo "<td>未受験</td>";
                                                                          } else {
                                                                              echo "<td>" . $item[ 'child_data' ][ $j ]['max_score'] . "点</td>";
                                                                          }
                                          echo "
                                                                      </tr>
                                                                  </table>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          ";
                                          break;

                                        }
                                      }
                                    }
                                  echo "</div>";
                                  break;
                                    }

                                }
                            ?>
                        <?php endforeach; ?>
                    <?php } else { ?>
                    <?php
                        if($i == 0 && isset($_GET['bid'])) {
                            echo "
                                <div class='contents-item no-item'>
                                    <p>コンテンツはありません</p>
                                </div>
                            ";
                        } else if($i == 0 && !isset($_GET['bid'])) {
                            echo "
                                <div class='contents-item no-item'>
                                    <p>コンテンツグループを選択してください</p>
                                </div>
                            ";
                        }
                    ?>
                    <?php }; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<script type="text/javascript" src="https://tbwp3.kaizen2.net/scripts/tbwp3"></script>-->
<script type="text/javascript" src="js/contentslist.js"></script>

</body>
</html>
