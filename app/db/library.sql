-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 09, 2026 at 09:39 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone_enc` varbinary(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `email`, `phone_enc`, `password_hash`, `is_active`, `created_at`) VALUES
(1, 'Yaseen', 'yaseen@local.com', NULL, '$2y$10$iviOiUv9jWTdxO3sSDVlFeYCeKrV3Lh8IqYmTPnCZgfCGi3IOPxSC', 1, '2025-12-25 13:09:46');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_type` enum('admin','user') NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `actor_type`, `actor_id`, `action`, `details`, `created_at`) VALUES
(1, 'admin', 1, 'BOOK_ADD', 'Added book: python', '2025-12-25 13:38:21'),
(2, 'admin', 1, 'BOOK_DELETE', 'Deleted book ID: 6', '2025-12-25 13:38:44'),
(3, 'admin', 1, 'ADMIN_LOGIN', 'Admin logged in', '2025-12-25 14:20:49'),
(4, 'user', 4, 'MFA_SUCCESS', 'User OTP verified', '2025-12-25 14:29:36'),
(5, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-25 14:32:04'),
(6, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-25 14:32:13'),
(7, 'user', 4, 'MFA_SUCCESS', 'User OTP verified', '2025-12-25 15:09:13'),
(8, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-25 15:10:19'),
(9, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-25 15:10:31'),
(10, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-25 22:47:17'),
(11, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-25 22:47:46'),
(12, 'admin', 1, 'BOOK_DELETE', 'Deleted book ID: 5', '2025-12-25 22:48:11'),
(13, 'admin', 1, 'BOOK_ADD', 'Added book: yaseen', '2025-12-25 22:48:50'),
(14, 'admin', 1, 'ADMIN_LOGOUT', 'Admin logged out', '2025-12-25 22:49:21'),
(15, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-25 22:49:30'),
(16, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-25 22:49:50'),
(17, 'user', 5, 'MFA_SUCCESS', 'User OTP verified', '2025-12-25 22:53:06'),
(18, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-25 23:26:46'),
(19, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-25 23:26:56'),
(20, 'admin', 1, 'USER_STATUS_CHANGE', 'User ID: 5', '2025-12-25 23:27:39'),
(21, 'admin', 1, 'USER_STATUS_CHANGE', 'User ID: 5', '2025-12-25 23:27:40'),
(22, 'admin', 1, 'USER_STATUS_CHANGE', 'User ID: 5', '2025-12-25 23:27:45'),
(23, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-28 06:56:30'),
(24, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-28 06:57:10'),
(25, 'admin', 1, 'ADMIN_LOGOUT', 'Admin logged out', '2025-12-28 06:58:29'),
(26, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2025-12-28 07:03:24'),
(27, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2025-12-28 07:03:34'),
(28, 'admin', 1, 'BOOK_ADD', 'Added book: Mo', '2025-12-28 07:05:10'),
(29, 'admin', 1, 'BOOK_ADD', 'Added book: Lorina Alhozon', '2025-12-28 07:06:13'),
(30, 'admin', 1, 'BOOK_ADD', 'Added book: Mukhtarat', '2025-12-28 07:07:09'),
(31, 'admin', 1, 'BOOK_ADD', 'Added book: Ja\'ash alfuaad', '2025-12-28 07:08:12'),
(32, 'admin', 1, 'BOOK_ADD', 'Added book: Hikayet Somod', '2025-12-28 07:10:39'),
(33, 'admin', 1, 'BOOK_ADD', 'Added book: Ja\'ash alfuaad', '2025-12-28 07:10:59'),
(34, 'admin', 1, 'BOOK_DELETE', 'Deleted book ID: 2', '2025-12-28 07:11:08'),
(35, 'admin', 1, 'BOOK_DELETE', 'Deleted book ID: 3', '2025-12-28 07:11:12'),
(36, 'admin', 1, 'BOOK_DELETE', 'Deleted book ID: 8', '2025-12-28 07:11:15'),
(37, 'admin', 1, 'ADMIN_LOGOUT', 'Admin logged out', '2025-12-28 07:11:45'),
(38, 'user', 6, 'MFA_SUCCESS', 'User OTP verified', '2025-12-28 07:12:23'),
(39, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2026-01-05 09:20:04'),
(40, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2026-01-05 09:20:18'),
(41, 'admin', 1, 'MFA_OTP_SENT', 'Admin OTP generated (demo)', '2026-01-06 10:52:51'),
(42, 'admin', 1, 'MFA_SUCCESS', 'Admin OTP verified', '2026-01-06 10:53:06');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(160) DEFAULT NULL,
  `category` varchar(120) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `category`, `description`, `is_active`, `created_at`) VALUES
(7, 'yaseen', 'C++', 'Programing', 'ssssssss', 1, '2025-12-25 22:48:50'),
(9, 'Lorina Alhozon', 'Farah Soman', 'depression', '', 1, '2025-12-28 07:06:13'),
(10, 'Mukhtarat', 'Farah Soman', 'talking about life', '', 1, '2025-12-28 07:07:09'),
(11, 'Ja\'ash alfuaad', 'Farah Soman', '', '', 1, '2025-12-28 07:08:12'),
(12, 'Hikayet Somod', 'Farah Soman', 'About Palestine', '', 1, '2025-12-28 07:10:39'),
(13, 'Ja\'ash alfuaad', 'Farah Soman', '', '', 1, '2025-12-28 07:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone_enc` varbinary(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone_enc`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
(4, 'yaseen', 'yaseen@hotmail.com', NULL, '$2y$10$oD6OX50FWXz26IbU2DE8H.K3xO6DnNyRCZKrLOMpsSz3LkGyPujJe', 'user', 1, '2025-12-25 14:29:12'),
(5, 'Zaid', 'Zaid@email.com', 0x6557536347626c645044464c334c445a6b476f64357868636b344636336f515061777956474255484a31376c52466e797178773d, '$2y$10$CEzi53qC.MMSljo0i8oKDeJpOBmvqE5bUsEa3gt5Icj7lnfpQAp2q', 'user', 0, '2025-12-25 22:52:17'),
(6, 'Farah', 'Farah@local.com', 0x32552f544a5675775a664438564335634473467430365257564878736e6e636867794c473759644e704a6337513471754c542f4e, '$2y$10$zujOoDChhkHGOHq2ByfazeBxFUCI2kxIlouMvleyBIM6mBMUl2NGS', 'user', 1, '2025-12-28 06:59:33'),
(7, 'Hasan', 'Hasan@local.com', 0x397350747576662f33625a6a427662493961424748456c473532687430506e33785a466a3878794a697064764737513d, '$2y$10$VzKi4LDR1IXxuMyTBClnk.OKY11bhgIVI9HflRyAsuBWFwbNw9l1i', 'user', 1, '2026-01-06 10:55:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admins_email` (`email`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actor` (`actor_type`,`actor_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
