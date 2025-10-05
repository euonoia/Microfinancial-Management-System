-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2025 at 12:28 PM
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
-- Database: `microfinance_hr2`
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
(2, 7749, 'pgog', '$2y$10$fffqf6CIjcSudGGX2wKiZuXysuQeyNvpWCp1NHdCIxDwkXveaYk12', 'ken@email.com', 'ken pogi', '2025-10-05 03:05:30');

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
(1, '', 'credit risk assessment', 'to assess the credit risks', NULL, '2025-10-05 03:29:15'),
(2, 'COMP001', 'lebron james', 'to become a lebrom james', 'leadership', '2025-10-05 03:34:06');

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
(2, 'COUR001', 'how to be kind', 'teaching you to be kind', 'www.google.com', 20, '2025-10-05 01:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('enrolled','completed') DEFAULT 'enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(17, 'EMP-2025-0541F', 'COUR001', 'enrolled');

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
(4, '', 'ken', 'espanola', 'ken espanola', 'loan officer', '2025-10-16', 'mainbranch', 'ken@email.com', '0912345890', '$2y$10$dsfsTGi.YQkYS1dzEIf9R.2oYZRZ1XVPD8vqgT4g5SDZAeSIroZeW', 'employee', '2025-10-05 03:37:36', '2025-10-05 03:37:36'),
(5, '', 'john', 'doe', 'john doe', 'loan officer', '2025-10-30', 'mainbranch', 'johndoe@email.com', '12344535234', '$2y$10$hRoJd/YC5E9AvusI.nft2uwvYqDwxktuNXUg3tmJvRhv5pEbKqfVK', 'employee', '2025-10-05 03:56:15', '2025-10-05 03:56:15'),
(6, 'EMP-2025-0541F', 'john', 'lennon', 'john lennon', 'loan officer', '2025-10-05', 'mainbranch', 'johnlennon@email.com', '0912345890', '$2y$10$nk3cT0PCeLizC1xFGd8N1uxwWpqbDzedywKIya5jP8WkkpJAzcD1q', 'employee', '2025-10-05 07:21:13', '2025-10-05 07:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `employee_competencies`
--

CREATE TABLE `employee_competencies` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `level` varchar(50) DEFAULT NULL,
  `assessed_at` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ess_request`
--

INSERT INTO `ess_request` (`id`, `ess_id`, `employee_id`, `type`, `details`, `status`, `created_at`, `updated_at`) VALUES
(30, 'ESS0001', '6', 'Overtime', 'usto ko pira', 'pending', '2025-10-05 04:07:38', '2025-10-05 10:07:38'),
(31, 'ESS0002', 'EMP-2025-0541F', 'Overtime', 'usto ko pera', 'pending', '2025-10-05 04:10:20', '2025-10-05 10:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `ess_requests`
--

CREATE TABLE `ess_requests` (
  `id` int(11) NOT NULL,
  `ess_id` varchar(100) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ess_requests`
--

INSERT INTO `ess_requests` (`id`, `ess_id`, `employee_id`, `type`, `details`, `status`, `created_at`, `updated_at`) VALUES
(9, '', 6, 'to be the best', '2313', 'pending', '2025-10-05 09:42:26', '2025-10-05 09:42:26.397811');

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
(1, 'BR-0001', 'manager', 'medium');

-- --------------------------------------------------------

--
-- Table structure for table `successor_candidates`
--

CREATE TABLE `successor_candidates` (
  `id` int(11) NOT NULL,
  `branch_id` varchar(100) NOT NULL,
  `employee_id` varchar(100) NOT NULL,
  `readiness` varchar(100) NOT NULL,
  `development_plan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `successor_candidates`
--

INSERT INTO `successor_candidates` (`id`, `branch_id`, `employee_id`, `readiness`, `development_plan`) VALUES
(0, 'BR-0001', '6', 'ready', 'gakling'),
(0, 'BR-0001', '6', 'ready', 'gakling'),
(0, 'BR-0001', '6', 'ready', 'lebron haha'),
(0, 'BR-0001', 'EMP-2025-0541F', 'ready', 'galinfg'),
(0, 'BR-0001', 'EMP-2025-0541F', 'ready', 'galinfg');

-- --------------------------------------------------------

--
-- Table structure for table `trainings`
--

CREATE TABLE `trainings` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `trainer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `training_attendance`
--

CREATE TABLE `training_attendance` (
  `id` int(11) NOT NULL,
  `training_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `attended` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `training_enrollments`
--

CREATE TABLE `training_enrollments` (
  `id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `progress` enum('Not Started','In Progress','Completed') DEFAULT 'Not Started'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(1, 'EMP-2025-0541F', 'TRN-0001', 'enrolled', '2025-10-05 09:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `training_programs`
--

CREATE TABLE `training_programs` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `competency_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Planned','Ongoing','Completed') DEFAULT 'Planned'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(8, 'TRN-0001', 'how to wash hands', 'teaching you proper wash hands', '2025-10-05 12:06:00', '2025-10-05 14:06:00', 'virtual', 'lebron james', 30, '2025-10-05 04:06:17', '2025-10-05 04:06:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `employee_id` (`employee_id`);

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
-- Indexes for table `employee_competencies`
--
ALTER TABLE `employee_competencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ess_request`
--
ALTER TABLE `ess_request`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ess_id` (`ess_id`);

--
-- Indexes for table `ess_requests`
--
ALTER TABLE `ess_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `learning_modules`
--
ALTER TABLE `learning_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `competency_id` (`competency_id`);

--
-- Indexes for table `succession_positions`
--
ALTER TABLE `succession_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainings`
--
ALTER TABLE `trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_attendance`
--
ALTER TABLE `training_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `training_id` (`training_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `training_enrolls`
--
ALTER TABLE `training_enrolls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `training_programs`
--
ALTER TABLE `training_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `competency_id` (`competency_id`);

--
-- Indexes for table `training_sessions`
--
ALTER TABLE `training_sessions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `course_enrolls`
--
ALTER TABLE `course_enrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employee_competencies`
--
ALTER TABLE `employee_competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ess_request`
--
ALTER TABLE `ess_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `ess_requests`
--
ALTER TABLE `ess_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `learning_modules`
--
ALTER TABLE `learning_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `succession_positions`
--
ALTER TABLE `succession_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainings`
--
ALTER TABLE `trainings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_attendance`
--
ALTER TABLE `training_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_enrolls`
--
ALTER TABLE `training_enrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `training_programs`
--
ALTER TABLE `training_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_sessions`
--
ALTER TABLE `training_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `ess_requests`
--
ALTER TABLE `ess_requests`
  ADD CONSTRAINT `ess_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `learning_modules`
--
ALTER TABLE `learning_modules`
  ADD CONSTRAINT `learning_modules_ibfk_1` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`);

--
-- Constraints for table `training_attendance`
--
ALTER TABLE `training_attendance`
  ADD CONSTRAINT `training_attendance_ibfk_1` FOREIGN KEY (`training_id`) REFERENCES `trainings` (`id`),
  ADD CONSTRAINT `training_attendance_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `training_enrollments`
--
ALTER TABLE `training_enrollments`
  ADD CONSTRAINT `training_enrollments_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `training_programs` (`id`),
  ADD CONSTRAINT `training_enrollments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `training_programs`
--
ALTER TABLE `training_programs`
  ADD CONSTRAINT `training_programs_ibfk_1` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
