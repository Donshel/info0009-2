SELECT matricule, nom, prenom
FROM
(SELECT matricule, nom, prenom FROM auteurs) AS T1
NATURAL JOIN
(
	SELECT *
	FROM
	(
		SELECT DISTINCT matricule, matricule_auteur
		FROM
		(SELECT nom_conference, annee_conference, matricule FROM participations) AS T2
		LEFT JOIN
		(
			SELECT nom_conference, annee_conference, matricule_auteur
			FROM
			(SELECT url, nom_conference, annee_conference FROM articles_conferences) AS T3
			NATURAL JOIN
			(SELECT url, matricule_auteur FROM articles) AS T4
		) AS T5
		ON T2.matricule = T5.matricule_auteur AND T2.nom_conference = T5.nom_conference AND T2.annee_conference = T5.annee_conference
	) AS T6
	GROUP BY matricule
	HAVING COUNT(*) = 1 AND matricule_auteur IS NOT NULL
) AS T7;