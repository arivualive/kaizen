<?php
    $data = mb_convert_encoding( $_POST[ "data" ], "sjis-win", "utf8" );
    $name = $_POST[ "name" ].".csv";

    $name = mb_convert_encoding( $name, "sjis", "utf-8" );

    header('Content-Type: application/octet-stream');   
    header('Content-Length: '.strlen( $data ) );
    header('Content-Disposition: attachment; filename="'. $name .'"');

    echo $data;
?>