<?php
require dirname(filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . '/config.php';
require_once 'class.tbwp3_access.php';

$curl = new Curl( $url );
$tbwp3 = new Tbwp3Access();

$data[ 'student_id' ]      = filter_input( INPUT_POST, "student_id" );
$data[ 'contents_number' ] = filter_input( INPUT_POST, "contents_number" );
$data[ 'player3_code' ]    = filter_input( INPUT_POST, "player3_code" );
$data[ 'history_number']   = filter_input( INPUT_POST, "history_number" );

//$data[ 'student_id' ] = 593;
//$data[ 'player3_code'] = "2bf6a4fcb4accbaa0f5fa0f979729b0c3968b411fe56dcb9";

// tbwp3　ログ用にデータ加工
$after_json = $tbwp3->jsonEncodeLogCode( $data[ 'player3_code' ] );
// tbwp3サーバーからログを取得
$returned_json = $tbwp3->returnedLogData( $after_json );

// DB event table 用のデータを取得
$log_data = $tbwp3->secondDataCreate( $returned_json );
$log_data = array_merge( $log_data, array( 'hisoty_id' => $data[ 'history_number' ] ));

// event格納時間をDBへ
$curl_upload = array( 'repository' => 'ContentsLogRepository',
  'method' => 'player3HistoryUploadTime',
  'params' => $log_data
);

$hoge = $curl->send( $curl_upload );

// seek_bar event の前か後ろかの判別
$log_data = $tbwp3->seekBarEventJudge ( $log_data );

// blocks データが取得済がどうか
$blocks = array( 'repository' => 'ContentsLogRepository',
  'method' => 'player3BlocksDataSql',
  'params' => $data
);

$blocks_data = $curl->send( $blocks );

// block-data が未取得のコンテンツの時だけDBへ格納する
if ( empty( $blocks_data ) ) {

  $block = $tbwp3->contentsBlocks ( $returned_json, $data );
  $block_counts = count( $block[ 'block' ] );

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

$proportion = [];

foreach  ( $proportion_data as $key => $value ) {
  $proportion[ $key ] = $value[ 'history_id' ];
}

$unique = array_unique( $proportion );
$align_unique[ 'hisoty_id' ] = array_values( $unique );

// 視聴割合計算
////////////////// ここから新手法  ////////////////////////////////////////////

// 対象コンテンツを100分割
$division = $tbwp3->frameLengthDivision( $returned_json );

$log_count = array( 'repository' => 'ContentsLogRepository',
  'method' => 'logData',
  'params' => $align_unique
);

$log = $curl->send( $log_count );

$log_test = $tbwp3->positionArray( $log );
// 視聴割合計算
$log_judge = $tbwp3->newProportionJudgement( $log, $division );

$align_unique[ 'proportion' ] = $log_judge[ 'proportion' ];

// この講座の視聴クリア割合を取得
$log_count[ 'method' ] = 'getSubjectSectionProportion';
$log_count[ 'params' ] = $data;
$clear_proportions = [];
$clear_proportions = $curl->send( $log_count );

// 視聴割合をクリアしているか判定
if ( $log[ 0 ][ 0 ][ 'proportion_flg' ] === 0 ) {

  if ( $clear_proportions[ 0 ][ 'proportion' ] <= $align_unique[ 'proportion' ] ) {
      $align_unique[ 'proportion_flg' ] = 1;
  } else {
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
/*
print json_safe_encode( $proportion_default );
>>>>>>> b5d70d9f28bcd8ec202b7f9ecf1974f9b64ba2a4
exit();
*/

?>
