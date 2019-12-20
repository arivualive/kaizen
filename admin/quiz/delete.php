<?php
require_once "../../config.php";

$curl = new Curl($url);

$quiz_id = filter_input(INPUT_GET, 'id');

$QuizObj = new Quiz($quiz_id, $curl);

$result = false;
$result = $QuizObj->deleteQuizId($quiz_id);

if ($result) {
    $QuizObj->redirect('index.php');
}

