<?php
require_once "../../config.php";
require_once 'class.tbwp3_access.php';

$curl = new Curl( $url );
$tbwp3 = new Tbwp3Access();

$student_data[ 'student_id' ]      = filter_input( INPUT_POST, "student_id" );
$student_data[ 'contents_number' ] = filter_input( INPUT_POST, "contents_number" );
$student_data[ 'send_file' ]       = filter_input( INPUT_POST, "send_file" );

// contents_id student_id を元にplayer3_codeを取得しにいく
function debug_r ( $data ) {

  echo "<pre>";
  print_r( $data );
  echo "</pre>";
  //exit();
}

$data = array(
  'repository' => 'ContentsLogRepository',
  'method' => 'findStudentLog',
  'params' => $student_data
);

$log_data = $curl->send( $data );

/*
foreach ( $log_data as $log ) {

  if ( $log[ 'proportion' ] < 100 ) {
    $flg = "true";
  }
}
*/
//if ( $flg == "true" ) {

  $log_datas       = [];
  $event_id        = [];
  $proportion_data = [];

  $rser = 0;

  for ( $log = 0; $log < count( $log_data ); $log++ ) {

    $after_json = $tbwp3->jsonEncodeLogCode ( $log_data[ $log ][ 'player_code' ] );

    $returned_json = $tbwp3->returnedLogData ( $after_json );

    if ( $returned_json[ 'datas' ][ 0 ][ 'message' ] !== 'このcodeでのリクエストはログモードではありません。' ) {

      $repeat_results = [];
      $log_datas[ $log ] = $tbwp3->secondDataCreate ( $returned_json );

      // repeat再生用の視聴割合
      for ( $re = 0; $re < count( $log_datas[ $log ] ); ++$re ) {
        for( $res = 0; $res < count( $log_datas[ $log ][ $re ][ 'reasons' ] ); ++$res ) {
          if ( $log_datas[ $log ][ $re ][ 'reasons' ][ $res ] !== 12 ) {
            $repeat_results[ $rser ][] = $log_datas[ $log ][ $re ];
          } else {
            $rser++;
            $repeat_results[ $rser ][] = $log_datas[ $log ][ $re ];
          }
        }
      }

      $log_datas[ $log ] = array_merge( $log_datas[ $log ], array( 'history_id' => $log_data[ $log ][ 'history_id' ] ));
      // 再取得日時を格納
      $data[ 'method' ] = 'player3HistoryUploadTime';
      $data[ 'params' ] = $log_data[ $log ];
      $hoge = $curl->send( $data );
      // seek_bar event 前か後ろかの判断
      $log_datas[ $log ] = $tbwp3->seekBarEventJudge ( $log_datas[ $log ] );
      // event_id を取得
      $data[ 'method' ] = 'getEventidEventLogData';
      $data[ 'params' ] = $log_datas[ $log ];

      $event_id[ $log ] = $curl->send( $data );

      // log-insert前にeventとreason-eventをDELETE
      $data[ 'method' ] = 'deleteReasonValueData';
      $data[ 'params' ] = $event_id[ $log ];

      $hoge = $curl->send( $data );
      // event_value delete
      $data[ 'method' ] = 'deleteEventLogData';
      $data[ 'params' ] = $log_datas[ $log ];
      $hoges = $curl->send( $data );

      $data[ 'method' ] = 'player3EventLogInsert';
      $data[ 'params' ] = $log_datas[ $log ];
      $hoge = $curl->send( $data );

    }

  }

  // ここから再INSERTしたログの視聴割合計算
  foreach ( $log_datas as $value) {

    $data[ 'method' ] = 'getContentsReacquire';
    $data[ 'params' ] = $value;
    $contents_data[] = $curl->send( $data );

  }

  foreach ( $contents_data as $key => $c_id ) {
    $contents_ids[ $key ] = $c_id[ 'school_contents_id' ];
  }

  $cid_unique = array_unique( $contents_ids );
  $align_unique[ 'school_contents_id' ] = array_values( $cid_unique );
  $align_unique[ 'student_id' ] = $student_data[ 'student_id' ];

  $data[ 'method' ] = 'reaquireProportionData';
  $data[ 'params' ] = $align_unique;
  $history_data = $curl->send( $data );

  foreach ( $history_data as $hs => $history ) {

    foreach ( $history as $key => $value ) {
      $history_id[ $hs ][ $key ] = $value[ 'history_id' ];
    }
  }

  for ( $h = 0; $h < count( $history_id ); $h++ ) {

    $hid_unique[ $h ] = array_unique( $history_id[ $h ] );
    $history_unique[ $h ][ 'history_id' ] = array_values( $hid_unique[ $h ] ); // $align_unique

    $data[ 'method' ] = 'logData';
    $data[ 'params' ] = $history_unique[ $h ];
    $history_reaquire_data[] = $curl->send( $data ); // $log

  }

  $repeat_log = [];
  // 対象コンテンツを100分割
  for ( $s = 0;  $s < count( $history_reaquire_data ); ++$s ) {
    for ( $ss = 0; $ss < count( $history_reaquire_data[ $s ] ); ++$ss ) {
      $division[ $s ] = $tbwp3->reacquireFrameLengthDivision ( $history_reaquire_data[ $s ][ $ss ] );

      // 取得したlogの中にAUTO_REPEATがあった時の処理
      // 2019/6/03 count関数対策
      $ls_count = 0;
      if(is_countable($history_reaquire_data[ $s ][ $ss ])){
        $ls_count = count( $history_reaquire_data[ $s ][ $ss ] );
      }
      //$ls_count = count( $history_reaquire_data[ $s ][ $ss ] );

      for ( $ls = 0; $ls < $ls_count; ++$ls ) {

        if ( $history_reaquire_data[ $s ][ $ss ][ $ls ][ 'event_reason_id' ] == '12' ) {
          $repeat_log[ $s ][] = $history_reaquire_data[ $s ][ $ss ];
          //debug_r( $history_reaquire_data[ $s ][ $ss ][ $ls ][ 'event_reason_id' ] );
          unset( $history_reaquire_data[ $s ][ $ss ] );
          //$history_reaquire_data[ $s ][] = array_values( $history_reaquire_data[ $s ]);
          break;
        }

      }
      //debug_r( $history_reaquire_data );
      $history_reaquire_data[ $s ][ $ss ] = array_values( $history_reaquire_data[ $s ][ $ss ] );
    }

    // AUTO_REPEAT が入っているlogをprogress_time昇順に並び替え
    foreach ( $repeat_log as $key => $log_1 ) {
      foreach ( $log_1 as $key_1 => $log_2 ) {
        foreach ( $log_2 as $key_2 => $log_3 ) {
          $log_s[ $key ][ $key_1 ][ $key_2 ] = $log_3[ 'progress_time' ];
        }
        array_multisort( $log_s[ $key ][ $key_1 ], SORT_ASC, $repeat_log[ $key ][ $key_1 ] );

      }
    }

    // AUTO_REPEATが入るpositionでログを分割 ////////////////////////////////////
    // 2019/6/03 count関数対策
    $rl_count = 0;
    if(is_countable($repeat_log)){
      $rl_count = count( $repeat_log );
    }
    //$rl_count = count( $repeat_log );
    $repeat_log_devision = [];

    for ( $rl = 0; $rl < $rl_count; ++$rl ) {
      // 2019/6/03 count関数対策
      $rls_count = 0;
      if(is_countable($repeat_log[ $rl ])){
        $rls_count = count( $repeat_log[ $rl ] );
      }
      //$rls_count = count( $repeat_log[ $rl ] );

      for ( $rls = 0; $rls < $rls_count; ++$rls ) {

        $de = 0;
        $repeat_number = 0;
        // 2019/6/03 count関数対策
        $rlss_count = 0;
        if(is_countable($repeat_log[ $rl ][ $rls ])){
          $rlss_count = count( $repeat_log[ $rl ][ $rls ] );
        }
        //$rlss_count = count( $repeat_log[ $rl ][ $rls ] );

        for ( $rlss = 0; $rlss < $rlss_count; ++$rlss ) {

          if ( $repeat_log[ $rl ][ $rls ][ $rlss ][ 'event_reason_id' ] !== '12' ) {
            $repeat_log_devision[ $rls ][ $de ][] = $repeat_log[ $rl ][ $rls ][ $rlss ];

          } else {
            // 2019/6/03 count関数対策
            $la = 0;
            if(is_countable($repeat_log_devision[ $rls ][ $de ])){
              $la = count( $repeat_log_devision[ $rls ][ $de ] ) - 1;
            }
            //$la = count( $repeat_log_devision[ $rls ][ $de ] ) - 1;

            $repeat_log_devision[ $rls ][ $de ][ $la ][ 'position' ] = $repeat_log[ $rl ][ $rls ][ $rlss ][ 'duration' ];
            $de++;
            $repeat_log_devision[ $rls ][ $de ][] = $repeat_log[ $rl ][ $rls ][ $rlss ];
          }

        }
      }
    }

    // 分割したログをposition昇順で並び変え ////////////////////////////////////
    foreach ( $repeat_log_devision as $k_log1 => $v_log1 ) {
      foreach ( $v_log1 as $k_log2 => $v_log2 ) {
        foreach ( $v_log2 as $k_log3 => $v_log3 ) {
          $r_log[ $k_log1 ][ $k_log2 ][ $k_log3 ] = $v_log3[ 'position' ];
        }
        array_multisort( $r_log[ $k_log1 ][ $k_log2 ], SORT_ASC, $repeat_log_devision[ $k_log1 ][ $k_log2 ] );

      }
    }

    foreach ( $history_reaquire_data as $rk => $value_1 ) {
      foreach ( $value_1 as $rk_1 => $value_2) {
        if ( empty( $history_reaquire_data[ $rk ][ $rk_1 ] )) {
          unset( $history_reaquire_data[ $rk ][ $rk_1 ] );
        }
      }
    }

    foreach ( $history_reaquire_data as $key => $data1 ) {
      foreach ( $data1 as $key1 => $data2 ) {
        $history_datas[] = $history_reaquire_data[ $key ][ $key1 ];
      }
    }

    // 分割したログをそれ以外のログと結合 ///////////////////////////////////////
    foreach ( $repeat_log_devision as $log_1 => $data1 ) {
      foreach ( $data1 as $log_2 => $data2 ) {
        $history_datas[] = $repeat_log_devision[ $log_1 ][ $log_2 ];
      }
    }

   // 視聴割合計算
   $reacquire_log_judge[ $s ] = $tbwp3->newProportionJudgement ( $history_datas, $division[ $s ] );

   // 視聴割合を四捨五入
   $history_unique[ 'proportion' ] = round( $reacquire_log_judge[ 0 ][ 'judge' ][ 'proportion' ], -1 );

   // このコンテンツの視聴割合を取得
   $data[ 'method' ] = 'getSubjectSectionContentProportion';
   $data[ 'params' ] = $student_data;
   $clear_proportion = $curl->send( $data );

   // 視聴割合をクリアしているか判定
   if ( $history_datas[ 0 ][ 0 ][ 'proportion_flg' ] == 0 ) {
     if ( $clear_proportion[ 0 ][ 'proportion' ] <= $history_unique[ 'proportion' ] ) {
       $history_unique[ 'proportion_flg' ] = 1;
     } else {
       $history_unique[ 'proportion_flg' ] = 0;
     }
   } else {
     $history_unique[ 'proportion_flg' ] = 1;
   }

   foreach ( $history_unique[ 0 ][ 'history_id' ] as $key => $value ) {
     $history_unique[ 'history_id' ][] = $value;
   }

   $history_unique[ 'reacquire_data_flg' ] = 1;
   unset( $history_unique[ 0 ] );

   // 計算した視聴割合をDBへ
   $data[ 'method' ] = 'reacquireProportionUpadate';
   $data[ 'params' ] = $history_unique;
   $test = $curl->send( $data );


  }

//}
  echo json_encode( $history_unique [ 'proportion' ] );
  exit();
?>
