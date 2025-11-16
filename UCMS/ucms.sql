-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 29, 2025 at 06:05 PM
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
-- Database: `ucms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_login`
--

CREATE TABLE `admin_login` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login`
--

INSERT INTO `admin_login` (`admin_id`, `username`, `password_hash`, `full_name`, `email`, `created_at`) VALUES
(1, 'admin_mree', 'admin@123', 'Mrinmoye Azad', 'mree.azad@sub.edu.bd', '2025-10-29 17:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `clubs`
--

CREATE TABLE `clubs` (
  `club_id` int(11) NOT NULL,
  `club_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `establishment_date` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `faculty_advisor_name` varchar(100) DEFAULT NULL,
  `faculty_advisor_email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clubs`
--

INSERT INTO `clubs` (`club_id`, `club_name`, `description`, `establishment_date`, `email`, `faculty_advisor_name`, `faculty_advisor_email`, `created_at`) VALUES
(1, 'Tech Innovators Club', 'A community of students passionate about coding, AI, and technology.', '2015-03-10', 'techinnovators@sub.edu.bd', 'Dr. Ahsan Habib', 'ahsan.habib@sub.edu.bd', '2025-10-29 16:46:45'),
(2, 'Robotics & Automation Society', 'Focused on robotics projects, workshops, and national competitions.', '2016-07-15', 'robotics.society@sub.edu.bd', 'Engr. Shaila Rahman', 'shaila.rahman@sub.edu.bd', '2025-10-29 16:46:45'),
(3, 'Debate & Oratory Club', 'Develops critical thinking, public speaking, and logical reasoning skills.', '2012-02-01', 'debate.club@sub.edu.bd', 'Mr. Tanvir Hossain', 'tanvir.hossain@sub.edu.bd', '2025-10-29 16:46:45'),
(4, 'Sports & Fitness Club', 'Encourages participation in indoor and outdoor sports for physical wellbeing.', '2014-11-20', 'sportsclub@sub.edu.bd', 'Md. Rashed Karim', 'rashed.karim@sub.edu.bd', '2025-10-29 16:46:45'),
(5, 'Cultural & Arts Club', 'Organizes music, dance, drama, and art exhibitions throughout the year.', '2013-05-30', 'culturalarts@sub.edu.bd', 'Ms. Farzana Akter', 'farzana.akter@sub.edu.bd', '2025-10-29 16:46:45');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_date` date NOT NULL,
  `organiser_club_id` int(11) NOT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_date`, `organiser_club_id`, `venue`, `description`) VALUES
(1, 'Hack-Fest 2024', '2024-11-20', 1, 'Main Auditorium', 'Annual inter-university hackathon organized by the Tech Innovators Club.'),
(2, 'Robo-Challenge 2024', '2024-12-05', 2, 'Engineering Lab Complex', 'Robotics & Automation Society presents a national-level robotics contest.'),
(3, 'Intra-University Debate Championship', '2024-10-15', 3, 'Seminar Hall B', 'Debate & Oratory Club hosts a public speaking and argumentation tournament.'),
(4, 'Annual Sports Week', '2024-12-10', 4, 'University Sports Ground', 'Sports & Fitness Club organizes week-long sports competitions and tournaments.'),
(5, 'Cultural Night 2024', '2024-11-30', 5, 'University Auditorium', 'Cultural & Arts Club showcases dance, drama, and music performances.'),
(6, 'Tech Talk: The Future of AI', '2024-09-25', 1, 'IT Department Seminar Room', 'Guest speakers discuss emerging trends in artificial intelligence.'),
(7, 'Art Exhibition: Colors of Campus', '2024-10-22', 5, 'Art Gallery Hall', 'Cultural & Arts Club exhibits student artworks and photography.');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `club_id` int(11) NOT NULL,
  `position` varchar(100) DEFAULT 'Member',
  `email` varchar(100) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `join_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `name`, `student_id`, `club_id`, `position`, `email`, `contact_no`, `join_date`) VALUES
(1, 'Mrinmoye Azad', 'CSE2021001', 1, 'President', 'mree.azad@sub.edu.bd', '01711000001', '2021-03-12'),
(2, 'Nusrat Jahan', 'CSE2021010', 1, 'Vice President', 'nusrat.jahan@sub.edu.bd', '01711000002', '2021-04-05'),
(3, 'Tarek Mahmud', 'CSE2021045', 1, 'Member', 'tarek.mahmud@sub.edu.bd', '01711000003', '2022-01-10'),
(4, 'Diganta Das', 'EEE2021020', 2, 'President', 'das.diganta@sub.edu.bd', '01711000004', '2021-02-15'),
(5, 'Rafia Islam', 'EEE2021055', 2, 'Vice President', 'rafia.islam@sub.edu.bd', '01711000005', '2021-06-25'),
(6, 'Jahidul Alam', 'EEE2021078', 2, 'Member', 'jahidul.alam@sub.edu.bd', '01711000006', '2022-02-18'),
(7, 'Samiha Anjum', 'ENG2021005', 3, 'President', 'samiha.anjum@sub.edu.bd', '01711000007', '2021-03-11'),
(8, 'Nabil Hasan', 'ENG2021030', 3, 'Member', 'nabil.hasan@sub.edu.bd', '01711000008', '2021-09-19'),
(9, 'Fariha Noor', 'ENG2021042', 3, 'Member', 'fariha.noor@sub.edu.bd', '01711000009', '2022-04-21'),
(10, 'Rakibul Islam', 'BBA2021022', 4, 'President', 'rakibul.islam@sub.edu.bd', '01711000010', '2021-07-15'),
(11, 'Afsana Akter', 'BBA2021040', 4, 'Vice President', 'afsana.akter@sub.edu.bd', '01711000011', '2022-03-04'),
(12, 'Mahfuz Ahmed', 'BBA2021056', 4, 'Member', 'mahfuz.ahmed@sub.edu.bd', '01711000012', '2022-08-13'),
(13, 'Puja Rani Das', 'LAW2021007', 5, 'President', 'puja.das@sub.edu.bd', '01711000013', '2021-05-29'),
(14, 'Priya Sultana', 'LAW2021023', 5, 'Vice President', 'priya.sultana@sub.edu.bd', '01711000014', '2021-11-09'),
(15, 'Fahim Ahmed', 'LAW2021055', 5, 'Member', 'fahim.ahmed@sub.edu.bd', '01711000015', '2022-05-17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_login`
--
ALTER TABLE `admin_login`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `clubs`
--
ALTER TABLE `clubs`
  ADD PRIMARY KEY (`club_id`),
  ADD UNIQUE KEY `club_name` (`club_name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organiser_club_id` (`organiser_club_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `club_id` (`club_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_login`
--
ALTER TABLE `admin_login`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clubs`
--
ALTER TABLE `clubs`
  MODIFY `club_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organiser_club_id`) REFERENCES `clubs` (`club_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`club_id`) REFERENCES `clubs` (`club_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
