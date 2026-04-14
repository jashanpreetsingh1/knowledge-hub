<?php

// search.php
// Searches TWO tables - professor showed this in class
// 1. topic titles   2. meta keywords
// Returns combined unique list, no duplicates

require_once("./inc/connect_pdo.php");

$search_text = $_POST["search_text"];

$error_id      = 0;
$error_message = "";

$results = [];

if (!empty($search_text)) {

	// SEARCH 1: Search topic titles
	$query = "SELECT topics.id, topics.title, topics.description, topics.week
	FROM topics
	WHERE topics.title LIKE '%$search_text%'
	ORDER BY topics.title
	LIMIT 0,20";

	foreach ($dbo->query($query) as $row) {
		$topic_id = stripslashes($row["id"]);

		$result["id"]          = $topic_id;
		$result["title"]       = stripslashes($row["title"]);
		$result["description"] = stripslashes($row["description"]);
		$result["week"]        = stripslashes($row["week"]);

		$results["$topic_id"] = $result;
	}

	// SEARCH 2: Search meta keyword names - professor's join query
	$query = "SELECT topics.id, topics.title, topics.description, topics.week
	FROM topics, meta
	WHERE topics.id = meta.topic_id
	AND meta.name LIKE '%$search_text%'
	ORDER BY topics.title
	LIMIT 0,20";

	foreach ($dbo->query($query) as $row) {
		$topic_id = stripslashes($row["id"]);

		$result["id"]          = $topic_id;
		$result["title"]       = stripslashes($row["title"]);
		$result["description"] = stripslashes($row["description"]);
		$result["week"]        = stripslashes($row["week"]);

		// same key = no duplicate if already found above
		$results["$topic_id"] = $result;
	}

	ksort($results);
}

if (empty($results)) {
	$error_id      = 1;
	$error_message = "Nothing Found";
}

if (empty($search_text)) {
	unset($results);
}

$error["error_id"]      = $error_id;
$error["error_message"] = $error_message;

$data["error"]   = $error;
$data["results"] = $results;

$data = json_encode($data);

header("Content-Type: application/json");
print($data);

?>
