<?php

require dirname(filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . '/config.php';
$curl = new Curl( $url );

$data[ 'student_id' ]      = filter_input( INPUT_POST, "student_id" );
$data[ 'contents_number' ] = filter_input( INPUT_POST, "contents_number" );
$data[ 'player3_code' ]    = filter_input( INPUT_POST, "player3_code" );

// player視聴ログ初期データINSERT
$curl_contents = array('repository' => 'ContentsLogRepository', 'method' => 'firstLogInsert', 'params' => $data );
$log = $curl->send( $curl_contents );
// INSERTしたIDを取得
$curl_get_id = array( 'repository' => 'ContentsLogRepository', 'method' => 'getHistoryId', 'params' => $data );
$history_id = $curl->send( $curl_get_id );

function json_safe_encode($data){
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
// 1秒おきにreached_frameを格納
//return $history_id;
print json_safe_encode( $history_id );
exit();

?>
