// main.js - Knowledge Hub - Durham College


//
// CARD BUILDER FUNCTIONS 
//

function commonTopicCards(id, title, description, week, course_name, keywords) {
	let content = ``;

	// Build keyword bubbles -  said show meta as "jelly beans"
	let keywordHTML = ``;
	if (keywords && keywords.length > 0) {
		keywords.forEach(function(kw) {
			keywordHTML += `<span class="badge-keyword">${kw}</span>`;
		});
	}

	return content += `
		<div class="topic-card-wrap">
			<div class="topic-card click-topic pointer" data-id="${id}" data-title="${title}">
				<div class="topic-card__title">${title}</div>
				<div class="topic-card__desc">${description}</div>
				<div class="topic-card__badges">
					<span class="badge-week">Week ${week}</span>
					<span class="badge-course">${course_name}</span>
				</div>
				${keywordHTML ? `<div class="topic-card__keywords">${keywordHTML}</div>` : ``}
			</div>
		</div>`;
}

function commonPDFItems(id, title, file_path, created_at) {
	let content = ``;
	// if title is blank in DB, use filename as display title
	let displayTitle = (title && title.trim() !== '')
		? title
		: file_path.split('/').pop().replace(/_/g, ' ').replace('.pdf', '');
	return content += `
		<div class="pdf-item" data-id="${id}" data-file="${file_path}" data-title="${displayTitle}">
			<div class="pdf-item__icon">&#128196;</div>
			<div class="pdf-item__info click-pdf pointer" data-id="${id}" data-file="${file_path}" data-title="${displayTitle}">
				<div class="pdf-item__title">${displayTitle}</div>
				<div class="pdf-item__date">Uploaded: ${created_at}</div>
			</div>
			<a class="pdf-item__download" href="./${file_path}" target="_blank" download>&#11123; Download PDF</a>
		</div>`;
}

//
// PAGE FUNCTIONS ( getHome, getMovies)
//

function getTopics() {
	$(".hideAll").hide();
	window.scrollTo(0, 0);

	let getTopics = $.ajax({
		url: './assets/services/getTopics.php',
		type: 'POST',
		dataType: 'json'
	});

	getTopics.fail(function(jqXHR, textStatus) {
		alert('Something went wrong! (getTopics) ' + textStatus);
	});

	getTopics.done(function(data) {
		let content = ``;
		$.each(data, function(i, item) {
			content += commonTopicCards(
				item.id,
				item.title,
				item.description,
				item.week,
				item.course_name,
				item.keywords
			);
		});
		if (content === ``) {
			content = `<div class="no-results">No topics found.</div>`;
		}
		$(".topics-container").html(content);
		$(".result-count").html(data.length + " topic(s) found");
		$(".topics-show").show();
	});
}

function getPDFs(topic_id, topic_title) {
	$(".hideAll").hide();
	window.scrollTo(0, 0);
	$(".pdfs-topic-title").html(topic_title);

	let getPDFs = $.ajax({
		url: './assets/services/getPDFs.php',
		type: 'POST',
		data: { topic_id: topic_id },
		dataType: 'json'
	});

	getPDFs.fail(function(jqXHR, textStatus) {
		alert('Something went wrong! (getPDFs) ' + textStatus);
	});

	getPDFs.done(function(data) {
		let content = ``;
		$.each(data, function(i, item) {
			content += commonPDFItems(item.id, item.title, item.file_path, item.created_at);
		});
		if (content === ``) {
			content = `<div class="no-pdfs-box">
				<strong>&#128196; No PDFs uploaded yet for this topic</strong>
				<span>Faculty will be adding resources here soon. Please check back later.</span>
			</div>`;
		}
		$(".pdfs-list-container").html(content);
		$(".pdfs-show").show();
	});
}

function viewPDF(file_path, pdf_title) {
	$(".hideAll").hide();
	window.scrollTo(0, 0);
	$(".viewer-pdf-title").html(pdf_title);
	$(".btn-download").attr("href", "./" + file_path);
	$(".pdf-viewer-frame").attr("src", "./" + file_path);
	$(".viewer-show").show();
}

//
// SEARCH (title + meta keywords dual search)
//

const getSearch = (search) => {
	let getSearch = $.ajax({
		url: './assets/services/search.php',
		type: 'POST',
		data: { search_text: search },
		dataType: 'json'
	});

	getSearch.fail(function(jqXHR, textStatus) {
		alert('Something went wrong! (getSearch) ' + textStatus);
	});

	getSearch.done(function(data) {
		let content = ``;
		if (data.error.error_id == 0) {
			$.each(data.results, function(i, item) {
				content = `${content}<li class="click-topic pointer" data-id="${item.id}" data-title="${item.title}">
					${item.title} &nbsp;<small>Week ${item.week}</small>
				</li>`;
			});
		} else {
			content = `<li style="color:#999;padding:0.5rem 1rem;cursor:default;">No results found</li>`;
		}
		$(".search-results").show().html(content);
	});
};

//
// FILTER FUNCTIONS
// : auto-run when ANY dropdown changes
// No button needed - just select and it filters
//

function loadFilters() {
	let loadFilters = $.ajax({
		url: './assets/services/getFilters.php',
		type: 'POST',
		dataType: 'json'
	});

	loadFilters.fail(function(jqXHR, textStatus) {
		console.log('Could not load filters: ' + textStatus);
	});

	loadFilters.done(function(data) {
		let courseOptions = `<option value="">All Courses</option>`;
		$.each(data.courses, function(i, item) {
			courseOptions += `<option value="${item.course_id}">${item.name}</option>`;
		});
		$("#filter-course").html(courseOptions);

		let keywordOptions = `<option value="">All Keywords</option>`;
		$.each(data.keywords, function(i, item) {
			keywordOptions += `<option value="${item}">${item}</option>`;
		});
		$("#filter-keyword").html(keywordOptions);
	});
}

function applyFilters() {
	let course_id = $("#filter-course").val();
	let week      = $("#filter-week").val();
	let keyword   = $("#filter-keyword").val();

	let applyFilters = $.ajax({
		url: './assets/services/filterTopics.php',
		type: 'POST',
		data: { course_id: course_id, week: week, keyword: keyword },
		dataType: 'json'
	});

	applyFilters.fail(function(jqXHR, textStatus) {
		alert('Something went wrong! (applyFilters) ' + textStatus);
	});

	applyFilters.done(function(data) {
		let content = ``;
		$.each(data, function(i, item) {
			content += commonTopicCards(
				item.id,
				item.title,
				item.description,
				item.week,
				item.course_name,
				item.keywords
			);
		});
		if (content === ``) {
			content = `<div class="no-results">No topics match your filters.</div>`;
		}
		$(".topics-container").html(content);
		$(".result-count").html(data.length + " topic(s) found");
		$(".hideAll").hide();
		$(".topics-show").show();
	});
}

//
// WINDOW LOAD - EVENTS + SAMMY ROUTING
//

$(window).on("load", function () {

	loadFilters();

	// 's focus trick: PHP writes a value into #focus_target hidden input
	// JS reads it on load and focuses the right field automatically
	let focusTarget = $("#focus_target").val();
	if (focusTarget === "keyword") {
		$("#meta_name").focus();
	}

	// Search keyup
	$("#search").keyup(function () {
		let search = $(this).val();
		if (search.length > 1) {
			getSearch(search);
		} else {
			$(".search-results").hide().html("");
		}
	});

	// Close search when clicking outside
	$(document).click(function(e) {
		if (!$(e.target).closest(".search-container").length) {
			$(".search-results").hide();
		}
	});

	// Logo click -> home
	$(document).on('click', 'body .click-home', function() {
		location.href = `#/topics/`;
	});

	// Click topic card -> PDF list
	$(document).on('click', 'body .click-topic', function() {
		let topic_id    = $(this).attr("data-id");
		let topic_title = $(this).attr("data-title") || $(this).find(".topic-card__title").text();
		$(".search-results").hide();
		location.href = `#/pdfs/${topic_id}/${encodeURIComponent(topic_title)}`;
	});

	// Click PDF info area -> viewer
	$(document).on('click', 'body .click-pdf', function() {
		let file_path = $(this).attr("data-file");
		let pdf_title = $(this).attr("data-title");
		if (!pdf_title || pdf_title.trim() === '') {
			pdf_title = file_path.split('/').pop().replace(/_/g, ' ').replace('.pdf', '');
		}
		location.href = `#/viewer/${encodeURIComponent(file_path)}/${encodeURIComponent(pdf_title)}`;
	});

	// Back buttons
	$(document).on('click', 'body .back-to-topics', function() {
		location.href = `#/topics/`;
	});

	$(document).on('click', 'body .back-to-pdfs', function() {
		history.back();
	});

	// AUTO-FILTER on dropdown change
	$("#filter-course, #filter-week, #filter-keyword").change(function() {
		applyFilters();
	});

	// Reset filters
	$("#btn-reset-filter").click(function() {
		$("#filter-course").val("");
		$("#filter-week").val("");
		$("#filter-keyword").val("");
		getTopics();
	});

	// SAMMY ROUTING - 's exact pattern
	var app = $.sammy(function () {

		this.get('#/topics/', function () {
			getTopics();
		});

		this.get('#/pdfs/:topic_id/:topic_title', function () {
			let topic_id    = this.params["topic_id"];
			let topic_title = decodeURIComponent(this.params["topic_title"]);
			getPDFs(topic_id, topic_title);
		});

		this.get('#/viewer/:file_path/:pdf_title', function () {
			let file_path = decodeURIComponent(this.params["file_path"]);
			let pdf_title = decodeURIComponent(this.params["pdf_title"]);
			viewPDF(file_path, pdf_title);
		});

	});

	// Default page on load
	$(function () {
		app.run('#/topics/');
	});

});