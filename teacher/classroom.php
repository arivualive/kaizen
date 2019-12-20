<?php

require_once '../../config.php';

require_once $class_dir . '/curlClass.php';
require_once $model_dir . '/classroom.php';
require_once $model_dir . '/course.php';

$classroom = new Classroom();
$curl = new Curl($url);

$data = $classroom->findId(4);
$result = $curl->send($data);

print_r($result);
echo "<br/>";

print_r($_SESSION);
echo "<br/>";

$sql = "select school_id, classroom_name from tbl_classroom where school_id = :school_id";
$school_id = 2;
$params = array(":school_id" => $school_id);
$data = $classroom->fetchAll($sql, $params);
$result = $curl->send($data);
print_r($result);

/*
$data = $classroom->findAll();
$result = $curl->send($data);
print_r($result);
echo "<br/>";

$course = new Course();
$data = $course->findAll();
$result = $curl->send($data);
print_r($result);
echo "<br/>";

$data = $classroom->findId(6);
$result = $curl->send($data);
print_r($result);
echo "<br/>";

print_r($_SESSION);
 */
