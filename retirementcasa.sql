-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2021 at 11:56 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.3.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `retirementcasa_production`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblarticle`
--

CREATE TABLE `tblarticle` (
  `Article_id` int(11) NOT NULL,
  `Url_id` int(11) NOT NULL,
  `Url` text NOT NULL,
  `Article_title` text NOT NULL,
  `Article_desc` tinytext NOT NULL,
  `Url_image` text NOT NULL,
  `Article_date` date NOT NULL,
  `Author` text NOT NULL,
  `Domain_id` int(11) NOT NULL,
  `Userid` int(11) NOT NULL,
  `Clicks` int(11) NOT NULL,
  `Thumbs_up` int(11) NOT NULL,
  `Thumbs_down` int(11) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblclassification`
--

CREATE TABLE `tblclassification` (
  `Classify_id` int(11) NOT NULL,
  `Article_id` int(11) NOT NULL,
  `Tags` varchar(100) NOT NULL,
  `Tag_id` int(11) NOT NULL,
  `Confidence` varchar(10) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblconfig`
--

CREATE TABLE `tblconfig` (
  `Config_id` int(11) NOT NULL,
  `Key_name` varchar(40) NOT NULL,
  `Value` varchar(40) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblcrawler_queue`
--

CREATE TABLE `tblcrawler_queue` (
  `Url_id` int(11) NOT NULL,
  `Url` text NOT NULL,
  `Status` varchar(40) NOT NULL,
  `Stage` varchar(40) NOT NULL,
  `Status_description` text NOT NULL,
  `Userid` int(11) NOT NULL,
  `Batchid` int(11) NOT NULL,
  `Process_time_in_sec` varchar(100) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblmedia_data`
--

CREATE TABLE `tblmedia_data` (
  `Media_id` int(11) NOT NULL,
  `Meta_id` int(11) NOT NULL,
  `Media_desc` varchar(40) NOT NULL,
  `Media_url` varchar(40) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblranking_score`
--

CREATE TABLE `tblranking_score` (
  `Domain_id` int(11) NOT NULL,
  `Domain_name` varchar(100) NOT NULL,
  `Domain_score` float NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblsaved_article`
--

CREATE TABLE `tblsaved_article` (
  `Id` int(11) NOT NULL,
  `Userid` int(11) NOT NULL,
  `Article_id` int(11) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `Userid` int(11) NOT NULL,
  `First_name` varchar(40) NOT NULL,
  `Last_name` varchar(40) NOT NULL,
  `Email` varchar(40) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Role` varchar(20) NOT NULL,
  `Agreement` char(1) NOT NULL,
  `Status` char(1) NOT NULL,
  `Verification_code` varchar(50) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbluser_comment`
--

CREATE TABLE `tbluser_comment` (
  `Comment_id` int(11) NOT NULL,
  `Article_id` int(11) NOT NULL,
  `Comment` varchar(100) NOT NULL,
  `Userid` int(11) NOT NULL,
  `Datecreated` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblarticle`
--
ALTER TABLE `tblarticle`
  ADD PRIMARY KEY (`Article_id`),
  ADD KEY `Domain_id` (`Domain_id`);
ALTER TABLE `tblarticle` ADD FULLTEXT KEY `Article_title` (`Article_title`);
ALTER TABLE `tblarticle` ADD FULLTEXT KEY `Article_desc` (`Article_desc`);
ALTER TABLE `tblarticle` ADD FULLTEXT KEY `Article_title_2` (`Article_title`,`Article_desc`);
ALTER TABLE `tblarticle` ADD FULLTEXT KEY `Article_desc_2` (`Article_desc`);

--
-- Indexes for table `tblclassification`
--
ALTER TABLE `tblclassification`
  ADD PRIMARY KEY (`Classify_id`),
  ADD KEY `confidence_idx` (`Confidence`),
  ADD KEY `article_idx` (`Article_id`);

--
-- Indexes for table `tblconfig`
--
ALTER TABLE `tblconfig`
  ADD PRIMARY KEY (`Config_id`);

--
-- Indexes for table `tblcrawler_queue`
--
ALTER TABLE `tblcrawler_queue`
  ADD PRIMARY KEY (`Url_id`);

--
-- Indexes for table `tblmedia_data`
--
ALTER TABLE `tblmedia_data`
  ADD PRIMARY KEY (`Media_id`);

--
-- Indexes for table `tblranking_score`
--
ALTER TABLE `tblranking_score`
  ADD PRIMARY KEY (`Domain_id`);

--
-- Indexes for table `tblsaved_article`
--
ALTER TABLE `tblsaved_article`
  ADD PRIMARY KEY (`Id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`Userid`);

--
-- Indexes for table `tbluser_comment`
--
ALTER TABLE `tbluser_comment`
  ADD PRIMARY KEY (`Comment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblarticle`
--
ALTER TABLE `tblarticle`
  MODIFY `Article_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblclassification`
--
ALTER TABLE `tblclassification`
  MODIFY `Classify_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblconfig`
--
ALTER TABLE `tblconfig`
  MODIFY `Config_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcrawler_queue`
--
ALTER TABLE `tblcrawler_queue`
  MODIFY `Url_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblmedia_data`
--
ALTER TABLE `tblmedia_data`
  MODIFY `Media_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblranking_score`
--
ALTER TABLE `tblranking_score`
  MODIFY `Domain_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblsaved_article`
--
ALTER TABLE `tblsaved_article`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `Userid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbluser_comment`
--
ALTER TABLE `tbluser_comment`
  MODIFY `Comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblarticle`
--
ALTER TABLE `tblarticle`
  ADD CONSTRAINT `tblarticle_ibfk_1` FOREIGN KEY (`Domain_id`) REFERENCES `tblranking_score` (`Domain_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
