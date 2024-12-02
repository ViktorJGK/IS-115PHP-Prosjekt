-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 02. Des, 2024 17:00 PM
-- Tjener-versjon: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookingsystem`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `adults` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `check_in`, `check_out`, `adults`, `children`, `total_price`, `created_at`) VALUES
(7, 9, 2, '2024-12-08', '2024-12-15', 1, 1, 4200.00, '2024-12-01 16:51:57');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `unavailable_from` date DEFAULT NULL,
  `unavailable_to` date DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type_id`, `is_available`, `unavailable_from`, `unavailable_to`, `description`) VALUES
(1, '101', 1, 0, NULL, NULL, NULL),
(2, '102', 2, 1, NULL, NULL, NULL),
(3, '103', 2, 2, NULL, NULL, NULL),
(4, '104', 2, 1, NULL, NULL, NULL),
(5, '105', 1, 0, NULL, NULL, NULL),
(6, '106', 2, 2, NULL, NULL, NULL),
(7, '107', 1, 0, NULL, NULL, NULL), 
(8, '108', 2, 1, NULL, NULL, NULL),
(9, '109', 2, 2, NULL, NULL, NULL),
(10, '110', 1, 0, NULL, NULL, NULL),
(11, '111', 2, 1, NULL, NULL, NULL),
(12, '112', 2, 1, NULL, NULL, NULL),
(13, '113', 1, 0, NULL, NULL, NULL),
(14, '114', 2, 1, NULL, NULL, NULL),
(15, '115', 2, 2, NULL, NULL, NULL),
(16, '116', 1, 0, NULL, NULL, NULL),
(17, '117', 2, 1, NULL, NULL, NULL),
(18, '118', 2, 2, NULL, NULL, NULL),
(19, '119', 1, 0, NULL, NULL, NULL),
(20, '120', 2, 1, NULL, NULL, NULL),
(21, '121', 2, 2, NULL, NULL, NULL),
(22, '122', 1, 0, NULL, NULL, NULL),
(23, '123', 2, 1, NULL, NULL, NULL),
(24, '124', 2, 2, NULL, NULL, NULL),
(25, '125', 1, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `room_types`
--

CREATE TABLE `room_types` (
  `room_type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `max_adults` int(11) NOT NULL,
  `max_children` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `room_types`
--

INSERT INTO `room_types` (`room_type_id`, `type_name`, `description`, `max_adults`, `max_children`, `price`) VALUES
(1, 'Enkeltrom', 'Et komfortabelt enkeltrom med alle nødvendige fasiliteter.', 1, 0, 450),
(2, 'Dobbeltrom', 'Et rom med plass til to voksne og ett barn.', 2, 1, 600),
(3, 'Junior Suite', 'En romslig suite med plass til to voksne og to barn.', 2, 2, 1000);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` tinyint(1) NOT NULL COMMENT 'boolean 0 = gjest, 1 = admin',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `last_failed_attempt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`, `failed_attempts`, `lockout_until`, `last_failed_attempt`) VALUES
(9, 'Viktor_0', '$2y$10$7ufUc9qClqNbb0Q7gXwkLuZBjzkkF7U692PNYBtEiAnhiG/1W8gmi', 'viktorkallhovd02@gmail.com', 0, '2024-09-13 11:30:02', 0, NULL, NULL),
(11, 'Viktor_1', '$2y$10$jX0J2v7W4HdHBvsILwLfo.aXsRS1ujM8jAQgmPhRgkTSr.K5/gxN6', 'viktor@gmail.com', 1, '2024-09-13 12:34:09', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_room` (`room_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `fk_room_type` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`room_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `room_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Begrensninger for tabell `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`room_type_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
