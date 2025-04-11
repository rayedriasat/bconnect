-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql303.infinityfree.com
-- Generation Time: Apr 10, 2025 at 05:55 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38623557_bloodconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admin`
--

CREATE TABLE `Admin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Admin`
--

INSERT INTO `Admin` (`admin_id`, `user_id`) VALUES
(1, 1),
(2, 3),
(3, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `BloodInventory`
--

CREATE TABLE `BloodInventory` (
  `inventory_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `blood_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `quantity` int(11) NOT NULL,
  `last_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `BloodInventory`
--

INSERT INTO `BloodInventory` (`inventory_id`, `hospital_id`, `blood_type`, `quantity`, `last_updated`) VALUES
(1, 1, 'A+', 18, '2025-03-28 09:03:21'),
(2, 1, 'B+', 27, '2025-03-28 09:03:21'),
(3, 1, 'O+', 18, '2025-03-28 09:03:21'),
(4, 1, 'AB+', 14, '2025-03-28 09:03:21'),
(5, 1, 'A-', 9, '2025-03-28 09:03:21'),
(6, 1, 'B-', 11, '2025-03-28 09:03:21'),
(7, 1, 'O-', 3, '2025-03-28 09:03:21'),
(8, 1, 'AB-', 10, '2025-03-28 09:03:21'),
(9, 2, 'A+', 25, '2025-03-28 09:03:21'),
(10, 2, 'B+', 23, '2025-03-28 09:03:21'),
(11, 2, 'O+', 27, '2025-03-28 09:03:21'),
(12, 2, 'AB+', 11, '2025-03-28 09:03:21'),
(13, 2, 'A-', 11, '2025-03-28 09:03:21'),
(14, 2, 'B-', 13, '2025-03-28 09:03:21'),
(15, 2, 'O-', 15, '2025-03-28 09:03:21'),
(16, 2, 'AB-', 2, '2025-03-28 09:03:21'),
(17, 3, 'A+', 11, '2025-03-28 09:03:21'),
(18, 3, 'B+', 26, '2025-03-28 09:03:21'),
(19, 3, 'O+', 25, '2025-03-28 09:03:21'),
(20, 3, 'AB+', 16, '2025-03-28 09:03:21'),
(21, 3, 'A-', 11, '2025-03-28 09:03:21'),
(22, 3, 'B-', 9, '2025-03-28 09:03:21'),
(23, 3, 'O-', 6, '2025-03-28 09:03:21'),
(24, 3, 'AB-', 8, '2025-03-28 09:03:21'),
(25, 4, 'A+', 21, '2025-03-28 09:03:21'),
(26, 4, 'B+', 27, '2025-03-28 09:03:21'),
(27, 4, 'O+', 19, '2025-03-28 09:03:21'),
(28, 4, 'AB+', 8, '2025-03-28 09:03:21'),
(29, 4, 'A-', 5, '2025-03-28 09:03:21'),
(30, 4, 'B-', 10, '2025-03-28 09:03:21'),
(31, 4, 'O-', 7, '2025-03-28 09:03:21'),
(32, 4, 'AB-', 12, '2025-03-28 09:03:21'),
(33, 5, 'A+', 22, '2025-03-28 09:03:21'),
(34, 5, 'B+', 24, '2025-03-28 09:03:21'),
(35, 5, 'O+', 20, '2025-03-28 09:03:21'),
(36, 5, 'AB+', 10, '2025-03-28 09:03:21'),
(37, 5, 'A-', 12, '2025-03-28 09:03:21'),
(38, 5, 'B-', 5, '2025-03-28 09:03:21'),
(39, 5, 'O-', 9, '2025-03-28 09:03:21'),
(40, 5, 'AB-', 4, '2025-03-28 09:03:21'),
(41, 6, 'A+', 19, '2025-03-28 09:03:21'),
(42, 6, 'B+', 20, '2025-03-28 09:03:21'),
(43, 6, 'O+', 15, '2025-03-28 09:03:21'),
(44, 6, 'AB+', 11, '2025-03-28 09:03:21'),
(45, 6, 'A-', 14, '2025-03-28 09:03:21'),
(46, 6, 'B-', 5, '2025-03-28 09:03:21'),
(47, 6, 'O-', 7, '2025-03-28 09:03:21'),
(48, 6, 'AB-', 2, '2025-03-28 09:03:21'),
(49, 7, 'A+', 6, '2025-03-28 09:03:21'),
(50, 7, 'B+', 34, '2025-03-28 09:03:21'),
(51, 7, 'O+', 18, '2025-03-28 09:03:21'),
(52, 7, 'AB+', 2, '2025-03-28 09:03:21'),
(53, 7, 'A-', 13, '2025-03-28 09:03:21'),
(54, 7, 'B-', 7, '2025-03-28 09:03:21'),
(55, 7, 'O-', 0, '2025-03-28 09:03:21'),
(56, 7, 'AB-', 2, '2025-03-28 09:03:21'),
(57, 8, 'A+', 24, '2025-03-28 09:03:21'),
(58, 8, 'B+', 31, '2025-03-28 09:03:21'),
(59, 8, 'O+', 17, '2025-03-28 09:03:21'),
(60, 8, 'AB+', 20, '2025-03-28 09:03:21'),
(61, 8, 'A-', 2, '2025-03-28 09:03:21'),
(62, 8, 'B-', 9, '2025-03-28 09:03:21'),
(63, 8, 'O-', 13, '2025-03-28 09:03:21'),
(64, 8, 'AB-', 12, '2025-03-28 09:03:21'),
(65, 9, 'A+', 5, '2025-03-28 09:03:21'),
(66, 9, 'B+', 21, '2025-03-28 09:03:21'),
(67, 9, 'O+', 22, '2025-03-28 09:03:21'),
(68, 9, 'AB+', 16, '2025-03-28 09:03:21'),
(69, 9, 'A-', 3, '2025-03-28 09:03:21'),
(70, 9, 'B-', 6, '2025-03-28 09:03:21'),
(71, 9, 'O-', 6, '2025-03-28 09:03:21'),
(72, 9, 'AB-', 13, '2025-03-28 09:03:21'),
(73, 10, 'A+', 15, '2025-03-28 09:03:21'),
(74, 10, 'B+', 16, '2025-03-28 09:03:21'),
(75, 10, 'O+', 20, '2025-03-28 09:03:21'),
(76, 10, 'AB+', 3, '2025-03-28 09:03:21'),
(77, 10, 'A-', 15, '2025-03-28 09:03:21'),
(78, 10, 'B-', 8, '2025-03-28 09:03:21'),
(79, 10, 'O-', 12, '2025-03-28 09:03:21'),
(80, 10, 'AB-', 8, '2025-03-28 09:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `BloodTypeCompatibility`
--

CREATE TABLE `BloodTypeCompatibility` (
  `donor_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `recipient_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `BloodTypeCompatibility`
--

INSERT INTO `BloodTypeCompatibility` (`donor_type`, `recipient_type`) VALUES
('O-', 'O-'),
('O-', 'O+'),
('O-', 'A-'),
('O-', 'A+'),
('O-', 'B-'),
('O-', 'B+'),
('O-', 'AB-'),
('O-', 'AB+'),
('O+', 'O+'),
('O+', 'A+'),
('O+', 'B+'),
('O+', 'AB+'),
('A-', 'A-'),
('A-', 'A+'),
('A-', 'AB-'),
('A-', 'AB+'),
('A+', 'A+'),
('A+', 'AB+'),
('B-', 'B-'),
('B-', 'B+'),
('B-', 'AB-'),
('B-', 'AB+'),
('B+', 'B+'),
('B+', 'AB+'),
('AB-', 'AB-'),
('AB-', 'AB+'),
('AB+', 'AB+');

-- --------------------------------------------------------

--
-- Table structure for table `DonationAppointment`
--

CREATE TABLE `DonationAppointment` (
  `appointment_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `scheduled_time` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DonationAppointment`
--

INSERT INTO `DonationAppointment` (`appointment_id`, `request_id`, `donor_id`, `scheduled_time`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 9, '2025-03-31 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(4, 4, 2, '2025-03-29 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(10, 9, 11, '2025-04-04 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(13, 12, 5, '2025-04-03 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(14, 13, 9, '2025-03-28 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(16, 16, 3, '2025-04-11 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(17, 17, 5, '2025-04-10 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21'),
(18, 18, 8, '2025-03-28 04:03:21', 'pending', '2025-03-28 03:03:21', '2025-03-28 03:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `DonationRequest`
--

CREATE TABLE `DonationRequest` (
  `request_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `blood_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `quantity` int(11) NOT NULL,
  `urgency` enum('low','medium','high') DEFAULT 'medium',
  `contact_person` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DonationRequest`
--

INSERT INTO `DonationRequest` (`request_id`, `hospital_id`, `requester_id`, `blood_type`, `quantity`, `urgency`, `contact_person`, `contact_phone`, `created_at`) VALUES
(2, 3, 24, 'B+', 5, 'low', 'Dr. Abdul Alam', '01940455994', '2025-03-15 04:03:21'),
(4, 2, 5, 'A+', 2, 'low', 'Dr. Jahan Khatun', '01766224058', '2025-03-15 04:03:21'),
(9, 5, 29, 'O+', 5, 'medium', 'Dr. Zahir Mahmud', '01998254987', '2025-03-14 04:03:21'),
(12, 7, 20, 'AB-', 2, 'high', 'Dr. Tahmid Miah', '01729312678', '2025-03-04 04:03:21'),
(13, 1, 22, 'B+', 2, 'high', 'Dr. Imran Siddique', '01730407591', '2025-02-27 04:03:21'),
(14, 4, 9, 'B-', 1, 'high', 'Dr. Mohammad Begum', '01947819096', '2025-03-28 04:03:21'),
(16, 4, 22, 'O-', 1, 'low', 'Dr. Jahan Akter', '01751051626', '2025-03-16 04:03:21'),
(17, 6, 16, 'AB-', 1, 'high', 'Dr. Nasir Hossain', '01889229668', '2025-02-28 04:03:21'),
(18, 5, 5, 'A-', 1, 'low', 'Dr. Farida Ahmed', '01999739294', '2025-03-03 04:03:21');

-- --------------------------------------------------------

--
-- Table structure for table `DonationRequestHistory`
--

CREATE TABLE `DonationRequestHistory` (
  `history_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `blood_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `quantity` int(11) NOT NULL,
  `urgency` enum('low','medium','high') DEFAULT 'medium',
  `contact_person` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `fulfilled_at` datetime DEFAULT NULL,
  `fulfilled_by` int(11) DEFAULT NULL,
  `status` enum('fulfilled','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DonationRequestHistory`
--

INSERT INTO `DonationRequestHistory` (`history_id`, `request_id`, `hospital_id`, `requester_id`, `blood_type`, `quantity`, `urgency`, `contact_person`, `contact_phone`, `created_at`, `fulfilled_at`, `fulfilled_by`, `status`) VALUES
(1, 1, 8, 18, 'AB-', 5, 'high', 'Dr. Kamal Sultana', '01873398353', '2025-03-08 04:03:21', '2025-03-28 09:14:49', 5, 'fulfilled'),
(2, 3, 4, 23, 'B+', 2, 'low', 'Dr. Fatema Khan', '01923793797', '2025-03-14 04:03:21', '2025-03-28 09:14:49', 9, 'fulfilled'),
(3, 5, 4, 28, 'O-', 2, 'high', 'Dr. Rahim Huq', '01737547219', '2025-03-06 04:03:21', '2025-03-28 09:14:49', 16, 'fulfilled'),
(4, 6, 7, 17, 'AB-', 1, 'high', 'Dr. Imran Miah', '01898835091', '2025-03-08 04:03:21', '2025-03-28 09:14:49', 5, 'fulfilled'),
(5, 7, 7, 29, 'O+', 3, 'medium', 'Dr. Abdul Rahman', '01966905532', '2025-03-05 04:03:21', '2025-03-28 09:14:49', 11, 'fulfilled'),
(6, 8, 9, 9, 'A+', 2, 'medium', 'Dr. Imran Huq', '01887112917', '2025-03-07 04:03:21', '2025-03-28 09:14:49', 1, 'fulfilled'),
(7, 10, 1, 15, 'O+', 5, 'high', 'Dr. Farida Begum', '01766443042', '2025-03-24 04:03:21', '2025-03-28 09:14:49', 11, 'fulfilled'),
(8, 11, 9, 19, 'B+', 4, 'high', 'Dr. Abdul Sultana', '01994633695', '2025-03-15 04:03:21', '2025-03-28 09:14:49', 9, 'fulfilled'),
(9, 15, 1, 17, 'B+', 3, 'high', 'Dr. Nasir Mahmud', '01780195407', '2025-02-27 04:03:21', '2025-03-28 09:14:49', 9, 'fulfilled'),
(10, 19, 6, 24, 'B+', 2, 'low', 'Dr. Nasir Hossain', '01793691606', '2025-03-22 04:03:21', '2025-03-28 09:14:49', 9, 'fulfilled'),
(11, 20, 2, 3, 'AB+', 2, 'high', 'Dr. Zahir Sarkar', '01913183462', '2025-03-21 04:03:21', '2025-03-28 09:14:49', 10, 'fulfilled');

-- --------------------------------------------------------

--
-- Table structure for table `Donor`
--

CREATE TABLE `Donor` (
  `donor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `date_of_birth` date NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `has_medical_condition` tinyint(1) NOT NULL DEFAULT 0,
  `medical_notes` text DEFAULT NULL,
  `last_donation_date` date DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Donor`
--

INSERT INTO `Donor` (`donor_id`, `user_id`, `blood_type`, `date_of_birth`, `weight`, `has_medical_condition`, `medical_notes`, `last_donation_date`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 'A+', '2004-01-01', '65.00', 0, '', NULL, 1, '2025-03-28 02:49:45', '2025-03-28 11:18:51'),
(2, 2, 'A+', '2002-01-01', '68.00', 0, '', NULL, 1, '2025-03-28 02:52:16', '2025-03-28 02:52:16'),
(3, 6, 'O-', '2003-08-17', '76.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(4, 7, 'A+', '1986-08-09', '64.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(5, 8, 'AB-', '1971-10-01', '52.00', 0, NULL, '2025-02-07', 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(6, 9, 'A-', '1974-02-15', '60.00', 0, NULL, '2024-10-11', 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(7, 10, 'A+', '1982-01-24', '59.00', 0, NULL, '2024-12-06', 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(8, 11, 'A-', '1997-09-22', '58.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(9, 12, 'B+', '1969-11-02', '80.00', 0, NULL, '2025-02-14', 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(10, 13, 'AB+', '2006-02-03', '70.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(11, 14, 'O+', '2001-01-08', '74.00', 0, NULL, NULL, 0, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(12, 15, 'O-', '1996-12-26', '69.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(13, 16, 'AB+', '2007-02-17', '62.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(14, 17, 'A-', '1998-07-24', '50.00', 0, NULL, NULL, 1, '2025-03-28 03:02:52', '2025-03-28 03:02:52'),
(15, 18, 'O-', '1987-01-16', '59.00', 0, NULL, NULL, 1, '2025-03-28 03:02:53', '2025-03-28 03:02:53'),
(16, 19, 'O-', '2002-05-10', '54.00', 0, NULL, '2025-01-23', 1, '2025-03-28 03:02:53', '2025-03-28 03:02:53'),
(17, 20, 'O-', '1999-11-08', '59.00', 0, NULL, NULL, 1, '2025-03-28 03:02:53', '2025-03-28 03:02:53'),
(18, 31, 'A+', '2000-10-10', '45.00', 0, '', NULL, 1, '2025-04-02 13:29:10', '2025-04-02 13:29:49');

-- --------------------------------------------------------

--
-- Table structure for table `Hospital`
--

CREATE TABLE `Hospital` (
  `hospital_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Hospital`
--

INSERT INTO `Hospital` (`hospital_id`, `name`, `address`, `email`, `phone_number`) VALUES
(1, 'Dhaka Medical College Hospital', 'Secretariat Road, Dhaka 1000', 'dmch@example.com', '01712345678'),
(2, 'Bangabandhu Sheikh Mujib Medical University', 'Shahbag, Dhaka 1000', 'bsmmu@example.com', '01712345679'),
(3, 'Square Hospital', '18/F West Panthapath, Dhaka 1205', 'square@example.com', '01712345680'),
(4, 'Chittagong Medical College Hospital', 'K.B. Fazlul Kader Road, Chittagong', 'cmch@example.com', '01712345681'),
(5, 'Rajshahi Medical College Hospital', 'Medical College Road, Rajshahi', 'rmch@example.com', '01712345682'),
(6, 'Sylhet MAG Osmani Medical College Hospital', 'Medical College Road, Sylhet', 'somch@example.com', '01712345683'),
(7, 'Khulna Medical College Hospital', 'KDA Avenue, Khulna', 'kmch@example.com', '01712345684'),
(8, 'Ibn Sina Hospital', 'House-48, Road-9/A, Dhanmondi, Dhaka', 'ibnsina@example.com', '01712345685'),
(9, 'Evercare Hospital Dhaka', 'Plot 81, Block E, Bashundhara R/A, Dhaka', 'evercare@example.com', '01712345686'),
(10, 'United Hospital Limited', 'Plot 15, Road 71, Gulshan, Dhaka', 'united@example.com', '01712345687');

-- --------------------------------------------------------

--
-- Table structure for table `Location`
--

CREATE TABLE `Location` (
  `location_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `location_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Location`
--

INSERT INTO `Location` (`location_id`, `user_id`, `hospital_id`, `latitude`, `longitude`, `address`, `location_name`) VALUES
(1, 1, NULL, '23.81104584', '90.37170189', 'Mirpur 10, Mirpur, Mirpur 10, Dhaka, Dhaka Metropolitan, Dhaka District, Dhaka Division, 1216, Bangladesh', 'Home'),
(2, 2, NULL, '23.81113249', '90.37167329', 'Mirpur 10, Mirpur, Mirpur 10, Dhaka, Dhaka Metropolitan, Dhaka District, Dhaka Division, 1216, Bangladesh', 'Home'),
(3, NULL, 1, '23.72720000', '90.38540000', 'Secretariat Road, Dhaka 1000', NULL),
(4, NULL, 2, '23.73990000', '90.37210000', 'Shahbag, Dhaka 1000', NULL),
(5, NULL, 3, '23.74650000', '90.37600000', '18/F West Panthapath, Dhaka 1205', NULL),
(6, NULL, 4, '22.35690000', '91.83170000', 'K.B. Fazlul Kader Road, Chittagong', NULL),
(7, NULL, 5, '24.36360000', '88.62410000', 'Medical College Road, Rajshahi', NULL),
(8, NULL, 6, '24.89490000', '91.86870000', 'Medical College Road, Sylhet', NULL),
(9, NULL, 7, '22.84560000', '89.54030000', 'KDA Avenue, Khulna', NULL),
(10, NULL, 8, '23.79770000', '90.35570000', 'House-48, Road-9/A, Dhanmondi, Dhaka', NULL),
(11, NULL, 9, '23.81030000', '90.41250000', 'Plot 81, Block E, Bashundhara R/A, Dhaka', NULL),
(12, NULL, 10, '23.80180000', '90.41890000', 'Plot 15, Road 71, Gulshan, Dhaka', NULL),
(13, 3, NULL, '22.77930000', '89.52280000', 'সাচিবুনিয়া, খুলনা, খুলনা জেলা, খুলনা বিভাগ, 9260, বাংলাদেশ', 'Khulna Residence'),
(14, 4, NULL, '23.37370000', '91.16200000', 'Bojoypur, কুমিল্লা আদর্শ সদর উপজেলা, কুমিল্লা জেলা, চট্টগ্রাম বিভাগ, বাংলাদেশ', 'Comilla Residence'),
(15, 5, NULL, '22.31120000', '91.71940000', 'চট্টগ্রাম, চট্টগ্রাম জেলা, চট্টগ্রাম বিভাগ, 4000, বাংলাদেশ', 'Chittagong Residence'),
(16, 6, NULL, '24.80260000', '90.45060000', 'Konapara Mondir, Hindu temple, শেরপুর - ময়মনসিংহ মহাসড়ক, ময়মনসিংহ, ফুলপুর উপজেলা, ময়মনসিংহ জেলা, ময়মনসিংহ বিভাগ, বাংলাদেশ', 'Mymensingh Residence'),
(17, 8, NULL, '25.68440000', '89.34360000', 'রংপুর, রংপুর জেলা, রংপুর বিভাগ, বাংলাদেশ', 'Rangpur Residence'),
(18, 9, NULL, '23.38580000', '91.20730000', 'Bobonpur, কুমিল্লা আদর্শ সদর উপজেলা, কুমিল্লা জেলা, চট্টগ্রাম বিভাগ, 3500, বাংলাদেশ', 'Comilla Residence'),
(19, 10, NULL, '23.52790000', '90.43100000', 'আউটশাহী, টংগিবাড়ী উপজেলা, মুন্সিগঞ্জ জেলা, ঢাকা বিভাগ, 1521, বাংলাদেশ', 'Narayanganj Residence'),
(20, 11, NULL, '24.90340000', '91.86730000', 'সিলেট, সিলেট সদর উপজেলা, সিলেট জেলা, সিলেট বিভাগ, 3100, বাংলাদেশ', 'Sylhet Residence'),
(21, 12, NULL, '23.77540000', '90.51240000', 'রূপগঞ্জ, রূপগঞ্জ উপজেলা, নারায়নগঞ্জ জেলা, ঢাকা বিভাগ, 1460, বাংলাদেশ', 'Dhaka Residence'),
(22, 14, NULL, '24.69630000', '90.38830000', 'Ghagra, ময়মনসিংহ সদর উপজেলা, ময়মনসিংহ জেলা, ময়মনসিংহ বিভাগ, 2202, বাংলাদেশ', 'Mymensingh Residence'),
(23, 15, NULL, '23.86570000', '90.33530000', 'দামপাড়া, সাভার উপজেলা, ঢাকা জেলা, ঢাকা বিভাগ, 1341, বাংলাদেশ', 'Dhaka Residence'),
(24, 17, NULL, '22.73890000', '90.27670000', 'বাবুগঞ্জ উপজেলা, বরিশাল জেলা, বরিশাল বিভাগ, 8213, বাংলাদেশ', 'Barisal Residence'),
(25, 19, NULL, '22.38670000', '91.69390000', 'চট্টগ্রাম, চট্টগ্রাম জেলা, চট্টগ্রাম বিভাগ, 4000, বাংলাদেশ', 'Chittagong Residence'),
(26, 20, NULL, '22.31140000', '91.76170000', 'উত্তর মধ্য হালিশহর, চট্টগ্রাম, চট্টগ্রাম জেলা, চট্টগ্রাম বিভাগ, 4215, বাংলাদেশ', 'Chittagong Residence'),
(27, 21, NULL, '24.65450000', '90.47040000', 'ঈশ্বরগঞ্জ উপজেলা, ময়মনসিংহ জেলা, ময়মনসিংহ বিভাগ, 2220, বাংলাদেশ', 'Mymensingh Residence'),
(28, 22, NULL, '23.53920000', '91.11730000', 'বুড়িচং, বুড়িচং উপজেলা, কুমিল্লা জেলা, চট্টগ্রাম বিভাগ, বাংলাদেশ', 'Comilla Residence'),
(29, 23, NULL, '25.75700000', '89.31360000', 'কাউনিয়া উপজেলা, রংপুর জেলা, রংপুর বিভাগ, 5403, বাংলাদেশ', 'Rangpur Residence'),
(30, 25, NULL, '24.78480000', '90.49270000', 'Mymensingh - Netrokona Highway, ফুলপুর উপজেলা, ময়মনসিংহ জেলা, ময়মনসিংহ বিভাগ, বাংলাদেশ', 'Mymensingh Residence'),
(31, 27, NULL, '22.36650000', '91.86000000', 'চট্টগ্রাম, চট্টগ্রাম জেলা, চট্টগ্রাম বিভাগ, 4212, বাংলাদেশ', 'Chittagong Residence'),
(32, 28, NULL, '22.76900000', '89.61860000', 'Khulna - Mongla Road, কাটাখালী, খাজুরা, রূপসা উপজেলা, বাগেরহাট জেলা, খুলনা বিভাগ, 9370, বাংলাদেশ', 'Khulna Residence'),
(33, 29, NULL, '24.28040000', '88.68120000', 'Raninagar - II, Murshidabad, West Bengal, India', 'Rajshahi Residence'),
(34, 30, NULL, '23.85600000', '90.46800000', 'ভাটিরা, ঢাকা মহানগর, ঢাকা জেলা, ঢাকা বিভাগ, 0000, বাংলাদেশ', 'Dhaka Residence');

-- --------------------------------------------------------

--
-- Table structure for table `Matches`
--

CREATE TABLE `Matches` (
  `match_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Matches`
--

INSERT INTO `Matches` (`match_id`, `request_id`, `donor_id`, `score`) VALUES
(2, 2, 9, '60.00'),
(4, 4, 1, '70.00'),
(5, 4, 2, '65.00'),
(6, 4, 4, '82.00'),
(7, 4, 7, '74.00'),
(17, 9, 11, '70.00'),
(20, 12, 5, '72.00'),
(21, 13, 9, '88.00'),
(23, 16, 3, '90.00'),
(24, 16, 15, '87.00'),
(25, 16, 16, '69.00'),
(26, 17, 5, '80.00'),
(27, 18, 6, '96.00'),
(28, 18, 8, '84.00'),
(29, 18, 14, '77.00');

-- --------------------------------------------------------

--
-- Table structure for table `Message`
--

CREATE TABLE `Message` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Message`
--

INSERT INTO `Message` (`message_id`, `sender_id`, `receiver_id`, `content`, `sent_at`) VALUES
(1, 11, 10, 'Thank you for your quick response!', '2025-03-24 04:03:21'),
(2, 9, 24, 'Can you please confirm the appointment details?', '2025-02-26 04:03:21'),
(3, 24, 3, 'Thank you for offering to donate. When would be a good time?', '2025-03-12 04:03:21'),
(4, 9, 16, 'I have some questions about the donation process.', '2025-03-13 04:03:21'),
(5, 5, 24, 'Hello, I saw your blood donation request. I\'d like to help.', '2025-03-20 04:03:21'),
(6, 13, 8, 'Is the donation center at Square Hospital easy to find?', '2025-02-27 04:03:21'),
(7, 22, 8, 'Do I need to bring any identification documents?', '2025-03-08 04:03:21'),
(8, 19, 18, 'How long will the donation process take?', '2025-03-05 04:03:21'),
(9, 8, 27, 'Is the donation center at Evercare Hospital Dhaka easy to find?', '2025-03-18 04:03:21'),
(10, 4, 14, 'How long will the donation process take?', '2025-03-27 04:03:21'),
(11, 25, 12, 'I\'ve donated before at Khulna Medical College Hospital. The staff there is excellent.', '2025-03-27 04:03:21'),
(12, 13, 25, 'How long will the donation process take?', '2025-03-24 04:03:21'),
(13, 30, 20, 'I have some questions about the donation process.', '2025-03-13 04:03:21'),
(14, 28, 24, 'Is the donation center at Dhaka Medical College Hospital easy to find?', '2025-03-26 04:03:21'),
(15, 14, 21, 'Hello, I saw your blood donation request. I\'d like to help.', '2025-03-17 04:03:21'),
(16, 21, 19, 'Can you please confirm the appointment details?', '2025-03-19 04:03:21'),
(17, 29, 25, 'Do I need to bring any identification documents?', '2025-03-08 04:03:21'),
(18, 16, 22, 'Thank you for offering to donate. When would be a good time?', '2025-02-27 04:03:21'),
(19, 27, 4, 'I have some questions about the donation process.', '2025-03-17 04:03:21'),
(20, 25, 20, 'Hello, I saw your blood donation request. I\'d like to help.', '2025-03-10 04:03:21'),
(21, 26, 27, 'Hello, I saw your blood donation request. I\'d like to help.', '2025-03-22 04:03:21'),
(22, 10, 27, 'Is the donation center at Rajshahi Medical College Hospital easy to find?', '2025-03-28 04:03:21'),
(23, 5, 3, 'Thank you for your quick response!', '2025-02-28 04:03:21'),
(24, 8, 9, 'Do I need to bring any identification documents?', '2025-03-18 04:03:21'),
(25, 8, 26, 'I\'ve donated before at Square Hospital. The staff there is excellent.', '2025-03-19 04:03:21'),
(26, 6, 9, 'Hello, I saw your blood donation request. I\'d like to help.', '2025-02-26 04:03:21'),
(27, 26, 27, 'Do I need to bring any identification documents?', '2025-03-19 04:03:21'),
(28, 18, 21, 'Is the donation center at Rajshahi Medical College Hospital easy to find?', '2025-03-12 04:03:21'),
(29, 6, 8, 'Can you please confirm the appointment details?', '2025-03-01 04:03:21'),
(30, 5, 26, 'Thank you for your quick response!', '2025-03-02 04:03:21'),
(31, 31, 2, 'oi kire oi kire oi kire... rokto rokto rokto...', '2025-03-28 06:16:10'),
(32, 1, 31, 'Hi', '2025-04-02 06:34:49'),
(33, 31, 1, 'oi kire oi kire oi kire... rokto rokto rokto...', '2025-04-02 06:36:20'),
(34, 1, 31, 'XD', '2025-04-02 06:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `Notification`
--

CREATE TABLE `Notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('sms','email','in-app') NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Notification`
--

INSERT INTO `Notification` (`notification_id`, `user_id`, `message`, `type`, `sent_at`, `is_read`) VALUES
(1, 18, 'Appointment status update: The donation appointment with Farid Alam for AB- blood at Ibn Sina Hospital on March 31, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(2, 18, 'Appointment status update: The donation appointment with Farid Alam for AB- blood at Ibn Sina Hospital on March 31, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(3, 8, 'Appointment status update: Your donation appointment for AB- blood at Ibn Sina Hospital on March 31, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(4, 8, 'Appointment status update: Your donation appointment for AB- blood at Ibn Sina Hospital on March 31, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(5, 23, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Chittagong Medical College Hospital on March 29, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(6, 23, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Chittagong Medical College Hospital on March 29, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(7, 12, 'Appointment status update: Your donation appointment for B+ blood at Chittagong Medical College Hospital on March 29, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(8, 12, 'Appointment status update: Your donation appointment for B+ blood at Chittagong Medical College Hospital on March 29, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(9, 28, 'Appointment status update: The donation appointment with Fatema Siddique for O- blood at Chittagong Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(10, 28, 'Appointment status update: The donation appointment with Fatema Siddique for O- blood at Chittagong Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(11, 19, 'Appointment status update: Your donation appointment for O- blood at Chittagong Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(12, 19, 'Appointment status update: Your donation appointment for O- blood at Chittagong Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(13, 17, 'Appointment status update: The donation appointment with Farid Alam for AB- blood at Khulna Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(14, 17, 'Appointment status update: The donation appointment with Farid Alam for AB- blood at Khulna Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(15, 8, 'Appointment status update: Your donation appointment for AB- blood at Khulna Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(16, 8, 'Appointment status update: Your donation appointment for AB- blood at Khulna Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(17, 29, 'Appointment status update: The donation appointment with Jahan Sarkar for O+ blood at Khulna Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(18, 29, 'Appointment status update: The donation appointment with Jahan Sarkar for O+ blood at Khulna Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(19, 14, 'Appointment status update: Your donation appointment for O+ blood at Khulna Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(20, 14, 'Appointment status update: Your donation appointment for O+ blood at Khulna Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(21, 9, 'Appointment status update: The donation appointment with Rayed Riasat Rabbi for A+ blood at Evercare Hospital Dhaka on April 8, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(22, 9, 'Appointment status update: The donation appointment with Rayed Riasat Rabbi for A+ blood at Evercare Hospital Dhaka on April 8, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(23, 1, 'Appointment status update: Your donation appointment for A+ blood at Evercare Hospital Dhaka on April 8, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(24, 1, 'Appointment status update: Your donation appointment for A+ blood at Evercare Hospital Dhaka on April 8, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(25, 15, 'Appointment status update: The donation appointment with Jahan Sarkar for O+ blood at Dhaka Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(26, 15, 'Appointment status update: The donation appointment with Jahan Sarkar for O+ blood at Dhaka Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(27, 14, 'Appointment status update: Your donation appointment for O+ blood at Dhaka Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(28, 14, 'Appointment status update: Your donation appointment for O+ blood at Dhaka Medical College Hospital on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(29, 19, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Evercare Hospital Dhaka on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(30, 19, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Evercare Hospital Dhaka on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(31, 12, 'Appointment status update: Your donation appointment for B+ blood at Evercare Hospital Dhaka on April 4, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(32, 12, 'Appointment status update: Your donation appointment for B+ blood at Evercare Hospital Dhaka on April 4, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(33, 17, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Dhaka Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(34, 17, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Dhaka Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(35, 12, 'Appointment status update: Your donation appointment for B+ blood at Dhaka Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(36, 12, 'Appointment status update: Your donation appointment for B+ blood at Dhaka Medical College Hospital on April 7, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(37, 24, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Sylhet MAG Osmani Medical College Hospital on April 2, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(38, 24, 'Appointment status update: The donation appointment with Rahim Jahan for B+ blood at Sylhet MAG Osmani Medical College Hospital on April 2, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(39, 12, 'Appointment status update: Your donation appointment for B+ blood at Sylhet MAG Osmani Medical College Hospital on April 2, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(40, 12, 'Appointment status update: Your donation appointment for B+ blood at Sylhet MAG Osmani Medical College Hospital on April 2, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(41, 3, 'Appointment status update: The donation appointment with Jahan Alam for AB+ blood at Bangabandhu Sheikh Mujib Medical University on April 5, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(42, 3, 'Appointment status update: The donation appointment with Jahan Alam for AB+ blood at Bangabandhu Sheikh Mujib Medical University on April 5, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(43, 13, 'Appointment status update: Your donation appointment for AB+ blood at Bangabandhu Sheikh Mujib Medical University on April 5, 2025 at 4:03 AM has been Confirmed.', 'in-app', '2025-03-28 09:03:21', 0),
(44, 13, 'Appointment status update: Your donation appointment for AB+ blood at Bangabandhu Sheikh Mujib Medical University on April 5, 2025 at 4:03 AM has been Confirmed.', 'email', '2025-03-28 09:03:21', 0),
(45, 3, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-06 04:03:21', 0),
(46, 3, 'New blood request matching your B+ type in Chittagong', 'in-app', '2025-02-26 04:03:21', 1),
(47, 3, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-09 04:03:21', 0),
(48, 4, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-11 04:03:21', 0),
(49, 5, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-26 04:03:21', 1),
(50, 6, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-02-26 04:03:21', 1),
(51, 6, 'Donor found for your request at Bangabandhu Sheikh Mujib Medical University', 'in-app', '2025-03-08 04:03:21', 0),
(52, 6, 'New blood request matching your B+ type in Chittagong', 'in-app', '2025-03-07 04:03:21', 1),
(53, 6, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-23 04:03:21', 0),
(54, 6, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-28 04:03:21', 1),
(55, 7, 'Donor found for your request at Bangabandhu Sheikh Mujib Medical University', 'in-app', '2025-03-03 04:03:21', 0),
(56, 7, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-12 04:03:21', 1),
(57, 7, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-20 04:03:21', 1),
(58, 7, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-05 04:03:21', 1),
(59, 8, 'Thank you for your recent donation at Square Hospital', 'in-app', '2025-03-26 04:03:21', 0),
(60, 8, 'New blood request matching your B+ type in Chittagong', 'in-app', '2025-03-25 04:03:21', 1),
(61, 8, 'URGENT: O+ blood needed at Chittagong Medical College Hospital', 'in-app', '2025-03-04 04:03:21', 0),
(62, 8, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-07 04:03:21', 1),
(63, 8, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-25 04:03:21', 0),
(64, 9, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-03 04:03:21', 0),
(65, 10, 'Thank you for your recent donation at Square Hospital', 'in-app', '2025-03-19 04:03:21', 0),
(66, 10, 'New blood request matching your B+ type in Chittagong', 'in-app', '2025-03-07 04:03:21', 1),
(67, 10, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-26 04:03:21', 0),
(68, 10, 'Thank you for your recent donation at Square Hospital', 'in-app', '2025-03-01 04:03:21', 0),
(69, 11, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-06 04:03:21', 0),
(70, 11, 'Thank you for your recent donation at Square Hospital', 'in-app', '2025-03-06 04:03:21', 0),
(71, 11, 'Donor found for your request at Bangabandhu Sheikh Mujib Medical University', 'in-app', '2025-03-09 04:03:21', 0),
(72, 11, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-20 04:03:21', 1),
(73, 11, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-25 04:03:21', 1),
(74, 12, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-20 04:03:21', 0),
(75, 12, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-19 04:03:21', 0),
(76, 13, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-02 04:03:21', 1),
(77, 14, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-01 04:03:21', 0),
(78, 14, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-28 04:03:21', 0),
(79, 15, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-07 04:03:21', 0),
(80, 15, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-26 04:03:21', 1),
(81, 16, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-18 04:03:21', 0),
(82, 16, 'URGENT: O+ blood needed at Chittagong Medical College Hospital', 'in-app', '2025-03-02 04:03:21', 0),
(83, 16, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-08 04:03:21', 1),
(84, 17, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-27 04:03:21', 1),
(85, 18, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-12 04:03:21', 1),
(86, 18, 'Thank you for your recent donation at Square Hospital', 'in-app', '2025-03-19 04:03:21', 0),
(87, 19, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-15 04:03:21', 0),
(88, 19, 'URGENT: O+ blood needed at Chittagong Medical College Hospital', 'in-app', '2025-03-05 04:03:21', 1),
(89, 20, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-03 04:03:21', 1),
(90, 21, 'Thank you for your recent donation at Square Hospital', 'in-app', '2025-03-28 04:03:21', 1),
(91, 21, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-27 04:03:21', 1),
(92, 22, 'Donor found for your request at Bangabandhu Sheikh Mujib Medical University', 'in-app', '2025-03-13 04:03:21', 1),
(93, 22, 'URGENT: O+ blood needed at Chittagong Medical College Hospital', 'in-app', '2025-03-23 04:03:21', 1),
(94, 22, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-14 04:03:21', 1),
(95, 22, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-16 04:03:21', 0),
(96, 23, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-14 04:03:21', 1),
(97, 23, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-27 04:03:21', 1),
(98, 23, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-06 04:03:21', 1),
(99, 23, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-04 04:03:21', 0),
(100, 24, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-23 04:03:21', 1),
(101, 24, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-05 04:03:21', 0),
(102, 24, 'URGENT: O+ blood needed at Chittagong Medical College Hospital', 'in-app', '2025-03-19 04:03:21', 1),
(103, 24, 'Donor found for your request at Bangabandhu Sheikh Mujib Medical University', 'in-app', '2025-03-22 04:03:21', 1),
(104, 25, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-24 04:03:21', 1),
(105, 25, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-01 04:03:21', 0),
(106, 25, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-12 04:03:21', 1),
(107, 26, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-02-27 04:03:21', 1),
(108, 26, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-06 04:03:21', 0),
(109, 27, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-14 04:03:21', 0),
(110, 27, 'New blood request matching your B+ type in Chittagong', 'in-app', '2025-03-18 04:03:21', 0),
(111, 27, 'Admin message: Your donation offer needs verification', 'in-app', '2025-03-19 04:03:21', 1),
(112, 27, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-02-27 04:03:21', 0),
(113, 27, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-20 04:03:21', 1),
(114, 28, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-03-03 04:03:21', 0),
(115, 28, 'Appointment cancelled: Sylhet MAG Osmani Medical College Hospital', 'in-app', '2025-02-27 04:03:21', 0),
(116, 28, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-16 04:03:21', 0),
(117, 28, 'Today\'s appointment: Ibn Sina Hospital at 2:30 PM', 'in-app', '2025-03-06 04:03:21', 0),
(118, 29, 'Donor found for your request at Bangabandhu Sheikh Mujib Medical University', 'in-app', '2025-03-20 04:03:21', 1),
(119, 29, 'Your donation appointment at Dhaka Medical College Hospital has been confirmed', 'in-app', '2025-03-06 04:03:21', 0),
(120, 30, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-13 04:03:21', 0),
(121, 30, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-02-27 04:03:21', 1),
(122, 30, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-22 04:03:21', 0),
(123, 30, 'Request fulfilled: Your AB+ donation at Khulna Medical College Hospital', 'in-app', '2025-03-04 04:03:21', 0),
(124, 30, 'Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow', 'in-app', '2025-03-05 04:03:21', 0),
(125, 8, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(126, 12, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(127, 19, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(128, 8, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(129, 14, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(130, 1, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 1),
(131, 14, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(132, 12, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(133, 12, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(134, 12, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0),
(135, 13, 'Thank you for your blood donation! Your contribution has helped save lives.', 'in-app', '2025-03-28 09:14:49', 0);

-- --------------------------------------------------------

--
-- Table structure for table `PasswordResets`
--

CREATE TABLE `PasswordResets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 10 minute),
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PasswordResets`
--

INSERT INTO `PasswordResets` (`id`, `user_id`, `token`, `created_at`, `expires_at`, `is_used`) VALUES
(1, 1, '71f76f6cfbfc50706e371f912df46bbd184395370b13bff03e4d6782147341b5', '2025-04-10 21:10:47', '2025-04-10 21:20:47', 0),
(2, 1, '8dba5cd65231c9730e64808297ce40c7991c296161cd8860372697a76ee8c7ae', '2025-04-10 21:34:20', '2025-04-10 21:44:20', 0),
(3, 2, 'adff7d0c709888b72f6ccdf6cd7fe472d588b7d822accab0d58c13d3af946111', '2025-04-10 21:43:57', '2025-04-10 21:53:57', 1),
(4, 2, 'c692107dd00751c96d0dbbb634af39188b2e45b27612628c3345ef86039bdb32', '2025-04-10 21:46:13', '2025-04-10 21:56:13', 0),
(5, 1, 'd4725ca5a870314aea8b9daec3283c0a5ac4c084134bccccd018d57baaac0755', '2025-04-10 21:47:02', '2025-04-10 21:57:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Reminder`
--

CREATE TABLE `Reminder` (
  `reminder_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `method` enum('sms','email') NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Reminder`
--

INSERT INTO `Reminder` (`reminder_id`, `appointment_id`, `method`, `sent_at`) VALUES
(1, 2, 'email', '2025-03-25 20:10:45'),
(2, 2, 'email', '2025-03-27 16:16:11'),
(3, 2, 'sms', '2025-03-29 21:11:16'),
(4, 4, 'email', '2025-03-24 20:28:25'),
(7, 10, 'email', '2025-03-29 12:35:53'),
(8, 13, 'sms', '2025-03-27 11:27:06'),
(9, 13, 'email', '2025-03-27 22:09:55'),
(10, 13, 'email', '2025-03-30 08:02:29'),
(11, 14, 'email', '2025-03-25 10:16:48'),
(12, 16, 'sms', '2025-04-09 12:57:20'),
(13, 16, 'sms', '2025-04-07 09:00:21'),
(14, 16, 'sms', '2025-04-06 09:42:28'),
(15, 17, 'email', '2025-04-09 20:36:05'),
(16, 18, 'email', '2025-03-23 04:12:18'),
(17, 18, 'email', '2025-03-22 07:42:09');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(70) NOT NULL,
  `email` varchar(70) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password_hash` varchar(70) NOT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `name`, `email`, `phone_number`, `password_hash`, `two_factor_enabled`, `phone_verified`) VALUES
(1, 'Rayed Riasat Rabbi', 'rayedriasat@gmail.com', '01777158099', '$2y$10$vAdOibR24/PZVzIaLIxIA.6W0hDobaOypxx9IWEAa9uBVakov2Auu', 0, 0),
(2, 'Rayed Riasat', 'rcubethe@gmail.com', '01789176264', '$2y$10$/ymaQ8vbzzPJM.om66lwYOqmRipCgtEhOaqFl8ktK1orofGgLAYrq', 1, 0),
(3, 'Sabrina Rahman', 'admin1@bloodconnect.com', '01890952976', '$2y$10$8smuzKHqCupj1xg0Gc4FzeacGyX23seqmXUGmndsvVi4LOPPdWrTa', 0, 0),
(4, 'Ayesha Begum', 'admin2@bloodconnect.com', '01791759830', '$2y$10$y6OqSrNwdSBaERSAyqO9H.E//89y6Rfxq9PcQbTnpRt14aQhdVbNK', 0, 0),
(5, 'Ayesha Akter', 'admin3@bloodconnect.com', '01829309998', '$2y$10$zYRM7n4B9RUPZtVVoBugteXi9IZiCIE0VJdAhvOkmmOTWl9zT4dZ.', 0, 0),
(6, 'Farid Begum', 'donor1@example.com', '01750920765', '$2y$10$CTp21PalJRjzrUXt4PDmQerHJXtw2LFdCtCcHGG/ZZZQNGAgNBBsm', 0, 0),
(7, 'Jahan Uddin', 'donor2@example.com', '01972256842', '$2y$10$dMMgRIOVuY83O7uWvlet3eiAvv1Jup1pIiRkOGFJXwbnxLwmPvVWG', 0, 0),
(8, 'Farid Alam', 'donor3@example.com', '01725422092', '$2y$10$5FmUa1DYjQqYPm1vpElZ9.1ZROC2oLyK6d1BgO7azIXdSye5tuem2', 0, 0),
(9, 'Rahim Miah', 'donor4@example.com', '01869439784', '$2y$10$Y4/A6xGyo/5zj.tBOF8p4.TIV5tzGHO1B5HbyAdlICWGb3NPKBRra', 0, 0),
(10, 'Nusrat Rahman', 'donor5@example.com', '01751761168', '$2y$10$jMFSt4xPdL2tM7GmGmonTuK/1B7aSdTVS0bCImpU5uLTxxxdR2C5K', 0, 0),
(11, 'Ayesha Khatun', 'donor6@example.com', '01781259002', '$2y$10$rZJDH7f6Yj3MAu5lLV9xounguQY12WZD3.KYkaEys77nGPV2Lfbu.', 0, 0),
(12, 'Rahim Jahan', 'donor7@example.com', '01787649830', '$2y$10$E53ivj6h.fSeRyqUj4KTke9N//79jiV6a58Eb3AookytZ4oQ15Ocq', 0, 0),
(13, 'Jahan Alam', 'donor8@example.com', '01960638562', '$2y$10$S/4LUiSDCHb2uB0PyV1mS.DPrwkD.8wLOxPEOBvyjeovvbC/sKecO', 0, 0),
(14, 'Jahan Sarkar', 'donor9@example.com', '01786733696', '$2y$10$QqtMqT1Sk5Lvi.dzCogHJOxoeu7mnhJGpBFt6ebMoPDRi8SlAB5Fi', 0, 0),
(15, 'Imran Alam', 'donor10@example.com', '01963967068', '$2y$10$WuPaXBXM8/Q.hLhYF8aLauab29Z8BZSgoqwUAzHitgQWmvWPsSsKm', 0, 0),
(16, 'Tahmid Hossain', 'donor11@example.com', '01766531567', '$2y$10$eOWgyjmn/g3C8uURtmAdTeFjBUzo5oWpPDJJS4zEi3ASXaiZUmrnC', 0, 0),
(17, 'Nusrat Khatun', 'donor12@example.com', '01763758355', '$2y$10$NLtcnKUp/wT5/t8H/ZldZuAbhcaQ7eKdPqGtN4Q0KzTiaBQ9D2SA2', 0, 0),
(18, 'Rafiq Mahmud', 'donor13@example.com', '01968002672', '$2y$10$phU.3Vw4.VVVzvt9Ab0Mpuvo6.77/JmkALtxUD/MJntpMMfFb8KTa', 0, 0),
(19, 'Fatema Siddique', 'donor14@example.com', '01854722577', '$2y$10$1sKMzuWnl.NRo/YcpqDbJOyVlH/PDPSWzZonSNfAIVoIIo2tJoq8C', 0, 0),
(20, 'Farida Alam', 'donor15@example.com', '01824775110', '$2y$10$Me/A5H3tXBJYQFE4qjL88Ou7RtJ8M2kTQX8ujn1MEw39yA/Wm959m', 0, 0),
(21, 'Sabrina Islam', 'user1@example.com', '01798685152', '$2y$10$nphLGxNFVe903wErb9Ltq.7hlYZDz/go2JtwPUmlzO0Wyy5zJRtXu', 0, 0),
(22, 'Sabrina Islam', 'user2@example.com', '01712991718', '$2y$10$HGS2IsFaLfXiq7yNp.hyi.9CbRdPOtfxJuq1Oo3EFjdfceUzRooHm', 0, 0),
(23, 'Imran Miah', 'user3@example.com', '01868794340', '$2y$10$vqO6aMaRCUAm49UK0JPxweGO8.Rupi5TL0cmY9hYHwLJl6p9HphFm', 0, 0),
(24, 'Kamal Alam', 'user4@example.com', '01762744786', '$2y$10$Nmbqki8goykBkWhKx172GeMXXREzvprXYQFJzhiv.YJffC6aFERCW', 0, 0),
(25, 'Farid Rahman', 'user5@example.com', '01813701236', '$2y$10$euknKnDEfs7uP7/SymBMRONuycqYuAIwHnKz91vR1ZjDrfovpGXtq', 0, 0),
(26, 'Sadia Siddique', 'user6@example.com', '01988905742', '$2y$10$N2SeyEHsM3WFMhXVWPMIfubNnVhKssLwald4.qC1/s7gjRBvC8VBO', 0, 0),
(27, 'Farid Islam', 'user7@example.com', '01814530097', '$2y$10$3geH.9d7IkKhCw..4POueu3K5wFZjkNiImvpbGUa37tmRIsaSHq8W', 0, 0),
(28, 'Sabrina Hossain', 'user8@example.com', '01772157214', '$2y$10$dR6cVvS4.htUA6fyGMtmXeSAdUsKD8mo3k51fWcek.CdVWg3kuMi2', 0, 0),
(29, 'Tasnim Miah', 'user9@example.com', '01897338922', '$2y$10$Zj7wCsiLGMXS2KCyps5ULu2tGZ8lU6VTtJoPLlOCUwa0z0TAdHeqi', 0, 0),
(30, 'Tahmid Chowdhury', 'user10@example.com', '01911352880', '$2y$10$0vGXeZvvUG20ludeKzNRCe7v4ImvzxiH6cnWiDl2cd13YtKYGbiz6', 0, 0),
(31, 'Walidur Rahman', 'walidur236@gmail.com', '01537507938', '$2y$10$mFnRCdmBO4pR2Qek3b8cK.xgNqx29adiBUnUBBR6SLPZs0iHWaVOG', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `VerificationCodes`
--

CREATE TABLE `VerificationCodes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 10 minute),
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `VerificationCodes`
--

INSERT INTO `VerificationCodes` (`id`, `user_id`, `code`, `created_at`, `expires_at`, `is_used`) VALUES
(1, 2, '521806', '2025-04-10 21:49:17', '2025-04-10 21:59:17', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Admin`
--
ALTER TABLE `Admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `BloodInventory`
--
ALTER TABLE `BloodInventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `BloodTypeCompatibility`
--
ALTER TABLE `BloodTypeCompatibility`
  ADD PRIMARY KEY (`donor_type`,`recipient_type`);

--
-- Indexes for table `DonationAppointment`
--
ALTER TABLE `DonationAppointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `DonationRequest`
--
ALTER TABLE `DonationRequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `requester_id` (`requester_id`);

--
-- Indexes for table `DonationRequestHistory`
--
ALTER TABLE `DonationRequestHistory`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `fulfilled_by` (`fulfilled_by`);

--
-- Indexes for table `Donor`
--
ALTER TABLE `Donor`
  ADD PRIMARY KEY (`donor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Hospital`
--
ALTER TABLE `Hospital`
  ADD PRIMARY KEY (`hospital_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `Location`
--
ALTER TABLE `Location`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `Matches`
--
ALTER TABLE `Matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `Message`
--
ALTER TABLE `Message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `Notification`
--
ALTER TABLE `Notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `PasswordResets`
--
ALTER TABLE `PasswordResets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Reminder`
--
ALTER TABLE `Reminder`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `VerificationCodes`
--
ALTER TABLE `VerificationCodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Admin`
--
ALTER TABLE `Admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `BloodInventory`
--
ALTER TABLE `BloodInventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `DonationAppointment`
--
ALTER TABLE `DonationAppointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `DonationRequest`
--
ALTER TABLE `DonationRequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `DonationRequestHistory`
--
ALTER TABLE `DonationRequestHistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Donor`
--
ALTER TABLE `Donor`
  MODIFY `donor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `Hospital`
--
ALTER TABLE `Hospital`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Location`
--
ALTER TABLE `Location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `Matches`
--
ALTER TABLE `Matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `Message`
--
ALTER TABLE `Message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `Notification`
--
ALTER TABLE `Notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `PasswordResets`
--
ALTER TABLE `PasswordResets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Reminder`
--
ALTER TABLE `Reminder`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `VerificationCodes`
--
ALTER TABLE `VerificationCodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Admin`
--
ALTER TABLE `Admin`
  ADD CONSTRAINT `Admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `BloodInventory`
--
ALTER TABLE `BloodInventory`
  ADD CONSTRAINT `BloodInventory_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `Hospital` (`hospital_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `DonationAppointment`
--
ALTER TABLE `DonationAppointment`
  ADD CONSTRAINT `DonationAppointment_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `DonationRequest` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `DonationAppointment_ibfk_2` FOREIGN KEY (`donor_id`) REFERENCES `Donor` (`donor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `DonationRequest`
--
ALTER TABLE `DonationRequest`
  ADD CONSTRAINT `DonationRequest_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `Hospital` (`hospital_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `DonationRequest_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `DonationRequestHistory`
--
ALTER TABLE `DonationRequestHistory`
  ADD CONSTRAINT `DonationRequestHistory_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `Hospital` (`hospital_id`),
  ADD CONSTRAINT `DonationRequestHistory_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `Users` (`user_id`),
  ADD CONSTRAINT `DonationRequestHistory_ibfk_3` FOREIGN KEY (`fulfilled_by`) REFERENCES `Donor` (`donor_id`);

--
-- Constraints for table `Donor`
--
ALTER TABLE `Donor`
  ADD CONSTRAINT `Donor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Location`
--
ALTER TABLE `Location`
  ADD CONSTRAINT `Location_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Location_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `Hospital` (`hospital_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Matches`
--
ALTER TABLE `Matches`
  ADD CONSTRAINT `Matches_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `DonationRequest` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Matches_ibfk_2` FOREIGN KEY (`donor_id`) REFERENCES `Donor` (`donor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Message`
--
ALTER TABLE `Message`
  ADD CONSTRAINT `Message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Notification`
--
ALTER TABLE `Notification`
  ADD CONSTRAINT `Notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `PasswordResets`
--
ALTER TABLE `PasswordResets`
  ADD CONSTRAINT `PasswordResets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Reminder`
--
ALTER TABLE `Reminder`
  ADD CONSTRAINT `Reminder_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `DonationAppointment` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `VerificationCodes`
--
ALTER TABLE `VerificationCodes`
  ADD CONSTRAINT `VerificationCodes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
