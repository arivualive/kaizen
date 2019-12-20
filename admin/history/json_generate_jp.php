<?php
function TABLE_JSON($curl, $subject_genre_id, $school_id) {
	//$send_data['subject_genre_id'] = $subject_genre_id;
	$send_data['subject_genre_id'] = $subject_genre_id[1]; //第2カテゴリー追加で修正
	$send_data['school_id'] = $school_id;
	//$send_data['school_id'] = $_SESSION['auth']['school_id'];

	//$absolute = '/home/lms/web/'; //本番環境
	$absolute = '/home/kaizen/web/htdocs/';

	//$absolute = '/home/test-lms/web/'; //テスト環境
	//$absolute = '/home/nishioka/newlms/web/'; //ローカル環境

	//require_once('class.history_data_create.php'); //絶対パスに変更
	//require_once('../../class/StringEncrypt.php'); //絶対パスに変更
	require_once($absolute . 'admin/history/class.history_data_create.php');
	require_once($absolute . 'class/StringEncrypt.php');

	$history_data = new HistoryDataCreate();
	$stringEncrypt = new StringEncrypt();




//$path = '../../library/category/'; //絶対パスに変更
//$csvpath = $path. 'csv/'; //絶対パスに変更
$csvpath = $absolute . 'library/category/csv/';

if($subject_genre_id[0] == 1) { //第2カテゴリー追加で修正
	// 選択された科目の項目を取得
	if(empty($_POST['csvfile'])) { $_POST['csvfile'] = $csvpath . 'contents.csv'; }
	$subject_category = @file($_POST['csvfile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	if(!$subject_category) { $subject_category = array(); }

	mb_convert_variables('UTF-8', 'SJIS-win', $subject_category);
	unset($subject_category[0], $subject_category[1]);

	foreach($subject_category as $subject) {
	  $subject_section[] = explode(',', $subject);
	}

	foreach($subject_section as $key => $value) {
	  if($value[2] === $send_data['subject_genre_id']) { $select_section[] = $value; }
	}
} else {
	$select_section[] = $subject_genre_id; //第2カテゴリー追加で修正
}

/*
echo '<pre>';
print_r($select_section);
echo '</pre>';
*/

//unset($select_section);

$_POST['csvfile'] = $csvpath . 'users.csv';

// users.csv を配列にする
$users_category = @file($_POST['csvfile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$users_category) { $users_category = array(); }

mb_convert_variables('UTF-8', 'SJIS-win', $users_category);
unset($users_category[0], $users_category[1]);

foreach($users_category as $users) {
  $users_section[] = explode(',', $users);
}

// users.csv の項目の数分の空配列を作成
// 2019/6/03 count関数対策
$section_count = 0;
if(is_countable($users_section)){
	$section_count = count($users_section);
}
//$section_count = count($users_section);
//$basic_section = array_fill(0, $section_count, ''); *2019/05/29
$basic_section = array_fill(0, $section_count, []);
$s = $g =$c = $co = null; //Notice対策

foreach($users_section as $key => $section) {
  switch($section[0]) {
    case '1':
      $s++;
      //$users_section[$key]['attribute'] = 'school_name-' . $s;
      $users_section[$key]['attribute'] = 'sn-' . $s;
      break;
    case '2':
      $g++;
      //$users_section[$key]['attribute'] = 'grade-' . $g;
      $users_section[$key]['attribute'] = 'g-' . $g;
      break;
    case '3':
      $c++;
      //$users_section[$key]['attribute'] = 'classroom-' . $c;
      $users_section[$key]['attribute'] = 'cl-' . $c;
      break;
    case '4':
      $co++;
      //$users_section[$key]['attribute'] = 'course-' . $co;
      $users_section[$key]['attribute'] = 'cs-' . $co;
      break;
    default:
      $oh++;
      $users_section[$key]['attribute'] = 'others-' . $oh;
      break;
  }
}
// keyに応じて属性の名称を付ける
foreach($basic_section as $key => $number) {
  foreach($users_section as $key1 => $attribute) {
    if($key == $key1) {
      $basic_section[$key]['attribute'] = $attribute['attribute'];
      $basic_section[$key]['flg'] = '';
    }
  }
}

// users.csv をカテゴリー別に分ける
foreach($users_section as $section_attribute) {
  //$sc_count = count($section_count); *2019/05/29
  $sc_count = $section_count;
  for($i = 0; $i < $sc_count; ++$i) {
    $is = $i + 1;
    if($section_attribute[0] == $is) {
      $attribute_create[$i][] = $section_attribute;
    }
  }
}

////////////////////////////////////////////////////////////////////////////////

// DBからbit_subjectとstudent_idを取得
$bit_subject = [];
$db_data = array(
  'repository' => 'SubjectSectionRepository'
 ,'method' => 'getStudentBitSubject'
 ,'params' => $send_data
);

// student-bit-subject とusers.csvを照合
$bit_subject = $curl->send($db_data);
//$result = [];
$access_code = [];

if(empty($_POST['csvfile'])) { $_POST['csvfile'] = $csvpath . 'users.csv'; }
//require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php'); //絶対パスに変更
require_once($absolute . 'library/category/catecalc.php');

$tempo = 0; //Notice対策
foreach((array)$bit_subject as $key => $student) {
  $database = $student['bit_subject'];
  //debug_r($student);
  $access_code['student_data'][$key]['number'] = "";
  /*
  $access_code['student_data'][$key]['student_id'] = $student['student_id'];
  $access_code['student_data'][$key]['student_name'] = $student['student_name'];
  */
  $access_code['student_data'][$key]['sid'] = $student['sid'];
  $access_code['student_data'][$key]['sn'] = $student['sn'];

  $access_code['student_data'][$key]['bit_subject'] = $student['bit_subject'];
  $access_code['student_data'][$key]['id'] = mb_strcut($stringEncrypt->decrypt($student['id']),3);//$student['id'];

  if($database) { list($check) = string_to_array($database); }
  list($csvTree01,,, $csvRowD01) = load_csv($_POST['csvfile'], 1); //CSVファイルを処理

  $select = '';
  //$selectItem_t = ''; *2019/05/29
  $selectItem_t = [];

  for($current = count($csvTree01); $current >= 1; $current--) {
    foreach($csvTree01[$current] as $value) {
      //debug_r($value);
      foreach($value as $line) {
        //debug_r($line);
        //フォルダと項目を判別（真はフォルダ、否は項目）
        $order = !empty($csvTree01[$current + 1][$line]) ? 1 : 0; //Notice対策
        //（-）で value値を分割し一時保存の配列に格納
        $temp = explode('-', $line);
        //配列の底を2とする対数のキーが空でなければ $select に追加
        if(!empty($check[$temp[0]][log(hexdec($temp[1]), 2)])) { //Notice対策
          //選択された項目を生成（フォルダは上に、項目は下に追加）
          if($order) { $selectFolder_t[] = $line; }
            $select .= $csvRowD01[$line] . '（' . $line . '）' . "\n";
            $selectItem_t[] = $line;
        }
      }
    }
    //$select_section[] = $selectAll;
  }

  $selectData = '';
  $cnt = 0;
  foreach($csvRowD01 as  $key_n => $value) {
    /*if(search_value((array)$key, 0, $select01)) { $selectData .= $csvRowD[$key]; }
    $selectData .=  ',';*/
    //$item = explode(',', $line01);
    if(search_value((array)$key_n, 0, $select)) { $selectData .= $csvRowD[$key_n];
      $master[$cnt] = '1';
      //debug_r($selectData);
    }
    $selectData .=  ',';
    $cnt ++;
  }

  foreach($select_section as $key_1 => $first_data) {
    //debug_r($first_data);
    if(search_multi($csvpath . 'contents.csv', $first_data[1], $csvTree01, $select)) {
      $access_code['student_data'][$key]['select_item'] = $selectItem_t;
      $access_code['student_data'][$key]['csv_data'] = $selectData;
      $access_code['student_data'][$key]['attribute_key'] = explode(',', $selectData);

      $create = explode(',', $selectData);
      foreach($create as $c_key => $key_num) {
        if(!empty($key_num)) {
          foreach($basic_section as $key_b => $section_value) {
            if($c_key == $key_b) {
              if(empty($basic_section[$key_b]['flg'])) { $basic_section[$key_b]['flg'] = 1; }
            }
          }
        }
      }
      //$access_code['student_data'][$key]['master'] = $master;
      $select_section[$key_1]['student_data'][] = $access_code['student_data'][$key];
    }
    $tempo++;
  }
}
/*
debug_r($access_code['student_data']);
exit();
*/
// 生徒の所属情報のobject作成
foreach($select_section as $ss_key => $section) {
  if(isset($section['student_data'])) {
    foreach($section['student_data'] as $key => $attribute) {
      foreach($attribute['attribute_key'] as $key_atr => $atr_value) {
        $i = 0;
        foreach($basic_section as $key_s => $section_value) {
          if($section_value['flg'] == '1') {
            if($key_s == $key_atr) {
              if(empty($atr_value)) {
                $a_key = $section_value['attribute'];
                $attribute_object[$a_key] = "-";
                $attribute_csv_object[$i] = "-";
              } else {
                $a_key = $section_value['attribute'];
                $attribute_object[$a_key] = $atr_value;
                $attribute_csv_object[$i] = $atr_value;
              }
            }
            $i++;
          }
        }
      }
      unset($select_section[$ss_key]['student_data'][$key]['select_item']);
      unset($select_section[$ss_key]['student_data'][$key]['attribute_key']);
      $select_section[$ss_key]['student_data'][$key]['attribute_object'] = $attribute_object;
      //$select_section[$ss_key]['student_data'][$key] = $attribute_object;
      $select_section[$ss_key]['student_data'][$key]['attribute_csv_array'] = $attribute_csv_object;
    }
  }
}
/*
debug_r($select_section);
exit();
*/
// 各コンテンツの取得 2018/02/17
$data = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'findSubjectSectionContentsSearch_Type1'
    //, 'params' => $send_data
);

$student_table_contents = [];

foreach($select_section as $key => $qe_contents) {
  list($sels_count, $check) = array_to_string((array)$qe_contents[1]);
  //$sels_count = strpos($qe_contents[1], "x")+1;
  $send_data['subject_section_id'] = $sels_count;//;substr($qe_contents[1] , $sels_count);
  $data['method'] = 'findSubjectSectionContentsSearch_Type1';
  $data['params'] = $send_data;

  // 動画を取得
  $contents = $curl->send($data);
  //debug_r($send_data);

  $c_section = array('section' => $qe_contents[3]);

  if(empty($contents)) {
    $empty_contents = array('section' => $qe_contents[3], 'contents_id' => '-', 'contents_name' => '-');
    $select_section[$key]['contents'] = "contentsはありません。";
    $student_table_contents['contents'][][0] = $empty_contents;
  } else {
    foreach($contents as $ckey => $c_value) {
      $contents[$ckey]['section'] = $qe_contents[3];
    }
    $select_section[$key]['contents'] = $contents;
    $student_table_contents['contents'][] = $contents;
  }

  // テストを取得
  $data['method'] = 'findSubjectSectionTestSearch_Type1';
  $data['params'] = $send_data;
  $quiz = $curl->send($data);

  if(empty($quiz)) {
    $empty_quiz = array('section' => $qe_contents[3], 'quiz_id' => '-', 'title' => '-');
    $select_section[$key]['quiz'] = "quizはありません。";
    $student_table_contents['quiz'][][0] = $empty_quiz;
  } else {
    foreach($quiz as $qkey => $q_value) {
      $quiz[$qkey]['section'] = $qe_contents[3];
    }
    $select_section[$key]['quiz'] = $quiz;
    $student_table_contents['quiz'][] = $quiz;
  }

  // アンケート取得
  $send_data['type'] = 0;
  $data['method'] = 'findSubjectSectionQuestionnaireSearch_Type1';
  $data['params'] = $send_data;
  $questionnaire = $curl->send($data);
/*
  debug_r($questionnaire);
  exit();
*/
  if(empty($questionnaire)) {
    $empty_questionnaire = array('section' => $qe_contents[3], 'questionnaire_id' => '-', 'title' => '-');
    $select_section[$key]['questionnaire'] = "questionnaireはありません。";
    $student_table_contents['questionnaire'][][0] = $empty_questionnaire;
  } else {
    foreach($questionnaire as $qukey => $qu_value) {
      $questionnaire[$qukey]['section'] = $qe_contents[3];
    }

    $select_section[$key]['questionnaire'] = $questionnaire;
    $student_table_contents['questionnaire'][] = $questionnaire;
  }

  $send_data['type'] = 1;
  $data['params'] = $send_data;
  $report = $curl->send($data);

  if(empty($report)) {
    $empty_report = array('section' => $qe_contents[3], 'questionnaire_id' => '-', 'title' => '-');
    $select_section[$key]['report'] = "reportはありません。";
    $student_table_contents['report'][][0] = $empty_report;
  } else {
    foreach($report as $rkey => $r_value) {
      $report[$rkey]['section'] = $qe_contents[3];
    }

    $select_section[$key]['report'] = $report;
    $student_table_contents['report'][] = $report;
  }
}

$access_code = [];
$access_code = $select_section;
/*
debug_r($access_code);
exit();
*/
// contents 対象 student 結果を抽出 201802/17
$contents_result = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'contentsStudentResultSearch'
);

$access_code = $history_data->contents_students_result_Type1
  (
    $access_code,
    $contents_result,
    $curl
 );

// quiz 対象student 結果を抽出 20180217
$test_result = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'quizStudentResultSearch_Type1'
);

$junle_contents = 'quiz';

$access_code = $history_data->quiz_students_result_Type1
  (
    $access_code,
    $test_result,
    $curl,
    $junle_contents
 );

// アンケート結果を抽出 20180217
$questionnaire_result = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'questionnaireResultSearch_Type1'
);

$questionnaire_result['params']['questionnaire_id'] = "";
$questionnaire_result['params']['type'] = 0;
$junle_qeu_or_report = "questionnaire";

$access_code = $history_data->questionnaire_and_report_students_result
  (
    $access_code,
    $questionnaire_result,
    $curl,
    $junle_qeu_or_report
 );
/*
  debug_r($access_code);
  exit();
*/
// レポート結果を抽出 20180217
$report_result = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'questionnaireResultSearch_Type1'
);

$report_result['params']['type'] = 1;
$junle_qeu_or_report = "report";

$access_code = $history_data->questionnaire_and_report_students_result
  (
    $access_code,
    $report_result,
    $curl,
    $junle_qeu_or_report
 );
/*
debug_r($access_code);
exit();
*/
  // 不要になった配列を削除
	// 2019/6/03 count関数対策
	$section_count = 0;
	if(is_countable($access_code)){
		$section_count = count($access_code);
	}
  // = count($access_code);

  for($s = 0; $s < $section_count; ++$s) {
    for($si = 0; $si < count($access_code[$s]['student_data']); ++$si) {
      if(isset($access_code[$s]['student_data'][$si]['scf' . $s])) {
        unset($access_code[$s]['student_data'][$si]['scf' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sct' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sct' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sqzf' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sqzf' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sqzt' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sqzt' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sqef' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sqef' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sqet' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sqet' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sref' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sref' . $s]);
      }
      if(isset($access_code[$s]['student_data'][$si]['sret' . $s])) {
        unset($access_code[$s]['student_data'][$si]['sret' . $s]);
      }
    }
  }

///////// table用にデータ加工 20180217 ///////////////////////////////////////////////////////////////////
$students_object = [];
$after_cav_data = [];
/*
debug_r($access_code);
exit();
*/
foreach($access_code as $key => $student_data) {
  unset($access_code[$key][0]);
  unset($access_code[$key][1]);
  unset($access_code[$key][2]);
  unset($access_code[$key][4]);
  unset($access_code[$key]['master']);

  if(isset($access_code[$key]['student_data'])) {
    foreach($student_data['student_data'] as $key_1 => $value) {
      //debug_r($value);
      /*
      $after_cav_data[] = $value['student_id'];
      $after_cav_data[] = $value['student_name'];
      $after_cav_data[] = $value['csv_data'];
      $after_cav_data[] = $value['attribute_csv_array'];
      */
      unset($access_code[$key]['student_data'][$key_1]['csv_data']);
      unset($access_code[$key]['student_data'][$key_1]['attribute_csv_array']);
      //debug_r($value);
      $students_object[] = $access_code[$key]['student_data'][$key_1];
      // 重複している生徒をまとめる
      //$students_id[] = $value['student_id'];
      $students_id[] = $value['sid'];
    }
  }
}
// 重複生徒idを抽出
$student_results = [];
$student_results_a= [];

//debug_r($students_id);

$students_id = array_unique($students_id);
/*
debug_r($students_id);
exit();
*/
$num = 1;
foreach($students_id as $key1 => $id) {
  //$ids = 0;
  foreach($students_object as $key => $object) {
      //if($id === $object['student_id']) {
      if($id === $object['sid']) {
        if(!isset($students_id[$key1]['number'])) {
          $return_data['student_data'][$key1]['number'] = $num;
        }

        if(!isset($students_id[$key1]['id'])) {
          if(isset($object['id'])) {
            $return_data['student_data'][$key1]['id'] = $object['id'];
          }
        }
        /*
        if(!isset($students_id[$key1]['student_id'])) {
          if(isset($object['student_id'])) {
            $student_results[$key1]['student_id'] = $object['student_id'];
          }
        }
        */
        if(!isset($students_id[$key1]['sid'])) {
          if(isset($object['sid'])) {
            $return_data['student_data'][$key1]['sid'] = $object['sid'];
          }
        }
        /*
        if(!isset($students_id[$key1]['student_name'])) {
          $student_results[$key1]['student_name'] = $object['student_name'];
        }
        */
        if(!isset($students_id[$key1]['sn'])) {
          $return_data['student_data'][$key1]['sn'] = $object['sn'];
        }
      //  debug_r($object['attribute_object'] );

        if(!isset($students_id[$key1]['attribute_object'])) {
          //debug_r("1525");
          if(isset($object['attribute_object'])) {
            // csv-header用に保存
            $csv_header = $object['attribute_object'];

            foreach($object['attribute_object'] as $atr_key => $atr) {
              //debug_r("test");
              $return_data['student_data'][$key1][$atr_key] = $atr;
            }
          }
        }

        for($ss = 0; $ss < $section_count; ++$ss) {
          if(!isset($return_data['student_data'][$key1]['scr' . $ss])) {
            $return_data['student_data'][$key1]['scr' . $ss] = isset($object['scr' . $ss]) ? $object['scr' . $ss] : null; //Notice対策
          }
          if(!isset($return_data['student_data'][$key1]['sqzr' . $ss])) {
            $return_data['student_data'][$key1]['sqzr' . $ss] = isset($object['sqzr' . $ss]) ? $object['sqzr' . $ss] : null; //Notice対策
          }
          if(!isset($return_data['student_data'][$key1]['sqer' . $ss])) {
            $return_data['student_data'][$key1]['sqer' . $ss] = isset($object['sqer' . $ss]) ? $object['sqer' . $ss] : null; //Notice対策
          }
          if(!isset($return_data['student_data'][$key1]['srer' . $ss])) {
            $return_data['student_data'][$key1]['srer' . $ss] = isset($object['srer' . $ss]) ? $object['srer' . $ss] : null; //Notice対策
          }
        }
      }
  }
  $num++;
}

// 空の配列を埋める
foreach($return_data['student_data'] as $key => $first_data) {
  foreach($first_data as $key1 => $value) {
    if(empty($value)) {
      $return_data['student_data'][$key][$key1] = "-";
    }
  }
}

// table1- csv-student-data作成
foreach($return_data['student_data'] as $key => $value) {
  //if($key !== 'student_id') {
  if($key !== 'sid') {
    $result = $return_data['student_data'][$key];
    //unset($result['student_id']);
    unset($result['sid']);
    $return_data['csv_student_data'][] = array_values($result);
  }
}
/*
debug_r($students_csv_data);
exit();
*/
////////////////////////////////////////////////////////////////////////////////
// header-data作成 20180217
$table_header = [];
$hide_column = [];
// 2019/6/03 count関数対策
$student_count = 0;
if(is_countable($return_data['student_data'])){
	$student_count = count($return_data['student_data']);
}
//$student_count = count($return_data['student_data']);

foreach($return_data['student_data'] as $key => $data) {
  $sc = 0;
  $gr = 0;
  $cl = 0;
  $co = 0;
  $num_c = 0;

  foreach($data as $key_1 => $value) {
    if($key_1 === 'number') {
      $return_data['table_create'][0]['title'] = 'No.';
      $return_data['table_create'][0]['field'] = 'number';
      $return_data['table_create'][0]['width'] = 60;
      $return_data['table_create'][0]['headerFilter'] = 'input';
      $return_data['table_create'][0]['frozen'] = true;
      $return_data['table_create'][0]['sorter'] = 'number';
      $return_data['table_create'][0]['align'] = 'center';
    } else if($key_1 === 'id') {
      $return_data['table_create'][1]['title'] = '受講者ID';
      //$table_header[1]['field'] = "student_id";
      $return_data['table_create'][1]['field'] = 'id';
      $return_data['table_create'][1]['width'] = 100;
      $return_data['table_create'][1]['headerFilter'] = 'input';
      $return_data['table_create'][1]['frozen'] = true;
      //$table_header[1]['hideInHtml'] = true;
      $return_data['table_create'][1]['sorter'] = 'number';
      $return_data['table_create'][1]['align'] = 'left';
    } else if($key_1 === 'sn'/*'student_name'*/) {
      $return_data['table_create'][2]['title'] = '受講者名';
      //$table_header[2]['field'] = 'student_name';
      $return_data['table_create'][2]['field'] = 'sn';
      $return_data['table_create'][2]['width'] = 100;
      $return_data['table_create'][2]['headerFilter'] = 'input';
      $return_data['table_create'][2]['frozen'] = true;
      $return_data['table_create'][2]['align'] = 'left';
    } else if($key_1 === 'sid') {
      $return_data['table_create'][3]['title'] = 'student_id';
      $return_data['table_create'][3]['field'] = 'sid';
      $return_data['table_create'][3]['width'] = 60;
      $return_data['table_create'][3]['headerFilter'] = 'input';
      $return_data['table_create'][3]['frozen'] = true;
      $return_data['table_create'][3]['align'] = 'center';
      $return_data['table_create'][3]['visible'] = false;
    } else {
      $key_name = $key_1;
      //if(strpos($key_name,'-') !== false) { //by Choka
      if(strpos($key_name,'-') !== 0) {
        //'abcd'のなかに'bc'が含まれている場合
        //debug_r($key_name);
        $trimed = substr($key_name, 0, strcspn($key_name,'-'));
        //debug_r($trimed);
        $hide_column[$num_c] = $key_name;

        switch($trimed) {
          //case 'school_name':
          case 'sn':
          //debug_r($value);
            $sc++;
            $trimed_name = 'school_name' . $sc;
            //$return_data['table_create'][$num_c]['title'] = $trimed_name;
            $return_data['table_create'][$num_c]['title'] = '所属1';
            $return_data['table_create'][$num_c]['field'] = $key_name;
            $return_data['table_create'][$num_c]['width'] = 100;
            $return_data['table_create'][$num_c]['headerFilter'] = 'input';
            $return_data['table_create'][$num_c]['align'] = 'center';
            $return_data['table_create'][$num_c]['cssClass'] = 'attribute';
            $return_data['table_create'][$num_c]['visible'] = false;
            break;
          //case 'grade':
          case 'g':
            $gr++;
            $trimed_name = 'grade' . $gr;
            //$return_data['table_create'][$num_c]['title'] = $trimed_name;
            $return_data['table_create'][$num_c]['title'] = '所属2';
            $return_data['table_create'][$num_c]['field'] = $key_name;
            $return_data['table_create'][$num_c]['width'] = 100;
            $return_data['table_create'][$num_c]['headerFilter'] = 'input';
            $return_data['table_create'][$num_c]['align'] = 'center';
            $return_data['table_create'][$num_c]['cssClass'] = 'attribute';
            $return_data['table_create'][$num_c]['visible'] = false;
            break;
          //case 'classroom':
          case 'cl':
            $cl++;
            $trimed_name = 'classroom' . $cl;
            //$return_data['table_create'][$num_c]['title'] = $trimed_name;
            $return_data['table_create'][$num_c]['title'] = '所属3';
            $return_data['table_create'][$num_c]['field'] = $key_name;
            $return_data['table_create'][$num_c]['width'] = 100;
            $return_data['table_create'][$num_c]['headerFilter'] = 'input';
            $return_data['table_create'][$num_c]['align'] = 'center';
            $return_data['table_create'][$num_c]['cssClass'] = 'attribute';
            $return_data['table_create'][$num_c]['visible'] = false;
            break;
          //case 'course':
          case 'cs':
            $co++;
            $trimed_name = 'course' . $co;
            //$return_data['table_create'][$num_c]['title'] = $trimed_name;
            $return_data['table_create'][$num_c]['title'] = '所属4';
            $return_data['table_create'][$num_c]['field'] = $key_name;
            $return_data['table_create'][$num_c]['width'] = 100;
            $return_data['table_create'][$num_c]['headerFilter'] = 'input';
            $return_data['table_create'][$num_c]['align'] = 'center';
            $return_data['table_create'][$num_c]['cssClass'] = 'attribute';
            $return_data['table_create'][$num_c]['visible'] = false;
            break;
          default:
            # code...
            break;
        }
      }
    }
    $num_c++;
    //debug_r($key_1);
  }
}

$return_data['table_create'] = array_values($return_data['table_create']);
unset($return_data['table_create'][2]);
$return_data['table_create'] = array_values($return_data['table_create']);

// table1- csv-header-data作成
$return_data['csv_header'] = [];

foreach($return_data['table_create'] as $key => $value) {
  $return_data['csv_header'][] = $value['title'];
}

// 2019/6/03 count関数対策
$table_header_count = 0;
if(is_countable($return_data['table_create'])){
	$table_header_count = count($return_data['table_create']);
}

//$table_header_count = count($return_data['table_create']);
if($section_count > 0) {
  for($sc = 0; $sc < $section_count; ++$sc) {
    $return_data['table_create'][$table_header_count + $sc]['title'] = $access_code[$sc][3];
    $return_data['table_create'][$table_header_count + $sc]['columnVertAlign'] = 'bottom';
    //$return_data['table_create'][$table_header_count + $sc]['width'] = 160;

    $columns[0]['title'] = '動画';
    //$columns[0]['field'] = "subject_contents_result".$sc;
    $columns[0]['field'] = 'scr' . $sc;
    $columns[0]['align'] = 'center';
    //$columns[0]['sorter'] = 'number';
		$columns[0]['min-width'] = 90;
    $columns[0]['width'] = 130;

    $columns[1]['title'] = 'テスト';
    //$columns[1]['field'] = "subject_quiz_result".$sc;
    $columns[1]['field'] = 'sqzr' . $sc;
    $columns[1]['align'] = 'center';
		$columns[1]['min-width'] = 90;
    $columns[1]['width'] = 130;

    $columns[2]['title'] = 'アンケート';
    //$columns[2]['field'] = "subject_questionnaire_result".$sc;
    $columns[2]['field'] = 'sqer' . $sc;
    $columns[2]['align'] = 'center';
		$columns[2]['min-width'] = 130;
    $columns[2]['width'] = 180;

    $columns[3]['title'] = 'レポート';
    $columns[3]['align'] = 'center';
    //$columns[3]['field'] = "subject_report_result".$sc;
    $columns[3]['field'] = 'srer' . $sc;
    $columns[3]['min-width'] = 90;
		$columns[3]['width'] = 150;

    $return_data['table_create'][$table_header_count + $sc]['columns'] = $columns;

    $return_data['csv_header'][] = $access_code[$sc][3] . ' - 動画';
    $return_data['csv_header'][] = $access_code[$sc][3] . ' - テスト';
    $return_data['csv_header'][] = $access_code[$sc][3] . ' - アンケート';
    $return_data['csv_header'][] = $access_code[$sc][3] . ' - レポート';
  }
}

foreach($hide_column as $key => $value) {
  $hide_columns[] = $value;
}

$return_data['attribute'] = $hide_columns;

//更新時間を挿入
date_default_timezone_set('Asia/Tokyo');
$return_data['update_time'] = date('m/d G:i');

//検索結果をテキストに保存
//$jsonpath = 'json/'; //絶対パスに変更
$jsonpath = $absolute . 'admin/history/json/';
$testrr = [];
$testrr = array_values($return_data['student_data']);
$return_data['student_data'] = $testrr;

//$return_data をJSONに変換して保存
//$filename = $jsonpath . $subject_genre_id . '.json';
$filename = $jsonpath . $send_data['subject_genre_id'] . '.json'; //第2カテゴリー追加で修正
$json = json_encode($return_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
file_put_contents($filename, $json, LOCK_EX);
//chmod($filename, 0666); //Notice対策

//$access_code をJSONに変換して保存
//$filename = $jsonpath . $subject_genre_id . 'a.json';
$filename = $jsonpath . $send_data['subject_genre_id'] . 'a.json'; //第2カテゴリー追加で修正
$json = json_encode($access_code, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
file_put_contents($filename, $json, LOCK_EX);
//chmod($filename, 0666); //Notice対策

//$student_table_contents をJSONに変換して保存
//$filename = $jsonpath . $subject_genre_id . 't.json';
$filename = $jsonpath . $send_data['subject_genre_id'] . 't.json'; //第2カテゴリー追加で修正
$json = json_encode($student_table_contents, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
file_put_contents($filename, $json, LOCK_EX);
//chmod($filename, 0666); //Notice対策

//$return_data をCSVに変換して保存
$filename = $jsonpath . $send_data['subject_genre_id'] . '.csv';
DOWNLOAD_CSV($filename, $return_data['csv_student_data'], $return_data['csv_header']);








//第2テーブル処理
// 2019/6/03 count関数対策
$send_data['first_number'] = 0;
if(is_countable($return_data['csv_student_data'])){
	$send_data['first_number'] = count($return_data['csv_student_data']) ? 1 : 0;
}
//$send_data['first_number'] = count($return_data['csv_student_data']) ? 1 : 0;

// 2019/6/03 count関数対策
if(is_countable($return_data['csv_student_data'])){
	$send_data['second_number'] = count($return_data['csv_student_data']);
}
//$send_data['second_number'] = count($return_data['csv_student_data']);
$send_data['category'] = 0;

//$student_id = $send_data['student_id'];
$select_student_id= [];

foreach($return_data['student_data'] as $key => $data_1) {
  $s_number = $data_1['number'];

  for($n = $send_data['first_number']; $n <= $send_data['second_number']; ++$n) {
    if($s_number == $n) {
      //$select_student_id[] = $student_results[$key]['student_id'];
      $select_student_id[] = $return_data['student_data'][$key]['sid'];
    }
  }
}

foreach($select_student_id as $s_id => $students_id) {
  //debug_r($students_id);
  $student_results_data = [];

  foreach($access_code as $key => $data) {
    foreach($data['student_data'] as $key2 => $student) {
      //if($student['student_id'] === $students_id) {
      if($student['sid'] === $students_id) {
        //debug_r($student['student_id']);
        unset($student['bit_subject']);
        unset($student['attribute_object']);

        for($sc = 0; $sc < $section_count; ++$sc) {
          if(isset($student['scr' . $sc])) {
            unset($student['scr' . $sc]);
          }
          if(isset($student['sqzr' . $sc])) {
            unset($student['sqzr' . $sc]);
          }
          if(isset($student['sqer' . $sc])) {
            unset($student['sqer' . $sc]);
          }
          if(isset($student['srer' . $sc])) {
            unset($student['srer' . $sc]);
          }
        }
        $student_results_data[] = $student;
      }
    }
  }
  //debug_r($student_results_data);
  $student_name = '';
  //$student_name = $student_results_data[0]['student_name'];
  $student_name = $student_results_data[0]['sn'];

  $contents_result_data = [];
  $quiz_result_data = [];
  $questionnaire_result_data = [];
  $report_result_data = [];

  foreach($student_results_data as $key => $data_1) {
    for($sc = 0; $sc < $section_count; ++$sc) {
      if(isset($data_1['cr' . $sc])) {
        if($data_1['cr' . $sc] !== 'contentsなし') {
					// 2019/6/03 count関数対策
					$c_count = 0;
					if(is_countable($data_1['cr' . $sc])){
						$c_count = count($data_1['cr' . $sc]);
					}
          //$c_count = count($data_1['cr' . $sc]);

          for($c = 0; $c < $c_count; ++$c) {
            //if($data_1['cr' . $sc][$c] !== 'false') { //by Choka
            if($data_1['cr' . $sc][$c] !== 0) {
              $contents_result_data['cr' . $sc][$c] = $data_1['cr' . $sc][$c][0];
            } else {
              $contents_result_data['cr' . $sc][$c] = "-";
            }
          }
        } else {
          $contents_result_data['cr' . $sc][] = "-";
        }
      }

      if(isset($data_1['contents_proportion' . $sc])) {
        $contents_result_data['contents_proportion' . $sc] = $data_1['contents_proportion' . $sc];
      }

      if(isset($data_1['qzr' . $sc])) {
        if($data_1['qzr' . $sc] !== 'quizなし') {
					// 2019/6/03 count関数対策
					$q_count = 0;
					if(is_countable($data_1['qzr' . $sc])){
						$q_count = count($data_1['qzr' . $sc]);
					}
          //$q_count = count($data_1['qzr' . $sc]);

          for($q = 0; $q < $q_count; ++$q) {
            //if($data_1['qzr' . $sc][$q] !== 'false') { //by Choka
            if($data_1['qzr' . $sc][$q] !== 0) {
              $quiz_result_data['qzr' . $sc][$q] = $data_1['qzr' . $sc][$q][0];
            } else {
              $quiz_result_data['qzr' . $sc][$q] = "-";
            }
          }
        } else {
          $quiz_result_data['qzr' . $sc][] = "-";
        }
      }

      if(isset($data_1['qer' . $sc])) {
        $qu = 0; //Notice対策
        if($data_1['qer' . $sc] !== 'questionnaireなし') {
					// 2019/6/03 count関数対策
					$qu_count = 0;
					if(is_countable($data_1['qer' . $sc])){
						$qu_count = count($data_1['qer' . $sc]);
					}
          //$qu_count = count($data_1['qer' . $sc]);

          for($qu = 0; $qu < $qu_count; ++$qu) {
            //if($data_1['qer' . $sc][$qu] !== 'false') { //by Choka
            if($data_1['qer' . $sc][$qu] !== 0) {
              $questionnaire_result_data['qer' . $sc][$qu] = $data_1['qer' . $sc][$qu][0];
            } else {
              $questionnaire_result_data['qer' . $sc][$qu] = "-";
            }
          }
        } else {
          $questionnaire_result_data['qer' . $sc][$qu] = "-";
        }
      }

      if(isset($data_1['rer' . $sc])) {
        $re = 0; //Notice対策
        if($data_1['rer' . $sc] !== 'reportなし') {
					// 2019/6/03 count関数対策
					$re_count = 0;
					if(is_countable($data_1['rer' . $sc])){
						$re_count = count($data_1['rer' . $sc]);
					}
          //$re_count = count($data_1['rer' . $sc]);

          for($re = 0; $re < $re_count; ++$re) {
            //if($data_1['rer' . $sc][$re] !== 'false') { //by Choka
            if($data_1['rer' . $sc][$re] !== 0) {
              $report_result_data['rer' . $sc][$re] = $data_1['rer' . $sc][$re][0];
            } else {
              $report_result_data['rer' . $sc][$re] = "-";
            }
          }
        } else {
          $report_result_data['rer' . $sc][$re] = "-";
        }
      }
    }
  }

  foreach($student_table_contents as $key1 => $value1) {
    foreach($value1 as $key2 => $data) {
      foreach($data as $key3 => $data2) {
        if(isset($data2['bit_classroom'])) {
          unset($student_table_contents[$key1][$key2][$key3]['bit_classroom']);
        }
      }
    }
  }

  // 各コンテンツの結果を作成していく
  $student_contents_result      = [];
  $student_quiz_result          = [];
  $student_questionnaire_result = [];
  $student_report_result        = [];

  $contents_header = [];
  $sg = 0;
  for($sc = 0; $sc < $section_count; ++$sc) {
    // 各講座のコンテンツ数
		// 2019/6/03 count関数対策
		$contents_counts = 0;
		if(is_countable($student_table_contents['contents'][$sc])){
			$contents_counts = count($student_table_contents['contents'][$sc]);
		}
    //$contents_counts = count($student_table_contents['contents'][$sc]);
    // コンテンツの配列作成
    for($ss = 0; $ss < $contents_counts; ++$ss) {
      $student_contents_result[$sc][$ss]['id']      = $student_table_contents['contents'][$sc][$ss]['contents_id'];
      $student_contents_result[$sc][$ss]['section'] = $student_table_contents['contents'][$sc][$ss]['section'];
      $student_contents_result[$sc][$ss]['title']   = $student_table_contents['contents'][$sc][$ss]['contents_name'];

      $contents_null_data['history_id'] = "";
      $contents_null_data['section'] = "";
      $contents_null_data['title'] = "";

      for($scs = 0; $scs < $section_count; ++$scs) {
        $student_contents_result[$sc][$ss]['proportion' . $scs] = '';
        $student_contents_result[$sc][$ss]['play_start_datetime' . $scs] = '';

        $contents_null_data['proportion' . $scs] = '';
        $contents_null_data['play_start_datetime' . $scs] = '';
      }
    }
    // quiz 配列作成
		// 2019/6/03 count関数対策
		$quiz_counts = 0;
		if(is_countable($student_table_contents['quiz'][$sc])) {
			$quiz_counts = count($student_table_contents['quiz'][$sc]);
		}
    //$quiz_counts = count($student_table_contents['quiz'][$sc]);

    for($qs = 0; $qs < $quiz_counts; ++$qs) {
      $student_quiz_result[$sc][$qs]['id']        = $student_table_contents['quiz'][$sc][$qs]['quiz_id'];
      $student_quiz_result[$sc][$qs]['answer_id'] = isset($student_table_contents['quiz'][$sc][$qs]['quiz_answer_id']) ? $student_table_contents['quiz'][$sc][$qs]['quiz_answer_id'] : null; //Notice対策
      $student_quiz_result[$sc][$qs]['section']   = $student_table_contents['quiz'][$sc][$qs]['section'];
      $student_quiz_result[$sc][$qs]['title']     = $student_table_contents['quiz'][$sc][$qs]['title'];

      $quiz_null_data['id'] = '';
      $quiz_null_data['answer_id'] = '';
      $quiz_null_data['section'] = '';
      $quiz_null_data['title'] = '';

      for($sqs = 0; $sqs < $section_count; ++$sqs) {
        $student_quiz_result[$sc][$qs]['total_score' . $sqs] = '';
        $student_quiz_result[$sc][$qs]['register_datetime' . $sqs] = '';

        $quiz_null_data['total_score' . $sqs] = '';
        $quiz_null_data['register_datetime' . $sqs] = '';
      }
    }
    // questionnaire 配列作成
		// 2019/6/03 count関数対策
		$questionnaire_counts = 0;
		if(is_countable($student_table_contents['questionnaire'][$sc])){
			$questionnaire_counts = count($student_table_contents['questionnaire'][$sc]);
		}
    //$questionnaire_counts = count($student_table_contents['questionnaire'][$sc]);

    for($qe = 0; $qe < $questionnaire_counts; ++$qe) {
      $student_questionnaire_result[$sc][$qe]['id']      = $student_table_contents['questionnaire'][$sc][$qe]['questionnaire_id'];
      $student_questionnaire_result[$sc][$qe]['section'] = $student_table_contents['questionnaire'][$sc][$qe]['section'];
      $student_questionnaire_result[$sc][$qe]['title']   = $student_table_contents['questionnaire'][$sc][$qe]['title'];

      $questionnaire_null_data['id'] = '';
      $questionnaire_null_data['section'] = '';
      $questionnaire_null_data['title'] = '';

      for($qer = 0; $qer < $section_count; ++$qer) {
        $student_questionnaire_result[$sc][$qe]['result' . $qer] = '';
        $student_questionnaire_result[$sc][$qe]['answer_datetime' . $qer] = '';

        $questionnaire_null_data['result' . $qer] = '';
        $questionnaire_null_data['answer_datetime' . $qer] = '';
      }
    }
    // report 配列作成
		// 2019/6/03 count関数対策
		$report_counts = 0;
		if(is_countable($student_table_contents['report'][$sc])){
			$report_counts = count($student_table_contents['report'][$sc]);
		}
    //$report_counts = count($student_table_contents['report'][$sc]);

    for($re = 0; $re < $report_counts; ++$re) {
      $student_report_result[$sc][$re]['id']      = $student_table_contents['report'][$sc][$re]['questionnaire_id'];
      $student_report_result[$sc][$re]['section'] = $student_table_contents['report'][$sc][$re]['section'];
      $student_report_result[$sc][$re]['title']   = $student_table_contents['report'][$sc][$re]['title'];

      $report_null_data['id'] = '';
      $report_null_data['section'] = '';
      $report_null_data['title'] = '';

      for($res = 0; $res < $section_count; ++$res) {
        $student_report_result[$sc][$re]['result' . $res] = '';
        $student_report_result[$sc][$re]['answer_datetime' . $res] = '';

        $report_null_data['result' . $res] = '';
        $report_null_data['answer_datetime' . $res] = '';
      }
    }
  }

  // 結果のnullの部分に空配列を挿入 contents
  for($cs = 0; $cs < $section_count; ++$cs) {
		// 2019/6/03 count関数対策
		$crcount = 0;
		if(isset($contents_result_data['cr' . $cs]) && is_countable($contents_result_data['cr' . $cs])){
			$crcount = isset($contents_result_data['cr' . $cs]) ? count($contents_result_data['cr' . $cs]) : 0; //Notice対策
		}
    //$crcount = isset($contents_result_data['cr' . $cs]) ? count($contents_result_data['cr' . $cs]) : 0; //Notice対策

    for($rc = 0; $rc < $crcount; ++$rc) {
      $contents_result_data['cr' . $cs] = array_values($contents_result_data['cr' . $cs]);
      $c_judge = $contents_result_data['cr' . $cs][$rc];

      if($c_judge == "-") {
        $contents_result_data['cr' . $cs][$rc] = $contents_null_data;
      }
    }
  }

  // 結果のnullの部分に空配列を挿入 quiz
  for($rs = 0; $rs < $section_count; ++$rs) {
		// 2019/6/03 count関数対策
		$qccount = 0;
		if(isset($quiz_result_data['qzr' . $rs]) && is_countable($quiz_result_data['qzr' . $rs])){
			$qccount = isset($quiz_result_data['qzr' . $rs]) ? count($quiz_result_data['qzr' . $rs]) : 0; //Notice対策
		}
    //$qccount = isset($quiz_result_data['qzr' . $rs]) ? count($quiz_result_data['qzr' . $rs]) : 0; //Notice対策

    for($rq = 0; $rq < $qccount; ++$rq) {
      $quiz_result_data['qzr' . $rs] = array_values($quiz_result_data['qzr' . $rs]);
      $q_judge = $quiz_result_data['qzr' . $rs][$rq];

      if($q_judge == "-") {
        $quiz_result_data['qzr' . $rs][$rq] = $quiz_null_data;
      }
    }
  }

  // 結果のnullの部分に空配列を挿入 questionnaire
  for($qe = 0; $qe < $section_count; ++$qe) {
		// 2019/6/03 count関数対策
		$qeccount = 0;
		if(isset($questionnaire_result_data['qer' . $qe]) && is_countable($questionnaire_result_data['qer' . $qe])){
			$qeccount = isset($questionnaire_result_data['qer' . $qe]) ? count($questionnaire_result_data['qer' . $qe]) : 0; //Notice対策
		}
    //$qeccount = isset($questionnaire_result_data['qer' . $qe]) ? count($questionnaire_result_data['qer' . $qe]) : 0; //Notice対策

    for($qer = 0; $qer < $qeccount; ++$qer) {
      $questionnaire_result_data['qer' . $qe] = array_values($questionnaire_result_data['qer' . $qe]);
      $qe_judge = $questionnaire_result_data['qer' . $qe][$qer];

      if($qe_judge == "-") {
        $questionnaire_result_data['qer' . $qe][$qer] = $questionnaire_null_data;
      }
    }
  }

  // 結果のnullの部分に空配列を挿入 report
  for($re = 0; $re < $section_count; ++$re) {
		// 2019/6/03 count関数対策
		$qeccount = 0;
		if(isset($report_result_data['rer' . $re]) && is_countable($report_result_data['rer' . $re])){
			$qeccount = isset($report_result_data['rer' . $re]) ? count($report_result_data['rer' . $re]) : 0; //Notice対策
		}
    //$qeccount = isset($report_result_data['rer' . $re]) ? count($report_result_data['rer' . $re]) : 0; //Notice対策

    for($rer = 0; $rer < $qeccount; ++$rer) {
      $report_result_data['rer' . $re] = array_values($report_result_data['rer' . $re]);
      $qe_judge = $report_result_data['rer' . $re][$rer];

      if($qe_judge == "-") {
        $report_result_data['rer' . $re][$rer] = $report_null_data;
      }
    }
  }

  // 結果を入れていく
  // contents-data ここから /////////////////////////////////////////////////////////
    for($cr = 0; $cr < count($contents_result_data); ++$cr) {
      for($ss = 0; $ss < $section_count; ++$ss) {
        if(isset($contents_result_data['contents_proportion' . $ss])) {
          unset($contents_result_data['contents_proportion' . $ss]);
        }
      }
    }

    // all 以外
    $contents_table = [];
    $contents_table[0]['title'] = '講座';
    $contents_table[0]['field'] = 'section';
    $contents_table[0]['width'] = 180;
    $contents_table[0]['headerFilter'] = 'input';
    $contents_table[0]['frozen'] = true;
    $contents_table[0]['sorter'] = 'number';
    $contents_table[0]['value'] = 'id';
    $contents_table[0]['align'] = "center";

    $contents_table[1]['title'] = 'タイトル';
    $contents_table[1]['field'] = 'title';
    $contents_table[1]['width'] = 180;
    $contents_table[1]['headerFilter'] = 'input';
    $contents_table[1]['frozen'] = true;
    $contents_table[1]['sorter'] = 'number';
    $contents_table[1]['align'] = "center";
    $contents_table[1]['value'] = 'answer_id';

    // all
    $all_contents_table = [];
    $all_contents_table[0]['title'] = 'Name';
    $all_contents_table[0]['field'] = 'name';
    $all_contents_table[0]['width'] = 180;
    $all_contents_table[0]['headerFilter'] = 'input';
    $all_contents_table[0]['frozen'] = true;
    $all_contents_table[0]['sorter'] = 'number';
    $all_contents_table[0]['value'] = 'id';
    $all_contents_table[0]['align'] = "center";

    $all_contents_table[1]['title'] = 'contents';
    $all_contents_table[1]['field'] = 'type';
    $all_contents_table[1]['width'] = 180;
    $all_contents_table[1]['headerFilter'] = 'input';
    $all_contents_table[1]['frozen'] = true;
    $all_contents_table[1]['sorter'] = 'number';
    $all_contents_table[1]['align'] = "center";

    $all_contents_table[2]['title'] = '講座';
    $all_contents_table[2]['field'] = 'section';
    $all_contents_table[2]['width'] = 180;
    $all_contents_table[2]['headerFilter'] = 'input';
    $all_contents_table[2]['frozen'] = true;
    $all_contents_table[2]['sorter'] = 'number';
    $all_contents_table[2]['align'] = "center";

    $all_contents_table[3]['title'] = 'タイトル';
    $all_contents_table[3]['field'] = 'title';
    $all_contents_table[3]['width'] = 180;
    $all_contents_table[3]['headerFilter'] = 'input';
    $all_contents_table[3]['frozen'] = true;
    $all_contents_table[3]['sorter'] = 'number';
    $all_contents_table[3]['align'] = "center";

    $section = [];

    for($sec = 0; $sec < $section_count; ++$sec) {
      $section[] = $student_contents_result[$sec][0]['section'];
    }

    switch($send_data['category']) {
      case '0':
        $contents = 'all';

        $student_contents_result = $history_data->all_results_table_create(
          $student_contents_result, $contents_result_data,
          $student_quiz_result, $quiz_result_data,
          $student_questionnaire_result, $questionnaire_result_data,
          $student_report_result, $report_result_data,
          $section_count, $student_name
       );
        break;
      case '1':
        $contents = 'contents';
        $all = 0;
        // 履歴があるdataの挿入
        $student_contents_result = $history_data->student_conents_results_table(
          $student_contents_result, $contents_result_data, $contents, $all
       );
        // 判定が×のデータの挿入
        $student_contents_result = $history_data->student_conents_results_table_judge(
          $student_contents_result, $contents, $all
       );
        break;
      case '2':
        $contents = 'quiz';
        $all = 0;
        // 履歴があるdataの挿入
        $student_contents_result = $history_data->student_conents_results_table(
          $student_quiz_result, $quiz_result_data, $contents
       );
        // 判定が×のデータの挿入
        $student_contents_result = $history_data->student_conents_results_table_judge(
          $student_contents_result, $contents
       );
        break;
      case '3':
        $contents = 'questionnaire';
        $all = 0;
        // 履歴があるdataの挿入
        $student_contents_result = $history_data->student_conents_results_table(
          $student_questionnaire_result, $questionnaire_result_data, $contents
       );
        // 判定が×のデータの挿入
        $student_contents_result = $history_data->student_conents_results_table_judge(
          $student_contents_result, $contents
       );
        //debug_r($student_contents_result);
        break;
      case '4':
        $contents = 'report';
        $all = 0;

        $student_contents_result = $history_data->student_conents_results_table(
          $student_report_result, $report_result_data, $contents
       );
        // 判定が×のデータの挿入
        $student_contents_result = $history_data->student_conents_results_table_judge(
          $student_contents_result, $contents);
        break;
      default:
        # code...
        break;
    }

    // 空の配列に"-"を挿入
    if($contents !== 'all') {
      $student_contents_result = $history_data->null_data_insert($student_contents_result);
      // second_table_header 作成
      $contents_table = $history_data->second_table_header(
        $contents_table, $section_count, $student_contents_result, $contents, $section
     );
    } else {
      $contents_table = $history_data->second_table_header(
        $all_contents_table, $section_count, $student_contents_result, $contents, $section
     );
    }

    ////////////// csv_contents_table_header ///////////////////////////////////////.mozilla
    for($ch = 0; $ch < count($contents_table); ++$ch) {
      if($contents == 'all') {
        if($ch < 4) {
          $csv_contents_table_header[] = $contents_table[$ch]['title'];
        } else {
          //for($cc = 0; $cc < 2; $cc++) {
          $csv_contents_table_header[] = $contents_table[$ch]['title'] . "-" . $contents_table[$ch]['columns'][0]['title'];
          $csv_contents_table_header[] = $contents_table[$ch]['title'] . "-" . $contents_table[$ch]['columns'][1]['title'];
        }
      } else {
        if($ch == 0 || $ch == 1) {
          $csv_contents_table_header[] = $contents_table[$ch]['title'];
        } else {
          //for($cc = 0; $cc < 2; $cc++) {
          $csv_contents_table_header[] = $contents_table[$ch]['title'] . "-" . $contents_table[$ch]['columns'][0]['title'];
          $csv_contents_table_header[] = $contents_table[$ch]['title'] . "-" . $contents_table[$ch]['columns'][1]['title'];
        }
      }
    }

    // student_table_csv_data 作成//////////////////////////////////////////////
    $csv_student_contents_results = [];
    if($contents === 'all' || $contents === 'quiz') {
    //debug_r($student_contents_results);
    foreach((array)$student_contents_result as $key => $c_results) {
      foreach((array)$c_results as $key2 => $c_results2) {
        if($key2 !== 'id' && $key2 !== 'answer_id') {
          $csv_student_contents_results[$key][] = $c_results2;
        }
      }
    }
  } else {
    foreach((array)$student_contents_result as $key => $c_results) {
      //debug_r($student_contents_results);
      foreach($c_results as $key2 => $c_results2) {
        if($key2 !== 'id') {
          $csv_student_contents_results[$key][] = $c_results2;
        }
      }
    }
  }

  $table_data = [];
  $table_data['contents_table']   = $contents_table;
  $table_data['contents']         = $student_contents_result;
  $table_data['csv_header']       = $csv_contents_table_header;
  //$table_data['csv_student_data'] = $csv_student_contents_results;

  $all_csv_array[] = $csv_student_contents_results;
  //debug_r($csv_student_contents_results);

}// 最初のforeach

foreach((array)$all_csv_array as $key1 => $data1) {
  foreach($data1 as $key2 => $data2) {
    $csv_select_results[] = $data2;
  }
}

// 2019/6/03 count関数対策
$header_count = 0;
if(is_countable($csv_select_results[0])){
	$header_count = count($csv_select_results[0]);
}
//$header_count = count($csv_select_results[0]);

for($h = 0; $h < $header_count; ++$h) {
  $header[$h] = $table_data['csv_header'][$h];
}

$table_data['csv_header']       = $header;
$table_data['csv_student_data'] = $csv_select_results;

//$table_data をJSONに変換して保存
//$filename = $jsonpath . $subject_genre_id . 'd.json';
$filename = $jsonpath . $send_data['subject_genre_id'] . 'd.json'; //第2カテゴリー追加で修正
$json = json_encode($table_data, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
file_put_contents($filename, $json, LOCK_EX);
//chmod($filename, 0666); //Notice対策

//$table_data をCSVに変換して保存
$filename = $jsonpath . $send_data['subject_genre_id'] . 'd.csv';
DOWNLOAD_CSV($filename, $table_data['csv_student_data'], $table_data['csv_header']);

return array(number_format($cnt), number_format($tempo), number_format(memory_get_usage(true) / 1024));
}








function DOWNLOAD_CSV($filename, $csvdata, $csvheader) {
	//ヘッダー処理（配列を渡す）
	if($csvheader) { array_unshift($csvdata, $csvheader); }

	//PHPのテンポラリに読み書き準備
	$fp = fopen('php://temp', 'r+');
	//テンポラリにCSV形式で書き込む（2次元配列を渡す）
	foreach((array)$csvdata as $value) { fputcsv($fp, (array)$value); }

	//ファイルポインタを先頭に戻す
	rewind($fp);
	//ファイルポインタの現在位置から全てを読み込み文字列へ代入
	$csv = stream_get_contents($fp);
	//Shift_JISに変換
	//$csv = mb_convert_encoding($csv, 'SJIS', mb_internal_encoding());

	//BOM付きUTF-8ファイルにする
	$csv = pack('C*', 0xEF, 0xBB, 0xBF) . $csv;
	//ファイルポインタを閉じる
	fclose($fp);

	//CSVを保存
	file_put_contents($filename, $csv, LOCK_EX);
	//chmod($filename, 0666); //Notice対策

	//渡されたファイル名のパスを切り落とす
	//$filename=basename($filename);
	//ダウンロードヘッダ定義
	//header('Content-Disposition:attachment; filename="' . $filename . '"');
	//header('Content-Type:application/octet-stream');
	//header('Content-Length:' . strlen($csv));
	//echo $csv;
}
?>
