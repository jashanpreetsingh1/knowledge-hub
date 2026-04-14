<?php

// getPDFs.php
// Receives topic_id via POST
// Returns all PDFs for that topic

require_once("./inc/connect_pdo.php");

$topic_id = $_POST["topic_id"];

if (empty($topic_id)) {
	$topic_id = "1";
}

$pdfs = [];

$query = "SELECT pdfs.id, pdfs.title, pdfs.file_path, pdfs.created_at
FROM pdfs
WHERE pdfs.topic_id = '$topic_id'
ORDER BY pdfs.title";

foreach ($dbo->query($query) as $row) {

	$pdf["id"]         = stripslashes($row["id"]);
	$pdf["title"]      = stripslashes($row["title"]);
	$pdf["file_path"]  = stripslashes($row["file_path"]);
	$pdf["created_at"] = stripslashes($row["created_at"]);

	$pdfs[] = $pdf;
}

$data = json_encode($pdfs);

header("Content-Type: application/json");
print($data);

?>
