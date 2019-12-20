<?php

require_once '../config.php';

$module  = filter_input(INPUT_POST, "module");
$method = filter_input(INPUT_POST, "method");
$sql    = filter_input(INPUT_POST, "sql");
$params = filter_input(INPUT_POST, "params", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if ($method == '') {
    echo "メソッド名が不明です";
    exit();
}

if ($module == 'sql') {
    $model = new pdoBase;
    echo json_encode($model->$method($sql, $params));
}
