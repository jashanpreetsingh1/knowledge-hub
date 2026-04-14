<?php

// admin/index.php
// Knowledge Hub Admin - Topics, Keywords, PDFs

date_default_timezone_set('America/Toronto');
require_once("./inc/connect_pdo.php");

// 
// SECTION 1: RECEIVE ALL POST VALUES
// 's pattern - receive everything at top
// 

$topic_id        = $_POST["topic_id"];
$topic_id_update = $_POST["topic_id_update"];
$check           = $_POST["check"];

$title       = $_POST["title"];
$description = $_POST["description"];
$week        = $_POST["week"];
$course_id   = $_POST["course_id"];

$meta_name     = $_POST["meta_name"];
$meta_topic_id = $_POST["meta_topic_id"];
$delete_meta   = $_POST["delete_meta"];

$pdf_title    = $_POST["pdf_title"];
$pdf_topic_id = $_POST["pdf_topic_id"];
$delete_pdf   = $_POST["delete_pdf"];
$focus_target = "";  //  focus trick - set by keyword actions

// 
// SECTION 2: PROCESS ACTIONS

if ($check == "Cancel") {
	$topic_id = "";
}

// New Topic - blank form, no ID yet
if ($check == "New Topic") {
	$topic_id = -1;
}

// Save topic (Insert or Update)
if ($check == "Update" || $check == "Add Topic") {

	$topic_id    = $topic_id_update;
	$title       = addslashes($title);
	$description = addslashes($description);
	$week        = addslashes($week);
	$course_id   = addslashes($course_id);

	if ($topic_id_update == -1) {
		// INSERT new topic into database
		$query = "INSERT INTO topics
		SET title       = '$title',
		description     = '$description',
		week            = '$week',
		course_id       = '$course_id'";
		$dbo->query($query);
		$topic_id = $dbo->lastInsertId();
		$save_success = "Topic saved! You can now add keywords and upload PDFs below.";
	} else {
		// UPDATE existing topic
		$query = "UPDATE topics
		SET title       = '$title',
		description     = '$description',
		week            = '$week',
		course_id       = '$course_id'
		WHERE id        = '$topic_id'";
		$dbo->query($query);
		$save_success = "Topic updated successfully.";
	}
}

// Add keyword bubble to topic
if ($check == "Add Keyword") {
	$meta_name     = addslashes(strtolower(trim($meta_name)));
	$meta_topic_id = addslashes($meta_topic_id);
	if (!empty($meta_name) && !empty($meta_topic_id)) {
		$query = "INSERT INTO meta
		SET name     = '$meta_name',
		topic_id     = '$meta_topic_id'";
		$dbo->query($query);
		$topic_id     = $meta_topic_id;
		$focus_target = "keyword"; // re-focus keyword field
	}
}

// Delete keyword - 's array delete pattern
if (!is_null($delete_meta)) {
	foreach ($delete_meta as $key => $value) {
		$dbo->query("DELETE FROM meta WHERE meta_id = '$key'");
	}
	$focus_target = "keyword"; // re-focus keyword field after delete
}

// Delete entire topic (removes its keywords and PDFs too)
if ($check == "Delete Topic") {
	$del_id = addslashes($topic_id_update);
	$dbo->query("DELETE FROM meta   WHERE topic_id = '$del_id'");
	$dbo->query("DELETE FROM pdfs   WHERE topic_id = '$del_id'");
	$dbo->query("DELETE FROM topics WHERE id       = '$del_id'");
	$topic_id = "";
}

// Upload PDF - linked to current topic
// : "PDF goes with topic, on same page, built into this area"
if ($check == "Upload PDF") {
	$pdf_topic_id = addslashes($pdf_topic_id);

	if (!empty($_FILES["pdf_file"]["name"])) {
		// 's filename cleaning pattern (from dynamic_site admin)
		$fileName  = $_FILES["pdf_file"]["name"];
		$fileName  = preg_replace('/^-+|-+$/', '', strtolower(preg_replace('/[^a-zA-Z0-9.]+/', '_', $fileName)));
		$temporary = explode(".", $_FILES["pdf_file"]["name"]);
		$file_ext  = strtolower(end($temporary));

		// FIX: If no title typed, use the filename (without extension) as the title
		// This prevents blank titles which break the student PDF viewer
		if (empty(trim($pdf_title))) {
			$nameOnly  = pathinfo($_FILES["pdf_file"]["name"], PATHINFO_FILENAME);
			$pdf_title = addslashes(ucwords(str_replace(['_', '-'], ' ', $nameOnly)));
		} else {
			$pdf_title = addslashes($pdf_title);
		}

		if ($file_ext == "pdf") {
			$upload_dir = "../uploads/pdfs/";
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}
			$target    = $upload_dir . $fileName;
			$file_path = "uploads/pdfs/" . $fileName;

			if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target)) {
				$query = "INSERT INTO pdfs
				SET title     = '$pdf_title',
				file_path     = '$file_path',
				topic_id      = '$pdf_topic_id'";
				$dbo->query($query);
				$pdf_success  = "PDF uploaded and linked to this topic!";
				$topic_id     = $pdf_topic_id;
				$focus_target = "pdf";
			} else {
				$pdf_error    = "Upload failed. Make sure the uploads/pdfs/ folder exists with write permissions (chmod 777).";
				$topic_id     = $pdf_topic_id;
				$focus_target = "pdf";
			}
		} else {
			$pdf_error    = "Only .pdf files are allowed.";
			$topic_id     = $pdf_topic_id;
			$focus_target = "pdf";
		}
	} else {
		$pdf_error    = "Please choose a PDF file before clicking Upload.";
		$topic_id     = $pdf_topic_id;
		$focus_target = "pdf";
	}
}

// Delete PDF record
if (!is_null($delete_pdf)) {
	foreach ($delete_pdf as $key => $value) {
		$dbo->query("DELETE FROM pdfs WHERE id = '$key'");
	}
	$focus_target = "pdf";
}

// 
// SECTION 3: PRINT HTML
// 's exact print() pattern throughout
// 

print("<!doctype html>
<html lang=\"en\">
<head>
<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<title>Admin - Knowledge Hub</title>
<link href=\"css/main.css\" rel=\"stylesheet\">
</head>
<body>
<main>

<header>
<div class=\"header__title\">Knowledge Hub</div>
<div class=\"header__name\">Faculty Administration</div>
<ul>
<li><a href=\"./courses.php\">Courses</a></li>
<li><a href=\"./index.php\" class=\"tab-active\">&#9656; Topics</a></li>
<li><a href=\"./pdfs.php\">All PDFs</a></li>
</ul>
</header>

<form action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">
<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"10000000\">
<input type=\"hidden\" id=\"focus_target\" value=\"$focus_target\">
<article>

<section>
<h1>Topics <span>Select a topic from the list, or add a new one</span></h1>
<p>
<label>Select Topic:</label>
<select name=\"topic_id\" onchange=\"this.form.submit()\">
<option value=\"\">-- Choose a topic --</option>
");

// Topic dropdown - auto-submits on change (: fewer clicks, no GO button)
$query = "SELECT topics.id, topics.title, course.name AS course_name
FROM topics
JOIN course ON topics.course_id = course.course_id
ORDER BY topics.title";
foreach ($dbo->query($query) as $row) {
	$id    = $row["id"];
	$t     = stripslashes($row["title"]);
	$cname = stripslashes($row["course_name"]);
	$sel   = ($topic_id == $id) ? " selected=\"selected\"" : "";
	print("<option value=\"$id\"$sel>$t &mdash; $cname</option>\n");
}

print("</select>
&nbsp;&nbsp;
<input type=\"submit\" name=\"check\" value=\"New Topic\" class=\"primaryButton\">
</p>
</section>
");

// Show success message if topic was just saved
if (!empty($save_success)) {
	print("<p class=\"success-msg\">&#10003; $save_success</p>\n");
}

// 
// TOPIC EDIT / ADD FORM
// Only shows when a topic is selected or New Topic clicked
// 

// Set blank values for new topic
if ($topic_id == -1) {
	$t_title = ""; $t_description = ""; $t_week = "1"; $t_course_id = "";
}

if ($topic_id) {

	// Load existing topic data from database
	if ($topic_id != -1) {
		$query = "SELECT id, title, description, week, course_id
		FROM topics
		WHERE id = '$topic_id'";
		foreach ($dbo->query($query) as $row) {
			$t_title       = stripslashes($row["title"]);
			$t_description = stripslashes($row["description"]);
			$t_week        = stripslashes($row["week"]);
			$t_course_id   = stripslashes($row["course_id"]);
		}
	}

	// Build course dropdown with correct option pre-selected
	$course_select = "";
	$query = "SELECT course_id, name FROM course ORDER BY name";
	foreach ($dbo->query($query) as $row) {
		$cid  = $row["course_id"];
		$name = stripslashes($row["name"]);
		$sel  = ($t_course_id == $cid) ? " selected=\"selected\"" : "";
		$course_select .= "<option value=\"$cid\"$sel>$name</option>\n";
	}

	// Build week dropdown 1-14 with correct option pre-selected
	$week_select = "";
	for ($w = 1; $w <= 14; $w++) {
		$sel = ($t_week == $w) ? " selected=\"selected\"" : "";
		$week_select .= "<option value=\"$w\"$sel>Week $w</option>\n";
	}

	$heading = ($topic_id == -1) ? "Add New Topic" : "Edit Topic";
	$id_display = ($topic_id == -1) ? "" : "ID: $topic_id";
	// : autofocus title field when New Topic clicked
	// Cursor goes straight to title so admin can start typing immediately
	$autofocus = ($topic_id == -1) ? "autofocus" : "";

	print("<section>
	<h1>$heading <span>$id_display</span></h1>

<p>
<label for=\"title\">Topic Title:</label>
<input type=\"text\" id=\"title\" name=\"title\" value=\"$t_title\"
	placeholder=\"e.g. Web Development\" maxlength=\"150\" $autofocus>
</p>

<p>
<label for=\"description\">Description:</label>
<textarea id=\"description\" name=\"description\" rows=\"3\"
	placeholder=\"Short description of this topic\">$t_description</textarea>
</p>

<p>
<label for=\"course_id\">Course:</label>
<select id=\"course_id\" name=\"course_id\">
$course_select
</select>
</p>

<p>
<label for=\"week\">Week:</label>
<select id=\"week\" name=\"week\">
$week_select
</select>
</p>

<p>
<input type=\"hidden\" name=\"topic_id_update\" value=\"$topic_id\">
<input type=\"submit\" name=\"check\" value=\"Update\" class=\"primaryButton\">
&nbsp;
<input type=\"reset\" value=\"Reset\" class=\"secondaryButton\">
&nbsp;
<input type=\"submit\" name=\"check\" value=\"Cancel\" class=\"secondaryButton\">
");

	// Only show Delete button for existing topics (not new ones)
	if ($topic_id != -1) {
		print("&nbsp;
<input type=\"submit\" name=\"check\" value=\"Delete Topic\" class=\"dangerButton\"
	onclick=\"return confirm('WARNING: This permanently deletes the topic, all its PDFs and all its keywords. Are you sure?')\">\n");
	}

	print("</p>
</section>
");

	// 
	// KEYWORDS SECTION
	// Only for existing saved topics (need a real ID)
	// 

	if ($topic_id != -1) {

		print("<section>
<h1>Search Keywords <span>shown as bubbles on the student page</span></h1>
<p>
<input type=\"hidden\" name=\"meta_topic_id\" value=\"$topic_id\">
<input type=\"text\" name=\"meta_name\" id=\"meta_name\"
	placeholder=\"e.g. html\" maxlength=\"30\" style=\"width:15rem;\">
&nbsp;
<input type=\"submit\" name=\"check\" value=\"Add Keyword\" class=\"secondaryButton\">
</p>
<p class=\"keyword-list\">
");

		$query = "SELECT meta_id, name FROM meta WHERE topic_id = '$topic_id' ORDER BY name";
		foreach ($dbo->query($query) as $row) {
			$mid   = $row["meta_id"];
			$mname = stripslashes($row["name"]);
			print("<span class=\"keyword-tag\">$mname
<input type=\"submit\" name=\"delete_meta[$mid]\" value=\"&#215;\"
	class=\"keyword-delete\" title=\"Remove this keyword\"></span> ");
		}

		print("</p>
</section>
");

		// 
		// PDF UPLOAD SECTION
		// : "PDF goes with the topic, on same page"
		// "Text info and PDF go together as a group"
		// "Built into this area - PDF button disappears"
		//
		// NOTE: This only shows for existing topics because
		// we need a real topic_id to link the PDF to.
		// After clicking "Update" on a new topic, you get an ID
		// and this section appears automatically.
		// 

		// Show error or success messages
		if (!empty($pdf_error)) {
			print("<p class=\"error-msg\">&#9888; $pdf_error</p>\n");
		}
		if (!empty($pdf_success)) {
			print("<p class=\"success-msg\">&#10003; $pdf_success</p>\n");
		}

		print("<section id=\"pdf-section\">
<h1>Upload PDF <span>this PDF will be automatically linked to this topic</span></h1>

<p>
<label for=\"pdf_title\">PDF Title:</label>
<input type=\"text\" id=\"pdf_title\" name=\"pdf_title\"
	placeholder=\"e.g. HTML Basics Guide\" maxlength=\"150\">
</p>

<p>
<label for=\"pdf_file\">Choose PDF File <small class=\"article__reference\">(PDF only &mdash; max 10MB)</small>:</label>
<input type=\"file\" id=\"pdf_file\" name=\"pdf_file\" accept=\".pdf\">
</p>

<p>
<input type=\"hidden\" name=\"pdf_topic_id\" value=\"$topic_id\">
<input type=\"submit\" name=\"check\" value=\"Upload PDF\" class=\"primaryButton\">
</p>
</section>
");

		// Show table of PDFs already linked to this topic
		$query = "SELECT id, title, file_path, created_at
		FROM pdfs
		WHERE topic_id = '$topic_id'
		ORDER BY title";

		$pdf_rows = $dbo->query($query)->fetchAll(PDO::FETCH_ASSOC);

		if (!empty($pdf_rows)) {

			$count = count($pdf_rows);
			print("<section>
<h1>PDFs linked to this topic <span>$count PDF(s) uploaded</span></h1>
<table>
<tr>
<th>Title</th>
<th>View File</th>
<th>Path</th>
<th>Uploaded</th>
<th>Delete</th>
</tr>
");
			foreach ($pdf_rows as $row) {
				$pid = $row["id"];
				$pt  = stripslashes($row["title"]);
				$pfp = stripslashes($row["file_path"]);
				$pca = stripslashes($row["created_at"]);
				print("<tr>
<td><strong>$pt</strong></td>
<td><a href=\"../$pfp\" target=\"_blank\">&#128196; Open</a></td>
<td><small class=\"article__reference\">$pfp</small></td>
<td><small>$pca</small></td>
<td>
<input type=\"submit\" name=\"delete_pdf[$pid]\" value=\"Delete\"
	class=\"dangerButton\"
	onclick=\"return confirm('Delete this PDF?')\">
</td>
</tr>\n");
			}
			print("</table>
</section>
");

		} else {
			print("<section>
<p class=\"article__reference\">No PDFs uploaded yet for this topic. Use the form above to upload one.</p>
</section>
");
		}

	} else {
		// New topic not saved yet - explain why no PDF section
		print("<section>
<p class=\"article__reference\">
&#9432; Click <strong>Update</strong> to save this topic first &mdash;
then keywords and PDF upload will appear here.
</p>
</section>
");
	}
}

print("</article>
</form>

<footer>
<p>Knowledge Hub Administration &mdash; Durham College &mdash; Web Design 3</p>
<p><a href=\"../index.html\">&larr; View Student Site</a></p>
</footer>

</main>
<script>
// 's focus trick from class recording
// PHP sets hidden input value, JS reads it and focuses the right field
(function() {
  var ft = document.getElementById('focus_target');
  if (!ft) return;

  if (ft.value === 'keyword') {
    var kw = document.getElementById('meta_name');
    if (kw) { kw.focus(); }
  }

  if (ft.value === 'pdf') {
    var pdfSection = document.getElementById('pdf-section');
    if (pdfSection) {
      pdfSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
})();
</script>
</body>
</html>
");
?>