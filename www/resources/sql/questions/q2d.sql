SELECT matricule, nom, prenom
FROM
(
	SELECT matricule_auteur, matricule_participant AS matricule
	FROM
	(
		SELECT DISTINCT T3.matricule AS matricule_auteur, T4.matricule AS matricule_participant
		FROM
		(
			SELECT * 
			FROM
			(
				SELECT url, nom_conference, annee_conference
				FROM articles_conferences
			) AS T1
			NATURAL JOIN
			(
				SELECT url, matricule_auteur AS matricule
				FROM articles
			) AS T2
		) AS T3
		RIGHT JOIN
		(
			SELECT nom_conference, annee_conference, matricule
			FROM participations
		) AS T4
		ON T3.matricule = T4.matricule AND T3.nom_conference = T4.nom_conference AND T3.annee_conference = T4.annee_conference
	) AS T5
	GROUP BY matricule_participant
	HAVING COUNT(*) = 1 AND matricule_auteur IS NOT NULL
) AS T6
NATURAL JOIN
auteurs;