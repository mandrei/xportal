-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 02, 2014 at 10:28 PM
-- Server version: 5.5.34-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `xportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `ca_provinces`
--

CREATE TABLE IF NOT EXISTS `ca_provinces` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `iso` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `ca_provinces`
--

INSERT INTO `ca_provinces` (`id`, `name`, `iso`) VALUES
(1, 'Alberta', 'AB'),
(2, 'British Columbia', 'BC'),
(3, 'Manitoba', 'MB'),
(4, 'New Brunswick', 'NB'),
(5, 'Newfoundland and Labrador', 'NL'),
(6, 'Northwest Territories', 'NT'),
(7, 'Nova Scotia', 'NS'),
(8, 'Nunavut', 'NU'),
(9, 'Ontario', 'ON'),
(10, 'Prince Edward Island', 'PE'),
(11, 'Quebec', 'QC'),
(12, 'Saskatchewan', 'SK'),
(13, 'Yukon', 'YT');

-- --------------------------------------------------------

--
-- Table structure for table `clients_corporations`
--

CREATE TABLE IF NOT EXISTS `clients_corporations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) unsigned NOT NULL COMMENT 'clients_details.id',
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calendar_year_start` date NOT NULL DEFAULT '0000-00-00',
  `contact_person_last_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person_first_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='corporations, charity, not-for-profit' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `clients_corporations`
--

INSERT INTO `clients_corporations` (`id`, `client_id`, `name`, `calendar_year_start`, `contact_person_last_name`, `contact_person_first_name`, `position`) VALUES
(3, 48, 'CLIENT1', '2013-05-16', 'person', 'contact', 1);

-- --------------------------------------------------------

--
-- Table structure for table `clients_details`
--

CREATE TABLE IF NOT EXISTS `clients_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'users.id',
  `street_address` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` int(11) NOT NULL DEFAULT '0',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fax` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_type_id` int(11) unsigned NOT NULL COMMENT 'clients_types.id',
  `client_size_type_id` int(11) NOT NULL,
  `folders_size` int(11) NOT NULL COMMENT 'is in bytes',
  `update_client` tinyint(1) NOT NULL DEFAULT '0',
  `new_files` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - no new files; 1- new files were added',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `id` (`id`),
  KEY `client_type_id` (`client_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=49 ;

--
-- Dumping data for table `clients_details`
--

INSERT INTO `clients_details` (`id`, `user_id`, `street_address`, `city`, `province`, `postal_code`, `state`, `zip_code`, `country`, `phone`, `fax`, `client_type_id`, `client_size_type_id`, `folders_size`, `update_client`, `new_files`) VALUES
(48, 76, 'AVENUE 65 ST', 'NEW YORK', '', '', '37', '2341', 1, '5835835893', '3433431096', 2, 7, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `clients_individuals`
--

CREATE TABLE IF NOT EXISTS `clients_individuals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int(11) unsigned NOT NULL COMMENT 'clients_details.id',
  `calendar_year_start` int(11) unsigned NOT NULL DEFAULT '0',
  `title` tinyint(4) NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `initials` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_client_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `marital_status` int(11) unsigned NOT NULL COMMENT 'on december 31',
  PRIMARY KEY (`id`),
  KEY `clients_id` (`client_id`),
  KEY `marital_status` (`marital_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `clients_types`
--

CREATE TABLE IF NOT EXISTS `clients_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `clients_types`
--

INSERT INTO `clients_types` (`id`, `name`) VALUES
(1, 'Individual'),
(2, 'Corporate');

-- --------------------------------------------------------

--
-- Table structure for table `client_individual_titles`
--

CREATE TABLE IF NOT EXISTS `client_individual_titles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `client_individual_titles`
--

INSERT INTO `client_individual_titles` (`id`, `title`) VALUES
(1, 'Mr'),
(2, 'Ms'),
(3, 'Mrs'),
(4, 'Miss');

-- --------------------------------------------------------

--
-- Table structure for table `client_size_type`
--

CREATE TABLE IF NOT EXISTS `client_size_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sizetype` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `client_size_type`
--

INSERT INTO `client_size_type` (`id`, `sizetype`) VALUES
(1, 5),
(2, 25),
(3, 50),
(4, 75),
(5, 100),
(6, 200),
(7, 500);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`) VALUES
(1, 'USA'),
(2, 'CANADA');

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password`
--

CREATE TABLE IF NOT EXISTS `forgot_password` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `random_string` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `abbreviation`) VALUES
(1, 'English', 'EN');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=35 ;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip`, `timestamp`) VALUES
(29, '127.0.0.1', 1401185612),
(30, '127.0.0.1', 1401186146),
(31, '127.0.0.1', 1401186174),
(32, '127.0.0.1', 1401186660),
(33, '127.0.0.1', 1401186771),
(34, '127.0.0.1', 1401186914);

-- --------------------------------------------------------

--
-- Table structure for table `login_blocked_ips`
--

CREATE TABLE IF NOT EXISTS `login_blocked_ips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marital_status`
--

CREATE TABLE IF NOT EXISTS `marital_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `marital_status`
--

INSERT INTO `marital_status` (`id`, `name`) VALUES
(1, 'Married'),
(2, 'Widowed'),
(3, 'Divorced'),
(4, 'Separated'),
(5, 'Single'),
(6, 'Common-Law');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `parent`) VALUES
(2, 'Portal', 0),
(3, 'Portal - is admin', 2),
(4, 'Portal - delete folders', 2),
(5, 'Portal - upload/download', 2),
(6, 'Users', 0),
(7, 'Clients', 0),
(8, 'Settings', 0);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`) VALUES
(1, 'Director'),
(2, 'Secretary'),
(3, 'Treasurer'),
(4, 'General Manager'),
(5, 'Vice president'),
(6, 'Secretary-Treasurer'),
(7, 'Chairman of the Board'),
(8, 'Chief Executive Officer'),
(9, 'Assistant Secretary'),
(10, 'Assistant Treasurer'),
(11, '1st Vice President'),
(12, '2nd Vice President'),
(13, '3rd Vice President');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE IF NOT EXISTS `provinces` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `iso` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `name`, `iso`) VALUES
(1, 'Alberta', 'AB'),
(2, 'British Columbia', 'BC'),
(3, 'Manitoba', 'MB'),
(4, 'New Brunswick', 'NB'),
(5, 'Newfoundland and Labrador', 'NL'),
(6, 'Northwest Territories', 'NT'),
(7, 'Nova Scotia', 'NS'),
(8, 'Nunavut', 'NU'),
(9, 'Ontario', 'ON'),
(10, 'Prince Edward Island', 'PE'),
(11, 'Quebec', 'QC'),
(12, 'Saskatchewan', 'SK'),
(13, 'Yukon', 'YT');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(40) CHARACTER SET latin1 NOT NULL,
  `last_activity` int(10) NOT NULL,
  `data` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'system_email', 'no-reply@portal.com'),
(2, 'system_name_email', 'Portal'),
(3, 'alert_notification_email', 'demo@portal.com');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE IF NOT EXISTS `states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL,
  `abbrev` char(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=66 ;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`, `abbrev`) VALUES
(1, 'Alaska', 'AK'),
(2, 'Alabama', 'AL'),
(3, 'American Samoa', 'AS'),
(4, 'Arizona', 'AZ'),
(5, 'Arkansas', 'AR'),
(6, 'California', 'CA'),
(7, 'Colorado', 'CO'),
(8, 'Connecticut', 'CT'),
(9, 'Delaware', 'DE'),
(10, 'District of Columbia', 'DC'),
(11, 'Federated States of Micronesia', 'FM'),
(12, 'Florida', 'FL'),
(13, 'Georgia', 'GA'),
(14, 'Guam', 'GU'),
(15, 'Hawaii', 'HI'),
(16, 'Idaho', 'ID'),
(17, 'Illinois', 'IL'),
(18, 'Indiana', 'IN'),
(19, 'Iowa', 'IA'),
(20, 'Kansas', 'KS'),
(21, 'Kentucky', 'KY'),
(22, 'Louisiana', 'LA'),
(23, 'Maine', 'ME'),
(24, 'Marshall Islands', 'MH'),
(25, 'Maryland', 'MD'),
(26, 'Massachusetts', 'MA'),
(27, 'Michigan', 'MI'),
(28, 'Minnesota', 'MN'),
(29, 'Mississippi', 'MS'),
(30, 'Missouri', 'MO'),
(31, 'Montana', 'MT'),
(32, 'Nebraska', 'NE'),
(33, 'Nevada', 'NV'),
(34, 'New Hampshire', 'NH'),
(35, 'New Jersey', 'NJ'),
(36, 'New Mexico', 'NM'),
(37, 'New York', 'NY'),
(38, 'North Carolina', 'NC'),
(39, 'North Dakota', 'ND'),
(40, 'Northern Mariana Islands', 'MP'),
(41, 'Ohio', 'OH'),
(42, 'Oklahoma', 'OK'),
(43, 'Oregon', 'OR'),
(44, 'Palau', 'PW'),
(45, 'Pennsylvania', 'PA'),
(46, 'Puerto Rico', 'PR'),
(47, 'Rhode Island', 'RI'),
(48, 'South Carolina', 'SC'),
(49, 'South Dakota', 'SD'),
(50, 'Tennessee', 'TN'),
(51, 'Texas', 'TX'),
(52, 'Utah', 'UT'),
(53, 'Vermont', 'VT'),
(54, 'Virgin Islands', 'VI'),
(55, 'Virginia', 'VA'),
(56, 'Washington', 'WA'),
(57, 'West Virginia', 'WV'),
(58, 'Wisconsin', 'WI'),
(59, 'Wyoming', 'WY'),
(60, 'Armed Forces Africa', 'AE'),
(61, 'Armed Forces Americas (except Canada)', 'AA'),
(62, 'Armed Forces Canada', 'AE'),
(63, 'Armed Forces Europe', 'AE'),
(64, 'Armed Forces Middle East', 'AE'),
(65, 'Armed Forces Pacific', 'AP');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` char(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` int(11) NOT NULL COMMENT '1 - user (admin or superadmin) 2 - client',
  `language_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='clients and users' AUTO_INCREMENT=78 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `user_type`, `language_id`, `deleted`) VALUES
(1, 'test@demo.com', 'admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', 0, 1, 0),
(76, 'client@gmail.com', 'client', '7c4a8d09ca3762af61e59520943dc26494f8941b', 2, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_details`
--

CREATE TABLE IF NOT EXISTS `users_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT 'from table users.id',
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users_details`
--

INSERT INTO `users_details` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `address`) VALUES
(1, 1, 'Admin', 'Admin', '0720 000 000', 'test test ');

-- --------------------------------------------------------

--
-- Table structure for table `users_permissions`
--

CREATE TABLE IF NOT EXISTS `users_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `module_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `module_id` (`module_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=308 ;

--
-- Dumping data for table `users_permissions`
--

INSERT INTO `users_permissions` (`id`, `user_id`, `module_id`) VALUES
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(284, 76, 2),
(285, 76, 5);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients_corporations`
--
ALTER TABLE `clients_corporations`
  ADD CONSTRAINT `clients_corporations_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `clients_details`
--
ALTER TABLE `clients_details`
  ADD CONSTRAINT `clients_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `clients_details_ibfk_2` FOREIGN KEY (`client_type_id`) REFERENCES `clients_types` (`id`);

--
-- Constraints for table `clients_individuals`
--
ALTER TABLE `clients_individuals`
  ADD CONSTRAINT `clients_individuals_ibfk_1` FOREIGN KEY (`marital_status`) REFERENCES `marital_status` (`id`),
  ADD CONSTRAINT `clients_individuals_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_details`
--
ALTER TABLE `users_details`
  ADD CONSTRAINT `users_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_permissions`
--
ALTER TABLE `users_permissions`
  ADD CONSTRAINT `users_permissions_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_permissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
