-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2026 at 09:41 AM
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
-- Database: `complaint_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`) VALUES
(1, 'admin@example.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` varchar(50) DEFAULT 'pending',
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `reply` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `name`, `email`, `phone`, `category`, `subject`, `description`, `priority`, `status`, `date_submitted`, `reply`) VALUES
(5, 'Pavan Naik', 'pavannaik1328@gmail.com', '7862079070', 'delivery', 'bus pick up', 'bus doesn\'t pick up me from my home ', 'medium', 'Resolved', '2025-10-13 11:54:52', ''),
(6, 'Pavan Naik', 'cc@gmail.com', '7862079070', 'product', 'book', 'some  book are not available ', 'high', 'Pending', '2025-11-29 15:48:24', ''),
(7, 'Pavan Naik', 'qq@gmail.com', '7862079070', 'delivery', 'food', 'some food are not available ', 'medium', 'pending', '2025-12-04 13:28:48', ''),
(9, 'Pavan Naik', 'bb@gmail.com', '7862079070', 'delivery', 'food', 'food', 'medium', 'Pending', '2025-12-04 14:07:55', ''),
(10, 'sumit', 'bb@gmail.com', '9876543210', 'delivery', 'food', ' some foods not available ', 'high', 'pending', '2025-12-06 04:07:22', ''),
(11, 'Pavan Naik', 'bb@gmail.com', '7862079070', 'delivery', 'food', 'food nn', 'medium', 'pending', '2025-12-06 04:24:21', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` varchar(50) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `pin` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `dob`, `mobile`, `pin`, `address`) VALUES
(3, 'pavannaik1328@gmail.com', '$2y$10$.87wzISh0n2mmJ4gvRdkBew95O418xI9nhjqpzsIHQJuhoH4vsDzS', '2025-10-08', '7862079070', '394230', 'Bhatya'),
(4, 'ss@gmail.com', '$2y$10$.0NrbkqtxzjQWnMY8Hs.4uHYWKACT5r5earNsH96.Hzv0f3HWG3Je', '2025-10-14', '7862079070', '394230', 'Bhatya'),
(5, 'kk@Gmail.com', '$2y$10$ZvfGf.Sd7O8fX4DeeveCmupRnsF0h6fyGM/4c0igs0Mw8svIDVLwu', '2025-10-08', '7862079070', '394230', 'Bhatya'),
(6, 'cc@gmail.com', '$2y$10$TON3YIePaZdaopkm10OSyeyApYS.42nYHgixSet2xhzJPc9eVCEBK', '2025-11-13', '7862079070', '394230', 'Bhatya'),
(7, 'qq@gmail.com', '$2y$10$HMU5ddGxuSfVh1lLiI6e0OGTKnWB9iJ3o6KQbaKIoRPRpvO0NLVoq', '2025-12-11', '7862079070', '394230', 'Bhatya'),
(8, 'bb@gmail.com', '$2y$10$N/2h5nvzXqhew/.jG54.y.VxhbmFi8FRDeDttW7M9ERTUJ3B/nPyO', '2025-12-11', '7862079070', '394230', 'Bhatya');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
