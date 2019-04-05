<?php

$dbhost = 'ms800.montefiore.ulg.ac.be';
$dbname = 'group2';
$dbusername = 'group2';
$dbpassword = 'NgcHJ1T9uq';

try {
	$connect = new PDO('mysql:host='.$dbhost.'; dbname='.$dbname, $dbusername, $dbpassword);
} catch (PDOException $e) {
	echo 'Error while connecting to the database.';
	exit;
}

?>