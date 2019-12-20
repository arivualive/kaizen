<?php
header( 'Access-Control-Allow-Origin: https://www.thinkboard.jp' );
if( isset( $_POST[ "csv_name" ] ) === false ){
	exit( "export_csv_data_" . __LINE__ );
}
if( isset( $_POST[ "export_csv" ] ) === false ){
	exit( "export_csv_data_" . __LINE__ );
}
//var_dump( $_POST[ "student_csv" ] );
//exit();

	$file_name = $_POST[ "csv_name" ].".csv";
	$text_data = mb_convert_encoding( $_POST[ "export_csv" ], "sjis-win", "utf8" );

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false ) {
		$file_name = mb_convert_encoding( $file_name, "sjis", "utf-8" );
	}

	header( 'Content-Type: application/octet-stream-dummy' );
	header( 'Content-Disposition: attachment; filename="'. $file_name .'"');
	header( 'Content-Length: '.strlen( $text_data ) );

	echo $text_data;


?>