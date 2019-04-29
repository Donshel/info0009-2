SELECT sujet, COUNT(*) AS popularity
FROM sujets_articles
NATURAL JOIN
(SELECT url, nom_conference, annee_conference FROM articles_conferences) AS T1
NATURAL JOIN
(
	SELECT nom_conference, annee_conference, COUNT(*) AS popularity
	FROM participations
	WHERE annee_conference >= 2012
	GROUP BY nom_conference, annee_conference
	ORDER BY popularity DESC
	LIMIT 5
) AS T2
GROUP BY sujet
ORDER BY popularity DESC;