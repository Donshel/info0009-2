<?php

include('../connect.php');

foreach ($_GET as $key => $value) {
	$$key = $value;
}

// Unspecified
if (!isset($type)) {
	echo "Unspecified article type.";
	exit;
} else {
	if ($type != 'articles_conferences' && $type != 'articles_journaux') {
		echo "Wrong article type.";
		exit;
	}
}

if ($type == 'articles_conferences') {
	if (isset($conference)) {
		$annee_conference = substr($conference, -4);
		$nom_conference = substr($conference, 0, -5);
	} else {
		echo "Unspecified conference.";
		exit;
	}
}

$response = $connect->query('DESC articles;');
$desc = $response->fetchAll(PDO::FETCH_NUM);

foreach ($desc as $attr) {
	if (!isset($$attr[0])) {
		echo "Unspecified attribute ".$attr[0].".";
		exit;
	}
}

$response = $connect->query("DESC $type;");
$desc = $response->fetchAll(PDO::FETCH_NUM);

foreach ($desc as $attr) {
	if (!isset($$attr[0])) {
		echo "Unspecified attribute ".$attr[0].".";
		exit;
	}
}

// Incompatibility

if ($type == 'articles_conferences') {
	if (substr($date_publication, 0, 4) != $annee_conference) {
		echo "Incompatibility : date_publication isn't within annee_conference.";
		exit;
	}
} else {
	if ($pg_debut > $pg_fin) {
		echo "Incompatibility : pg_debut is greater than pg_fin.";
		exit;
	}

	if ($pg_debut > $pg_fin) {
		echo "Incompatibility : pg_debut is greater than pg_fin.";
		exit;
	}
}

// Conference/revue existence
if ($type == 'articles_conferences') {
	$sql = "SELECT * FROM conferences WHERE nom = '$nom_conference' AND annee = '$annee_conference';";

	$response = $connect->query($sql);
	$table = $response->fetchAll(PDO::FETCH_NUM);

	if (sizeof($table) == 0) {
		echo "Unexisting conference.";
		exit;
	}
} else {
	$sql = "SELECT * FROM revues WHERE nom = '$nom_revue';";

	$response = $connect->query($sql);
	$table = $response->fetchAll(PDO::FETCH_NUM);

	if (sizeof($table) == 0) {
		echo "Unexisting revue.";
		exit;
	}
}

// LOCK
$sql = "LOCK TABLES articles WRITE, $type WRITE;";

if (!$connect->query($sql)) {
	echo "Error while locking the tables.";
	exit;
}

// Article existence
$sql = "SELECT * FROM articles WHERE url = '$url' OR doi = '$doi';";

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

if (sizeof($table) != 0) {
	echo "Already existing article.";
	$connect->query("ROLLBACK; UNLOCK TABLES;");
	exit;
}

// INSERT
$sql = "INSERT INTO articles (url, doi, titre, date_publication, matricule_auteur) VALUES ('$url', $doi, '$titre', '$date_publication', $matricule_auteur);";

if ($type == 'articles_conferences') {
	$sql .= "INSERT INTO $type (url, presentation, nom_conference, annee_conference) VALUES ('$url', '$presentation', '$nom_conference', $annee_conference);";
} else {
	$sql .= "INSERT INTO $type (url, pg_debut, pg_fin, nom_revue, n_journal) VALUES ('$url', $pg_debut, $pg_fin, '$nom_revue', $n_journal);";
}

if (!$connect->query($sql)) {
	echo "Error while inserting.";
	$connect->query("ROLLBACK; UNLOCK TABLES;");
	exit;
}

// COMMIT

$connect->query("COMMIT; UNLOCK TABLES;");

echo "success";

?>