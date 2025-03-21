-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 07:13 PM
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
-- Table structure for table `activitylog`
--

CREATE TABLE `activitylog` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `user_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `scheduled_time` datetime NOT NULL,
  `status` enum('pending','confirmed','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `donor_id`, `hospital_id`, `scheduled_time`, `status`) VALUES
(1, 1, 6, '2025-03-22 10:00:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `bloodinventory`
--

CREATE TABLE `bloodinventory` (
  `inventory_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `blood_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `quantity` int(11) NOT NULL,
  `last_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bloodinventory`
--

INSERT INTO `bloodinventory` (`inventory_id`, `hospital_id`, `blood_type`, `quantity`, `last_updated`) VALUES
(1, 2, 'O-', 5, '2025-03-21 21:13:47');

-- --------------------------------------------------------

--
-- Table structure for table `bloodtypecompatibility`
--

CREATE TABLE `bloodtypecompatibility` (
  `donor_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL,
  `recipient_type` enum('O-','O+','A-','A+','B-','B+','AB-','AB+') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bloodtypecompatibility`
--

INSERT INTO `bloodtypecompatibility` (`donor_type`, `recipient_type`) VALUES
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
-- Table structure for table `donationrequest`
--

CREATE TABLE `donationrequest` (
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
-- Table structure for table `donationrequesthistory`
--

CREATE TABLE `donationrequesthistory` (
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
-- Dumping data for table `donationrequesthistory`
--

INSERT INTO `donationrequesthistory` (`history_id`, `request_id`, `hospital_id`, `requester_id`, `blood_type`, `quantity`, `urgency`, `contact_person`, `contact_phone`, `created_at`, `fulfilled_at`, `fulfilled_by`, `status`) VALUES
(1, 1, 5, 1, 'A+', 2, 'high', 'Rayed Riasat', '01789176264', '2025-03-21 20:01:44', '2025-03-21 20:46:06', 3, 'fulfilled'),
(2, 2, 6, 1, 'A+', 5, 'low', 'Hasan Shakir', '01789176264', '2025-03-21 23:49:09', '2025-03-22 00:08:07', 1, 'fulfilled');

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
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
-- Dumping data for table `donor`
--

INSERT INTO `donor` (`donor_id`, `user_id`, `blood_type`, `date_of_birth`, `weight`, `has_medical_condition`, `medical_notes`, `last_donation_date`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 'A+', '2002-01-01', 62.00, 0, 'I guess no notes.', NULL, 1, '2025-03-21 10:14:01', '2025-03-21 17:47:51'),
(2, 2, 'B+', '2001-01-01', 71.00, 0, 'Nah', NULL, 1, '2025-03-21 14:03:08', '2025-03-21 14:03:08'),
(3, 3, 'A+', '2004-01-02', 75.20, 0, 'Fine', NULL, 1, '2025-03-21 14:27:48', '2025-03-21 15:04:02');

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

CREATE TABLE `hospital` (
  `hospital_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`hospital_id`, `name`, `address`, `email`, `phone_number`) VALUES
(1, 'Shahid Suhrawardy Hospital', 'Ser-e-Banglanagar, Collegegate, Dhaka', 'info@suhrawardyhospital.gov.bd', '9122560'),
(2, 'Ad-Din Hospital', 'Moghbazar, Dhaka', 'contact@ad-dinhospital.com', '9353391'),
(3, 'Ahmed Medical Centre Ltd', 'House # 71, Road # 15-A (New), Dhanmondi C/A, Dhaka', 'info@ahmedmedicalcentre.com', '8113628'),
(5, 'Al Anaiet Adhunik Hospital', 'House # 36, Road # 3, Dhanmondi, Dhaka', 'contact@alanaiethospital.com', '8631619'),
(6, 'Al-Helal Specialist Hospital', '150, Rokeya Sarani, Senpara Parbata, Mirpur-10, Dhaka', 'info@alhelalhospital.com', '9006820'),
(7, 'United Hospital Limited', 'Plot 15, Road 71, Gulshan, Dhaka 1212', 'info@uhlbd.com', '+8801914001234'),
(8, 'Evercare Hospital Dhaka', 'Plot # 81, Block E, Bashundhara R/A, Dhaka', 'info@evercarebd.com', '10678'),
(9, 'Square Hospitals Ltd.', '18/F, Bir Uttam Qazi Nuruzzaman Sarak, West Panthapath, Dhaka 1205', 'info@squarehospital.com', '10616'),
(10, 'BIRDEM General Hospital', '122 Kazi Nazrul Islam Avenue, Shahbag, Dhaka', 'info@birdembd.org', '9661551');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `location_name` varchar(100) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `sender_id`, `receiver_id`, `content`, `sent_at`) VALUES
(1, 1, 2, 'Hello Rcube', '2025-03-21 17:19:37'),
(2, 1, 2, 'What Are u doing?', '2025-03-21 17:19:56'),
(3, 1, 2, 'Hey there', '2025-03-21 17:20:08'),
(4, 2, 1, 'Hello Rayed', '2025-03-21 17:37:33'),
(5, 2, 1, 'What\'s up', '2025-03-21 17:37:40'),
(6, 2, 1, 'Iftar time', '2025-03-21 17:37:45'),
(7, 1, 2, 'Iftar time', '2025-03-21 17:38:46'),
(8, 3, 1, 'I can donate A+ to Al Anaiet Adhunik Hospital', '2025-03-21 20:41:06'),
(9, 3, 1, 'hello', '2025-03-21 20:41:16'),
(10, 3, 1, 'I can donate A+ to Al Anaiet Adhunik Hospital', '2025-03-21 20:45:49'),
(11, 3, 1, 'I have fulfilled this blood donation request.', '2025-03-21 20:46:06'),
(12, 1, 1, 'I can donate A+ to Al-Helal Specialist Hospital', '2025-03-21 23:49:24'),
(13, 1, 2, 'Hi', '2025-03-22 00:07:37'),
(14, 1, 3, 'Yo', '2025-03-22 00:07:45'),
(15, 1, 1, 'I have fulfilled this blood donation request.', '2025-03-22 00:08:07'),
(16, 3, 1, 'Assalamualaikum', '2025-03-22 00:11:43');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('sms','email','in-app') NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passwordresets`
--

CREATE TABLE `passwordresets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 10 minute),
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminder`
--

CREATE TABLE `reminder` (
  `reminder_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `method` enum('sms','email') NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone_number`, `password`, `two_factor_enabled`, `phone_verified`) VALUES
(1, 'Rayed', 'rayedriasat@gmail.com', '01777158099', '$2y$10$mHtQipzYeXdOdiUPmnTWxOyV80elc8x7QJjyX.q26a1tS1r2Lf2BS', 0, 0),
(2, 'Rcube', 'rcubethe@gmail.com', '01789176264', '$2y$10$b32P6urpyWOATNucEW/feOu81rsw0WqGIySxFuXtJBmpd0tCr6R5u', 0, 0),
(3, 'Azim', 'azim.tazbee@northsouth.edu', '01515005274', '$2y$10$JdcqMK46JQWr1KF.MteUIO7l4BlpCwguCL2ucrMbSx68.JGSRwQmC', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `verificationcodes`
--

CREATE TABLE `verificationcodes` (
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
-- Indexes for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `bloodinventory`
--
ALTER TABLE `bloodinventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `bloodtypecompatibility`
--
ALTER TABLE `bloodtypecompatibility`
  ADD PRIMARY KEY (`donor_type`,`recipient_type`);

--
-- Indexes for table `donationrequest`
--
ALTER TABLE `donationrequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `requester_id` (`requester_id`);

--
-- Indexes for table `donationrequesthistory`
--
ALTER TABLE `donationrequesthistory`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `fulfilled_by` (`fulfilled_by`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`donor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`hospital_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hospital_id` (`hospital_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `passwordresets`
--
ALTER TABLE `passwordresets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reminder`
--
ALTER TABLE `reminder`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `verificationcodes`
--
ALTER TABLE `verificationcodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activitylog`
--
ALTER TABLE `activitylog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bloodinventory`
--
ALTER TABLE `bloodinventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donationrequest`
--
ALTER TABLE `donationrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donationrequesthistory`
--
ALTER TABLE `donationrequesthistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `donor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hospital`
--
ALTER TABLE `hospital`
  MODIFY `hospital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `passwordresets`
--
ALTER TABLE `passwordresets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminder`
--
ALTER TABLE `reminder`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `verificationcodes`
--
ALTER TABLE `verificationcodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD CONSTRAINT `activitylog_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `donor` (`donor_id`),
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`);

--
-- Constraints for table `bloodinventory`
--
ALTER TABLE `bloodinventory`
  ADD CONSTRAINT `bloodinventory_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`);

--
-- Constraints for table `donationrequest`
--
ALTER TABLE `donationrequest`
  ADD CONSTRAINT `donationrequest_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`),
  ADD CONSTRAINT `donationrequest_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `donationrequesthistory`
--
ALTER TABLE `donationrequesthistory`
  ADD CONSTRAINT `donationrequesthistory_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`),
  ADD CONSTRAINT `donationrequesthistory_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `donationrequesthistory_ibfk_3` FOREIGN KEY (`fulfilled_by`) REFERENCES `donor` (`donor_id`);

--
-- Constraints for table `donor`
--
ALTER TABLE `donor`
  ADD CONSTRAINT `donor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `location_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `location_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospital` (`hospital_id`);

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `donationrequest` (`request_id`),
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`donor_id`) REFERENCES `donor` (`donor_id`);

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `passwordresets`
--
ALTER TABLE `passwordresets`
  ADD CONSTRAINT `passwordresets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reminder`
--
ALTER TABLE `reminder`
  ADD CONSTRAINT `reminder_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`);

--
-- Constraints for table `verificationcodes`
--
ALTER TABLE `verificationcodes`
  ADD CONSTRAINT `verificationcodes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
