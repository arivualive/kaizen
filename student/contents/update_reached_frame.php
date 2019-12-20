<?php

require_once "../../config.php";
$curl = new Curl( $url );

$data[ 'reached_frame' ]  = filter_input( INPUT_POST, "reached_frame" );
$data[ 'duration' ]       = filter_input( INPUT_POST, "duration" );
$data[ 'history_number' ] = filter_input( INPUT_POST, "history_number" );

// reached_frame UPDATE
$curl_frame = array('repository' => 'ContentsLogRepository', 'method' => 'updateReachedFrame', 'params' => $data );
$r_frame = $curl->send( $curl_frame );
/*
function json_safe_encode($data){
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
*/
?>
