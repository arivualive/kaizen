<?php
require_once "../../config.php";
require_once 'class.tbwp3_access.php';

$curl = new Curl( $url );
$tbwp3 = new Tbwp3Access();

$data[ 'student_id' ]      = filter_input( INPUT_POST, "student_id" );
$data[ 'contents_number' ] = filter_input( INPUT_POST, "contents_number" );
$data[ 'player3_code' ]    = "312e91d66cd18c0266029119c04dd977ac87dcf68b996785";//filter_input( INPUT_POST, "player3_code" );
$data[ 'history_number']   = filter_input( INPUT_POST, "history_number" );

function debug_r( $data ) {

  echo "<pre>";
  print_r( $data );
  echo "</pre>";
}
/*
debug_r( $data );
exit();
*/
//$data[ 'player3_code'] = "2bf6a4fcb4accbaa0f5fa0f979729b0c3968b411fe56dcb9";

// tbwp3　ログ用にデータ加工
$after_json = $tbwp3->jsonEncodeLogCode( $data[ 'player3_code' ] );

// tbwp3サーバーからログを取得
$returned_json = $tbwp3->returnedLogData( $after_json );
// DB event table 用のデータを取得
$log_data = $tbwp3->secondDataCreate( $returned_json );
debug_r($log_data);
exit();

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

//debug_r( $proportion_data );

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

//debug_r( $log );
//exit();
$log_test = $tbwp3->positionArray( $log );
// 視聴割合計算
$log_judge = $tbwp3->newProportionJudgement( $log, $division );
/*
$test = print round( 91,-1)."\n"; //
debug_r( $test );
$test = print round( 95,-1)."\n"; //
debug_r( $test );
$test = print round( 1,-1)."\n"; //
debug_r( $test );
debug_r( $log_judge );
exit();
*/
//$align_unique[ 'proportion' ] = $log_judge[ 'proportion' ];
$align_unique[ 'proportion' ] = round( $log_judge[ 'proportion' ], -1 );

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

//debug_r( $align_unique );

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
