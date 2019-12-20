<?php 
require_once "../../config.php";

$curl = new Curl($url);

$sort_nav = $_GET['sortitem'];

foreach ($sort_nav as $key => $value) {
    $data = array(
        'repository' => 'QuizQueryRepository',
        'method' => 'updateOrderQuery',
        'params' => array(
            'display_order' => $key,
            'query_id' => $value)
        );

    $result = $curl->send($data);
}

?>

