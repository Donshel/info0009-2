SELECT sujet, COUNT(*) AS popularity
FROM sujets_articles
NATURAL JOIN
articles_conferences
NATURAL JOIN
(
	SELECT nom_conference, annee_conference, COUNT(*) AS popularity
	FROM participations
	WHERE annee_conference >= 2012
	GROUP BY nom_conference, annee_conference
	ORDER BY popularity DESC
	LIMIT 5
) AS T1
GROUP BY sujet
ORDER BY popularity DESC;