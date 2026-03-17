-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 07:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `museum_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `title`, `content`) VALUES
(1, 'About Us', 'Welcome to our museum.');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 'Admin logged in', '2026-03-10 02:19:21'),
(2, 1, 'Downloaded a database backup', '2026-03-10 02:19:25'),
(3, 1, 'Checked for application updates', '2026-03-10 02:19:34'),
(4, 1, 'Downloaded a database backup', '2026-03-10 02:19:48'),
(5, 1, 'Enabled maintenance mode', '2026-03-10 02:20:03'),
(6, 1, 'Disabled maintenance mode', '2026-03-10 02:20:05'),
(7, 1, 'Downloaded a database backup', '2026-03-10 02:20:55'),
(8, 1, 'Admin logged in', '2026-03-10 02:21:51');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'password123');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image_path`) VALUES
(3, 'Local History Books', 'books.jpg'),
(4, 'Old Lamps', '238f30d987b072e5475d3fc5cb007cfb.jpg'),
(5, 'OLD WOODEN CANE AND HAT', '637157823_926852483074221_5622699732933520824_n.jpg'),
(6, 'Camera', 'd1cee0a9bd9adff6604c500fcbe368e0.jpg'),
(7, 'Old Watch', '1773036253_IMG_20260309_122219_297.jpg'),
(8, 'Music', '1773193028_IMG_20260309_122410_547.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `exhibits`
--

CREATE TABLE `exhibits` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `donated_by` varchar(255) DEFAULT NULL,
  `artifact_year` varchar(50) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `is_donated` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exhibits`
--

INSERT INTO `exhibits` (`id`, `title`, `description`, `image_url`, `category_id`, `image_path`, `donated_by`, `artifact_year`, `origin`, `is_donated`) VALUES
(5, 'KASAYSAY PAMANA NG LAHI: ALAY SA MGA TAGA LABO VOL. 1', 'The first ever local history books of Labo from 1591 - 1998 with special feature of Centennial celebration of Philippine Independence in town on June 12, 1998.', NULL, 3, '1book.jpg', 'Briones Family', 'June 12, 1998', 'Labo', 0),
(6, 'KASAYSAY PAMANA NG LAHI: ALAY SA MGA TAGA LABO VOL. 2', 'After 25 years the updated version of Kasaysayan Vol. 1 is produced which cover from 1570 – 2023, many special events that happened in the town are already included in the newest version.', NULL, 3, '2book.jpg', 'Malagueño', '1570 - 2023', 'Labo', 0),
(7, ' OLD LAMPS', 'Different sizes and design of old lamps used by our forefathers for various purposes donated by local populace.', NULL, 4, '639087160_1992852244773915_1201390354977356726_n.jpg', 'Elon', '1994', 'Labo', 0),
(8, 'OLD WOODEN CANE AND HAT', 'During Spanish times these were the common things carried by local leaders and “encargado” that symbolized authority and power in the community.', NULL, 5, '637157823_926852483074221_5622699732933520824_n.jpg', 'Garen', '1993', 'Labo', 0),
(9, 'COMPUR KODAK CAMERA', '(1910) owned by Mr. William Paguirillo (Ching Studio) is the oldest studio camera in Labo, Camarines Norte and still existing today.', NULL, 6, '638496593_3821753597961247_6027973135724181512_n.jpg', 'Paguirillo', '1890', 'Labo', 0),
(10, 'Old Wooden Clock', 'Old clock', NULL, 7, '1773036382_1773036253_IMG_20260309_122219_297.jpg', 'Brgy. San Francisco, Labo, Camarines Norte', '1939', 'N/A', 0),
(11, 'Wooden BoxType Monograph', 'Old music play disk', NULL, 8, '1773193187_1773193028_IMG_20260309_122410_547.jpg', 'Ms. Jaime Palado', '1946', 'Brgy. Fundado', 1);

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) NOT NULL,
  `guest_name` varchar(100) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `residence` varchar(255) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `num_days` int(11) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `contact_no` varchar(50) NOT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `access_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `guest_name`, `gender`, `residence`, `nationality`, `num_days`, `purpose`, `contact_no`, `visit_date`, `access_id`) VALUES
(2, 'jonel ramos', 'Male', 'Labo', 'Filipino', 1, 'Information', '09929139222', '2026-02-27 04:55:46', NULL),
(3, 'Eric John Kenneth Briones', 'Male', 'Labo', 'Filipino', 3, 'Information', '0912345678', '2026-02-27 05:11:39', NULL),
(4, 'Jonela P. Gerurro', 'Female', 'Outside Camarines Norte (Philippines)', 'Filipino', 2, 'View', '+639123458726', '2026-03-10 04:23:15', 'LABO-2026-0293'),
(5, 'Jonela P. Gerurro', 'Male', 'Labo, Camarines Norte', 'Filipino', 1, 'View', '+639123456321', '2026-03-10 05:58:36', 'LABO-2026-122316-5378'),
(6, 'Vincent Malagueno', 'Male', 'Other Municipality (Camarines Norte)', 'Filipino', 1, 'Information', '+639234561234', '2026-03-10 06:04:42', 'LABO-2026-122682-2585'),
(7, 'Sean Pural', 'Male', 'Labo, Camarines Norte', 'Filipino', 1, 'Information', '+639235167480', '2026-03-10 06:28:22', 'LABO-2026-3076'),
(8, 'Jonel Ramos', 'Male', 'Labo, Camarines Norte', 'Filipino', 1, 'OJT', '+639234561342', '2026-03-11 00:43:17', 'LABO-2026-1304'),
(9, 'Jonela P. Gerurro', 'Female', 'Daet, Camarines Norte', 'Filipino', 1, 'Information', '+639231451237', '2026-03-11 01:34:27', 'LABO-2026-1326');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_events`
--

CREATE TABLE `news_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `type` enum('news','event') DEFAULT 'news',
  `event_date` date DEFAULT NULL,
  `date_posted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news_events`
--

INSERT INTO `news_events` (`id`, `title`, `content`, `image_path`, `type`, `event_date`, `date_posted`) VALUES
(1, 'New Donated Artifacts', 'Head of Hercules', '1773190871_IMG_20260309_122351_651.jpg', 'news', '2025-02-23', '2026-03-11 01:01:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exhibits`
--
ALTER TABLE `exhibits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news_events`
--
ALTER TABLE `news_events`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exhibits`
--
ALTER TABLE `exhibits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news_events`
--
ALTER TABLE `news_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exhibits`
--
ALTER TABLE `exhibits`
  ADD CONSTRAINT `exhibits_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
