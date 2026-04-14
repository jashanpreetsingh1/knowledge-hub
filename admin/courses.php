<?php

// admin/courses.php
// Knowledge Hub Admin - Courses Management
// 's pattern: Section 1 → Section 2 → Section 3

date_default_timezone_set('America/Toronto');
require_once("./inc/connect_pdo.php");

// SECTION 1: RECEIVE POST
$course_id        = $_POST["course_id"];
$course_id_update = $_POST["course_id_update"];
$check            = $_POST["check"];
$course_name      = $_POST["course_name"];

// SECTION 2: PROCESS ACTIONS

if ($check == "Cancel")     { $course_id = ""; }
if ($check == "New Course") { $course_id = -1; }

if ($check == "Update" || $check == "Add Course") {
	$course_id   = $course_id_update;
	$course_name = addslashes($course_name);

	if ($course_id_update == -1) {
		$query = "INSERT INTO course SET name = '$course_name'";
		$dbo->query($query);
		$course_id    = $dbo->lastInsertId();
		$save_success = "Course added successfully.";
	} else {
		$query = "UPDATE course SET name = '$course_name' WHERE course_id = '$course_id'";
		$dbo->query($query);
		$save_success = "Course updated successfully.";
	}
}

if ($check == "Delete Course") {
	$del_id = addslashes($course_id_update);
	// Safety check: cannot delete if topics still linked
	$result = $dbo->query("SELECT COUNT(*) AS cnt FROM topics WHERE course_id = '$del_id'")->fetch();
	if ($result["cnt"] > 0) {
		$delete_error = "Cannot delete: $result[cnt] topic(s) are still linked to this course. Reassign or delete those topics first.";
	} else {
		$dbo->query("DELETE FROM course WHERE course_id = '$del_id'");
		$course_id    = "";
		$save_success = "Course deleted.";
	}
}

// SECTION 3: PRINT HTML

print("<!doctype html>
<html lang=\"en\">
<head>
<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<title>Admin Courses - Knowledge Hub</title>
<link href=\"css/main.css\" rel=\"stylesheet\">
</head>
<body>
<main>

<header>
<div class=\"header__title\">Knowledge Hub</div>
<div class=\"header__name\">Faculty Administration</div>
<ul>
<li><a href=\"./courses.php\" class=\"tab-active\">&#9656; Courses</a></li>
<li><a href=\"./index.php\">Topics</a></li>
<li><a href=\"./pdfs.php\">All PDFs</a></li>
</ul>
</header>

<form action=\"courses.php\" method=\"post\">
<article>

<section>
<h1>Courses <span>Select to edit, or add a new course</span></h1>
");

if (!empty($delete_error)) {
	print("<p class=\"error-msg\">&#9888; $delete_error</p>\n");
}
if (!empty($save_success)) {
	print("<p class=\"success-msg\">&#10003; $save_success</p>\n");
}

print("<p>
<label>Select Course:</label>
<select name=\"course_id\" onchange=\"this.form.submit()\">
<option value=\"\">-- Choose a course --</option>
");

$query = "SELECT course_id, name FROM course ORDER BY name";
foreach ($dbo->query($query) as $row) {
	$cid  = $row["course_id"];
	$name = stripslashes($row["name"]);
	$sel  = ($course_id == $cid) ? " selected=\"selected\"" : "";
	print("<option value=\"$cid\"$sel>$name</option>\n");
}

print("</select>
&nbsp;&nbsp;
<input type=\"submit\" name=\"check\" value=\"New Course\" class=\"secondaryButton\">
</p>
</section>
");

if ($course_id == -1) { $c_name = ""; }

if ($course_id) {

	if ($course_id != -1) {
		$query = "SELECT course_id, name FROM course WHERE course_id = '$course_id'";
		foreach ($dbo->query($query) as $row) {
			$c_name = stripslashes($row["name"]);
		}
	}

	$heading    = ($course_id == -1) ? "Add New Course" : "Edit Course";
	$id_display = ($course_id == -1) ? "" : "ID: $course_id";
	$autofocus = ($course_id == -1) ? "autofocus" : "";

	print("<section>
	<h1>$heading <span>$id_display</span></h1>
<p>
<label for=\"course_name\">Course Name:</label>
<input type=\"text\" id=\"course_name\" name=\"course_name\" value=\"$c_name\"
	placeholder=\"e.g. Web Development Fundamentals\" maxlength=\"50\" $autofocus>
</p>
<p>
<input type=\"hidden\" name=\"course_id_update\" value=\"$course_id\">
<input type=\"submit\" name=\"check\" value=\"Update\" class=\"primaryButton\">
&nbsp;
<input type=\"reset\" value=\"Reset\" class=\"secondaryButton\">
&nbsp;
<input type=\"submit\" name=\"check\" value=\"Cancel\" class=\"secondaryButton\">
");

	if ($course_id != -1) {
		print("&nbsp;
<input type=\"submit\" name=\"check\" value=\"Delete Course\" class=\"dangerButton\"
	onclick=\"return confirm('Delete this course? Make sure no topics are linked to it first.')\">
");
	}

	print("</p>
</section>
");
}

// All courses summary table
print("<section>
<h1>All Courses <span>and how many topics are linked to each</span></h1>
<table>
<tr>
<th>ID</th>
<th>Course Name</th>
<th>Topics Linked</th>
</tr>
");

$query = "SELECT course.course_id, course.name, COUNT(topics.id) AS topic_count
FROM course
LEFT JOIN topics ON course.course_id = topics.course_id
GROUP BY course.course_id, course.name
ORDER BY course.name";
foreach ($dbo->query($query) as $row) {
	$cid   = $row["course_id"];
	$cname = stripslashes($row["name"]);
	$count = $row["topic_count"];
	print("<tr>
<td>$cid</td>
<td>$cname</td>
<td>$count topic(s)</td>
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
