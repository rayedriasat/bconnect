-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 07:33 AM
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
-- Database: `bloodconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admin`
--

CREATE TABLE `Admin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `BloodTypeCompatibility`
--

CREATE TABLE `BloodTypeCompatibility` (
  `donor_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `recipient_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `BloodInventory`
--
ALTER TABLE `BloodInventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DonationAppointment`
--
ALTER TABLE `DonationAppointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DonationRequest`
--
ALTER TABLE `DonationRequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DonationRequestHistory`
--
ALTER TABLE `DonationRequestHistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Donor`
--
ALTER TABLE `Donor`
  MODIFY `donor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Hospital`
--
ALTER TABLE `Hospital`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Location`
--
ALTER TABLE `Location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Matches`
--
ALTER TABLE `Matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Message`
--
ALTER TABLE `Message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Notification`
--
ALTER TABLE `Notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PasswordResets`
--
ALTER TABLE `PasswordResets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Reminder`
--
ALTER TABLE `Reminder`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `VerificationCodes`
--
ALTER TABLE `VerificationCodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
