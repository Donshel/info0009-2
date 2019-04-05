<?php

if (isset($_GET['file'])) {
	$file = '../../resources/sql/'.$_GET['file'].'.sql';
	if (!file_exists($file)) {
		echo "Wrong file name.";
		exit;
	}
} else {
	echo "Unspecified file name.";
	exit;
}

header('Content-Type: application/json');

include('../connect.php');

$sql = file_get_contents($file);

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

echo json_encode($table);

?>