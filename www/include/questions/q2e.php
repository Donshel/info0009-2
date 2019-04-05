<?php

header('Content-Type: application/json');

include('../connect.php');

$file = '../../resources/sql/questions/q2e.sql';
$sql = file_get_contents($file);

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

echo json_encode($table);

?>