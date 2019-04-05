SELECT nom, prenom, matricule
FROM
	(
		SELECT T4.matricule AS matricule
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
			LEFT JOIN
			(
				SELECT nom_conference, annee_conference, matricule
				FROM participations
			) AS T4
			ON T3.matricule = T4.matricule AND T3.nom_conference = T4.nom_conference AND T3.annee_conference = T4.annee_conference
			GROUP BY T4.matricule
	) AS T5
NATURAL JOIN
auteurs;