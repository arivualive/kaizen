<?php
class HistoryDataCreate {
  // contents - quiz - student result (新アクセス権)

  // 受講履歴%表示用
  public function percentage_results ( $clear_count, $all_count ) {

    $percentage_results = '';

    $percentage_results = round(( $clear_count / $all_count ) * 100).'%';
    $percentage_results .= ' ( ' .$clear_count. ' / '.$all_count.' ) ';
    return $percentage_results;

  }

  // 表示スピードアップ用
  public function quiz_students_result_Type1 ($access_code, $contents_result, $curl, $junle) {
    //$count_access = count($access_code); *2019/05/29
    $count_access = is_array($access_code) ? count($access_code) : 0;

    $qz = 'qz';
    // sqzf sqzt sqzr
    $contents_result_data = [];
    $c_result = $qz . 'r';
    $subject_false  = 's' . $qz . 'f';
    $subject_true   = 's' . $qz . 't';
    $subject_result = 's' . $qz . 'r';
    $msg = $junle . 'はありません。';
    $junle_id = $junle . '_id';

    if($junle === 'contents') {
      $select = 'history_id';
    } else {
      $select = $junle_id;
    }

    for($c = 0; $c < $count_access; ++$c) {
      //$contents_count = count($access_code[$c][$junle]); *2019/05/29
      $contents_count = is_array($access_code[$c][$junle]) ? count($access_code[$c][$junle]) : 0;
      //$student_count  = count($access_code[$c]['student_data']); *2019/05/29
      $student_count  = is_array($access_code[$c]['student_data']) ? count($access_code[$c]['student_data']) : 0;

      if($access_code[$c]['student_data'] !== '対象生徒はいません。') {
        for($sd = 0; $sd < $student_count; ++$sd) {
          $contents_result['params']['sid'] = $access_code[$c]['student_data'][$sd]['sid'];
          //$contents_result['params']['student_id'] = $access_code[$c]['student_data'][$sd]['student_id'];
          $access_code[$c]['student_data'][$sd][$subject_false . $c]  = '';
          $access_code[$c]['student_data'][$sd][$subject_true . $c]   = '';
          $access_code[$c]['student_data'][$sd][$subject_result . $c] = '';

          if($access_code[$c][$junle] !== $msg) {
            $s_contents_false = 0;
            $s_contents_true  = 0;

            for($cc = 0; $cc < $contents_count; ++$cc) {
//usleep(0.015 * 1000 * 1000); //0.015秒遅延
              $contents_result['params'][$junle_id] = $access_code[$c][$junle][$cc][$junle_id];
              $contents_result_data[$cc] = $curl->send($contents_result);
              /*return $contents_result_data;
              exit();
              */
              if(empty($contents_result_data[$cc][0]['quiz_answer_id'])) {
                //$access_code[$c]['student_data'][$sd][$c_result . $c][$cc] = 'false'; //by Choka
                $access_code[$c]['student_data'][$sd][$c_result . $c][$cc] = 0;
                $access_code[$c]['student_data'][$sd][$subject_false . $c] = ++$s_contents_false;
                //$access_code[$c]['student_data'][$sd][$junle . '_score'] = $contents_result_data[$cc][0][$select]

                if($contents_count == $s_contents_false) {
                  //$access_code[$c]['student_data'][$sd][$subject_result . $c] = '×';
                  $access_code[$c]['student_data'][$sd][$subject_result . $c] = '0% ( 0 / '.$contents_count. ' )';
                  //$access_code[$c]['student_data'][$sd][]
                }
              } else {
                $access_code[$c]['student_data'][$sd][$c_result . $c][$cc] = $contents_result_data[$cc];
                $access_code[$c]['student_data'][$sd][$subject_true . $c] = ++$s_contents_true;
                //return $contents_result_data[$cc][0]['total_score'];
                $access_code[$c]['student_data'][$sd][$subject_result . $c] = $this->percentage_results ( $s_contents_true, $contents_count );
                  /*
                  if($contents_count == $s_contents_true) {
                    $access_code[$c]['student_data'][$sd][$subject_result . $c] = '○';
                  } else if($contents_count > $s_contents_true) {
                    $access_code[$c]['student_data'][$sd][$subject_result . $c] = '△';
                  }
                  */
              }
              //$access_code[$c]['student_data'][$sd]['headerClick'] = function(e, column){"console.dir(column)"};
              //$access_code[$c]['student_data'][$sd]['value'] = $access_code[$c]['student_data'][$sd]['student_id'];
              $access_code[$c]['student_data'][$sd]['value'] = $access_code[$c]['student_data'][$sd]['sid'];
            }
          } else {
            $access_code[$c]['student_data'][$sd][$c_result . $c] = $junle . 'なし';
            $access_code[$c]['student_data'][$sd][$subject_result . $c] = '-';
          }
        }
      }
    }
    //return $contents_result_data;
    return $access_code;
  }

  // 動画視聴結果抽出 (新アクセス権)
  public function contents_students_result_Type1 ($access_code, $contents_result, $curl) {
    //$count_access = count($access_code); *2019/05/29
    $count_access = is_array($access_code) ? count($access_code) : 0;
    //$contents_result_data = ''; *2019/05/29
    $contents_result_data = [];
    $junle_id = 'contents_id';

    $subject_contents_result_false = 'scf';
    $subject_contents_result_true  = 'sct';
    $subject_contents_result       = 'scr';
    $result_data                   = 'cr';

    for($c = 0; $c < $count_access; ++$c) {
      //$contents_count = count($access_code[$c]['contents']); *2019/05/29
      $contents_count = is_array($access_code[$c]['contents']) ? count($access_code[$c]['contents']) : 0;
      //$student_count  = count($access_code[$c]['student_data']); *2019/05/29
      $student_count  = is_array($access_code[$c]['student_data']) ? count($access_code[$c]['student_data']) : 0;

      if($access_code[$c]['student_data'] !== '対象生徒はいません。') {
        for($sd = 0; $sd < $student_count; ++$sd) {
          $contents_result['params']['sid'] = $access_code[$c]['student_data'][$sd]['sid'];
          //$contents_result['params']['student_id'] = $access_code[$c]['student_data'][$sd]['student_id'];
          $access_code[$c]['student_data'][$sd][$subject_contents_result_false . $c]  = '';
          $access_code[$c]['student_data'][$sd][$subject_contents_result_true . $c]   = '';
          $access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = '';
          //return $access_code;

          $percentage = [];

          if($access_code[$c]['contents'] !== 'contentsはありません。') {
            $s_contents_false = 0;
            $s_contents_true  = 0;

            for($cc = 0; $cc < $contents_count; ++$cc) {
//usleep(0.015 * 1000 * 1000); //0.015秒遅延
              $contents_result['params'][$junle_id] = $access_code[$c]['contents'][$cc][$junle_id];
              $contents_result_data[$cc] = $curl->send($contents_result);
              //return $contents_result_data[$cc][0]['proportion'];
              if(empty($contents_result_data[$cc][0]['history_id'])) {
                //$access_code[$c]['student_data'][$sd][$result_data . $c][$cc] = 'false'; //by Choka
                $access_code[$c]['student_data'][$sd][$result_data . $c][$cc] = 0;
                $access_code[$c]['student_data'][$sd]['contents_proportion' . $c][$cc] = '0%';
                $access_code[$c]['student_data'][$sd][$subject_contents_result_false . $c] = ++$s_contents_false;
                //$access_code[$c]['student_data'][$sd]['contents_judge'] = '視聴完了していません';

                if($contents_count == $s_contents_false) {
                  //$access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = '×';
                  $access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = '0% ( 0 / '.$contents_count. ' )';
                  $access_code[$c]['student_data'][$sd]['contents_proportion' . $c][$cc] = '0%';
                  //$access_code[$c]['student_data'][$sd]['contents_judge'] = '視聴完了していません';
                  //$access_code[$c]['student_data'][$sd][]
                }
              } else {
                $access_code[$c]['student_data'][$sd][$result_data . $c][$cc] = $contents_result_data[$cc];
                $access_code[$c]['student_data'][$sd]['contents_proportion' . $c][$cc] = $contents_result_data[$cc][0]['proportion'] . '%';

                if($contents_result_data[$cc][0]['proportion_flg'] == 1) {
                  $access_code[$c]['student_data'][$sd][$subject_contents_result_true . $c] = ++$s_contents_true;
                }


                $access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = $this->percentage_results ( $s_contents_true, $contents_count );

                /*
                if($contents_count == $s_contents_true) {
                  $access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = '○';

                } else if($contents_count > $s_contents_true) {
                  $access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = '△';
                }
                */
                //$access_code[$c]['student_data'][$sd]['contents_proportion' . $c][$cc] = $contents_result_data[$cc][0]['proportion'];
              }
              //$access_code[$c]['student_data'][$sd]['headerClick'] = function(e, column){"console.dir(column)"};
              //$access_code[$c]['student_data'][$sd]['value'] = $access_code[$c]['student_data'][$sd]['student_id'];
              $access_code[$c]['student_data'][$sd]['value'] = $access_code[$c]['student_data'][$sd]['sid'];
            }
          } else {
            $access_code[$c]['student_data'][$sd][$result_data . $c] = 'contentsなし';
            $access_code[$c]['student_data'][$sd][$subject_contents_result . $c] = '-';
            //$access_code[$c]['student_data'][$sd]['contents_judge'] = '動画はありません89';
          }
        }
      }
    }
    //$access_code['contents_count'] = 2;

    return $access_code;
  }

  // アンケート or レポート取得
  public function questionnaire_and_report_students_result ($access_code, $questionnaire_result, $curl, $junle) {
    //$count_access = count($access_code); *2019/05/29
    $count_access = is_array($access_code) ? count($access_code) : 0;

    if($junle === 'questionnaire') {
      $qe = 'qe';
    } else {
      $qe = 're';
    }

    // sqef sqet sqer sref sret srer
    $c_result = $qe . 'r';
    $subject_false  = 's' . $qe . 'f';
    $subject_true   = 's' . $qe . 't';
    $subject_result = 's' . $qe . 'r';
    $msg = $junle . 'はありません。';
    $junle_id = $junle . '_id';

    for($q = 0; $q < $count_access; ++$q) {
      //$questionnaire_count = count($access_code[$q][$junle]); *2019/05/29
      $questionnaire_count = is_array($access_code[$q][$junle]) ? count($access_code[$q][$junle]) : 0;
      //$student_count = count($access_code[$q]['student_data']); *2019/05/29
      $student_count = is_array($access_code[$q]['student_data']) ? count($access_code[$q]['student_data']) : 0;

      if($access_code[$q]['student_data'] !== '対象生徒はいません。') {
        for($sd = 0; $sd < $student_count; ++$sd) {
          $questionnaire_result['params']['sid'] = $access_code[$q]['student_data'][$sd]['sid'];
          //$questionnaire_result['params']['student_id'] = $access_code[$q]['student_data'][$sd]['student_id'];
          $access_code[$q]['student_data'][$sd][$subject_false . $q] = '';
          $access_code[$q]['student_data'][$sd][$subject_true . $q] = '';
          $access_code[$q]['student_data'][$sd][$subject_result . $q] = '';

          if($access_code[$q][$junle] !== $msg) {
            $s_questionnaire_false = 0;
            $s_questionnaire_true  = 0;

            for($qu = 0; $qu < $questionnaire_count; ++$qu) {
//usleep(0.015 * 1000 * 1000); //0.015秒遅延
              $questionnaire_result['params']['questionnaire_id'] = $access_code[$q][$junle][$qu]['questionnaire_id'];
              $questionnaire_result['params']['bit_classroom'] = $access_code[$q][$junle][$qu]['bit_classroom'];
              $questionnaire_result_data[$qu] = $curl->send($questionnaire_result);
              //return $questionnaire_result_data;

              //if(empty($questionnaire_result_data[$qu][0]['questionnaire_id'])) {
              if(empty($questionnaire_result_data[$qu][0]['answer_id'])) {
                //$access_code[$q]['student_data'][$sd][$c_result . $q][$qu] = 'false'; //by Choka
                $access_code[$q]['student_data'][$sd][$c_result . $q][$qu] = 0;
                $access_code[$q]['student_data'][$sd][$subject_false . $q] = ++$s_questionnaire_false;

                if($questionnaire_count === $s_questionnaire_false) {
                  //$access_code[$q]['student_data'][$sd][$subject_result . $q] = '×';
                  $access_code[$q]['student_data'][$sd][$subject_result . $q] = '0% ( 0 / '.$questionnaire_count. ' )';

                }
              } else {
                $access_code[$q]['student_data'][$sd][$c_result . $q][$qu] = $questionnaire_result_data[$qu];
                $access_code[$q]['student_data'][$sd][$subject_true . $q] = ++$s_questionnaire_true;

                $access_code[$q]['student_data'][$sd][$subject_result . $q] = $this->percentage_results ( $s_questionnaire_true, $questionnaire_count );
                /*
                if($questionnaire_count === $s_questionnaire_true) {
                  $access_code[$q]['student_data'][$sd][$subject_result . $q] = '○';
                } else if($questionnaire_count > $s_questionnaire_true) {
                  $access_code[$q]['student_data'][$sd][$subject_result . $q] = '△';
                }
                */
              }
            }
          } else {
            $access_code[$q]['student_data'][$sd][$c_result . $q] = $junle . 'なし';
            $access_code[$q]['student_data'][$sd][$subject_result . $q] = '-';
          }
        }
      }
    }
    return $access_code;
  }

  // アンケート　student-table data-object作成
  public function student_questionnaire_result_object ($subject_count, $student_table, $table_type1_column, $student_results, $q_type) {
    //$column_count = count($student_table) + 2 *2019/05/29;
    $column_count = is_array($student_table) ? count($student_table)+2 : 2;

    //return $table_type1_column;
    //return $student_results;
    if($q_type === 0) {
      $type = 'questionnaire';
      $type_string = 'アンケート';
    } else {
      $type = 'report';
      $type_string = 'レポート';
    }
    //return $table_type1_column;

    for($ct = 0; $ct < $subject_count; ++$ct) {
      $student_table[$type . '_table'][$column_count+$ct]['title'] = $table_type1_column[$ct]['subject_section_name'];
      $student_table[$type . '_table'][$column_count+$ct]['field'] = $type . '_result' . $ct;//$school_subject['contents_table'][2 + $js]['field'] = 'contents_results' . $js;
      $student_table[$type . '_table'][$column_count+$ct]['align'] = 'center';

      if(isset($table_type1_column[$ct][$type])) {
        if($table_type1_column[$ct][$type] !== $type . 'はありません。') {
        //if($student_results['questionnaire_result' . $ct] !== 'questionnaireなし') {
          //$contents_count = count($table_type1_column[$ct][$type]); *2019/05/29
          $contents_count = is_array($table_type1_column[$ct][$type]) ? count($table_type1_column[$ct][$type]) : 0;

          for($cc = 0; $cc < $contents_count; ++$cc) {
            $table_type1_column[$ct][$type][$cc]['subject_section_name'] = $table_type1_column[$ct]['subject_section_name'];

            if(isset($student_results['number'])) {
              $number = (String)$student_results['number'];
              $table_type1_column[$ct][$type][$cc]['number'] = $number;
            }

            if(isset($student_results['student_name'])) {
              //$table_type1_column[$ct][$type][$cc]['student_name'] = $student_results['student_name'];
              $table_type1_column[$ct][$type][$cc]['sn'] = $student_results['sn'];
            }

            if(isset($student_results[$type . '_result' . $ct] )) {
              if($student_results[$type . '_result' . $ct] !== $type . 'なし') {
                if(isset($student_results[$type . '_result' . $ct][$cc][0]['questionnaire_id'])) {
                  $answer_value = $student_results[$type . '_result' . $ct][$cc][0]['questionnaire_id'];
                  $table_type1_column[$ct][$type][$cc]['subject_section_name'] = $table_type1_column[$ct]['subject_section_name'];

                  $table_type1_column[$ct][$type][$cc][$type . '_result' . $ct] = '○';
                  $table_type1_column[$ct][$type][$cc]['value'] = $student_results[$type . '_result' . $ct][$cc][0]['questionnaire_id'];

                  if(isset($student_results[$type . '_result' . $ct][$cc][0]['answer_datetime'])) {
                    $table_type1_column[$ct][$type][$cc]['datetime'] = $student_results[$type . '_result' . $ct][$cc][0]['answer_datetime'];
                  }
                } else {
                  $table_type1_column[$ct][$type][$cc][$type . '_result' . $ct] = '×';
                  $table_type1_column[$ct][$type][$cc]['value'] = '未回答です';
                  $table_type1_column[$ct][$type][$cc]['datetime'] = '-';
                  $table_type1_column[$ct][$type][$cc]['subject_section_name'] = $table_type1_column[$ct]['subject_section_name'];
                }
            } else {
              $table_type1_column[$ct][$type][$cc][$type . '_result' . $ct] = '-';
              $table_type1_column[$ct][$type][$cc]['value'] = $type_string . 'はありません';
              $table_type1_column[$ct][$type][0]['title'] = '-';
              $table_type1_column[$ct][$type][$cc]['datetime'] = '-';
              $table_type1_column[$ct][$type][$cc]['subject_section_name'] = '-';
            }

            //$table_type1_column[$ct][$type][$cc]['subject_section_name'] = $table_type1_column[$ct]['subject_section_name'];
            if(isset($student_results['number'])) {
              $number = (String)$student_results['number'];
              $table_type1_column[$ct][$type][$cc]['number'] = $number;
            }
            /*
            if(isset($student_results['student_name'])) {
              $table_type1_column[$ct][$type][$cc]['student_name'] = $student_results['student_name'];
            }
            */
            if(isset($student_results['sn'])) {
              $table_type1_column[$ct][$type][$cc]['sn'] = $student_results['sn'];
            }
          }
        }
      } else if($table_type1_column[$ct][$type] === $type . 'はありません。') {
        $table_type1_column[$ct][$type] = [];
        $table_type1_column[$ct][$type][0][$type . '_result' . $ct] = '-';

        $table_type1_column[$ct][$type][0]['value'] = $type_string . 'はありません';
        $table_type1_column[$ct][$type][0]['title'] = '-';
        $table_type1_column[$ct][$type][0]['datetime'] = '-';
        $table_type1_column[$ct][$type][0]['subject_section_name']  = $table_type1_column[$ct]['subject_section_name'];
      }

      if(isset($student_results['number'])) {
        $number = (String)$student_results['number'];
        $table_type1_column[$ct][$type][0]['number'] = $number;
      }
      /*
      if(isset($student_results['student_name'])) {
        $table_type1_column[$ct][$type][0]['student_name'] = $student_results['student_name'];
      }
      */
      if(isset($student_results['sn'])) {
        $table_type1_column[$ct][$type][0]['sn'] = $student_results['sn'];
      }
    }
  }

    //$table_type1_column_count = count($table_type1_column); *2019/05/29
    $table_type1_column_count = is_array($table_type1_column) ? count($table_type1_column) : 0;
    $contents_students_result = [];

    for($tac = 0; $tac < $table_type1_column_count; ++$tac) {
      //$section_contents_count = count($table_type1_column[$tac][$type]); *2019/05/29
      $section_contents_count = is_array($table_type1_column[$tac][$type]) ? count($table_type1_column[$tac][$type]) : 0;

      for($sec = 0; $sec < $section_contents_count; ++$sec) {
        for($ss = 0; $ss < $subject_count; ++$ss) {
          if($table_type1_column[$tac][$type] !== $type . 'はありません。') {
            if(!isset($table_type1_column[$tac][$type][$sec][$type . '_result' . $ss])) {
              $table_type1_column[$tac][$type][$sec][$type . '_result' . $ss] = '-';
            }
          } else {
            $table_type1_column[$tac][$type] = [];
            $table_type1_column[$tac][$type][$sec][$type . '_result' . $ss] = '-';
          }
        }
        $contents_students_result[$type][] = $table_type1_column[$tac][$type][$sec];
      }
    }
    $contents_students_result['table_data'] = $student_table;
    return $contents_students_result;
  }

  // csv_student_data_作成
  public function csv_student_data_create ($csv, $column_count, $type_array, $data) {
    if(isset($column_count)) {
      for($cl = 0; $cl < $column_count; ++$cl) {
        if(isset($data[$type_array . $cl])) {
          $csv[] = $data[$type_array . $cl];
        } else {
          $csv[] = '-';
        }
      }
    } else {
      $csv[] = '-';
    }
    return $csv;
  }

  public function student_conents_results_table ($student_results, $contents_results, $select) {
    switch ($select) {
      case 'contents':
        $junle_results_1 = 'proportion';
        $junle_results_2 = 'play_start_datetime';
        //$select_result   = 'contents_result';
        $select_result   = 'cr';
        $select_id       = 'school_contents_id';
        $per = '%';
        break;
      case 'quiz':
        $junle_results_1 = 'total_score';
        $junle_results_2 = 'register_datetime';
        //$select_result   = 'quiz_result';
        $select_result   = 'qzr';
        $select_id       = 'quiz_id';
        $anwser_id       = 'quiz_answer_id';
        $per = '点';
        break;
      case 'questionnaire':
        $junle_results_1 = 'result';
        $junle_results_2 = 'answer_datetime';
        //$select_result   = 'questionnaire_result';
        $select_result   = 'qer';
        $select_id       = 'questionnaire_id';
        $per = '';
        break;
      case 'report':
        $junle_results_1 = 'result';
        $junle_results_2 = 'answer_datetime';
        //$select_result   = 'report_result';
        $select_result   = 'rer';
        $select_id       = 'questionnaire_id';
        $per = '';
        break;
      default:
        # code...
        break;
    }

    //for($sc = 0; $sc < count($student_results); ++$sc) { *2019/05/29
    for($sc = 0; $sc < (is_array($student_results) ? count($student_results) : 0); ++$sc) {
      //$s_count = count($student_results[$sc]); *2019/05/29
      $s_count = is_array($student_results[$sc]) ? count($student_results[$sc]) : 0;
      for($sr = 0; $sr < $s_count; ++$sr) {
        $id = $student_results[$sc][$sr]['id'];

        if($id === '-') {
          $student_results[$sc][$sr][$junle_results_1 . $sc] = '-';
          $student_results[$sc][$sr][$junle_results_2 . $sc] = '-';
        } else {
          //$crd_count = count($contents_results); *2019/05/29
          $crd_count = is_array($contents_results) ? count($contents_results) : 0;

          for($cr = 0; $cr < $crd_count; ++$cr) {
            if(isset($contents_results[$select_result . $sc])) {
              //$crs_count = count($contents_results[$select_result . $sc]); *2019/05/29
              $crs_count = is_array($contents_results[$select_result . $sc]) ? count($contents_results[$select_result . $sc]) : 0;
              //debug_r($crs_count);
              for($crs = 0; $crs < $crs_count; ++$crs) {
                if(isset($contents_results[$select_result . $sc][$crs][$select_id])) {
                  $sc_id = $contents_results[$select_result . $sc][$crs][$select_id];

                  if($id == $sc_id) {
                    //Notice対策
                    if(isset($contents_results[$select_result . $sc][$crs][$junle_results_1])) {
                      $student_results[$sc][$sr][$junle_results_1 . $sc] = $contents_results[$select_result . $sc][$crs][$junle_results_1] . $per;
                    } else {
                      $student_results[$sc][$sr][$junle_results_1 . $sc] = '' . $per;
                    }

                    $student_results[$sc][$sr][$junle_results_2 . $sc] = $contents_results[$select_result . $sc][$crs][$junle_results_2];

                    if($select === 'quiz') {
                      $student_results[$sc][$sr]['answer_id'] = $contents_results[$select_result . $sc][$crs][$anwser_id];
                    }
                    if($select === 'questionnaire' || $select === 'report') {
                      $student_results[$sc][$sr][$junle_results_1 . $sc] = '○';
                    }
                  } /*else {
                      $student_contents_result[$sc][$sr]['proportion' . $sc] = '0%';
                      $student_contents_result[$sc][$sr]['play_start_datetime' . $sc] = '0000-00-00 00:00:00';
                  }*/
                }
              }
            }
          }
        }
      }
    }
    return $student_results;
  }

  public function student_conents_results_table_judge ($student_results, $select) {
    switch ($select) {
      case 'contents':
        $junle_results_1 = 'proportion';
        $junle_results_2 = 'play_start_datetime';

        $per_1 = '0%';
        $per_2 = '0000-00-00 00:00:00';
        break;
      case 'quiz':
        $junle_results_1 = 'total_score';
        $junle_results_2 = 'answer_id';
        $junle_results_3 = 'register_datetime';

        $per_1 = '0点';
        $per_2 = '0000-00-00 00:00:00';
        break;
      case 'questionnaire':
        $junle_results_1 = 'answer_datetime';
        $junle_results_2 = 'result';
        $select_result   = 'questionnaire_result';
        $select_id       = 'questionnaire_id';
        $per_1 = '0000-00-00 00:00:00';
        $per_2 = '×';
        break;
      case 'report':
        $junle_results_1 = 'answer_datetime';
        $junle_results_2 = 'result';
        $select_result   = 'report_result';
        $select_id       = 'questionnaire_id';
        $per_1 = '0000-00-00 00:00:00';
        $per_2 = '×';
        break;
      default:
        # code...
        break;
    }

    //for($rc = 0; $rc < count($student_results); ++$rc) { *2019/05/29
    for($rc = 0; $rc < (is_array($student_results) ? count($student_results) : 0); ++$rc) {
      //for($rcs = 0; $rcs < count($student_results[$rc]); ++$rcs) { *2019/05/29
      for($rcs = 0; $rcs < (is_array($student_results[$rc]) ? count($student_results[$rc]) : 0); ++$rcs) {
        if($select == 'quiz') {
          if(empty($student_results[$rc][$rcs][$junle_results_1 . $rc]) && empty($student_results[$rc][$rcs][$junle_results_3 . $rc])) {
            $student_results[$rc][$rcs][$junle_results_1 . $rc] = $per_1;
            $student_results[$rc][$rcs][$junle_results_3 . $rc] = $per_2;
            //return $student_results[$rc][$rcs][$junle_results_2];
          }
        } else if($select === 'contents') {
          if(empty($student_results[$rc][$rcs][$junle_results_1 . $rc]) && empty($student_results[$rc][$rcs][$junle_results_2 . $rc])) {
            $student_results[$rc][$rcs][$junle_results_1 . $rc] = $per_1;
            $student_results[$rc][$rcs][$junle_results_2 . $rc] = $per_2;
            //return $student_results[$rc][$rcs][$junle_results_1 . $rc];
          }
        } else if($select === 'questionnaire' || $select === 'report') {
          if($student_results[$rc][$rcs]['id'] === '-') {
            $student_results[$rc][$rcs][$junle_results_1 . $rc] = '-';
            $student_results[$rc][$rcs][$junle_results_2 . $rc] = '-';
          } else if(empty($student_results[$rc][$rcs][$junle_results_1 . $rc])) {
            $student_results[$rc][$rcs][$junle_results_1 . $rc] = $per_1;
            $student_results[$rc][$rcs][$junle_results_2 . $rc] = $per_2;
          }
        }
      }
    }
    return $student_results;
  }

  // 空の配列に'-'を挿入
  public function null_data_insert ($contents_result) {
    foreach($contents_result as $key_1 => $data_1) {
      foreach($data_1 as $key_2 => $data_2) {
        foreach($data_2 as $key_3 => $data_3) {
          if(empty($data_3)) {
            $contents_result[$key_1][$key_2][$key_3] = '-';
          }
        }
        $contents_results[] = $contents_result[$key_1][$key_2];
      }
    }
    return $contents_results;
  }

  // student_results_table - header 作成
  public function second_table_header ($contents_table, $s_count, $student_results, $select, $section) {
    //$hi = count($contents_table); *2019/05/29
    $hi = is_array($contents_table) ? count($contents_table) : 0;

    switch ($select) {
      case 'all':
        $result_title_1  = '結果';
        $select_result_1 = 'all_result';
        $result_title_2  = '日時';
        $select_result_2 = 'result_datetime';
        break;
      case 'contents':
        $result_title_1  = '視聴割合';
        $select_result_1 = 'proportion';
        $result_title_2  = '視聴日時';
        $select_result_2 = 'play_start_datetime';
        break;
      case 'quiz':
        $result_title_1  = '結果';
        $select_result_1 = 'total_score';
        $result_title_2  = '解答日時';
        $select_result_2 = 'register_datetime';
        break;
      case 'questionnaire':
        $result_title_1  = '解答';
        $select_result_1 = 'result';
        $result_title_2  = '解答日時';
        $select_result_2 = 'answer_datetime';
        break;
      case 'report':
        $result_title_1  = '解答';
        $select_result_1 = 'result';
        $result_title_2  = '解答日時';
        $select_result_2 = 'answer_datetime';
        break;
      default:
        # code...
        break;
    }

    for($is = 0; $is < $s_count; ++$is) {
      $contents_table[$hi+$is]['title'] = $section[$is];
      //$contents_table[$h1+$is]['columnVertAlign'] = 'bottom';
      //$contents_table[$h1+$is]['width'] = 100;
      /*if($select == 'quiz') {
        $contents_table[$hi+$is]['title']
      }*/
      // 結果と日時の2項目が決め打ちの為、count2まで
      for($jr = 0; $jr < 2; ++$jr) {
        if($jr == 0) {
          $contents_table[$hi+$is]['columns'][$jr]['title']  = $result_title_1;
          $contents_table[$hi+$is]['columns'][$jr]['field']  = $select_result_1 . $is;
          $contents_table[$hi+$is]['columns'][$jr]['sorter'] = 'number';
          $contents_table[$hi+$is]['columns'][$jr]['align']  = 'center';
          //$contents_table[$hi+$is]['columns'][$jr]['width']  = 50;
          $contents_table[$hi+$is]['columns'][$jr]['width']  = 60;
        } else {
          $contents_table[$hi+$is]['columns'][$jr]['title']  = $result_title_2;
          $contents_table[$hi+$is]['columns'][$jr]['field']  = $select_result_2 . $is;
          $contents_table[$hi+$is]['columns'][$jr]['sorter'] = 'number';
          $contents_table[$hi+$is]['columns'][$jr]['align']  = 'center';
          //$contents_table[$hi+$is]['columns'][$jr]['width']  = 50;
          $contents_table[$hi+$is]['columns'][$jr]['width']  = 200;
        }
      }
    }
    return $contents_table;
  }

  public function all_results_table_create ($sc_results, $cc_result, $sq_results, $qc_results,
    $sqer_results, $qerc_results, $sr_results, $rc_results, $section_count, $student_name) {

    $all_contents_results = [];
    $c_results = [];
    $select = '';
    //$all = 1;
    // 動画結果取得$all_contents_results
    $select = 'contents';
    $all_results = [];

    $c_results = $this->student_conents_results_table($sc_results, $cc_result, $select);
    $c_results = $this->student_conents_results_table_judge($c_results, $select);
    $c_results = $this->null_data_insert($c_results);

    if(!empty($c_results)) {
      foreach($c_results as $key1 => $value1) {
        //$c_results[$key1]['type'] = '動画';
        $all_contents_results[$key1]['name'] = $student_name;
        $all_contents_results[$key1]['id'] = $c_results[$key1]['id'];
        $all_contents_results[$key1]['answer_id'] = '';
        $all_contents_results[$key1]['type'] = '動画';
        $all_contents_results[$key1]['section'] = $c_results[$key1]['section'];
        $all_contents_results[$key1]['title'] = $c_results[$key1]['title'];

        for($ss = 0; $ss < $section_count; ++$ss) {
          $all_contents_results[$key1]['all_result' . $ss] = $c_results[$key1]['proportion' . $ss];
          $all_contents_results[$key1]['result_datetime' . $ss] = $c_results[$key1]['play_start_datetime' . $ss];
        }
      }
    }

    $all_results[] = $all_contents_results;
    //test結果取得
    $q_results = [];
    $all_quiz_results = [];
    $select = 'quiz';
    $q_results = $this->student_conents_results_table($sq_results, $qc_results, $select);
    $q_results = $this->student_conents_results_table_judge ($q_results, $select);
    $q_results = $this->null_data_insert($q_results);

    if(!empty($q_results)) {
      foreach($q_results as $key2 => $value2) {
        //$q_results[$key2]['type'] = 'テスト';
        $all_quiz_results[$key2]['name'] = $student_name;
        $all_quiz_results[$key2]['id'] = $q_results[$key2]['id'];
        $all_quiz_results[$key2]['answer_id'] = $q_results[$key2]['answer_id'];
        $all_quiz_results[$key2]['type'] = 'テスト';
        $all_quiz_results[$key2]['section'] = $q_results[$key2]['section'];
        $all_quiz_results[$key2]['title'] = $q_results[$key2]['title'];

        for($ss = 0; $ss < $section_count; ++$ss) {
          $all_quiz_results[$key2]['all_result' . $ss] = $q_results[$key2]['total_score' . $ss];
          $all_quiz_results[$key2]['result_datetime' . $ss] = $q_results[$key2]['register_datetime' . $ss];
        }
      }
    }

    $all_results[] = $all_quiz_results;
    // アンケート結果取得
    $qe_results = [];
    $all_questionnaire_results = [];
    $select = 'questionnaire';
    $qe_results = $this->student_conents_results_table($sqer_results, $qerc_results, $select);
    $qe_results = $this->student_conents_results_table_judge ($qe_results, $select);
    $qe_results = $this->null_data_insert($qe_results);

    if(!empty($qe_results)) {
      foreach($qe_results as $key3 => $value3) {
        //$qe_results[$key3]['type'] = 'アンケート';
        $all_questionnaire_results[$key3]['name'] = $student_name;
        $all_questionnaire_results[$key3]['id'] = $qe_results[$key3]['id'];
        $all_questionnaire_results[$key3]['answer_id'] = '';
        $all_questionnaire_results[$key3]['type'] = 'アンケート';
        $all_questionnaire_results[$key3]['section'] = $qe_results[$key3]['section'];
        $all_questionnaire_results[$key3]['title'] = $qe_results[$key3]['title'];

        for($ss = 0; $ss < $section_count; ++$ss) {
          $all_questionnaire_results[$key3]['all_result' . $ss] = $qe_results[$key3]['result' . $ss];
          $all_questionnaire_results[$key3]['result_datetime' . $ss] = $qe_results[$key3]['answer_datetime' . $ss];
        }
      }
    }

    $all_results[] = $all_questionnaire_results;
    // レポート結果取得
    $re_results = [];
    $all_report_results = [];
    $select = 'report';
    $re_results = $this->student_conents_results_table($sr_results, $rc_results, $select);
    $re_results = $this->student_conents_results_table_judge ($re_results, $select);
    $re_results = $this->null_data_insert($re_results);

    if(!empty($re_results)) {
      foreach($re_results as $key4 => $value4) {
        //$re_results[$key4]['type'] = 'レポート';
        $all_report_results[$key4]['name'] = $student_name;
        $all_report_results[$key4]['id'] = $re_results[$key4]['id'];
        $all_report_results[$key4]['answer_id'] = '';
        $all_report_results[$key4]['type'] = 'レポート';
        $all_report_results[$key4]['section'] = $re_results[$key4]['section'];
        $all_report_results[$key4]['title'] = $re_results[$key4]['title'];

        for($ss = 0; $ss < $section_count; ++$ss) {
          $all_report_results[$key4]['all_result' . $ss] = $re_results[$key4]['result' . $ss];
          $all_report_results[$key4]['result_datetime' . $ss] = $re_results[$key4]['answer_datetime' . $ss];
        }
      }
    }

    $all_results[] = $all_report_results;
    $return_results = [];

    foreach($all_results as $all => $data1) {
      foreach($data1 as $all_2 => $data2) {
        $return_results[] = $all_results[$all][$all_2];
      }
    }
    return $return_results;
  }
}
?>
