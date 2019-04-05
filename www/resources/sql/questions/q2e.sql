SELECT sujet, count(*) AS popularity
FROM
(
	SELECT sujet
	FROM
	(
		SELECT url
		FROM
		(
			SELECT nom_conference, annee_conference, count(*) AS popularity
			FROM participations
			WHERE annee_conference >= 2012
			GROUP BY nom_conference, annee_conference
			ORDER BY popularity DESC
			LIMIT 5
		) AS T1
		NATURAL JOIN
		(
			SELECT url, nom_conference, annee_conference
			FROM articles_conferences
		) AS T2
	) AS T3
	NATURAL JOIN
	sujets_articles
) AS T4
GROUP BY sujet
ORDER BY popularity DESC;