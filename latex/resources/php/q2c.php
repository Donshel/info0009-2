<?php

include('../connect.php');

foreach ($_GET as $key => $value) {
	if (!empty($value)) {
		$$key = $value;
	}
}

// Spécifications nécessaires
if (!isset($type)) {
	echo "Type de l'article non spécifié.";
	exit;
} else {
	if ($type != 'articles_conferences' && $type != 'articles_journaux') {
		echo "Type de l'article incorrect.";
		exit;
	}
}

if ($type == 'articles_conferences') {
	if (isset($conference)) {
		$i = strrpos($conference, ',');
		$annee_conference = substr($conference, $i + 1);
		$nom_conference = substr($conference, 0, $i);
	} else {
		echo "Conférence non spécifiée.";
		exit;
	}
} else {
	if (isset($journal)) {
		$i = strrpos($journal, ',');
		$n_journal = substr($journal, $i + 1);
		$nom_revue = substr($journal, 0, $i);
	} else {
		echo "Journal non spécifié.";
		exit;
	}
}

$response = $connect->query("DESC articles;");
$desc = $response->fetchAll(PDO::FETCH_NUM);

foreach ($desc as $attr) {
	if (!isset($$attr[0])) {
		echo "Attribut ".$attr[0]." non spécifié.";
		exit;
	}
}

$response = $connect->query("DESC $type;");
$desc = $response->fetchAll(PDO::FETCH_NUM);

foreach ($desc as $attr) {
	if (!isset($$attr[0])) {
		echo "Attribut ".$attr[0]." non spécifié.";
		exit;
	}
}

// Compatibilité et existence

if ($type == 'articles_conferences') {
	if (substr($date_publication, 0, 4) != $annee_conference) {
		echo "Incompatibilité : La date de publication n'est pas dans l'année de la conférence ($annee_conference).";
		exit;
	}
} else {
	if ($pg_debut > $pg_fin) {
		echo "Incompatibilité : La page de début est plus grande que celle de fin.";
		exit;
	}
}

$sql = "SELECT matricule FROM auteurs WHERE matricule = '$matricule_auteur';";

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

if (sizeof($table) == 0) {
	echo "L'auteur n'existe pas.";
	exit;
}

if ($type == 'articles_conferences') {
	$sql = "SELECT * FROM conferences WHERE nom = '$nom_conference' AND annee = '$annee_conference';";

	$response = $connect->query($sql);
	$table = $response->fetchAll(PDO::FETCH_NUM);

	if (sizeof($table) == 0) {
		echo "La conférence n'existe pas.";
		exit;
	}
} else {
	$sql = "SELECT date_publication FROM articles NATURAL JOIN $type WHERE nom_revue = '$nom_revue' AND n_journal = '$n_journal' LIMIT 1;";

	$response = $connect->query($sql);
	$table = $response->fetchAll(PDO::FETCH_NUM);

	if (sizeof($table) == 0) {
		echo "Le journal n'existe pas.";
		exit;
	}

	$annee_journal = substr($table[0][0], 0, 4);

	if (substr($date_publication, 0, 4) != $annee_journal) {
		echo "Incompatibilité : La date de publication n'est pas de la même année que les autres publications du journal ($annee_journal).";
		exit;
	}
}

// LOCK
$sql = "LOCK TABLES articles WRITE, $type WRITE";

if (isset($sujets_articles)) {
	$sql .= ", sujets_articles WRITE";
}
if (isset($seconds_auteurs)) {
	$sql .= ", seconds_auteurs WRITE, auteurs READ";
}

$sql .= ";";

if (!$connect->query($sql)) {
	echo "Erreur pendant le verrouillement des tables.";
	exit;
}

// Article existence
$sql = "SELECT * FROM articles WHERE url = '$url' OR doi = '$doi';";

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

if (sizeof($table) != 0) {
	echo "Article déjà existant.";
	$connect->query("ROLLBACK; UNLOCK TABLES;");
	exit;
}

// INSERT
$sql = "INSERT INTO articles (url, doi, titre, date_publication, matricule_auteur) VALUES ('$url', $doi, '$titre', '$date_publication', $matricule_auteur);";

if (!$connect->query($sql)) {
	echo "Erreur pendant l'insertion dans la table 'articles'.";
	$connect->query("ROLLBACK; UNLOCK TABLES;");
	exit;
}

if ($type == 'articles_conferences') {
	$sql = "INSERT INTO $type (url, presentation, nom_conference, annee_conference) VALUES ('$url', '$presentation', '$nom_conference', $annee_conference);";
} else {
	$sql = "INSERT INTO $type (url, pg_debut, pg_fin, nom_revue, n_journal) VALUES ('$url', $pg_debut, $pg_fin, '$nom_revue', $n_journal);";
}

if (!$connect->query($sql)) {
	echo "Erreur pendant l'insertion dans la table '$type'.";
	$connect->query("ROLLBACK; UNLOCK TABLES;");
	exit;
}

if (isset($sujets_articles)) {
	$sql = "INSERT INTO sujets_articles (url, sujet) VALUES ";

	$sujets_articles = explode(",", $sujets_articles);
	foreach ($sujets_articles as &$sujet) {
		$sujet = trim($sujet);
		$sql .= "('$url', '$sujet'),";
	}

	$sql = substr($sql, 0, -1).";";

	if (!$connect->query($sql)) {
		echo "Error pendant l'insertion dans la table 'sujets_articles'.";
		$connect->query("ROLLBACK; UNLOCK TABLES;");
		exit;
	}
}

if (isset($seconds_auteurs)) {
	$sql = "INSERT INTO seconds_auteurs (url, matricule) VALUES ";

	$seconds_auteurs = explode(",", $seconds_auteurs);
	foreach ($seconds_auteurs as &$matricule) {
		$matricule = trim($matricule);

		if ($matricule_auteur != $matricule) {
			$sub_sql = "SELECT matricule FROM auteurs WHERE matricule = $matricule;";

			$response = $connect->query($sub_sql);
			$table = $response->fetchAll(PDO::FETCH_NUM);

			if (sizeof($table) != 0) {
				$sql .= "('$url', $matricule),";
			}
		}
	}

	$sql = substr($sql, 0, -1).";";

	if (!$connect->query($sql)) {
		echo "Erreur pendant l'insertion dans la table 'seconds_auteurs'.";
		$connect->query("ROLLBACK; UNLOCK TABLES;");
		exit;
	}
}

// COMMIT

$connect->query("COMMIT; UNLOCK TABLES;");

echo "success";

?>