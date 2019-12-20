<?php

require_once "../../config.php";
require_once 'class.history_data_create.php';

$curl = new Curl( $url );
$history_data = new HistoryDataCreate();



$json_string = file_get_contents( 'php://input' );
$json_obj = json_decode( $json_string, true );

$table_header = [];


foreach ( ( array )$json_obj[ 'table_data' ] as $key => $title ) {

  $table_header[ 'csv_header' ][] = $title[ 'title' ];

}

$section_count = 0;

if ( $json_obj[ 'category_number' ] !== "0" ) {
  // 2019/5/31 count関数対策
  if(is_countable($table_header[ 'csv_header' ])){
    $section_count = count( $table_header[ 'csv_header' ] ) -3;
  }
  //$section_count = count( $table_header[ 'csv_header' ] ) -3;
} else {
  // 2019/5/31 count関数対策
  if(is_countable($table_header[ 'csv_header' ])){
    $section_count = count( $table_header[ 'csv_header' ] ) -4;
  }
  //$section_count = count( $table_header[ 'csv_header' ] ) -4;
}

// 2019/5/31 count関数対策
$student_count = 0;
if(is_countable($json_obj[ 'student_results' ])){
  $student_count = count( $json_obj[ 'student_results' ] );
}
//$student_count = count( $json_obj[ 'student_results' ] );

$title = "";

switch ( $json_obj[ 'category_number' ] ) {

  case '0':
    # code...
    break;

  case '1':
    $title = 'contents_name';
    $result = 'contents_proportion';
    break;

  case '2':
    $title = 'title';
    $result = 'quiz_result';
    break;

  case '3':
    $title = 'title';
    $result = 'questionnaire_result';
    break;

  case '4':
    $title = 'title';
    $result = 'report_result';
    break;

  default:
    # code...
    break;
}

//$section_count = count( $table_header );
for ( $r = 0; $r < $student_count; $r++ ) {

  $table_header[ 'student_results' ][ $r ][] = $json_obj[ 'student_results' ][ $r ][ 'subject_section_name' ];
  $table_header[ 'student_results' ][ $r ][] = $json_obj[ 'student_results' ][ $r ][ $title ];
  $table_header[ 'student_results' ][ $r ][] = $json_obj[ 'student_results' ][ $r ][ 'datetime' ];

  for ( $sc = 0; $sc < $section_count; $sc++ ) {

    $table_header[ 'student_results' ][ $r ][] = $json_obj[ 'student_results' ][ $r ][ $result.$sc ];

  }

}




function json_safe_encode( $data ){
    return json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
}


echo json_safe_encode( $table_header );
exit();
?>

r
