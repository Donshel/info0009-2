<?php

header('Content-Type: application/json');

include('../connect.php');

if (isset($_GET['table'])) {
	$tablename = $_GET['table'];
	unset($_GET['table']);
} else {
	echo "Unspecified table.";
	exit;
}

$response = $connect->query('DESC '.$tablename.';');
$desc = $response->fetchAll(PDO::FETCH_NUM);

$sql = "SELECT * FROM ".$tablename." WHERE ";

foreach ($desc as $attr) {
	if (isset($_GET[$attr[0]])) {
		if (!strpos($attr[1], 'int')) {
			$sql .= $attr[0]." LIKE '%".$_GET[$attr[0]]."%'";
		} else {
			$sql .= $attr[0]." = ".$_GET[$attr[0]];
		}
		$sql .= " AND ";
	}
}

$sql .= "1;";

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

echo json_encode(array($desc, $table));

?>