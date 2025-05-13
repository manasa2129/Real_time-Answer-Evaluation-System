-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2025 at 10:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `real_time_evaluation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `educator_profiles`
--

CREATE TABLE `educator_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `experience_years` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educator_profiles`
--

INSERT INTO `educator_profiles` (`id`, `user_id`, `department`, `designation`, `experience_years`, `created_at`) VALUES
(2, 31, 'CSE', 'Asst.Professor', 7, '2025-02-25 05:15:02'),
(3, 36, 'CSE', 'Asst. Professor', 3, '2025-02-28 09:33:37'),
(4, 39, 'Anatomy', 'HOD', 21, '2025-02-28 10:12:18'),
(5, 40, 'CSE', 'Asst. Professor', 10, '2025-02-28 14:52:05'),
(6, 45, 'CSE', 'Asst. Professor', 4, '2025-03-19 05:38:07');

-- --------------------------------------------------------

--
-- Table structure for table `educator_uploads`
--

CREATE TABLE `educator_uploads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educator_uploads`
--

INSERT INTO `educator_uploads` (`id`, `user_id`, `filename`, `uploaded_at`) VALUES
(1, 31, 'NANGUNOORI CHANDU Resume.pdf', '2025-02-27 10:30:42'),
(2, 31, 'IELTS Registration Acknowledgement.pdf', '2025-02-28 09:24:51'),
(3, 36, 'Review Student Registration Details – IELTS IDP India.pdf', '2025-02-28 09:34:01'),
(4, 31, '2. abstract_template_for major projects.doc', '2025-03-01 08:09:09'),
(5, 31, 'deepak-2024-ijca-924038.pdf', '2025-03-01 09:44:19'),
(6, 31, 'WhatsApp Image 2025-03-20 at 6.16.49 PM.jpeg', '2025-03-20 12:50:04'),
(7, 31, 'dummy_file_7.txt', '2025-03-20 20:24:02'),
(8, 31, 'dummy_file_8.txt', '2025-03-20 20:25:57'),
(9, 31, 'dummy_file_9.txt', '2025-03-20 20:32:23'),
(10, 31, 'dummy_file_10.txt', '2025-03-20 20:33:24'),
(11, 31, 'dummy_file_11.txt', '2025-03-20 20:41:51'),
(12, 31, 'dummy_file_12.txt', '2025-03-20 20:43:10'),
(13, 31, 'dummy_file_13.txt', '2025-03-20 20:44:08'),
(14, 31, 'dummy_file_14.txt', '2025-03-20 21:01:26'),
(15, 31, 'dummy_file_15.txt', '2025-03-20 21:48:01'),
(16, 31, 'dummy_file_16.txt', '2025-03-20 21:48:57'),
(17, 31, 'dummy_file_17.txt', '2025-03-21 04:47:37'),
(18, 31, 'dummy_file_18.txt', '2025-03-21 06:29:51'),
(19, 31, 'dummy_file_19.txt', '2025-03-21 06:31:57'),
(20, 31, 'dummy_file_20.txt', '2025-03-21 06:39:31'),
(21, 31, 'dummy_file_21.txt', '2025-03-21 06:41:02'),
(22, 31, 'dummy_file_22.txt', '2025-03-21 06:42:58'),
(23, 31, 'dummy_file_23.txt', '2025-03-21 06:46:17'),
(24, 36, 'dummy_file_24.txt', '2025-03-21 06:47:59'),
(25, 31, 'dummy_file_25.txt', '2025-04-03 07:20:34'),
(26, 31, 'dummy_file_26.txt', '2025-04-03 07:20:38'),
(27, 31, 'dummy_file_27.txt', '2025-04-03 08:04:18'),
(28, 31, 'dummy_file_28.txt', '2025-04-03 08:06:17'),
(29, 31, 'dummy_file_29.txt', '2025-04-03 08:09:02'),
(30, 31, 'dummy_file_30.txt', '2025-04-03 08:09:47');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `upload_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `student_id` int(11) NOT NULL,
  `educator_id` int(11) NOT NULL,
  `grammar_score` double NOT NULL,
  `relevance_score` double NOT NULL,
  `overall_score` double NOT NULL,
  `status` enum('Pending','Reviewed') NOT NULL DEFAULT 'Pending',
  `evaluated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `upload_id`, `title`, `student_id`, `educator_id`, `grammar_score`, `relevance_score`, `overall_score`, `status`, `evaluated_at`) VALUES
(2, 7, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-20 20:24:16'),
(3, 8, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-20 20:26:10'),
(4, 11, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-20 20:41:56'),
(5, 12, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-20 20:43:14'),
(6, 13, '', 1, 31, 50, 85.71428571428571, 67.85714285714286, 'Reviewed', '2025-03-20 20:44:12'),
(7, 14, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-20 21:01:37'),
(8, 15, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-20 21:48:16'),
(9, 16, '', 1, 31, 50, 85.71428571428571, 67.85714285714286, 'Reviewed', '2025-03-20 21:49:08'),
(10, 17, '', 1, 31, 50, 85.71428571428571, 67.85714285714286, 'Reviewed', '2025-03-21 04:47:55'),
(11, 18, '', 1, 31, 50, 85.71428571428571, 67.85714285714286, 'Reviewed', '2025-03-21 06:30:06'),
(12, 19, '', 1, 31, 66.66666666666667, 94.33962264150944, 80.50314465408806, 'Reviewed', '2025-03-21 06:32:06'),
(13, 20, '', 1, 31, 0, 1.5625, 0.78125, 'Reviewed', '2025-03-21 06:39:45'),
(14, 21, '', 1, 31, 0, 1.5625, 0.78125, 'Reviewed', '2025-03-21 06:41:08'),
(15, 22, '', 1, 31, 50, 85.71428571428571, 67.85714285714286, 'Reviewed', '2025-03-21 06:43:03'),
(16, 23, '', 38, 36, 40, 62.5, 51.25, 'Reviewed', '2025-03-21 06:46:28'),
(17, 24, '', 38, 36, 50, 85.71428571428571, 67.85714285714286, 'Reviewed', '2025-03-21 06:48:06');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enrollment_number` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `section` varchar(10) NOT NULL DEFAULT 'Unknown',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `educator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `enrollment_number`, `course`, `year`, `section`, `created_at`, `educator_id`) VALUES
(1, 1, '22b65a0513', 'CSE', 4, 'B', '2025-02-25 04:12:15', 31),
(2, 13, '2110080039', 'CSE-AI&DS', 4, 'A', '2025-02-25 04:15:16', 1),
(3, 29, '211008039', 'CSE-AI&DS', 3, 'A', '2025-02-25 04:54:14', NULL),
(6, 2, '12345678', 'MBBS', 1, 'A', '2025-02-25 08:07:11', 39),
(7, 38, '1234567', 'CSE', 3, 'A', '2025-02-28 09:46:59', 36),
(8, 34, '123456', 'MBBS', 1, 'A', '2025-02-28 10:14:54', 39),
(9, 41, '12345670', '0', 4, 'A', '2025-02-28 14:56:15', 40),
(10, 44, '1234567890', 'CSE', 2, 'A', '2025-03-01 08:03:23', 36);

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `user_id`, `filename`, `uploaded_at`) VALUES
(1, 31, 'Review Student Registration Details – IELTS IDP India.pdf', '2025-02-28 08:25:25'),
(2, 31, 'Review Student Registration Details – IELTS IDP India.pdf', '2025-02-28 08:25:48'),
(3, 31, 'Review Student Registration Details – IELTS IDP India.pdf', '2025-02-28 08:27:09'),
(4, 31, 'University List.pdf', '2025-02-28 09:25:31'),
(5, 31, 'University List.pdf', '2025-02-28 09:31:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','educator','admin') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Manasa', 'manasapanchagnula25@gmail.com', '$2y$10$uDAK9cOpgp0nU4OdIijNAuZNIrap41gI6PquPPEb81NkP282L1hxG', 'student', '2025-02-21 15:56:39'),
(2, 'Tanu', 'tanu@gmail.com', '$2y$10$j5nz.X9didVtl8bqNNEpw.esbnHklHJU/EWXlg7PsWb2cuZVJONJC', 'student', '2025-02-22 17:12:11'),
(13, 'Pandu', 'pandu@gmail.com', '$2y$10$oLMIV0dykXpj/0Q4hEV2xuLPo8Do7GxnOpSwxBwwzj5RxSDdBMQre', 'student', '2025-02-24 16:06:36'),
(29, 'Vikas', 'vikas@gmail.com', '$2y$10$z.WwqwKAi2RbMW1TlTdrQOeaJ9XT.TWYfnjmfZdFPgGrwmqan.f5m', 'student', '2025-02-25 04:46:17'),
(31, 'Chandu', 'chandu@gmail.com', '$2y$10$z433t9oKS7SyXc/y8742m.gI24/RW19cmmMvQ4x5FtyqZLEmgLSh.', 'educator', '2025-02-25 05:14:41'),
(34, 'Teju', 'teju@gmail.com', '$2y$10$NVdC3.9MzMMrAoBuGPespeGXSscc0ekhGG38p51hH3TseQC7ibDAy', 'student', '2025-02-27 10:51:45'),
(36, 'Chandra', 'chandra@gmail.com', '$2y$10$H71Aa2linYM8.3UGcZr7t.l4VXDrbbH5fspbFBVhJgqH29Eg68vWC', 'educator', '2025-02-28 09:33:05'),
(38, 'Hari', 'hari@gmail.com', '$2y$10$DRYuWSIukwqH7v6/QllZi.CxTA.604lz.379eipzSunL6p.z3myAm', 'student', '2025-02-28 09:43:39'),
(39, 'Manu', 'manu@gmail.com', '$2y$10$iWBncnmq/YztCao5DxZBFeDadmgeOYb.JW2J0RE01OsH0n0SUVPfO', 'educator', '2025-02-28 10:11:42'),
(40, 'Madhu', 'madhu@gmail.com', '$2y$10$Zwlbf9dXWZPOAeeFqE9fq.kJ/7iU.eLEMv7032CqhYb3Lw3FRP26i', 'educator', '2025-02-28 14:51:06'),
(41, 'Thanmaiyee', 'tanu1@gmail.com', '$2y$10$rHdmklTY2it.1QhiQ28sSefJ7H/eelg4TF.xSzN1CGCSZSU1/pv3u', 'student', '2025-02-28 14:52:38'),
(43, 'N. Chandu', 'chanduN@gmail.com', '$2y$10$AArbhsNH8zxuhkCX1rgMGuQ0ByUP/iBFPKWoZItw/H95lZ6omc3/2', 'educator', '2025-03-01 05:37:48'),
(44, 'Prabhas', 'prabhas@gmail.com', '$2y$10$wIyTWpGzliqJOVTQP5bzLunz6SfcTczEA2m35jo5PeJ.gCJW1csmG', 'student', '2025-03-01 08:02:16'),
(45, 'manu1', 'manu1@gmail.com', '$2y$10$W8qE4q38m/sqbFwlenGWV.rz.UIJYvGcsoGEpacNSKRKFwRCkF4uO', 'educator', '2025-03-19 05:37:52'),
(46, 'admin', 'admin@gmail.com', '$2y$10$Yw41tCnbOzeSVI3zTxLTn.PCxGO8BPgTP5OpwOEqTWiA2dNaoXMuS', 'admin', '2025-03-20 19:37:15'),
(49, 'student', 'student@gmail.com', '$2y$10$CTOlxD0Zyq7QrYIwKMvqROc4nWYj/zhQ7/LztKplrCuT.4YMEMtTK', 'student', '2025-03-20 21:45:25'),
(50, 'educator', 'educator@gmail.com', '$2y$10$MD.8RQXlmC7J02.u4mjG2eStCJBi2q4UX4BZgVSCBy2YtsuPYMNye', 'educator', '2025-03-20 21:46:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `educator_profiles`
--
ALTER TABLE `educator_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `educator_uploads`
--
ALTER TABLE `educator_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_evaluations_upload` (`upload_id`),
  ADD KEY `fk_evaluations_student` (`student_id`),
  ADD KEY `fk_evaluations_educator` (`educator_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_number` (`enrollment_number`),
  ADD KEY `fk_student_educator` (`educator_id`),
  ADD KEY `fk_student_profiles_user` (`user_id`),
  ADD KEY `idx_student_profiles_enrollment_number` (`enrollment_number`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `educator_profiles`
--
ALTER TABLE `educator_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `educator_uploads`
--
ALTER TABLE `educator_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `educator_profiles`
--
ALTER TABLE `educator_profiles`
  ADD CONSTRAINT `educator_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `educator_uploads`
--
ALTER TABLE `educator_uploads`
  ADD CONSTRAINT `educator_uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`upload_id`) REFERENCES `educator_uploads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_evaluations_educator` FOREIGN KEY (`educator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_evaluations_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_evaluations_upload` FOREIGN KEY (`upload_id`) REFERENCES `educator_uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `fk_student_educator` FOREIGN KEY (`educator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_student_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
