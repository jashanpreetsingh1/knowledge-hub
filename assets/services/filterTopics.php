<?php

// filterTopics.php
// Receives course_id, week, keyword via POST
// Returns filtered topics with keywords

require_once("./inc/connect_pdo.php");

$course_id = $_POST["course_id"];
$week      = $_POST["week"];
$keyword   = $_POST["keyword"];

$topics = [];

if (!empty($keyword)) {

	$keyword = addslashes($keyword);

	$query = "SELECT DISTINCT topics.id, topics.title, topics.description, topics.week, course.name AS course_name
	FROM topics
	JOIN course ON topics.course_id = course.course_id
	JOIN meta   ON topics.id = meta.topic_id
	WHERE meta.name = '$keyword'";

	if (!empty($course_id)) { $query .= " AND topics.course_id = '" . addslashes($course_id) . "'"; }
	if (!empty($week))      { $query .= " AND topics.week = '"      . addslashes($week)      . "'"; }

	$query .= " ORDER BY topics.title";

} else {

	$query = "SELECT topics.id, topics.title, topics.description, topics.week, course.name AS course_name
	FROM topics
	JOIN course ON topics.course_id = course.course_id
	WHERE 1=1";

	if (!empty($course_id)) { $query .= " AND topics.course_id = '" . addslashes($course_id) . "'"; }
	if (!empty($week))      { $query .= " AND topics.week = '"      . addslashes($week)      . "'"; }

	$query .= " ORDER BY topics.title";
}

foreach ($dbo->query($query) as $row) {

	$topic_id = $row["id"];

	$topic["id"]          = stripslashes($row["id"]);
	$topic["title"]       = stripslashes($row["title"]);
	$topic["description"] = stripslashes($row["description"]);
	$topic["week"]        = stripslashes($row["week"]);
	$topic["course_name"] = stripslashes($row["course_name"]);

	// Get keywords for this topic
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