<?php

// getTopics.php
// Returns all topics joined with course name
// Also returns keywords (meta) for each topic as array
// Called by main.js on page load via AJAX POST

require_once("./inc/connect_pdo.php");

$topics = [];

$query = "SELECT topics.id, topics.title, topics.description, topics.week, course.name AS course_name
FROM topics
JOIN course ON topics.course_id = course.course_id
ORDER BY topics.title";

foreach ($dbo->query($query) as $row) {

	$topic_id = $row["id"];

	$topic["id"]          = stripslashes($row["id"]);
	$topic["title"]       = stripslashes($row["title"]);
	$topic["description"] = stripslashes($row["description"]);
	$topic["week"]        = stripslashes($row["week"]);
	$topic["course_name"] = stripslashes($row["course_name"]);

	// Get keywords for this topic - professor's meta bubbles on card
	$keywords = [];
	$kq = "SELECT name FROM meta WHERE topic_id = '$topic_id' ORDER BY name";
	foreach ($dbo->query($kq) as $krow) {
		$keywords[] = stripslashes($krow["name"]);
	}
	$topic["keywords"] = $keywords;

	$topics[] = $topic;
}

$data = json_encode($topics);

header("Content-Type: application/json");
print($data);

?>