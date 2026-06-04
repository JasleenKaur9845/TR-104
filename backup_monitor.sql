-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 10:19 AM
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
-- Database: `backup_monitor`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$NXXUf3tzqcGqHyX0neTWYOQMabCQkd4cHgY8Y8ut5ES5mrRtyJ5qC', '2026-04-13 16:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `backups`
--

CREATE TABLE `backups` (
  `id` int(11) NOT NULL,
  `source_ip` varchar(45) NOT NULL,
  `destination_ip` varchar(45) NOT NULL,
  `source_path` varchar(255) NOT NULL,
  `destination_path` varchar(255) NOT NULL DEFAULT '/backups',
  `status` enum('RUNNING','SUCCESS','FAILED') DEFAULT 'RUNNING',
  `percentage` int(11) DEFAULT 0,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `job_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `backups`
--

INSERT INTO `backups` (`id`, `source_ip`, `destination_ip`, `source_path`, `destination_path`, `status`, `percentage`, `reason`, `created_at`, `updated_at`, `job_id`) VALUES
(29, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/test.txt', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/test.txt ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-15 09:36:24', '2026-05-15 09:36:37', '1234'),
(30, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/test.txt', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/test.txt ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-15 09:38:45', '2026-05-15 09:38:53', '1778837924'),
(31, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 20, 'Cannot reach source 192.168.253.162 ? ping failed. Check network.', '2026-05-18 07:12:50', '2026-05-18 07:12:58', '1779088369'),
(32, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 07:14:45', '2026-05-18 07:14:49', '1779088484'),
(33, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 07:14:57', '2026-05-18 07:15:02', '1779088497'),
(34, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/test.txt', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/test.txt ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-18 07:17:08', '2026-05-18 07:17:27', '1779088626'),
(35, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 07:35:13', '2026-05-18 07:35:19', '1779089712'),
(36, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk/jazzyranger', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 07:35:52', '2026-05-18 07:35:57', '1779089752'),
(37, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/jazzy', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/jazzy ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-18 07:40:35', '2026-05-28 08:21:15', '999'),
(38, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 08:16:17', '2026-05-18 08:16:22', '1779092176'),
(39, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'home/jasleen/backups', 'FAILED', 10, 'Invalid destination path: home/jasleen/backups ? must start with /', '2026-05-18 08:17:20', '2026-05-18 08:17:23', '1779092239'),
(40, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 08:25:21', '2026-05-18 08:25:25', '1779092719'),
(41, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/test.txt', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/test.txt ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-18 08:37:20', '2026-05-18 08:37:30', '1779093437'),
(42, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/Jasleenk', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-18 08:38:14', '2026-05-18 08:38:19', '1779093494'),
(43, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/jazzy', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/jazzy ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-23 07:55:10', '2026-05-28 08:18:42', ''),
(44, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/simran', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-28 07:14:38', '2026-05-28 07:14:44', '1779952476'),
(45, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/jazzy', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/jazzy ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-28 07:15:44', '2026-05-28 07:15:50', '1779952543'),
(46, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/simran', 'C:/Program Files/Git/home/jasleen/backups', 'FAILED', 30, 'SCP failed (exit code 255). Check SSH keys, paths, and permissions.', '2026-05-28 08:04:42', '2026-05-28 08:04:47', '1779955479'),
(47, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/jazzy', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/jazzy ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-28 08:05:43', '2026-05-28 08:05:51', '1779955543'),
(48, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/simran', 'C:/Program Files/Git/home/jasleen/backups', 'SUCCESS', 100, 'Backup complete. deepak@192.168.253.162:/home/deepak/simran ? jasleen@192.168.253.197:/home/jasleen/backups', '2026-05-28 08:14:10', '2026-05-28 08:14:18', '1779956050'),
(49, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/deepak', 'C:/Program Files/Git/home/jasleen/backups', 'SUCCESS', 100, 'Backup complete. deepak@192.168.253.162:/home/deepak/deepak ? jasleen@192.168.253.197:/home/jasleen/backups', '2026-05-28 08:25:52', '2026-05-28 08:25:59', '1779956752'),
(50, '192.168.253.197', '192.168.253.162', 'C:/Program Files/Git/home/jasleen/jasleen', 'C:/Program Files/Git/home/deepak/backups', 'SUCCESS', 100, 'Backup complete. jasleen@192.168.253.197:/home/jasleen/jasleen ? deepak@192.168.253.162:/home/deepak/backups', '2026-05-28 08:27:19', '2026-05-28 08:27:27', '1779956838'),
(51, '192.168.253.162', '192.168.253.197', 'C:/Program Files/Git/home/deepak/simran', 'C:/Program Files/Git/home/jasleen/backups', 'SUCCESS', 100, 'Backup complete. deepak@192.168.253.162:/home/deepak/simran ? jasleen@192.168.253.197:/home/jasleen/backups', '2026-06-02 08:17:24', '2026-06-02 08:17:32', '1780388241');

-- --------------------------------------------------------

--
-- Table structure for table `servers`
--

CREATE TABLE `servers` (
  `id` int(11) NOT NULL,
  `server_ip` varchar(45) NOT NULL,
  `server_name` varchar(100) NOT NULL,
  `ssh_username` varchar(64) NOT NULL DEFAULT 'root',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `servers`
--

INSERT INTO `servers` (`id`, `server_ip`, `server_name`, `ssh_username`, `created_at`) VALUES
(15, '192.168.253.197', '1', 'jasleen', '2026-04-30 08:40:28'),
(16, '192.168.253.162', '2', 'deepak', '2026-04-30 08:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'auto_refresh_interval', '2', '2026-04-13 16:12:05'),
(2, 'dashboard_ip', '192.168.137.1', '2026-04-30 08:39:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `backups`
--
ALTER TABLE `backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `servers`
--
ALTER TABLE `servers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `server_ip` (`server_ip`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `backups`
--
ALTER TABLE `backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `servers`
--
ALTER TABLE `servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
