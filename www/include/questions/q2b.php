<?php

header('Content-Type: application/json');

include('../connect.php');

if (isset($_GET['matricule'])) {
	$matricule = $_GET['matricule'];
} else {
	echo "Unspecified matricule.";
	exit;
}

$sql =
"
SELECT titre, date_publication, type, url
FROM
	(
		SELECT url, titre, date_publication
		FROM articles WHERE matricule_auteur = $matricule
	) AS T1
	NATURAL JOIN
	(
		SELECT url, 'journal' AS type
		FROM articles_journaux
		UNION
		SELECT url, 'conference' AS type
		FROM articles_conferences
	) AS T2
ORDER BY date_publication DESC;
";

$response = $connect->query($sql);
$table = $response->fetchAll(PDO::FETCH_NUM);

foreach ($table as &$value) {
	$url = $value[3];

	$sql =
"
SELECT CONCAT(' ', LEFT(prenom, 1), '.', nom)
FROM auteurs
NATURAL JOIN
(
	SELECT matricule
	FROM seconds_auteurs
	WHERE url = '$url'
) AS T1;
";

	$response = $connect->query($sql);
	$seconds_auteurs = $response->fetchAll(PDO::FETCH_NUM);

	$value[3] = $seconds_auteurs;
}

echo json_encode($table);

?>