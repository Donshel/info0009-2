SELECT matricule, nom, prenom
FROM auteurs
NATURAL JOIN
(
	SELECT *
	FROM
	(
		SELECT DISTINCT matricule, matricule_auteur
		FROM participations AS T1
		LEFT JOIN
		(
			SELECT nom_conference, annee_conference, matricule_auteur
			FROM articles_conferences
			NATURAL JOIN articles
		) AS T2
		ON T1.matricule = T2.matricule_auteur AND T1.nom_conference = T2.nom_conference AND T1.annee_conference = T2.annee_conference
	) AS T3
	GROUP BY matricule
	HAVING COUNT(*) = 1 AND matricule_auteur IS NOT NULL
) AS T4;