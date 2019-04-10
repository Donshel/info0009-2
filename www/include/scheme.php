<?php

header('Content-Type: application/json');

include('connect.php');

$response = $connect->query("SHOW TABLES;");
$list = $response->fetchAll(PDO::FETCH_NUM);

$tables = array();

foreach ($list as $value) {
	$tablename = $value[0];

	$response = $connect->query("DESC $tablename;");
	$table = $response->fetchAll(PDO::FETCH_NUM);

	$attrs = array();

	foreach ($table as $attribute) {
		$attrs[] = $attribute[0];
	}

	$tables[$tablename] = $attrs;
}

echo json_encode($tables);

?>