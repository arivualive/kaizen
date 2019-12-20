<?php
$absolute = '/home/kaizen/web/'; // テスト環境
//$absolute = 'https://kaizen2.net/';
$url = 'https://kaizen2.net/core/module/';

require ( $absolute . 'htdocs/class/Curl.php' );
require_once ( $absolute . 'htdocs/admin/history/class.tbwp3_access.php');

/*
require ( $absolute . '/class/Curl.php' );
require_once ( $absolute . '/admin/history/class.tbwp3_access.php');
*/
$curl = new Curl( $url );
$tbwp3 = new Tbwp3Access();
// contents_id student_id を元にplayer3_codeを取得しにいく
/*
function debug_r ( $data ) {

  echo "<pre>";
  print_r( $data );
  echo "</pre>";
  //exit();
}
*/

$student_data[ 'proportion' ] = 100;

$data = array(
  'repository' => 'ContentsLogRepository',
  'method' => 'reacquireHistory',
  'params' => $student_data
);

$log_student_data = $curl->send( $data );
$student_data[ 'proportion' ] = "";

$reaquire_history_id = [];


foreach ( $log_student_data as $s_key => $s_data ) {

  $reaquire_history_id[][ 'history_id' ] = $s_data[ 'history_id' ];

  $student_data[ 'student_id' ] = $s_data[ 'student_id' ];
  $student_data[ 'contents_number' ] = $s_data[ 'school_contents_id' ];
  $log_data = [];

  $data = array(
    'repository' => 'ContentsLogRepository',
    'method' => 'findStudentLog',
    'params' => $student_data
  );

  $log_data = $curl->send( $data );

    $log_datas       = [];
    $event_id        = [];
    $proportion_data = [];
    //$after_json      = [];
    $returned_json   = [];

    $rser = 0;
    // 2019/6/03 count関数対策
    $log_data_count = 0;
    if(is_countable($log_data)){
      $log_data_count = count( $log_data );
    }
    //$log_data_count = count( $log_data );

    for ( $log = 0; $log < $log_data_count; $log++ ) {

      //$reaquire_history_id[] = $log_data[ $log ][ 'history_id' ];

      $after_json = $tbwp3->jsonEncodeLogCode ( $log_data[ $log ][ 'player_code' ] );
      $returned_json = $tbwp3->returnedLogData ( $after_json );
      //if ( $returned_json[ 'datas' ][ 0 ][ 'message' ] !== 'このcodeでのリクエストはログモードではありません。' ) {
      $repeat_results = [];

      if ( !empty( $returned_json[ 'datas' ][ 0 ][ 'logs' ] ) ) {

        $log_datas[ $log ] = $tbwp3->secondDataCreate ( $returned_json );

        // repeat再生用の視聴割合
        // 2019/6/03 count関数対策
        $count_log_datas = 0;
        if(is_countable($log_datas[ $log ])){
          $count_log_datas = count( $log_datas[ $log ] );
        }
        //$count_log_datas = count( $log_datas[ $log ] );

        for ( $re = 0; $re < $count_log_datas; ++$re ) {
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

        $hoge2 = [];
        $data[ 'method' ] = 'player3EventLogInsert';
        $data[ 'params' ] = $log_datas[ $log ];
        $hoge2 = $curl->send( $data );

        if ( empty( $hoge2 ) ) {

          $student_data[ 'null_flg' ] = 8;
          $student_data[ 'history_id' ] = $log_data[ $log ][ 'history_id' ];
          $data[ 'method' ] = 'nullLogDataFlg';
          $data[ 'params' ] = $student_data;
          $hoge = $curl->send( $data );

        }

      } else {
        $student_data[ 'null_flg' ] = 9;
        $student_data[ 'history_id' ] = $log_data[ $log ][ 'history_id' ];
        $data[ 'method' ] = 'nullLogDataFlg';
        $data[ 'params' ] = $student_data;
        $hoge = $curl->send( $data );
      }

    }

  }

  /*
  debug_r( 'reaquire_history_id' );
  debug_r( $reaquire_history_id );
  exit();
  */

    // ここから再INSERTしたログの視聴割合計算


    $contents_data = [];

    foreach ( $reaquire_history_id as $value ) {

      $data[ 'method' ] = 'getContentsReacquire';
      $data[ 'params' ] = $value;
      $contents_data[]  = $curl->send( $data );
    }

    //
    /*
    debug_r( "上のほうのdata8559");
    debug_r( $data );
    debug_r( "上のほうのdata終わり
    */

  /*  debug_r( 'contents_data' );
    debug_r( $contents_data );*/

    // 同じstudent_id,contents_idをまとめる
    $implode_history = [];
    $implode_contentsID = [];

    foreach ( $contents_data as $key =>  $h_data ) {
      $implode_history[] = $h_data[ 'school_contents_id' ].','.$h_data[ 'student_id' ];
    }

    $implode_history = array_values( array_unique( $implode_history ) );


    $hid_unique            = [];
    //$history_unique        = [];
    //$history_reaquire_data = [];

    foreach ( $implode_history as $keys => $id ) {

      $hid_unique            = [];
      $history_reaquire_data = [];

      $id_s[] = explode( ',', $id );
      //debug_r( $id_s );

      $align_unique[ 'school_contents_id' ] = $id_s[ $keys ][ 0 ];
      $align_unique[ 'student_id' ] = $id_s[ $keys ][ 1 ];

      //debug_r( $align_unique );

      $student_data[ 'contents_number' ] = $id_s[ $keys ][ 0 ];

      $data[ 'method' ] = 'reaquireProportionData2';
      $data[ 'params' ] = $align_unique;
      $history_data = $curl->send( $data );

      $history_id = [];

      foreach ( $history_data as $hs => $history ) {

        foreach ( $history as $key => $value ) {
          $history_id[ $hs ][ $key ] = $value[ 'history_id' ];
        }
      }

      $history_unique = [];

      for ( $h = 0; $h < count( $history_id ); $h++ ) {

        $hid_unique[ $h ] = array_unique( $history_id[ $h ] );
        $history_unique[ $h ][ 'history_id' ] = array_values( $hid_unique[ $h ] ); // $align_unique
        $data[ 'method' ] = 'logData';
        $data[ 'params' ] = $history_unique[ $h ];
        $history_reaquire_data[] = $curl->send( $data ); // $log
      }

      $repeat_log = [];
      $division = [];
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
          //debug_r( $ls_count );
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
       $reacquire_log_judge = [];
       $clear_proportion = [];



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

       $history_unique[ 'reacquire_data_flg' ] = 2;
       unset( $history_unique[ 0 ] );

       // 計算した視聴割合をDBへ
       $data[ 'method' ] = 'reacquireProportionUpadate';
       $data[ 'params' ] = $history_unique;
       $test = $curl->send( $data );

  //}

    }
  }


$count = 0;
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>視聴ログ再取得</title>
    <meta charset="utf-8">
  </head>
  <body>
    <table border="1" style="width:70%; text-align:center">
      <tr style="background-color:black; color:white">
        <th>No</th><th>history_id</th><th>school_contents_id</th><th>student_id</th>
      </tr>
      <?php foreach ((array) $log_student_data as $value ): ?>
        <?php $count++; ?>
        <?php echo "<tr style='background-color:white'><td>".$count."</td><td>".$value[ 'history_id' ]. "</td><td>".$value[ 'school_contents_id' ]."</td>
          <td>".$value[ 'student_id' ]."</td></tr>" ;?>
      <?php endforeach; ?>
    </table>
  </body>
</html>
