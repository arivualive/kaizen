<?php
require '../config.php';

$module = filter_input(INPUT_POST, "module");
$repository = filter_input(INPUT_POST, "repository");
$method = filter_input(INPUT_POST, "method");
$params = filter_input(INPUT_POST, "params", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if ($module == 'default') {
    $obj = new $repository();
    echo json_encode($obj->$method($params));
}

?>
