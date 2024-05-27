-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2024 at 01:18 AM
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
-- Database: `blood_donate`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(257) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918');

-- --------------------------------------------------------

--
-- Table structure for table `blood_request`
--

CREATE TABLE `blood_request` (
  `id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blood_recipient_name` varchar(100) NOT NULL,
  `recipient_age` int(11) NOT NULL,
  `recipient_tel_no` int(11) NOT NULL,
  `reason_for_request` varchar(255) NOT NULL,
  `request_status` varchar(250) NOT NULL,
  `donate_date` date DEFAULT NULL,
  `donate_time` time NOT NULL,
  `donate_location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_request`
--

INSERT INTO `blood_request` (`id`, `recipient_id`, `donor_id`, `date`, `blood_recipient_name`, `recipient_age`, `recipient_tel_no`, `reason_for_request`, `request_status`, `donate_date`, `donate_time`, `donate_location`) VALUES
(14, 4, 5, '2024-05-27 21:54:23', 'Senesh Dilshan chanduma', 25, 768678313, 'asda', 'Confirm', '2024-05-31', '20:28:00', 'galle coop'),
(15, 4, 6, '2024-05-27 21:53:54', 'Danushka gunathilaka', 22, 768678313, 'adfasdad', 'pending', '0000-00-00', '00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `donation_status`
--

CREATE TABLE `donation_status` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_status`
--

INSERT INTO `donation_status` (`id`, `donor_id`, `status`) VALUES
(2, 4, 'like_to_donate'),
(3, 5, 'like_to_donate'),
(4, 6, 'like_to_donate');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `create_date`) VALUES
(4, 'Senesh', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2024-05-18 08:24:13'),
(5, 'Dilka', '6b51d431df5d7f141cbececcf79edf3dd861c3b4069f0b11661a3eefacbba918', '2024-05-18 09:20:48'),
(6, 'Dasun', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '2024-05-18 13:38:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `contact_info` varchar(15) NOT NULL,
  `blood_type` varchar(3) NOT NULL,
  `age` int(3) NOT NULL,
  `profile_picture` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `full_name`, `contact_info`, `blood_type`, `age`, `profile_picture`) VALUES
(1, 4, 'Senesh Dilshan', '0713828553', 'O+', 23, ''),
(2, 5, 'Dilka Subash', '0112356789', 'B+', 22, ''),
(3, 6, 'Dasun Shanaka', '0774589654', 'A+', 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blood_request`
--
ALTER TABLE `blood_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donation_status`
--
ALTER TABLE `donation_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blood_request`
--
ALTER TABLE `blood_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `donation_status`
--
ALTER TABLE `donation_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donation_status`
--
ALTER TABLE `donation_status`
  ADD CONSTRAINT `donation_status_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user_info`
--
ALTER TABLE `user_info`
  ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
