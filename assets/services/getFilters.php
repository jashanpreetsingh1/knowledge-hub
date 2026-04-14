<?php

// getFilters.php
// Returns courses list and unique meta keywords
// =duplicate keywords in dropdown

require_once("./inc/connect_pdo.php");

// All courses
$courses = [];
$query = "SELECT course_id, name FROM course ORDER BY name";
foreach ($dbo->query($query) as $row) {
	$course["course_id"] = stripslashes($row["course_id"]);
	$course["name"]      = stripslashes($row["name"]);
	$courses[] = $course;
}

// Unique meta keywords -  DISTINCT query
$keywords = [];
$query = "SELECT DISTINCT meta.name
FROM topics, meta
WHERE topics.id = meta.topic_id
ORDER BY meta.name";
foreach ($dbo->query($query) as $row) {
	$keywords[] = stripslashes($row["name"]);
}

$data["courses"]  = $courses;
$data["keywords"] = $keywords;

$data = json_encode($data);

header("Content-Type: application/json");
print($data);

?>
