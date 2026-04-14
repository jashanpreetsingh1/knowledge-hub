<?php


// Knowledge Hub Admin - View & Edit All PDFs
// : New PDF uploads happen on Topics page
// This tab = view all PDFs, edit title/topic, delete, open

date_default_timezone_set('America/Toronto');
require_once("./inc/connect_pdo.php");

// SECTION 1: RECEIVE POST
$pdf_id        = $_POST["pdf_id"];
$pdf_id_update = $_POST["pdf_id_update"];
$check         = $_POST["check"];
$pdf_title     = $_POST["pdf_title"];
$pdf_topic_id  = $_POST["pdf_topic_id"];

// SECTION 2: PROCESS ACTIONS

if ($check == "Cancel") { $pdf_id = ""; }

if ($check == "Update") {
	$pdf_id       = $pdf_id_update;
	$pdf_title    = addslashes($pdf_title);
	$pdf_topic_id = addslashes($pdf_topic_id);

	if (!empty($_FILES["pdf_file"]["name"])) {
		// Replace the PDF file
		$fileName  = $_FILES["pdf_file"]["name"];
		$fileName  = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9.]+/', '_', $fileName)));
		$temporary = explode(".", $_FILES["pdf_file"]["name"]);
		$file_ext  = strtolower(end($temporary));

		if ($file_ext == "pdf") {
			$upload_dir = "../uploads/pdfs/";
			if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
			$target    = $upload_dir . $fileName;
			$file_path = "uploads/pdfs/" . $fileName;
			if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target)) {
				$query = "UPDATE pdfs
				SET title     = '$pdf_title',
				file_path     = '$file_path',
				topic_id      = '$pdf_topic_id'
				WHERE id      = '$pdf_id'";
				$dbo->query($query);
				$save_success = "PDF updated with new file.";
			} else {
				$pdf_error = "File upload failed. Check permissions on uploads/pdfs/";
			}
		} else {
			$pdf_error = "Only .pdf files are allowed.";
		}
	} else {
		// Update title and topic only
		$query = "UPDATE pdfs
		SET title    = '$pdf_title',
		topic_id     = '$pdf_topic_id'
		WHERE id     = '$pdf_id'";
		$dbo->query($query);
		$save_success = "PDF details updated.";
	}
}

if ($check == "Delete PDF") {
	$del_id = addslashes($pdf_id_update);
	$dbo->query("DELETE FROM pdfs WHERE id = '$del_id'");
	$pdf_id       = "";
	$save_success = "PDF deleted.";
}

// SECTION 3: PRINT HTML

print("<!doctype html>
<html lang=\"en\">
<head>
<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<title>Admin All PDFs - Knowledge Hub</title>
<link href=\"css/main.css\" rel=\"stylesheet\">
</head>
<body>
<main>

<header>
<div class=\"header__title\">Knowledge Hub</div>
<div class=\"header__name\">Faculty Administration</div>
<ul>
<li><a href=\"./courses.php\">Courses</a></li>
<li><a href=\"./index.php\">Topics</a></li>
<li><a href=\"./pdfs.php\" class=\"tab-active\">&#9656; All PDFs</a></li>
</ul>
</header>

<form action=\"pdfs.php\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000000\">
<article>

<section>
<h1>All PDFs <span>Select to edit &mdash; to upload a new PDF use the Topics tab</span></h1>
<p class=\"article__reference\">
&#8594; To upload a new PDF: <a href=\"./index.php\">go to Topics tab</a>,
select a topic, scroll down to the Upload PDF section.
</p>
");

if (!empty($save_success)) {
	print("<p class=\"success-msg\">&#10003; $save_success</p>\n");
}
if (!empty($pdf_error)) {
	print("<p class=\"error-msg\">&#9888; $pdf_error</p>\n");
}

print("<p>
<label>Select PDF to Edit:</label>
<select name=\"pdf_id\" onchange=\"this.form.submit()\">
<option value=\"\">-- Choose a PDF --</option>
");

$query = "SELECT pdfs.id, pdfs.title, topics.title AS topic_title
FROM pdfs
JOIN topics ON pdfs.topic_id = topics.id
ORDER BY topics.title, pdfs.title";
foreach ($dbo->query($query) as $row) {
	$id  = $row["id"];
	$t   = stripslashes($row["title"]);
	$tt  = stripslashes($row["topic_title"]);
	$sel = ($pdf_id == $id) ? " selected=\"selected\"" : "";
	print("<option value=\"$id\"$sel>$t &mdash; $tt</option>\n");
}

print("</select>
</p>
</section>
");

// Edit form for selected PDF
if ($pdf_id) {
	$query = "SELECT id, title, file_path, topic_id FROM pdfs WHERE id = '$pdf_id'";
	foreach ($dbo->query($query) as $row) {
		$p_title    = stripslashes($row["title"]);
		$p_filepath = stripslashes($row["file_path"]);
		$p_topic_id = stripslashes($row["topic_id"]);
	}

	$topic_select = "";
	$query = "SELECT id, title FROM topics ORDER BY title";
	foreach ($dbo->query($query) as $row) {
		$tid   = $row["id"];
		$tname = stripslashes($row["title"]);
		$sel   = ($p_topic_id == $tid) ? " selected=\"selected\"" : "";
		$topic_select .= "<option value=\"$tid\"$sel>$tname</option>\n";
	}

	print("<section>
<h1>Edit PDF <span>ID: $pdf_id</span></h1>
<p>
<label for=\"pdf_title\">PDF Title:</label>
<input type=\"text\" id=\"pdf_title\" name=\"pdf_title\" value=\"$p_title\" maxlength=\"150\" autofocus>
</p>
<p>
<label for=\"pdf_topic_id\">Assigned to Topic:</label>
<select id=\"pdf_topic_id\" name=\"pdf_topic_id\">
$topic_select
</select>
</p>
<p>
<label for=\"pdf_file\">Replace PDF File <small class=\"article__reference\">(optional &mdash; leave blank to keep current)</small>:</label>
<input type=\"file\" id=\"pdf_file\" name=\"pdf_file\" accept=\".pdf\">
<br><small class=\"article__reference\">Current: $p_filepath</small>
</p>
<p>
<input type=\"hidden\" name=\"pdf_id_update\" value=\"$pdf_id\">
<input type=\"submit\" name=\"check\" value=\"Update\" class=\"primaryButton\">
&nbsp;
<input type=\"reset\" value=\"Reset\" class=\"secondaryButton\">
&nbsp;
<input type=\"submit\" name=\"check\" value=\"Cancel\" class=\"secondaryButton\">
&nbsp;
<input type=\"submit\" name=\"check\" value=\"Delete PDF\" class=\"dangerButton\"
	onclick=\"return confirm('Delete this PDF record?')\">
</p>
</section>

<section>
<h1>Preview <span>opens in new tab</span></h1>
<p>
<a href=\"../$p_filepath\" target=\"_blank\" class=\"primaryButton\"
	style=\"display:inline-block;padding:5px 16px;text-decoration:none;\">
&#128196; Open PDF</a>
&nbsp;
<span class=\"article__reference\">$p_filepath</span>
</p>
</section>
");
}

// Full PDFs table
print("<section>
<h1>All PDFs in Database</h1>
<table>
<tr>
<th>ID</th>
<th>PDF Title</th>
<th>Topic</th>
<th>Open</th>
<th>Uploaded</th>
</tr>
");

$query = "SELECT pdfs.id, pdfs.title, pdfs.file_path, pdfs.created_at, topics.title AS topic_title
FROM pdfs
JOIN topics ON pdfs.topic_id = topics.id
ORDER BY topics.title, pdfs.title";
foreach ($dbo->query($query) as $row) {
	$id = $row["id"];
	$t  = stripslashes($row["title"]);
	$fp = stripslashes($row["file_path"]);
	$ca = stripslashes($row["created_at"]);
	$tt = stripslashes($row["topic_title"]);
	print("<tr>
<td>$id</td>
<td>$t</td>
<td>$tt</td>
<td><a href=\"../$fp\" target=\"_blank\">&#128196; View</a></td>
<td><small>$ca</small></td>
</tr>\n");
}

print("</table>
</section>

</article>
</form>

<footer>
<p>Knowledge Hub Administration &mdash; Durham College &mdash; Web Design 3</p>
<p><a href=\"../index.html\">&larr; View Student Site</a></p>
</footer>

</main>
</body>
</html>
");
?>
