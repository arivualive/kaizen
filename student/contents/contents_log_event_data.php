<?php
require_once "../../config.php";
require_once 'class.tbwp3_access.php';

$curl = new Curl( $url );
$tbwp3 = new Tbwp3Access();

header('content-type: application/json; charset=utf-8');

$data[ 'student_id' ]      = filter_input( INPUT_POST, "student_id" );
$data[ 'contents_number' ] = filter_input( INPUT_POST, "contents_number" );
$data[ 'player3_code' ]    = filter_input( INPUT_POST, "player3_code" );
$data[ 'history_number']   = filter_input( INPUT_POST, "history_number" );


// tbwp3　ログ用にデータ加工
$after_json = $tbwp3->jsonEncodeLogCode( $data[ 'player3_code' ] );
// tbwp3サーバーからログを取得
$returned_json = $tbwp3->returnedLogData( $after_json );
// DB event table 用のデータを取得
$log_data = $tbwp3->secondDataCreate( $returned_json );
//debug_r($log_data);
$resr = 0;
$repeat_results = [];
// repeat 再生用の視聴割合
for ( $re = 0; $re < count( $log_data ); $re++ ) {
  for ( $res = 0; $res < count( $log_data[ $re ][ 'reasons' ] ); $res++ ) {
    if ( $log_data[ $re ][ 'reasons' ][ $res ] !== 12 ) {
      $repeat_results[ $resr ][] = $log_data[ $re ];
    } else {
      $resr++;
      $repeat_results[ $resr ][] = $log_data[ $re ];

    }
  }
}
/*
debug_r($repeat_results);
exit();
*/
$log_data = array_merge( $log_data, array( 'history_id' => $data[ 'history_number' ] ));
//debug_r( $log_data );
//exit();*/
// event格納時間をDBへ
$curl_upload = array( 'repository' => 'ContentsLogRepository',
  'method' => 'player3HistoryUploadTime',
  'params' => $log_data
);

$hoge = $curl->send( $curl_upload );
/*debug_r( $hoge );
exit();
*/
// seek_bar event の前か後ろかの判別
$log_data = $tbwp3->seekBarEventJudge ( $log_data );
//debug_r( $log_data );
//exit();
// blocks データが取得済がどうか
$blocks = array( 'repository' => 'ContentsLogRepository',
  'method' => 'player3BlocksDataSql',
  'params' => $data
);

$blocks_data = $curl->send( $blocks );

// block-data が未取得のコンテンツの時だけDBへ格納する
if ( empty( $blocks_data ) ) {

  $block = $tbwp3->contentsBlocks ( $returned_json, $data );
  // 2019/6/03 count関数対策
  $block_counts = 0;
  if(is_countable($block[ 'block' ])){
    $block_counts = count( $block[ 'block' ] );
  }
  //$block_counts = count( $block[ 'block' ] );

  foreach ( $block[ 'block' ] as $key => $blocks ) {

    $insert_block = array( 'repository' => 'ContentsLogRepository',
    'method' => 'player3BlocksDataInsertSql',
    'params' => array(
       'contents_id'=> $block[ 'contents_number' ]
      ,'first_frame'=> $blocks[ 'first_frame' ] * 10
      ,'final_frame'=> $blocks[ 'final_frame' ] * 10
    ) );
    $insert_blocks = $curl->send( $insert_block );
  }
}

// event log insert
$curl_getinfo = array( 'repository' => 'ContentsLogRepository',
  'method' => 'player3EventLogInsert',
  'params' => $log_data
);

$event_log = $curl->send( $curl_getinfo );

// 視聴割合log_data
$contents_log = array( 'repository' => 'ContentsLogRepository',
  'method' => 'proportionData',
  'params' => $data
);

$proportion_data = $curl->send( $contents_log );
/*
debug_r( $proportion_data );
exit();
*/
$proportion = [];

foreach  ( $proportion_data as $key => $value ) {
  $proportion[ $key ] = $value[ 'history_id' ];
}

//debug_r( $proportion );

$unique = array_unique( $proportion );

//debug_r( $unique );

$align_unique[ 'history_id' ] = array_values( $unique );
//debug_r( $align_unique );
//debug_r( count( $align_unique[ 'history_id']));

// 視聴割合計算
////////////////// ここから新手法  ////////////////////////////////////////////

// 対象コンテンツを100分割
$division = $tbwp3->frameLengthDivision( $returned_json );

//debug_r( $division );

$log_count = array( 'repository' => 'ContentsLogRepository',
  'method' => 'logData',
  'params' => $align_unique
);

$log = $curl->send( $log_count );

// 取得したlogの中にAUTO_REPEATがあった時の処理 ///////////////////////
// 2019/6/03 count関数対策
$c_log = 0;
if(is_countable($log)) {
  $c_log = count( $log );
}
//$c_log = count( $log );

$repeat_log = [];

for ( $l = 0; $l < $c_log; ++$l ) {
  // 2019/6/03 count関数対策
  $cs_log = 0;
  if(is_countable($log[ $l ])){
    $cs_log = count( $log[ $l ] );
  }
  //$cs_log = count( $log[ $l ] );

  for ( $cs = 0; $cs < $cs_log; ++$cs ) {
    if ( $log[ $l ][ $cs ][ 'event_reason_id' ] === '12' ) {

      $repeat_log[] = $log[ $l ];
      unset( $log[ $l ] );
      break;
    }
  }
}

$log = array_values( $log );

// AUTO_REPEAT が入っているlogをprogress_time昇順に並び替え
foreach ( $repeat_log as $key => $log_1 ) {
  foreach ( $log_1 as $key_1 => $log_2 ) {
    $log_s[ $key ][ $key_1 ] = $log_2[ 'progress_time' ];
  }
  array_multisort( $log_s[ $key ], SORT_ASC, $repeat_log[ $key ] );
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
  $de = 0;
  $repeat_number = 0;

  for ( $rls = 0; $rls < $rls_count; ++$rls ) {

    if ( $repeat_log[ $rl ][ $rls ][ 'event_reason_id' ] !== '12' ) {

      $repeat_log_devision[ $rl ][ $de ][] = $repeat_log[ $rl ][ $rls ];
      //$de++;:

    } else {
      // 2019/6/03 count関数対策
      $la = 0;
      if(is_countable($repeat_log_devision[ $rl ][ $de ])){
        $la = count( $repeat_log_devision[ $rl ][ $de ] ) - 1;
      }
      //$la = count( $repeat_log_devision[ $rl ][ $de ] ) - 1;

      $repeat_log_devision[ $rl ][ $de ][ $la ][ 'position' ] = $repeat_log[ $rl ][ $rls ][ 'duration' ];
      $de++;
      $repeat_log_devision[ $rl ][ $de ][] = $repeat_log[ $rl ][ $rls ];
      //$repeat_number = $rls;
      //break;
    }
  }
}
/*
debug_r( $repeat_log_devision );
exit();
*/
// 分割したログをposition昇順で並び変え ////////////////////////////////////
foreach ( $repeat_log_devision as $k_log1 => $v_log1 ) {
  foreach ( $v_log1 as $k_log2 => $v_log2 ) {
    foreach ( $v_log2 as $k_log3 => $v_log3 ) {
      $r_log[ $k_log1 ][ $k_log2 ][ $k_log3 ] = $v_log3[ 'position' ];
    }
    array_multisort( $r_log[ $k_log1 ][ $k_log2 ], SORT_ASC, $repeat_log_devision[ $k_log1 ][ $k_log2 ] );
  }
}

// 分割したログをそれ以外のログと結合 ///////////////////////////////////////
foreach ( $repeat_log_devision as $log_1 => $data1 ) {
  foreach ( $data1 as $log_2 => $data2 ) {
    $log[] = $repeat_log_devision[ $log_1 ][ $log_2 ];
  }
}
/*
debug_r( "AUTO_REPEAT" );
debug_r( $log );
exit();
*/
/////////////////////////////////////////////////////////////////////
/*
debug_r( $log );
exit();
*/
$log_test = $tbwp3->positionArray( $log );
// 視聴割合計算
$log_judge = $tbwp3->newProportionJudgement( $log, $division );
/*
debug_r( $log_judge );
exit();
*/
//$align_unique[ 'proportion' ] = $log_judge[ 'proportion' ];
$align_unique[ 'proportion' ] = round( $log_judge[ 'judge' ][ 'proportion' ], -1 );

//debug_r( $align_unique );
//exit();
// この講座の視聴クリア割合を取得
$log_count[ 'method' ] = 'getSubjectSectionContentProportion';
$log_count[ 'params' ] = $data;
$clear_proportions = [];
$clear_proportions = $curl->send( $log_count );

//debug_r($log_count);
//debug_r( $log );
//debug_r( $clear_proportions );

//exit();

// 視聴割合をクリアしているか判定
if ( $log[ 0 ][ 0 ][ 'proportion_flg' ] == 0 ) {
  //debug_r( "proportion_flg" );
  if ( $clear_proportions[ 0 ][ 'proportion' ] <= $align_unique[ 'proportion' ] ) {
      //debug_r( "proportion_flg-1" );
      $align_unique[ 'proportion_flg' ] = 1;
  } else {
    //debug_r( "proportion_flg-0" );
      $align_unique[ 'proportion_flg' ] = 0;
  }
} else {
  $align_unique[ 'proportion_flg' ] = 1;
}

$proportion_update = array( 'repository' => 'ContentsLogRepository',
  'method' => 'proportionUpadate',
  'params' => $align_unique
);

$curl->send( $proportion_update );

// block dataを取得 ( 今はまだ使わない )
//$blocks = $tbwp3->blocksData( $returned_json );

function json_safe_encode( $data ){
    return json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
}

unset( $align_unique[ 'history_id' ] );

print json_safe_encode( $align_unique[ 'proportion' ] );
exit();


?>
