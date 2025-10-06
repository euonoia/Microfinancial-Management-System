-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 09:01 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr2_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `employee_id`, `username`, `password`, `email`, `full_name`, `created_at`) VALUES
(2, 7749, 'pgog', '$2y$10$fffqf6CIjcSudGGX2wKiZuXysuQeyNvpWCp1NHdCIxDwkXveaYk12', 'ken@email.com', 'ken pogi', '2025-10-05 03:05:30'),
(3, 7854, 'admin', '$2y$10$ZFA8NmdyLmp/1v6ELPvr1.jTtJgWzE2.OyhXS9ccWTxaViravzoxO', 'admin@email.com', 'admin', '2025-10-05 10:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `location`, `created_at`) VALUES
(1, 'sky borge', 'mandalayung', '2025-10-05 12:42:13'),
(2, 'sky borge', 'mandalayung', '2025-10-05 12:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `competency_group` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `code`, `title`, `description`, `competency_group`, `created_at`) VALUES
(2, 'COMP001', 'lebron james', 'to become a lebrom james', 'leadership', '2025-10-05 03:34:06'),
(4, 'COMP003', 'being dominant', 'dominant carrer', 'leadership', '2025-10-05 11:53:25');

-- --------------------------------------------------------

--
-- Table structure for table `competencies_archive`
--

CREATE TABLE `competencies_archive` (
  `id` int(11) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `competency_group` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `competencies_archive`
--

INSERT INTO `competencies_archive` (`id`, `code`, `title`, `description`, `competency_group`, `created_at`, `deleted_at`) VALUES
(1, '', 'credit risk assessment', 'to assess the credit risks', NULL, '2025-10-05 11:29:15', '2025-10-06 14:30:50'),
(2, 'COMP002', 'being dominant', 'dominant carrer', 'leadership', '2025-10-05 19:53:25', '2025-10-06 14:32:06');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_id` varchar(100) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `content_url` varchar(255) DEFAULT NULL,
  `duration_minutes` int(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_id`, `title`, `description`, `content_url`, `duration_minutes`, `created_at`) VALUES
(1, '', 'how to wash your hands', 'inorder to wash hands', 'www.google.com', 30, '2025-10-05 03:40:10'),
(2, 'COUR001', 'how to be kind', 'teaching you to be kind', 'www.google.com', 20, '2025-10-05 01:20:27'),
(3, 'COUR002', 'being clean worker', 'inorder to clean the worker', 'www.google.com', 20, '2025-10-05 05:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `course_archive`
--

CREATE TABLE `course_archive` (
  `id` int(11) NOT NULL,
  `course_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `content_url` varchar(255) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course_archive`
--

INSERT INTO `course_archive` (`id`, `course_id`, `title`, `description`, `content_url`, `duration_minutes`, `created_at`, `archived_at`) VALUES
(1, 'COUR003', 'lebron', 'teach you to become lebron', 'www.google.com', 21, '2025-10-05 15:21:49', '2025-10-06 05:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrolls`
--

CREATE TABLE `course_enrolls` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `course_id` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course_enrolls`
--

INSERT INTO `course_enrolls` (`id`, `employee_id`, `course_id`, `status`) VALUES
(17, 'EMP-2025-0541F', 'COUR001', 'enrolled'),
(18, 'EMP-2025-0541F', '', 'enrolled'),
(19, 'EMP-2025-F45A4', 'COUR001', 'enrolled'),
(20, 'EMP-2025-0541F', 'COUR002', 'enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `first_name` varchar(150) DEFAULT NULL,
  `last_name` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','hr','employee') DEFAULT 'employee',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `first_name`, `last_name`, `name`, `position`, `hire_date`, `branch`, `email`, `phone`, `password`, `role`, `created_at`, `updated_at`) VALUES
(6, 'EMP-2025-0541F', 'john', 'lennon', 'john lennon', 'loan officer', '2025-10-05', 'mainbranch', 'johnlennon@email.com', '0912345890', '$2y$10$nk3cT0PCeLizC1xFGd8N1uxwWpqbDzedywKIya5jP8WkkpJAzcD1q', 'employee', '2025-10-05 07:21:13', '2025-10-05 07:21:13'),
(7, 'EMP-2025-F45A4', 'john', 'doe', 'john doe', 'loan officer', '2025-10-05', 'mainbranch', 'johndoe@email.com', '0912345890', '$2y$10$NUieiLVM6no/B9FQ3TlsLeyUem0B2U.k41SzzFC//iBUP022Fk1ra', 'employee', '2025-10-05 13:06:11', '2025-10-05 13:06:11');

-- --------------------------------------------------------

--
-- Table structure for table `ess_request`
--

CREATE TABLE `ess_request` (
  `id` int(11) NOT NULL,
  `ess_id` varchar(100) NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `status` enum('pending','approved','rejected','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ess_request`
--

INSERT INTO `ess_request` (`id`, `ess_id`, `employee_id`, `type`, `details`, `status`, `created_at`, `updated_at`) VALUES
(35, 'ESS0006', 'EMP-2025-F45A4', 'Payroll Issue', 'hoy kulang sahod ko bwahhas', 'pending', '2025-10-05 07:58:32', '2025-10-05 13:58:32'),
(37, 'ESS0008', 'EMP-2025-0541F', 'Payroll Issue', 'kulang natatanggap ko hahahaaahahahah', 'pending', '2025-10-05 08:02:42', '2025-10-05 14:02:42'),
(38, 'ESS0009', 'EMP-2025-0541F', 'Leave', 'yoko muna magtrabaho', 'pending', '2025-10-05 22:44:30', '2025-10-06 04:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `ess_request_archive`
--

CREATE TABLE `ess_request_archive` (
  `id` int(11) NOT NULL,
  `ess_id` varchar(100) NOT NULL,
  `employee_id` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `status` enum('approved','rejected','closed') DEFAULT 'closed',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ess_request_archive`
--

INSERT INTO `ess_request_archive` (`id`, `ess_id`, `employee_id`, `type`, `details`, `status`, `created_at`, `updated_at`, `archived_at`) VALUES
(1, '36', 'EMP-2025-F45A4', 'Payroll Issue', 'hoy kulang sahod ko bwahhas', 'rejected', '2025-10-05 15:58:32', '2025-10-06 13:14:03', '2025-10-06 05:14:03'),
(2, '39', 'EMP-2025-0541F', 'Overtime', 'puro nalang overtime haha', 'approved', '2025-10-06 06:45:20', '2025-10-06 13:17:50', '2025-10-06 05:17:50');

-- --------------------------------------------------------

--
-- Table structure for table `learning_modules`
--

CREATE TABLE `learning_modules` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `competency_id` int(11) DEFAULT NULL,
  `learning_type` enum('Online','Workshop','Seminar','Coaching') DEFAULT 'Online',
  `duration` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `learning_modules`
--

INSERT INTO `learning_modules` (`id`, `title`, `description`, `competency_id`, `learning_type`, `duration`) VALUES
(1, 'How to read graphs', 'to teach you to read graphs', 1, 'Workshop', '3');

-- --------------------------------------------------------

--
-- Table structure for table `succession_positions`
--

CREATE TABLE `succession_positions` (
  `id` int(11) NOT NULL,
  `branch_id` varchar(100) NOT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  `criticality` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `succession_positions`
--

INSERT INTO `succession_positions` (`id`, `branch_id`, `position_title`, `criticality`) VALUES
(1, 'BR-0001', 'manager', 'medium'),
(4, 'BR705C31', 'ceo', 'high');

-- --------------------------------------------------------

--
-- Table structure for table `succession_positions_archive`
--

CREATE TABLE `succession_positions_archive` (
  `id` int(11) NOT NULL,
  `position_title` varchar(255) DEFAULT NULL,
  `branch_id` varchar(50) DEFAULT NULL,
  `criticality` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `successor_candidates`
--

CREATE TABLE `successor_candidates` (
  `id` int(11) NOT NULL,
  `branch_id` varchar(100) NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `readiness` varchar(100) NOT NULL,
  `effective_at` date DEFAULT NULL,
  `development_plan` varchar(100) NOT NULL,
  `created_at` timestamp(5) NOT NULL DEFAULT current_timestamp(5),
  `updated_at` timestamp(5) NOT NULL DEFAULT current_timestamp(5)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `successor_candidates`
--

INSERT INTO `successor_candidates` (`id`, `branch_id`, `employee_id`, `readiness`, `effective_at`, `development_plan`, `created_at`, `updated_at`) VALUES
(3, 'BR-0001', 'EMP-2025-0541F', 'ready', NULL, 'galinfg', '2025-10-06 05:34:05.35244', '2025-10-06 05:34:05.35244'),
(5, 'BR705C31', 'EMP-2025-F45A4', 'ready', '2025-10-24', 'magiging tagapagmana ka boss', '2025-10-06 06:48:20.36731', '2025-10-06 06:48:20.36731');

-- --------------------------------------------------------

--
-- Table structure for table `successor_candidates_archive`
--

CREATE TABLE `successor_candidates_archive` (
  `id` int(11) NOT NULL,
  `branch_id` varchar(50) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `readiness` varchar(50) DEFAULT NULL,
  `effective_at` date DEFAULT NULL,
  `development_plan` text DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `successor_candidates_archive`
--

INSERT INTO `successor_candidates_archive` (`id`, `branch_id`, `employee_id`, `readiness`, `effective_at`, `development_plan`, `deleted_at`) VALUES
(1, 'BR705C31', 'EMP-2025-F45A4', 'ready', NULL, 'you are the next tagapagmana', '2025-10-06 06:55:21');

-- --------------------------------------------------------

--
-- Table structure for table `training_enrolls`
--

CREATE TABLE `training_enrolls` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `training_id` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'enrolled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `training_enrolls`
--

INSERT INTO `training_enrolls` (`id`, `employee_id`, `training_id`, `status`, `created_at`) VALUES
(1, 'EMP-2025-0541F', 'TRN-0001', 'enrolled', '2025-10-05 09:22:38'),
(2, 'EMP-2025-0541F', 'TRN-0000', 'enrolled', '2025-10-05 12:04:01'),
(3, 'EMP-2025-F45A4', 'TRN-0000', 'enrolled', '2025-10-05 12:14:35');

-- --------------------------------------------------------

--
-- Table structure for table `training_sessions`
--

CREATE TABLE `training_sessions` (
  `id` int(11) NOT NULL,
  `training_id` varchar(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `trainer` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `training_sessions`
--

INSERT INTO `training_sessions` (`id`, `training_id`, `title`, `description`, `start_datetime`, `end_datetime`, `location`, `trainer`, `capacity`, `created_at`, `updated_at`) VALUES
(9, 'TRN-0000', 'becoming who you are', 'overcoming fears', '2025-10-05 19:54:00', '2025-10-05 20:54:00', 'company gym', 'stephen curry', 20, '2025-10-05 11:54:41', '2025-10-05 11:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `training_sessions_archive`
--

CREATE TABLE `training_sessions_archive` (
  `id` int(11) NOT NULL,
  `training_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `trainer` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `training_sessions_archive`
--

INSERT INTO `training_sessions_archive` (`id`, `training_id`, `title`, `description`, `start_datetime`, `end_datetime`, `location`, `trainer`, `capacity`, `archived_at`) VALUES
(1, 'TRN-0001', 'how to wash hands', 'teaching you proper wash hands', '2025-10-05 12:06:00', '2025-10-05 14:06:00', 'virtual', 'lebron james', 30, '2025-10-06 14:37:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competencies_archive`
--
ALTER TABLE `competencies_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_archive`
--
ALTER TABLE `course_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_enrolls`
--
ALTER TABLE `course_enrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ess_request`
--
ALTER TABLE `ess_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ess_request_archive`
--
ALTER TABLE `ess_request_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_modules`
--
ALTER TABLE `learning_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `succession_positions`
--
ALTER TABLE `succession_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `succession_positions_archive`
--
ALTER TABLE `succession_positions_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `successor_candidates`
--
ALTER TABLE `successor_candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `successor_candidates_archive`
--
ALTER TABLE `successor_candidates_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_enrolls`
--
ALTER TABLE `training_enrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_sessions`
--
ALTER TABLE `training_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_sessions_archive`
--
ALTER TABLE `training_sessions_archive`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `competencies_archive`
--
ALTER TABLE `competencies_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_archive`
--
ALTER TABLE `course_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_enrolls`
--
ALTER TABLE `course_enrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ess_request`
--
ALTER TABLE `ess_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `ess_request_archive`
--
ALTER TABLE `ess_request_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `learning_modules`
--
ALTER TABLE `learning_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `succession_positions`
--
ALTER TABLE `succession_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `succession_positions_archive`
--
ALTER TABLE `succession_positions_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `successor_candidates`
--
ALTER TABLE `successor_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `successor_candidates_archive`
--
ALTER TABLE `successor_candidates_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `training_enrolls`
--
ALTER TABLE `training_enrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training_sessions`
--
ALTER TABLE `training_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `training_sessions_archive`
--
ALTER TABLE `training_sessions_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
