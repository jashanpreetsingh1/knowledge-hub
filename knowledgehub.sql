-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 08, 2026 at 07:13 AM
-- Server version: 8.0.45-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `team2`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `name`) VALUES
(2, 'Advanced JavaScript'),
(10, 'ajax'),
(8, 'API Development222'),
(9, 'Cyberduck'),
(5, 'Database Design'),
(3, 'PHP & MySQL Programming'),
(6, 'Responsive Web Design'),
(4, 'UI/UX Design Principles'),
(7, 'Version Control & Git'),
(1, 'Web Development Fundamentals');

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE `meta` (
  `meta_id` int UNSIGNED NOT NULL,
  `topic_id` int UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`meta_id`, `topic_id`, `name`) VALUES
(1, 1, 'html'),
(2, 1, 'css'),
(4, 3, 'html'),
(5, 6, 'sql'),
(6, 1, 'html'),
(7, 1, 'css'),
(9, 3, 'html'),
(10, 6, 'sql'),
(13, 22, 'cyberduck'),
(17, 22, 'ftp files');

-- --------------------------------------------------------

--
-- Table structure for table `pdfs`
--

CREATE TABLE `pdfs` (
  `id` int NOT NULL,
  `title` varchar(150) NOT NULL DEFAULT '',
  `file_path` varchar(255) DEFAULT NULL,
  `topic_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pdfs`
--

INSERT INTO `pdfs` (`id`, `title`, `file_path`, `topic_id`, `created_at`) VALUES
(1, 'HTML Basics Guide', 'uploads/pdfs/html_basics.pdf', 1, '2026-03-18 08:05:51'),
(2, 'CSS Flexbox Cheatsheet', 'uploads/pdfs/css_flexbox.pdf', 1, '2026-03-18 08:05:51'),
(4, 'PHP CRUD Operations', 'uploads/pdfs/php_crud.pdf', 3, '2026-03-18 08:05:51'),
(5, 'MySQL Database Design', 'uploads/pdfs/cmp_4200_2025.pdf', 5, '2026-03-18 08:05:51'),
(9, 'Cyberduck', 'uploads/pdfs/cyberduck.pdf', 22, '2026-04-01 12:31:44'),
(10, 'Ajax Guide', 'uploads/pdfs/ajax_guide.pdf', 24, '2026-04-08 06:13:23'),
(11, 'Api Development Guide', 'uploads/pdfs/api_development_guide.pdf', 12, '2026-04-08 06:16:02'),
(12, 'Cyber Security Basics', 'uploads/pdfs/cyber_security_basics.pdf', 10, '2026-04-08 06:16:24'),
(13, 'Cyber Security Basics', 'uploads/pdfs/cyber_security_basics.pdf', 22, '2026-04-08 06:17:07'),
(14, 'Mysql Database Design', 'uploads/pdfs/mysql_database_design.pdf', 13, '2026-04-08 06:18:14'),
(15, 'Mysql Database Design', 'uploads/pdfs/mysql_database_design.pdf', 5, '2026-04-08 06:18:49'),
(16, 'Foundation Css Guide', 'uploads/pdfs/foundation_css_guide.pdf', 18, '2026-04-08 06:19:14'),
(17, 'Html Basics Guide', 'uploads/pdfs/html_basics_guide.pdf', 20, '2026-04-08 06:19:42'),
(18, 'Html Basics', 'uploads/pdfs/html_basics.pdf', 20, '2026-04-08 06:20:15'),
(19, 'React Guide', 'uploads/pdfs/react_guide.pdf', 17, '2026-04-08 06:21:03'),
(20, 'Laravel', 'uploads/pdfs/laravel.pdf', 4, '2026-04-08 06:22:13'),
(21, 'Mysql Database Design', 'uploads/pdfs/mysql_database_design.pdf', 9, '2026-04-08 06:27:06'),
(22, 'Api Development Guide', 'uploads/pdfs/api_development_guide.pdf', 14, '2026-04-08 06:27:31'),
(23, 'Php Introduction', 'uploads/pdfs/php_introduction.pdf', 3, '2026-04-08 06:27:58'),
(24, 'React Guide', 'uploads/pdfs/react_guide.pdf', 16, '2026-04-08 06:28:18'),
(25, 'Responsive Design Guide', 'uploads/pdfs/responsive_design_guide.pdf', 8, '2026-04-08 06:28:39'),
(26, 'Html Basics Guide', 'uploads/pdfs/html_basics_guide.pdf', 15, '2026-04-08 06:29:08'),
(27, 'Css Styling Guide', 'uploads/pdfs/css_styling_guide.pdf', 15, '2026-04-08 06:29:17'),
(28, 'Mysql Database Design', 'uploads/pdfs/mysql_database_design.pdf', 6, '2026-04-08 06:29:54'),
(29, 'Responsive Design Guide', 'uploads/pdfs/responsive_design_guide.pdf', 7, '2026-04-08 06:30:17'),
(30, 'Api Development Guide', 'uploads/pdfs/api_development_guide.pdf', 11, '2026-04-08 06:30:53'),
(31, 'Responsive Design Guide', 'uploads/pdfs/responsive_design_guide.pdf', 1, '2026-04-08 06:31:22'),
(33, 'Css Styling Guide', 'uploads/pdfs/css_styling_guide.pdf', 1, '2026-04-08 06:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int NOT NULL,
  `course_id` int UNSIGNED NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text,
  `week` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`id`, `course_id`, `title`, `description`, `week`, `created_at`) VALUES
(1, 1, 'Web Development', 'Learn HTML, CSS, JavaScript fundamentals for building websites', 1, '2026-03-18 07:49:39'),
(3, 3, 'PHP Programming', 'Server-side scripting using PHP and MySQL', 1, '2026-03-18 07:49:39'),
(4, 1, 'Laravel Framework', 'Modern PHP framework for web applications', 1, '2026-03-18 07:49:39'),
(5, 5, 'Database Design', 'Learn relational databases, normalization and ER diagrams', 1, '2026-03-18 07:49:39'),
(6, 5, 'SQL Queries', 'Writing efficient SQL queries and joins', 1, '2026-03-18 07:49:39'),
(7, 4, 'UI/UX Design', 'User interface and experience design principles', 1, '2026-03-18 07:49:39'),
(8, 6, 'Responsive Design', 'Build mobile-friendly websites using media queries', 1, '2026-03-18 07:49:39'),
(9, 1, 'Networking Basics', 'Introduction to networking concepts and protocols', 1, '2026-03-18 07:49:39'),
(10, 1, 'Cyber Security', 'Basic security concepts and best practices', 1, '2026-03-18 07:49:39'),
(11, 7, 'Version Control', 'Using Git and GitHub for project management', 1, '2026-03-18 07:49:39'),
(12, 8, 'API Development', 'Creating RESTful APIs using PHP and JSON.', 1, '2026-03-18 07:49:39'),
(13, 5, 'Data Structures', 'Understanding arrays, stacks, queues, and algorithms', 1, '2026-03-18 07:49:39'),
(14, 8, 'Object Oriented Programming', 'Concepts like classes, objects, inheritance', 1, '2026-03-18 07:49:39'),
(15, 1, 'Software Testing', 'Basics of testing and debugging applications', 1, '2026-03-18 07:49:39'),
(16, 1, 'react', 'react app building', 9, '2026-03-25 12:50:36'),
(17, 2, 'jahan react ', 'reat app learning', 2, '2026-03-25 13:16:00'),
(18, 2, 'foundation.js', 'it is framework for front end', 1, '2026-03-25 14:38:38'),
(19, 2, 'react.js', 'javascript library for managing the style and the routing of the websites', 1, '2026-03-28 19:43:32'),
(20, 1, 'html ', 'building structure of a website', 1, '2026-03-29 23:26:42'),
(22, 9, 'Cyberduck Learning', 'how to Use Cyberduck and connect using credentials', 2, '2026-04-01 12:30:29'),
(24, 2, 'ajax', 'javascript library for easy functionality', 1, '2026-04-07 22:35:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `meta`
--
ALTER TABLE `meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `pdfs`
--
ALTER TABLE `pdfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `week` (`week`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `meta`
--
ALTER TABLE `meta`
  MODIFY `meta_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `pdfs`
--
ALTER TABLE `pdfs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
