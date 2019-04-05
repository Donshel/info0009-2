<?php

include('connect.php');

$create = '../resources/sql/initialization/create.sql';
$delete = '../resources/sql/initialization/delete.sql';
$load = '../resources/sql/initialization/load.sql';

if (file_exists($create) && file_exists($delete) && file_exists($load)) {
	$sql = file_get_contents($create).file_get_contents($delete).file_get_contents($load);

	if (!$connect->query($sql)) {
		echo 'Something went wrong while initializing.';
	} else {
		echo 'Successful initialization.';
	}
} else {
	echo 'Missing initialization file(s).';
}

?>