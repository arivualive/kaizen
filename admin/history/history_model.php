<?php
error_reporting(~E_NOTICE);
require_once "../../config.php";
$curl = new Curl( $url );


require_once 'class.history_data_create.php';
require_once '../../class/StringEncrypt.php';
$history_data = new HistoryDataCreate();
$stringEncrypt = new StringEncrypt();


$send_data[ 'subject_genre_id' ] = filter_input( INPUT_POST, 'genre_id' );
$send_data[ 'select_student' ]   = filter_input( INPUT_POST, 'select_student' );
$send_data[ 'table_type' ]       = filter_input( INPUT_POST, 'table_type' );
$send_data[ 'student_id' ]       = filter_input( INPUT_POST, 'student_id' );
$send_data[ 'first_number' ]     = filter_input( INPUT_POST, 'first_number' );
$send_data[ 'second_number' ]    = filter_input( INPUT_POST, 'second_number' );
$send_data[ 'school_id' ]        = filter_input( INPUT_POST, 'school_id' );


function debug_r( $data ) { echo "<pre>"; print_r( $data ); echo "</pre>"; }
function json_safe_encode( $data ){ return json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); }


/*
//第1テーブルの処理をファンクション化（外部ファイル）
require_once('json_generate.php');
//TABLE_JSON($curl, $send_data[ 'subject_genre_id' ]);
//CSVファイルを読み込み「UTF-8」に変換
$csvfile = '../../library/category/csv/contents.csv';
$lines = @file($csvfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if(!$lines) { $lines = array(); }
//mb_convert_variables('UTF-8', 'SJIS-win', $lines);
//第1カテゴリを抽出
foreach($lines as $line) {
	$item = explode(',', $line);
	if($item[0] == '1') { $genres[] = $item[1]; }
}
//JSONファイルを作成
foreach($genres as $genre) { TABLE_JSON($curl, $genre); }
//print_r($genres);
*/


//JSONファイルを配列に変換
$jsonpath = 'json/';
//$return_data に変換
$json = @file_get_contents($jsonpath . $send_data[ 'subject_genre_id' ] . '.json');
$return_data = json_decode($json, true);


//第2テーブルとCSV出力時に使用
if ( isset( $send_data[ 'first_number' ] ) && isset( $send_data[ 'second_number' ]) || isset( $send_data[ 'table_type' ] ) && isset( $send_data[ 'student_id' ]) ) {
	//$access_code に変換
	$access_code = json_decode(@file_get_contents($jsonpath . $send_data[ 'subject_genre_id' ] . 'a.json'), true);
	// 2019/5/31 count関数対策
	$section_count = 0;
	if(is_countable($access_code)){
		$section_count = count( $access_code );
	}
	//$section_count = count( $access_code );

	//$student_table_contents に変換
	$student_table_contents = json_decode(@file_get_contents($jsonpath . $send_data[ 'subject_genre_id' ] . 't.json'), true);
}

//////////////////////  table-1 ここまで ///////////////////////////////////////

// ここからstudent の個人結果テーブルの作成機構
if ( isset( $send_data[ 'table_type' ] ) && isset( $send_data[ 'student_id' ]) ) {

  $send_data[ 'category' ] = filter_input( INPUT_POST, 'category' );
  $student_id = $send_data[ 'student_id' ];

  $student_results_data = [];
  // 選択されたstudent情報を取得
  foreach ( $access_code as $key => $data ) {
    foreach ( $data[ 'student_data' ] as $key2 =>  $student ) {
      //if ( $student[ 'student_id' ] === $student_id ) {
      if ( $student[ 'sid' ] === $student_id ) {
        unset( $student[ 'bit_subject' ] );
        unset( $student[ 'attribute_object' ] );

        for ( $sc = 0; $sc < $section_count; ++$sc ) {
          if ( isset( $student[ 'scr'.$sc ]) ) {
            unset( $student[ 'scr'.$sc  ] );
          }

          if ( isset( $student[ 'sqzr'.$sc ]) ) {
            unset( $student[ 'sqzr'.$sc ] );
          }

          if ( isset( $student[ 'sqer'.$sc ]) ) {
            unset( $student[ 'sqer'.$sc ] );
          }

          if ( isset( $student[ 'srer'.$sc ]) ) {
            unset( $student[ 'srer'.$sc ] );
          }

        }
        $student_results_data[] = $student;
      }
    }
  }

  $student_name = '';
  //$student_name = $student_results_data[ 0 ][ 'student_name' ];
  $student_name = $student_results_data[ 0 ][ 'sn' ];

  $contents_result_data = [];
  $quiz_result_data = [];
  $questionnaire_result_data = [];
  $report_result_data = [];

  foreach ( $student_results_data as $key => $data_1 ) {

    for ( $sc = 0; $sc < $section_count; ++$sc ) {

      if ( isset( $data_1[ 'cr'.$sc ]) ) {
        if ( $data_1[ 'cr'.$sc ] !== 'no contents' ) {
					// 2019/5/31 count関数対策
					$c_count = 0;
					if(is_countable($data_1[ 'cr'.$sc ])){
						$c_count = count( $data_1[ 'cr'.$sc ] );
					}
          //$c_count = count( $data_1[ 'cr'.$sc ] );

          for( $c = 0; $c < $c_count; ++$c ) {
            //if ( $data_1[ 'cr'.$sc ][ $c ] !== 'false' ) { //by Choka
            if ( $data_1[ 'cr'.$sc ][ $c ] !== 0 ) {
              $contents_result_data[ 'cr'.$sc ][ $c ] = $data_1[ 'cr'.$sc ][ $c ][ 0 ];
            } else {
              $contents_result_data[ 'cr'.$sc ][ $c ] = "-";
            }

          }
        } else {
          $contents_result_data[ 'cr'.$sc ][ ] = "-";
        }

      }

      if ( isset( $data_1[ 'contents_proportion'.$sc ] )) {
        $contents_result_data[ 'contents_proportion'.$sc ] = $data_1[ 'contents_proportion'.$sc ];
      }

      if ( isset( $data_1[ 'qzr'.$sc ]) ) {
        if ( $data_1[ 'qzr'.$sc ] !== 'no quiz' ) {
					// 2019/5/31 count関数対策
					$q_count = 0;
					if(is_countable($data_1[ 'qzr'.$sc ])){
						$q_count = count( $data_1[ 'qzr'.$sc ] );
					}
          //$q_count = count( $data_1[ 'qzr'.$sc ] );

          for( $q = 0; $q < $q_count; ++$q ) {
            //if ( $data_1[ 'qzr'.$sc ][ $q ] !== 'false' ) { //by Choka
            if ( $data_1[ 'qzr'.$sc ][ $q ] !== 0 ) {
              $quiz_result_data[ 'qzr'.$sc ][ $q ] = $data_1[ 'qzr'.$sc ][ $q ][ 0 ];
            } else {
              $quiz_result_data[ 'qzr'.$sc ][ $q ] = "-";
            }
          }
        } else {
          $quiz_result_data[ 'qzr'.$sc ][] = "-";
        }

      }

      if ( isset( $data_1[ 'qer'.$sc ]) ) {
        if ( $data_1[ 'qer'.$sc ] !== 'no questionnaire' ) {
					// 2019/5/31 count関数対策
					$qu_count = 0;
					if(is_countable($data_1[ 'qer'.$sc ])){
						$qu_count = count( $data_1[ 'qer'.$sc ] );
					}
          //$qu_count = count( $data_1[ 'qer'.$sc ] );

          for( $qu = 0; $qu < $qu_count; ++$qu ) {
            //if ( $data_1[ 'qer'.$sc ][ $qu ] !== 'false' ) { //by Choka
            if ( $data_1[ 'qer'.$sc ][ $qu ] !== 0 ) {
              $questionnaire_result_data[ 'qer'.$sc ][ $qu ] = $data_1[ 'qer'.$sc ][ $qu ][ 0 ];
            } else {
              $questionnaire_result_data[ 'qer'.$sc ][ $qu ] = "-";
            }
          }
        } else {
          $questionnaire_result_data[ 'qer'.$sc ][ $qu ] = "-";
        }

      }

      if ( isset( $data_1[ 'rer'.$sc ]) ) {
        if ( $data_1[ 'rer'.$sc ] !== 'no report' ) {
					// 2019/5/31 count関数対策
					$re_count = 0;
					if(is_countable($data_1[ 'rer'.$sc ])){
						$re_count = count( $data_1[ 'rer'.$sc ] );
					}
          //$re_count = count( $data_1[ 'rer'.$sc ] );

          for( $re = 0; $re < $re_count; ++$re ) {
            //if ( $data_1[ 'rer'.$sc ][ $re ] !== 'false' ) { //by Choka
            if ( $data_1[ 'rer'.$sc ][ $re ] !== 0 ) {
              $report_result_data[ 'rer'.$sc ][ $re ] = $data_1[ 'rer'.$sc ][ $re ][ 0 ];
            } else {
              $report_result_data[ 'rer'.$sc ][ $re ] = "-";
            }
          }
        } else {
          $report_result_data[ 'rer'.$sc ][ $re ] = "-";
        }

      }

    }
  }

  foreach ( $student_table_contents as $key1 => $value1 ) {

    foreach ( $value1 as $key2 => $data ) {

      foreach ( $data as $key3 => $data2 ) {

        if ( isset( $data2[ 'bit_classroom' ]) ) {
          unset( $student_table_contents[ $key1 ][ $key2 ][ $key3 ][ 'bit_classroom'] );

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
  for ( $sc = 0; $sc < $section_count; ++$sc ) {
    // 各講座のコンテンツ数
		// 2019/5/31 count関数対策
		$contents_counts = 0;
		if(is_countable($student_table_contents[ 'contents' ][ $sc ])){
			$contents_counts = count( $student_table_contents[ 'contents' ][ $sc ] );
		}
    //$contents_counts = count( $student_table_contents[ 'contents' ][ $sc ] );
    // コンテンツの配列作成
    for ( $ss = 0; $ss < $contents_counts; ++$ss ) {

      $student_contents_result[ $sc ][ $ss ][ 'id' ]      = $student_table_contents[ 'contents' ][ $sc ][ $ss ][ 'contents_id' ];
      $student_contents_result[ $sc ][ $ss ][ 'section' ] = $student_table_contents[ 'contents' ][ $sc ][ $ss ][ 'section' ];
      $student_contents_result[ $sc ][ $ss ][ 'title' ]   = $student_table_contents[ 'contents' ][ $sc ][ $ss ][ 'contents_name' ];

      $contents_null_data[ 'history_id' ] = "";
      $contents_null_data[ 'section' ] = "";
      $contents_null_data[ 'title' ] = "";

      for ( $scs = 0; $scs < $section_count; ++$scs ) {

        $student_contents_result[ $sc ][ $ss ][ 'proportion'.$scs ] = '';
        $student_contents_result[ $sc ][ $ss ][ 'play_start_datetime'.$scs ] = '';

        $contents_null_data[ 'proportion'.$scs ] = '';
        $contents_null_data[ 'play_start_datetime'.$scs ] = '';

      }


    }
    // quiz 配列作成
		// 2019/5/31 count関数対策
		$quiz_counts = 0;
		if(is_countable($student_table_contents[ 'quiz' ][ $sc ])){
			$quiz_counts = count( $student_table_contents[ 'quiz' ][ $sc ] );
		}
    //$quiz_counts = count( $student_table_contents[ 'quiz' ][ $sc ] );

    for ( $qs = 0; $qs < $quiz_counts; ++$qs ) {

      $student_quiz_result[ $sc ][ $qs ][ 'id' ]        = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'quiz_id' ];
      $student_quiz_result[ $sc ][ $qs ][ 'answer_id' ] = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'quiz_answer_id' ];
      $student_quiz_result[ $sc ][ $qs ][ 'section' ]   = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'section' ];
      $student_quiz_result[ $sc ][ $qs ][ 'title' ]     = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'title' ];

      $quiz_null_data[ 'id' ] = '';
      $quiz_null_data[ 'answer_id' ] = '';
      $quiz_null_data[ 'section' ] = '';
      $quiz_null_data[ 'title' ] = '';

      for ( $sqs = 0; $sqs < $section_count; ++$sqs ) {
        $student_quiz_result[ $sc ][ $qs ][ 'total_score'.$sqs ] = '';
        $student_quiz_result[ $sc ][ $qs ][ 'register_datetime'.$sqs ] = '';

        $quiz_null_data[ 'total_score'.$sqs ] = '';
        $quiz_null_data[ 'register_datetime'.$sqs ] = '';
      }

    }
    // questionnaire 配列作成
		// 2019/6/03 count関数対策
		$questionnaire_counts = 0;
		if(is_countable($student_table_contents[ 'questionnaire' ][ $sc ])){
			$questionnaire_counts = count( $student_table_contents[ 'questionnaire' ][ $sc ] );
		}
    //$questionnaire_counts = count( $student_table_contents[ 'questionnaire' ][ $sc ] );

    for ( $qe = 0; $qe < $questionnaire_counts; ++$qe ) {

      $student_questionnaire_result[ $sc ][ $qe ][ 'id' ]      = $student_table_contents[ 'questionnaire' ][ $sc ][ $qe ][ 'questionnaire_id' ];
      $student_questionnaire_result[ $sc ][ $qe ][ 'section' ] = $student_table_contents[ 'questionnaire' ][ $sc ][ $qe ][ 'section' ];
      $student_questionnaire_result[ $sc ][ $qe ][ 'title' ]   = $student_table_contents[ 'questionnaire' ][ $sc ][ $qe ][ 'title' ];

      $questionnaire_null_data[ 'id' ] = '';
      $questionnaire_null_data[ 'section' ] = '';
      $questionnaire_null_data[ 'title' ] = '';

      for ( $qer = 0; $qer < $section_count; ++$qer ) {
        $student_questionnaire_result[ $sc ][ $qe ][ 'result'.$qer ] = '';
        $student_questionnaire_result[ $sc ][ $qe ][ 'answer_datetime'.$qer ] = '';

        $questionnaire_null_data[ 'result'.$qer ] = '';
        $questionnaire_null_data[ 'answer_datetime'.$qer ] = '';
      }
    }
    // report 配列作成
		// 2019/6/03 count関数対策
		$report_counts = 0;
		if(is_countable($student_table_contents[ 'report' ][ $sc ])){
			$report_counts = count( $student_table_contents[ 'report' ][ $sc ] );
		}
    //$report_counts = count( $student_table_contents[ 'report' ][ $sc ] );

    for ( $re = 0; $re < $report_counts; ++$re ) {

      $student_report_result[ $sc ][ $re ][ 'id' ]      = $student_table_contents[ 'report' ][ $sc ][ $re ][ 'questionnaire_id' ];
      $student_report_result[ $sc ][ $re ][ 'section' ] = $student_table_contents[ 'report' ][ $sc ][ $re ][ 'section' ];
      $student_report_result[ $sc ][ $re ][ 'title' ]   = $student_table_contents[ 'report' ][ $sc ][ $re ][ 'title' ];

      $report_null_data[ 'id' ] = '';
      $report_null_data[ 'section' ] = '';
      $report_null_data[ 'title' ] = '';

      for ( $res = 0; $res < $section_count; ++$res ) {
        $student_report_result[ $sc ][ $re ][ 'result'.$res ] = '';
        $student_report_result[ $sc ][ $re ][ 'answer_datetime'.$res ] = '';

        $report_null_data[ 'result'.$res ] = '';
        $report_null_data[ 'answer_datetime'.$res ] = '';
      }
    }

  }

  // 結果のnullの部分に空配列を挿入 contents
  for ( $cs = 0; $cs < $section_count; ++$cs ) {
		// 2019/6/03 count関数対策
		$crcount = 0;
		if(is_countable($contents_result_data[ 'cr'.$cs ])){
			$crcount = count( $contents_result_data[ 'cr'.$cs ] );
		}
    //$crcount = count( $contents_result_data[ 'cr'.$cs ] );

    for ( $rc = 0; $rc < $crcount; ++$rc ) {

      $contents_result_data[ 'cr'.$cs ] =
        array_values($contents_result_data[ 'cr'.$cs ]);

      $c_judge = $contents_result_data[ 'cr'.$cs ][ $rc ];

      if ( $c_judge == "-" ) {
        $contents_result_data[ 'cr'.$cs ][ $rc ] = $contents_null_data;
      }
    }
  }

  // 結果のnullの部分に空配列を挿入 quiz
  for ( $rs = 0; $rs < $section_count; ++$rs ) {
		// 2019/6/03 count関数対策
		$qccount = 0;
		if(is_countable($quiz_result_data[ 'qzr'.$rs ])){
			$qccount = count( $quiz_result_data[ 'qzr'.$rs ] );
		}
    //$qccount = count( $quiz_result_data[ 'qzr'.$rs ] );

    for ( $rq = 0; $rq < $qccount; ++$rq ) {

      $quiz_result_data[ 'qzr'.$rs ] =
        array_values($quiz_result_data[ 'qzr'.$rs  ]);

      $q_judge = $quiz_result_data[ 'qzr'.$rs ][ $rq ];

      if ( $q_judge == "-" ) {
        $quiz_result_data[ 'qzr'.$rs ][ $rq ] = $quiz_null_data;
      }
    }
  }

  // 結果のnullの部分に空配列を挿入 questionnaire
  for ( $qe = 0; $qe < $section_count; ++$qe ) {
		// 2019/6/03 count関数対策
		$qeccount = 0;
		if(is_countable($questionnaire_result_data[ 'qer'.$qe ])){
			$qeccount = count( $questionnaire_result_data[ 'qer'.$qe ] );
		}
    //$qeccount = count( $questionnaire_result_data[ 'qer'.$qe ] );

    for ( $qer = 0; $qer < $qeccount; ++$qer ) {

      $questionnaire_result_data[ 'qer'.$qe ] =
        array_values($questionnaire_result_data[ 'qer'.$qe ]);

      $qe_judge = $questionnaire_result_data[ 'qer'.$qe ][ $qer ];

      if ( $qe_judge == "-" ) {
        $questionnaire_result_data[ 'qer'.$qe ][ $qer ] = $questionnaire_null_data;
      }
    }
  }


  // 結果のnullの部分に空配列を挿入 report
  for ( $re = 0; $re < $section_count; ++$re ) {
		// 2019/6/03 count関数対策
		$qeccount = 0;
		if(is_countable($report_result_data[ 'rer'.$re ])){
			$qeccount = count( $report_result_data[ 'rer'.$re ] );
		}
    //$qeccount = count( $report_result_data[ 'rer'.$re ] );

    for ( $rer = 0; $rer < $qeccount; ++$rer ) {

      $report_result_data[ 'rer'.$re ] =
        array_values($report_result_data[ 'rer'.$re ]);

      $qe_judge = $report_result_data[ 'rer'.$re ][ $rer ];

      if ( $qe_judge == "-" ) {
        $report_result_data[ 'rer'.$re ][ $rer ] = $report_null_data;
      }
    }
  }


// 結果を入れていく
// contents-data ここから /////////////////////////////////////////////////////////
  for ( $cr = 0; $cr < count( $contents_result_data ); ++$cr ) {

    for ( $ss = 0; $ss < $section_count; ++$ss ) {

      if ( isset( $contents_result_data[ 'contents_proportion'.$ss ] )) {
        unset( $contents_result_data[ 'contents_proportion'.$ss ] );
      }
    }
  }
	/*
	debug_r($contents_result_data);
	exit();
	*/
  // all 以外
  $contents_table = [];
  $contents_table[ 0 ][ 'title' ] = 'Course';
  $contents_table[ 0 ][ 'field' ] = 'section';
  $contents_table[ 0 ][ 'width' ] = 100;
  $contents_table[ 0 ][ 'headerFilter' ] = 'input';
  $contents_table[ 0 ][ 'frozen' ] = true;
  $contents_table[ 0 ][ 'sorter' ] = 'number';
  $contents_table[ 0 ][ 'value' ] = 'id';
  $contents_table[ 0 ][ 'align' ] = "center";

  $contents_table[ 1 ][ 'title' ] = 'Title';
  $contents_table[ 1 ][ 'field' ] = 'title';
  $contents_table[ 1 ][ 'width' ] = 100;
  $contents_table[ 1 ][ 'headerFilter' ] = 'input';
  $contents_table[ 1 ][ 'frozen' ] = true;
  $contents_table[ 1 ][ 'sorter' ] = 'number';
  $contents_table[ 1 ][ 'align' ] = "center";
  $contents_table[ 1 ][ 'value' ] = 'answer_id';

  // all
  $all_contents_table = [];
  $all_contents_table[ 0 ][ 'title' ] = 'Student name';
  $all_contents_table[ 0 ][ 'field' ] = 'name';
  $all_contents_table[ 0 ][ 'width' ] = 100;
  $all_contents_table[ 0 ][ 'headerFilter' ] = 'input';
  $all_contents_table[ 0 ][ 'frozen' ] = true;
  $all_contents_table[ 0 ][ 'sorter' ] = 'number';
  $all_contents_table[ 0 ][ 'value' ] = 'id';
  $all_contents_table[ 0 ][ 'align' ] = "center";

  $all_contents_table[ 1 ][ 'title' ] = 'Content';
  $all_contents_table[ 1 ][ 'field' ] = 'type';
  $all_contents_table[ 1 ][ 'width' ] = 100;
  $all_contents_table[ 1 ][ 'headerFilter' ] = 'input';
  $all_contents_table[ 1 ][ 'frozen' ] = true;
  $all_contents_table[ 1 ][ 'sorter' ] = 'number';
  $all_contents_table[ 1 ][ 'align' ] = "center";

  $all_contents_table[ 2 ][ 'title' ] = 'Course';
  $all_contents_table[ 2 ][ 'field' ] = 'section';
  $all_contents_table[ 2 ][ 'width' ] = 100;
  $all_contents_table[ 2 ][ 'headerFilter' ] = 'input';
  $all_contents_table[ 2 ][ 'frozen' ] = true;
  $all_contents_table[ 2 ][ 'sorter' ] = 'number';
  $all_contents_table[ 2 ][ 'align' ] = "center";

  $all_contents_table[ 3 ][ 'title' ] = 'Title';
  $all_contents_table[ 3 ][ 'field' ] = 'title';
  $all_contents_table[ 3 ][ 'width' ] = 180;
  $all_contents_table[ 3 ][ 'headerFilter' ] = 'input';
  $all_contents_table[ 3 ][ 'frozen' ] = true;
  $all_contents_table[ 3 ][ 'sorter' ] = 'number';
  $all_contents_table[ 3 ][ 'align' ] = "center";

  $section = [];

  for ( $sec = 0; $sec < $section_count; ++$sec ) {

    $section[] = $student_contents_result[ $sec ][ 0 ][ 'section' ];

  }

  switch ( $send_data[ 'category' ] ) {
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
      $student_contents_result = $history_data->student_conents_results_table (
			$student_contents_result, $contents_result_data, $contents/*, $all */);

      // 判定が×のデータの挿入
      $student_contents_result = $history_data->student_conents_results_table_judge (
        $student_contents_result, $contents, $all );
      break;

    case '2':
      $contents = 'quiz';
      $all = 0;
      // 履歴があるdataの挿入
      $student_contents_result = $history_data->student_conents_results_table (
        $student_quiz_result, $quiz_result_data, $contents );
      // 判定が×のデータの挿入
      $student_contents_result = $history_data->student_conents_results_table_judge (
        $student_contents_result, $contents );
      break;


    case '3':
      $contents = 'questionnaire';
      $all = 0;
      // 履歴があるdataの挿入
      $student_contents_result = $history_data->student_conents_results_table (
        $student_questionnaire_result, $questionnaire_result_data, $contents );
      /*  debug_r( $student_contents_result );
        exit();*/
      // 判定が×のデータの挿入
      $student_contents_result = $history_data->student_conents_results_table_judge (
        $student_contents_result, $contents );
        //debug_r( $student_contents_result );
      /*  debug_r( $contents );
        exit();*/
      break;

    case '4':
      $contents = 'report';
      $all = 0;

      $student_contents_result = $history_data->student_conents_results_table (
        $student_report_result, $report_result_data, $contents );
      // 判定が×のデータの挿入
      $student_contents_result = $history_data->student_conents_results_table_judge (
        $student_contents_result, $contents );
      break;

    default:
      # code...
      break;
  }

  // 空の配列に"-"を挿入
  if ( $contents !== 'all' ) {
    $student_contents_result = $history_data->null_data_insert( $student_contents_result );
    // second_table_header 作成
    $contents_table = $history_data->second_table_header(
      $contents_table, $section_count, $student_contents_result, $contents, $section );

  } else {
    $contents_table = $history_data->second_table_header(
      $all_contents_table, $section_count, $student_contents_result, $contents, $section );
  }

// second_table_header 作成
////////////// csv_contents_table_header ///////////////////////////////////////.mozilla
  for ( $ch = 0; $ch < count( $contents_table); ++$ch ) {

    if ( $contents == 'all' ) {

      if ( $ch < 4 ) {
        $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ];
      } else {
        //for ( $cc = 0; $cc < 2; $cc++ ) {
        $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 0 ][ 'title' ];
        $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 1 ][ 'title' ];

      }

    } else {

        if ( $ch == 0 || $ch == 1 ) {
          $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ];
        } else {
          //for ( $cc = 0; $cc < 2; $cc++ ) {
          $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 0 ][ 'title' ];
          $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 1 ][ 'title' ];

        }
    }

  }

// student_table_csv_data 作成//////////////////////////////////////////////
  $csv_student_contents_results = [];
  if ( $contents === 'all' || $contents === 'quiz' ) {
  //debug_r( $student_contents_results );
    foreach ( (array)$student_contents_result as $key => $c_results ) {
      foreach ( (array)$c_results as $key2 => $c_results2 ) {
        if ( $key2 !== 'id' && $key2 !== 'answer_id' ) {
          $csv_student_contents_results[ $key ][] = $c_results2;
        }
      }
    }

  } else {

    foreach ( (array)$student_contents_result as $key => $c_results ) {
      //debug_r( $student_contents_results );
      foreach ( $c_results as $key2 => $c_results2 ) {
        if ( $key2 !== 'id' ) {
          $csv_student_contents_results[ $key ][] = $c_results2;
        }
      }
    }
  }

  $table_data = [];
  $table_data[ 'contents_table' ]   = $contents_table;
  $table_data[ 'contents' ]         = $student_contents_result;
  $table_data[ 'csv_header' ]       = $csv_contents_table_header;
  $table_data[ 'csv_student_data' ] = $csv_student_contents_results;

////////////// 下の配列を使う ///////////////////////////////////////
//debug_r( $contents_table ); // header
//debug_r( $student_contents_result ); // result
//debug_r( $student_contents_results ); // student-data これを利用
//debug_r( $csv_contents_table_header ); // csv-header
//debug_r( $csv_student_contents_results ); // csv-student-results
  echo json_safe_encode( $table_data );
//////////////  2nd table ここまで /////////////////////////////////////////////
  exit();
//////////////// 第2テーブルここまで /////////////////////////////////////////////

}

//////////// 2nd table 総合での番号指定csv抽出 ここから /////////////////////////
if ( isset( $send_data[ 'first_number' ] ) && isset( $send_data[ 'second_number' ]) ) {

  $send_data[ 'category' ] = filter_input( INPUT_POST, 'category' );
  //$student_id = $send_data[ 'student_id' ];
  $select_student_id= [];

  foreach ( $return_data[ 'student_data' ] as $key => $data_1 ) {
    $s_number = $data_1[ 'number' ];

    for ( $n = $send_data[ 'first_number' ]; $n <= $send_data[ 'second_number' ]; ++$n ) {
      if ( $s_number == $n ) {

        //$select_student_id[] = $student_results[ $key ][ 'student_id' ];
        $select_student_id[] = $return_data[ 'student_data' ][ $key ][ 'sid' ];
      }
    }
  }

  foreach ( $select_student_id as $s_id => $students_id ) {
    //debug_r( $students_id );
    $student_results_data = [];

    foreach ( $access_code as $key => $data ) {
      foreach ( $data[ 'student_data' ] as $key2 => $student ) {
        //if ( $student[ 'student_id' ] === $students_id ) {
        if ( $student[ 'sid' ] === $students_id ) {
          //debug_r( $student[ 'student_id' ] );
          unset( $student[ 'bit_subject' ] );
          unset( $student[ 'attribute_object' ] );

          for ( $sc = 0; $sc < $section_count; ++$sc ) {
            if ( isset( $student[ 'scr'.$sc ]) ) {
              unset( $student[ 'scr'.$sc  ] );
            }

            if ( isset( $student[ 'sqzr'.$sc ]) ) {
              unset( $student[ 'sqzr'.$sc ] );
            }

            if ( isset( $student[ 'sqer'.$sc ]) ) {
              unset( $student[ 'sqer'.$sc ] );
            }

            if ( isset( $student[ 'srer'.$sc ]) ) {
              unset( $student[ 'srer'.$sc ] );
            }

          }
          $student_results_data[] = $student;

        }
      }
    }
  //  debug_r($student_results_data);
    $student_name = '';
    //$student_name = $student_results_data[ 0 ][ 'student_name' ];
    $student_name = $student_results_data[ 0 ][ 'sn' ];

    $contents_result_data = [];
    $quiz_result_data = [];
    $questionnaire_result_data = [];
    $report_result_data = [];

    foreach ( $student_results_data as $key => $data_1 ) {

      for ( $sc = 0; $sc < $section_count; ++$sc ) {

        if ( isset( $data_1[ 'cr'.$sc ]) ) {
          if ( $data_1[ 'cr'.$sc ] !== 'no contents' ) {
						// 2019/6/03 count関数対策
						$c_count = 0;
						if(is_countable($data_1[ 'cr'.$sc ])){
							$c_count = count( $data_1[ 'cr'.$sc ] );
						}
            //$c_count = count( $data_1[ 'cr'.$sc ] );

            for( $c = 0; $c < $c_count; ++$c ) {
              //if ( $data_1[ 'cr'.$sc ][ $c ] !== 'false' ) { //by Choka
              if ( $data_1[ 'cr'.$sc ][ $c ] !== 0 ) {
                $contents_result_data[ 'cr'.$sc ][ $c ] = $data_1[ 'cr'.$sc ][ $c ][ 0 ];
              } else {
                $contents_result_data[ 'cr'.$sc ][ $c ] = "-";
              }

            }
          } else {
            $contents_result_data[ 'cr'.$sc ][ ] = "-";
          }

        }

        if ( isset( $data_1[ 'contents_proportion'.$sc ] )) {
          $contents_result_data[ 'contents_proportion'.$sc ] = $data_1[ 'contents_proportion'.$sc ];
        }

        if ( isset( $data_1[ 'qzr'.$sc ]) ) {
          if ( $data_1[ 'qzr'.$sc ] !== 'no quiz' ) {
						// 2019/6/03 count関数対策
						$q_count = 0;
						if(is_countable($data_1[ 'qzr'.$sc ])){
							$q_count = count( $data_1[ 'qzr'.$sc ] );
						}
            //$q_count = count( $data_1[ 'qzr'.$sc ] );

            for( $q = 0; $q < $q_count; ++$q ) {
              //if ( $data_1[ 'qzr'.$sc ][ $q ] !== 'false' ) { //by Choka
              if ( $data_1[ 'qzr'.$sc ][ $q ] !== 0 ) {
                $quiz_result_data[ 'qzr'.$sc ][ $q ] = $data_1[ 'qzr'.$sc ][ $q ][ 0 ];
              } else {
                $quiz_result_data[ 'qzr'.$sc ][ $q ] = "-";
              }
            }
          } else {
            $quiz_result_data[ 'qzr'.$sc ][] = "-";
          }

        }

        if ( isset( $data_1[ 'qer'.$sc ]) ) {
          if ( $data_1[ 'qer'.$sc ] !== 'no questionnaire' ) {
						// 2019/6/03 count関数対策
						$qu_count = 0;
						if(is_countable($data_1[ 'qer'.$sc ])){
							$qu_count = count( $data_1[ 'qer'.$sc ] );
						}
            //$qu_count = count( $data_1[ 'qer'.$sc ] );

            for( $qu = 0; $qu < $qu_count; ++$qu ) {
              //if ( $data_1[ 'qer'.$sc ][ $qu ] !== 'false' ) { //by Choka
              if ( $data_1[ 'qer'.$sc ][ $qu ] !== 0 ) {
                $questionnaire_result_data[ 'qer'.$sc ][ $qu ] = $data_1[ 'qer'.$sc ][ $qu ][ 0 ];
              } else {
                $questionnaire_result_data[ 'qer'.$sc ][ $qu ] = "-";
              }
            }
          } else {
            $questionnaire_result_data[ 'qer'.$sc ][ $qu ] = "-";
          }

        }

        if ( isset( $data_1[ 'rer'.$sc ]) ) {
          if ( $data_1[ 'rer'.$sc ] !== 'no report' ) {
						// 2019/6/03 count関数対策
						$re_count = 0;
						if(is_countable($data_1[ 'rer'.$sc ])){
							$re_count = count( $data_1[ 'rer'.$sc ] );
						}
            //$re_count = count( $data_1[ 'rer'.$sc ] );

            for( $re = 0; $re < $re_count; ++$re ) {
              //if ( $data_1[ 'rer'.$sc ][ $re ] !== 'false' ) { //by Choka
              if ( $data_1[ 'rer'.$sc ][ $re ] !== 0 ) {
                $report_result_data[ 'rer'.$sc ][ $re ] = $data_1[ 'rer'.$sc ][ $re ][ 0 ];
              } else {
                $report_result_data[ 'rer'.$sc ][ $re ] = "-";
              }
            }
          } else {
            $report_result_data[ 'rer'.$sc ][ $re ] = "-";
          }

        }

      }
    }

    foreach ( $student_table_contents as $key1 => $value1 ) {

      foreach ( $value1 as $key2 => $data ) {

        foreach ( $data as $key3 => $data2 ) {

          if ( isset( $data2[ 'bit_classroom' ]) ) {
            unset( $student_table_contents[ $key1 ][ $key2 ][ $key3 ][ 'bit_classroom'] );

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
    for ( $sc = 0; $sc < $section_count; ++$sc ) {
      // 各講座のコンテンツ数
			// 2019/6/03 count関数対策
			$contents_counts = 0;
			if(is_countable($student_table_contents[ 'contents' ][ $sc ])){
				$contents_counts = count( $student_table_contents[ 'contents' ][ $sc ] );
			}
      //$contents_counts = count( $student_table_contents[ 'contents' ][ $sc ] );
      // コンテンツの配列作成
      for ( $ss = 0; $ss < $contents_counts; ++$ss ) {

        $student_contents_result[ $sc ][ $ss ][ 'id' ]      = $student_table_contents[ 'contents' ][ $sc ][ $ss ][ 'contents_id' ];
        $student_contents_result[ $sc ][ $ss ][ 'section' ] = $student_table_contents[ 'contents' ][ $sc ][ $ss ][ 'section' ];
        $student_contents_result[ $sc ][ $ss ][ 'title' ]   = $student_table_contents[ 'contents' ][ $sc ][ $ss ][ 'contents_name' ];

        $contents_null_data[ 'history_id' ] = "";
        $contents_null_data[ 'section' ] = "";
        $contents_null_data[ 'title' ] = "";

        for ( $scs = 0; $scs < $section_count; ++$scs ) {

          $student_contents_result[ $sc ][ $ss ][ 'proportion'.$scs ] = '';
          $student_contents_result[ $sc ][ $ss ][ 'play_start_datetime'.$scs ] = '';

          $contents_null_data[ 'proportion'.$scs ] = '';
          $contents_null_data[ 'play_start_datetime'.$scs ] = '';

        }


      }
      // quiz 配列作成
			// 2019/6/03 count関数対策
			$quiz_counts = 0;
			if(is_countable($student_table_contents[ 'quiz' ][ $sc ])){
				$quiz_counts = count( $student_table_contents[ 'quiz' ][ $sc ] );
			}
      //$quiz_counts = count( $student_table_contents[ 'quiz' ][ $sc ] );

      for ( $qs = 0; $qs < $quiz_counts; ++$qs ) {

        $student_quiz_result[ $sc ][ $qs ][ 'id' ]        = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'quiz_id' ];
        $student_quiz_result[ $sc ][ $qs ][ 'answer_id' ] = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'quiz_answer_id' ];
        $student_quiz_result[ $sc ][ $qs ][ 'section' ]   = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'section' ];
        $student_quiz_result[ $sc ][ $qs ][ 'title' ]     = $student_table_contents[ 'quiz' ][ $sc ][ $qs ][ 'title' ];

        $quiz_null_data[ 'id' ] = '';
        $quiz_null_data[ 'answer_id' ] = '';
        $quiz_null_data[ 'section' ] = '';
        $quiz_null_data[ 'title' ] = '';

        for ( $sqs = 0; $sqs < $section_count; ++$sqs ) {
          $student_quiz_result[ $sc ][ $qs ][ 'total_score'.$sqs ] = '';
          $student_quiz_result[ $sc ][ $qs ][ 'register_datetime'.$sqs ] = '';

          $quiz_null_data[ 'total_score'.$sqs ] = '';
          $quiz_null_data[ 'register_datetime'.$sqs ] = '';
        }

      }
      // questionnaire 配列作成
			// 2019/6/03 count関数対策
			$questionnaire_counts = 0;
			if(is_countable($student_table_contents[ 'questionnaire' ][ $sc ])){
				$questionnaire_counts = count( $student_table_contents[ 'questionnaire' ][ $sc ] );
			}
      //$questionnaire_counts = count( $student_table_contents[ 'questionnaire' ][ $sc ] );

      for ( $qe = 0; $qe < $questionnaire_counts; ++$qe ) {

        $student_questionnaire_result[ $sc ][ $qe ][ 'id' ]      = $student_table_contents[ 'questionnaire' ][ $sc ][ $qe ][ 'questionnaire_id' ];
        $student_questionnaire_result[ $sc ][ $qe ][ 'section' ] = $student_table_contents[ 'questionnaire' ][ $sc ][ $qe ][ 'section' ];
        $student_questionnaire_result[ $sc ][ $qe ][ 'title' ]   = $student_table_contents[ 'questionnaire' ][ $sc ][ $qe ][ 'title' ];

        $questionnaire_null_data[ 'id' ] = '';
        $questionnaire_null_data[ 'section' ] = '';
        $questionnaire_null_data[ 'title' ] = '';

        for ( $qer = 0; $qer < $section_count; ++$qer ) {
          $student_questionnaire_result[ $sc ][ $qe ][ 'result'.$qer ] = '';
          $student_questionnaire_result[ $sc ][ $qe ][ 'answer_datetime'.$qer ] = '';

          $questionnaire_null_data[ 'result'.$qer ] = '';
          $questionnaire_null_data[ 'answer_datetime'.$qer ] = '';
        }
      }
      // report 配列作成
			// 2019/6/03 count関数対策
			$report_counts = 0;
			if(is_countable($student_table_contents[ 'report' ][ $sc ])){
				$report_counts = count( $student_table_contents[ 'report' ][ $sc ] );
			}
      //$report_counts = count( $student_table_contents[ 'report' ][ $sc ] );

      for ( $re = 0; $re < $report_counts; ++$re ) {

        $student_report_result[ $sc ][ $re ][ 'id' ]      = $student_table_contents[ 'report' ][ $sc ][ $re ][ 'questionnaire_id' ];
        $student_report_result[ $sc ][ $re ][ 'section' ] = $student_table_contents[ 'report' ][ $sc ][ $re ][ 'section' ];
        $student_report_result[ $sc ][ $re ][ 'title' ]   = $student_table_contents[ 'report' ][ $sc ][ $re ][ 'title' ];

        $report_null_data[ 'id' ] = '';
        $report_null_data[ 'section' ] = '';
        $report_null_data[ 'title' ] = '';

        for ( $res = 0; $res < $section_count; ++$res ) {
          $student_report_result[ $sc ][ $re ][ 'result'.$res ] = '';
          $student_report_result[ $sc ][ $re ][ 'answer_datetime'.$res ] = '';

          $report_null_data[ 'result'.$res ] = '';
          $report_null_data[ 'answer_datetime'.$res ] = '';
        }
      }

    }

    // 結果のnullの部分に空配列を挿入 contents
    for ( $cs = 0; $cs < $section_count; ++$cs ) {
			// 2019/6/03 count関数対策
			$crcount = 0;
			if(is_countable($contents_result_data[ 'cr'.$cs ])){
				$crcount = count( $contents_result_data[ 'cr'.$cs ] );
			}
      //$crcount = count( $contents_result_data[ 'cr'.$cs ] );

      for ( $rc = 0; $rc < $crcount; ++$rc ) {

        $contents_result_data[ 'cr'.$cs ] =
          array_values($contents_result_data[ 'cr'.$cs ]);

        $c_judge = $contents_result_data[ 'cr'.$cs ][ $rc ];

        if ( $c_judge == "-" ) {
          $contents_result_data[ 'cr'.$cs ][ $rc ] = $contents_null_data;
        }
      }
    }

    // 結果のnullの部分に空配列を挿入 quiz
    for ( $rs = 0; $rs < $section_count; ++$rs ) {
			// 2019/6/03 count関数対策
			$qccount = 0;
			if(is_countable($quiz_result_data[ 'qzr'.$rs ])){
				$qccount = count( $quiz_result_data[ 'qzr'.$rs ] );
			}
      //$qccount = count( $quiz_result_data[ 'qzr'.$rs ] );

      for ( $rq = 0; $rq < $qccount; ++$rq ) {

        $quiz_result_data[ 'qzr'.$rs ] =
          array_values($quiz_result_data[ 'qzr'.$rs  ]);

        $q_judge = $quiz_result_data[ 'qzr'.$rs ][ $rq ];

        if ( $q_judge == "-" ) {
          $quiz_result_data[ 'qzr'.$rs ][ $rq ] = $quiz_null_data;
        }
      }
    }

    // 結果のnullの部分に空配列を挿入 questionnaire
    for ( $qe = 0; $qe < $section_count; ++$qe ) {
			// 2019/6/03 count関数対策
			$qeccount = 0;
			if(is_countable($questionnaire_result_data[ 'qer'.$qe ])){
				$qeccount = count( $questionnaire_result_data[ 'qer'.$qe ] );
			}
      //$qeccount = count( $questionnaire_result_data[ 'qer'.$qe ] );

      for ( $qer = 0; $qer < $qeccount; ++$qer ) {

        $questionnaire_result_data[ 'qer'.$qe ] =
          array_values($questionnaire_result_data[ 'qer'.$qe ]);

        $qe_judge = $questionnaire_result_data[ 'qer'.$qe ][ $qer ];

        if ( $qe_judge == "-" ) {
          $questionnaire_result_data[ 'qer'.$qe ][ $qer ] = $questionnaire_null_data;
        }
      }
    }

    // 結果のnullの部分に空配列を挿入 report
    for ( $re = 0; $re < $section_count; ++$re ) {
			// 2019/6/03 count関数対策
			$qeccount = 0;
			if(is_countable($report_result_data[ 'rer'.$re ])){
				$qeccount = count( $report_result_data[ 'rer'.$re ] );
			}
      //$qeccount = count( $report_result_data[ 'rer'.$re ] );

      for ( $rer = 0; $rer < $qeccount; ++$rer ) {

        $report_result_data[ 'rer'.$re ] =
          array_values($report_result_data[ 'rer'.$re ]);

        $qe_judge = $report_result_data[ 'rer'.$re ][ $rer ];

        if ( $qe_judge == "-" ) {
          $report_result_data[ 'rer'.$re ][ $rer ] = $report_null_data;
        }
      }
    }

    // 結果を入れていく
    // contents-data ここから /////////////////////////////////////////////////////////
      for ( $cr = 0; $cr < count( $contents_result_data ); ++$cr ) {

        for ( $ss = 0; $ss < $section_count; ++$ss ) {

          if ( isset( $contents_result_data[ 'contents_proportion'.$ss ] )) {
            unset( $contents_result_data[ 'contents_proportion'.$ss ] );
          }
        }
      }

      // all 以外
      $contents_table = [];
      $contents_table[ 0 ][ 'title' ] = 'Course';
      $contents_table[ 0 ][ 'field' ] = 'section';
      $contents_table[ 0 ][ 'width' ] = 180;
      $contents_table[ 0 ][ 'headerFilter' ] = 'input';
      $contents_table[ 0 ][ 'frozen' ] = true;
      $contents_table[ 0 ][ 'sorter' ] = 'number';
      $contents_table[ 0 ][ 'value' ] = 'id';
      $contents_table[ 0 ][ 'align' ] = "center";

      $contents_table[ 1 ][ 'title' ] = 'Title';
      $contents_table[ 1 ][ 'field' ] = 'title';
      $contents_table[ 1 ][ 'width' ] = 180;
      $contents_table[ 1 ][ 'headerFilter' ] = 'input';
      $contents_table[ 1 ][ 'frozen' ] = true;
      $contents_table[ 1 ][ 'sorter' ] = 'number';
      $contents_table[ 1 ][ 'align' ] = "center";
      $contents_table[ 1 ][ 'value' ] = 'answer_id';

      // all
      $all_contents_table = [];
      $all_contents_table[ 0 ][ 'title' ] = 'Name';
      $all_contents_table[ 0 ][ 'field' ] = 'name';
      $all_contents_table[ 0 ][ 'width' ] = 180;
      $all_contents_table[ 0 ][ 'headerFilter' ] = 'input';
      $all_contents_table[ 0 ][ 'frozen' ] = true;
      $all_contents_table[ 0 ][ 'sorter' ] = 'number';
      $all_contents_table[ 0 ][ 'value' ] = 'id';
      $all_contents_table[ 0 ][ 'align' ] = "center";

      $all_contents_table[ 1 ][ 'title' ] = 'contents';
      $all_contents_table[ 1 ][ 'field' ] = 'type';
      $all_contents_table[ 1 ][ 'width' ] = 180;
      $all_contents_table[ 1 ][ 'headerFilter' ] = 'input';
      $all_contents_table[ 1 ][ 'frozen' ] = true;
      $all_contents_table[ 1 ][ 'sorter' ] = 'number';
      $all_contents_table[ 1 ][ 'align' ] = "center";

      $all_contents_table[ 2 ][ 'title' ] = 'Course';
      $all_contents_table[ 2 ][ 'field' ] = 'section';
      $all_contents_table[ 2 ][ 'width' ] = 180;
      $all_contents_table[ 2 ][ 'headerFilter' ] = 'input';
      $all_contents_table[ 2 ][ 'frozen' ] = true;
      $all_contents_table[ 2 ][ 'sorter' ] = 'number';
      $all_contents_table[ 2 ][ 'align' ] = "center";

      $all_contents_table[ 3 ][ 'title' ] = 'Title';
      $all_contents_table[ 3 ][ 'field' ] = 'title';
      $all_contents_table[ 3 ][ 'width' ] = 180;
      $all_contents_table[ 3 ][ 'headerFilter' ] = 'input';
      $all_contents_table[ 3 ][ 'frozen' ] = true;
      $all_contents_table[ 3 ][ 'sorter' ] = 'number';
      $all_contents_table[ 3 ][ 'align' ] = "center";

      $section = [];

      for ( $sec = 0; $sec < $section_count; ++$sec ) {

        $section[] = $student_contents_result[ $sec ][ 0 ][ 'section' ];

      }

      switch ( $send_data[ 'category' ] ) {
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
          $student_contents_result = $history_data->student_conents_results_table (
            $student_contents_result, $contents_result_data, $contents, $all );
          // 判定が×のデータの挿入
          $student_contents_result = $history_data->student_conents_results_table_judge (
            $student_contents_result, $contents, $all );
          break;

        case '2':
          $contents = 'quiz';
          $all = 0;
          // 履歴があるdataの挿入
          $student_contents_result = $history_data->student_conents_results_table (
            $student_quiz_result, $quiz_result_data, $contents );
          // 判定が×のデータの挿入
          $student_contents_result = $history_data->student_conents_results_table_judge (
            $student_contents_result, $contents );
          break;


        case '3':
          $contents = 'questionnaire';
          $all = 0;
          // 履歴があるdataの挿入
          $student_contents_result = $history_data->student_conents_results_table (
            $student_questionnaire_result, $questionnaire_result_data, $contents );
          // 判定が×のデータの挿入
          $student_contents_result = $history_data->student_conents_results_table_judge (
            $student_contents_result, $contents );
            //debug_r( $student_contents_result );
          break;

        case '4':
          $contents = 'report';
          $all = 0;

          $student_contents_result = $history_data->student_conents_results_table (
            $student_report_result, $report_result_data, $contents );
          // 判定が×のデータの挿入
          $student_contents_result = $history_data->student_conents_results_table_judge (
            $student_contents_result, $contents );
          break;

        default:
          # code...
          break;
      }

      // 空の配列に"-"を挿入
      if ( $contents !== 'all' ) {
        $student_contents_result = $history_data->null_data_insert( $student_contents_result );
        // second_table_header 作成
        $contents_table = $history_data->second_table_header(
          $contents_table, $section_count, $student_contents_result, $contents, $section );

      } else {
        $contents_table = $history_data->second_table_header(
          $all_contents_table, $section_count, $student_contents_result, $contents, $section );
      }

      ////////////// csv_contents_table_header ///////////////////////////////////////.mozilla
        for ( $ch = 0; $ch < count( $contents_table); ++$ch ) {

          if ( $contents == 'all' ) {

            if ( $ch < 4 ) {
              $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ];
            } else {
              //for ( $cc = 0; $cc < 2; $cc++ ) {
              $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 0 ][ 'title' ];
              $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 1 ][ 'title' ];

            }

          } else {

              if ( $ch == 0 || $ch == 1 ) {
                $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ];
              } else {
                //for ( $cc = 0; $cc < 2; $cc++ ) {
                $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 0 ][ 'title' ];
                $csv_contents_table_header[] = $contents_table[ $ch ][ 'title' ] . "-" . $contents_table[ $ch ][ 'columns' ][ 1 ][ 'title' ];

              }
          }

        }

        // student_table_csv_data 作成//////////////////////////////////////////////
          $csv_student_contents_results = [];
          if ( $contents === 'all' || $contents === 'quiz' ) {
          //debug_r( $student_contents_results );
            foreach ( (array)$student_contents_result as $key => $c_results ) {
              foreach ( (array)$c_results as $key2 => $c_results2 ) {
                if ( $key2 !== 'id' && $key2 !== 'answer_id' ) {
                  $csv_student_contents_results[ $key ][] = $c_results2;
                }
              }
            }

          } else {

            foreach ( (array)$student_contents_result as $key => $c_results ) {
              //debug_r( $student_contents_results );
              foreach ( $c_results as $key2 => $c_results2 ) {
                if ( $key2 !== 'id' ) {
                  $csv_student_contents_results[ $key ][] = $c_results2;
                }
              }
            }
          }

          $table_data = [];
          $table_data[ 'contents_table' ]   = $contents_table;
          $table_data[ 'contents' ]         = $student_contents_result;
          $table_data[ 'csv_header' ]       = $csv_contents_table_header;
          //$table_data[ 'csv_student_data' ] = $csv_student_contents_results;

          $all_csv_array[] = $csv_student_contents_results;
          //debug_r($csv_student_contents_results);

  }// 最初のforeach

  foreach ( $all_csv_array as $key1 => $data1 ) {
    foreach ( $data1 as $key2 => $data2 ) {
      $csv_select_results[] = $data2;
    }
  }

	// 2019/6/03 count関数対策
	$header_count = 0;
	if(is_countable($csv_select_results[ 0 ])){
		$header_count = count( $csv_select_results[ 0 ] );
	}
  //$header_count = count( $csv_select_results[ 0 ] );

  for ( $h = 0; $h < $header_count; ++$h ) {
    $header[ $h ] = $table_data[ 'csv_header' ][ $h ];
  }

  $table_data[ 'csv_header' ]       = $header;
  $table_data[ 'csv_student_data' ] = $csv_select_results;

  echo json_safe_encode( $table_data );
  exit();

}
///////////// 2nd table 総合での番号指定csv抽出 ここまで /////////////////////////

//$testrr = [];
//$testrr = array_values($return_data['student_data'] );
//$return_data['student_data'] = $testrr;

///////////////// 第一テーブル 結果出力 /////////////////////////////////////////
//echo json_safe_encode( $return_data );
echo $json;
exit();


?>
