-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2016 at 06:41 AM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(24) NOT NULL,
  `university` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `university`) VALUES
('admin', 20);

--
-- Triggers `admin`
--
DELIMITER $$
CREATE TRIGGER `update_user_type_a` BEFORE INSERT ON `admin` FOR EACH ROW BEGIN
	update users
    set user_type = 'a'
    where userid = NEW.admin_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `Time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `student_id` varchar(20) NOT NULL,
  `event` varchar(50) NOT NULL,
  `text` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `name` varchar(45) NOT NULL,
  `date` varchar(45) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `time` varchar(45) DEFAULT NULL,
  `event_type` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `App_by_A` int(11) DEFAULT NULL,
  `App_by_SA` int(11) DEFAULT NULL,
  `created_by` varchar(30) DEFAULT NULL,
  `university_id` int(15) DEFAULT NULL,
  `rso_id` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`name`, `date`, `category`, `location`, `description`, `phone`, `time`, `event_type`, `email`, `App_by_A`, `App_by_SA`, `created_by`, `university_id`, `rso_id`) VALUES
('Niggas Hating', '2019-02-02', 'Pokemon Go', 'john', 'Niggas hating on me', '321-588-2383', '02:00', 'Public', 'navid@johnathon.com', NULL, NULL, 'admin', NULL, NULL),
('Niggas hating on ME', '2016-07-31', 'haters', 'THE', 'don''t hate mah nigga', '3123', '12:58', 'Private', 'naiv@gmial.com', NULL, NULL, 'admin', 20, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(15) NOT NULL,
  `name` varchar(50) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `name`, `latitude`, `longitude`) VALUES
(62, 'john', '80', '-24'),
(63, 'THE HOOD', '28.59846405613708', '-81.20704427361488');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `student_id` varchar(24) NOT NULL,
  `rso` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rso`
--

CREATE TABLE `rso` (
  `name` varchar(45) NOT NULL,
  `owned_by` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` varchar(24) NOT NULL,
  `university` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `student`
--
DELIMITER $$
CREATE TRIGGER `update_user_type_S` BEFORE INSERT ON `student` FOR EACH ROW BEGIN
	update users
    set user_type = 's'
    where userid = NEW.student_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `super_admin`
--

CREATE TABLE `super_admin` (
  `sadmin_id` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `super_admin`
--

INSERT INTO `super_admin` (`sadmin_id`) VALUES
('super'),
('superduper');

--
-- Triggers `super_admin`
--
DELIMITER $$
CREATE TRIGGER `update_user_type_sa` BEFORE INSERT ON `super_admin` FOR EACH ROW BEGIN
	update users
    set user_type = 'sa'
    where userid = NEW.sadmin_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `university`
--

CREATE TABLE `university` (
  `university_id` int(15) NOT NULL,
  `name` varchar(60) NOT NULL,
  `location_id` int(15) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `no_students` int(11) DEFAULT NULL,
  `created_by` varchar(24) NOT NULL,
  `domain` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `university`
--

INSERT INTO `university` (`university_id`, `name`, `location_id`, `description`, `no_students`, `created_by`, `domain`) VALUES
(20, 'University of Central Florida', NULL, 'asdf', 500, 'super', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `user_type` enum('s','a','sa') NOT NULL,
  `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `password`, `user_type`, `email`) VALUES
('admin', 'asdfadsf', 'a', 'admin@admins.com'),
('newuser1', 'asdfadsf', 's', 'navid.fanaian@gmail.com'),
('nfanaian', '123123', 's', 'nfa@na.com'),
('nfanaian1', '123123', 's', 'navid@gmail.com'),
('super', '123123', 'sa', '1@1.com'),
('superduper', '123123', 'sa', 'diker@gmail.com'),
('tester1', 'asdfasdf', 's', 'nights@knights.com'),
('user2', 'asdfadsf', 's', '1@2.com'),
('user3', 'asdfasdf', 's', '1@3.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `A_Uni_idx` (`university`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`Time`),
  ADD KEY `event_name_comment_idx` (`event`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`name`),
  ADD KEY `created_by_eventA_idx` (`created_by`),
  ADD KEY `university_key_idx` (`university_id`),
  ADD KEY `rso_key_idx` (`rso_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `name_2` (`name`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`student_id`,`rso`),
  ADD KEY `rso_name_rso_idx` (`rso`);

--
-- Indexes for table `rso`
--
ALTER TABLE `rso`
  ADD PRIMARY KEY (`name`),
  ADD KEY `owned_by_idx` (`owned_by`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD KEY `S_Uni_idx` (`university`);

--
-- Indexes for table `super_admin`
--
ALTER TABLE `super_admin`
  ADD PRIMARY KEY (`sadmin_id`);

--
-- Indexes for table `university`
--
ALTER TABLE `university`
  ADD PRIMARY KEY (`university_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `created_bySA_idx` (`created_by`),
  ADD KEY `loc_U_idx` (`location_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
--
-- AUTO_INCREMENT for table `university`
--
ALTER TABLE `university`
  MODIFY `university_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `a_uni` FOREIGN KEY (`university`) REFERENCES `university` (`university_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `admin_id` FOREIGN KEY (`admin_id`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `Eid` FOREIGN KEY (`event`) REFERENCES `event` (`name`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `created_key` FOREIGN KEY (`created_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `rso_key` FOREIGN KEY (`rso_id`) REFERENCES `rso` (`name`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `university_key` FOREIGN KEY (`university_id`) REFERENCES `university` (`university_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `rso_name_rso` FOREIGN KEY (`rso`) REFERENCES `rso` (`name`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_id_rso` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `rso`
--
ALTER TABLE `rso`
  ADD CONSTRAINT `owned_by` FOREIGN KEY (`owned_by`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `s_uni` FOREIGN KEY (`university`) REFERENCES `university` (`university_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `student_id` FOREIGN KEY (`student_id`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `super_admin`
--
ALTER TABLE `super_admin`
  ADD CONSTRAINT `sadmin_id` FOREIGN KEY (`sadmin_id`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `university`
--
ALTER TABLE `university`
  ADD CONSTRAINT `created_bySA` FOREIGN KEY (`created_by`) REFERENCES `super_admin` (`sadmin_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `loc_id` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
