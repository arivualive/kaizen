<?php

require dirname(filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . '/config.php';
$curl = new Curl( $url );

$data[ 'student_id' ]      = filter_input( INPUT_POST, "student_id" );
$data[ 'contents_number' ] = filter_input( INPUT_POST, "contents_number" );
// reached_frame UPDATE
//var_export( $data );
$start_frames = [];
$curl_frame = array( 'repository'=> 'ContentsLogRepository', 'method'=> 'getReachedFrame', 'params'=> $data );
$start_frame = $curl->send( $curl_frame );

function json_safe_encode($data){
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

if ( !empty ( $start_frame ) ) {
    // 続きから再生
    //var_export( $start_frame );
    $start_frames[ 'reached_frame_flg' ] = true;
    $start_frames[ 'reached_frame' ]  = $start_frame[ 0 ][ 'reached_frame' ] / 10;
    $start_frames[ 'registered_datetime' ] = $start_frame[ 0 ][ 'registered_datetime' ];
    print json_safe_encode( $start_frames );
    exit();
} else {
    // 初回視聴
    //var_export( $start_frame );
    $start_frames[ 'reached_frame_flg' ] = false;
    $start_frames[ 'reached_frame' ] = 0;
    print json_safe_encode( $start_frames );
    exit();
}


?>
