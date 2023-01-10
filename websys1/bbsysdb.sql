-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2023 at 04:49 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bbsysdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(14) NOT NULL,
  `bookid` varchar(100) NOT NULL,
  `title` varchar(300) NOT NULL,
  `author` varchar(100) NOT NULL,
  `publish_date` varchar(30) NOT NULL,
  `pagesno` int(6) NOT NULL,
  `copyno` int(6) NOT NULL,
  `copynov` int(6) NOT NULL,
  `create_date` varchar(30) NOT NULL,
  `update_date` varchar(30) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `bookid`, `title`, `author`, `publish_date`, `pagesno`, `copyno`, `copynov`, `create_date`, `update_date`, `status`) VALUES
(1, '001-2305', 'Visual Basic.Net', 'Brian Siler, Jeff Spotts', '889869600', 776, 1, 1, '1673175478', '1673175715', 1),
(2, '001-2306', 'Beginning PHP6', 'Bronzyk, Timothy', '1240218000', 837, 1, 1, '1673175680', '1673175680', 1),
(3, '001-2307', 'ASP.Net', 'Jiff', '1116147600', 576, 1, 1, '1673175818', '1673175818', 1),
(4, '001-2308', 'Java', 'Josef', '329479200', 695, 2, 2, '1673175873', '1673175873', 1),
(5, '000-2601', 'السيرة النبوية', 'ابن هشام', '-1405520624', 576, 1, 1, '1673175959', '1673175959', 1),
(6, '000-2602', 'السيرة النبوية', 'ابن الجوزي', '-1453904624', 975, 1, 1, '1673176026', '1673176026', 1),
(7, '000-2603', 'ماذا يعني انتمائي للاسلام', 'فتحي يكن', '1490349600', 187, 1, 1, '1673176146', '1673176146', 1),
(8, '003-9119', 'ازمة الحضارة', 'ستيف روكي', '1239008400', 350, 3, 3, '1671213344', '1671213344', 1),
(9, '003-9117', 'في جبال الالب', 'فان جاك', '799923600', 107, 8, 8, '1671212929', '1671838733', 1),
(10, '003-9115', 'عناصر النجاح ال7', 'جاك لافي', '799923600', 400, 7, 7, '1671211612', '1671319422', 1),
(11, '003-9116', 'لنذهب نحو الحضارة', 'فيليب نادين', '547117200', 24, 2, 2, '1671211715', '1671211715', 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowing`
--

CREATE TABLE `borrowing` (
  `id` int(14) NOT NULL,
  `user_id` int(14) NOT NULL,
  `book_id` int(14) NOT NULL,
  `secret` varchar(15) NOT NULL,
  `create_date` varchar(25) NOT NULL,
  `t_date` varchar(25) NOT NULL,
  `return_date` varchar(25) NOT NULL,
  `warning_email_status` int(1) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `borrowing`
--

INSERT INTO `borrowing` (`id`, `user_id`, `book_id`, `secret`, `create_date`, `t_date`, `return_date`, `warning_email_status`, `status`) VALUES
(111122, 111118, 111115, 'gIySblusgv', '1671940365', '1671940385', '1671987054', 0, 3),
(111123, 111123, 1, '8mrCg38ecv', '1671986936', '1671987330', '1671987349', 0, 3),
(111127, 111118, 28, 'sQ6SLBWpqo', '1671987295', '1671987395', '1671987404', 0, 3),
(111130, 111118, 1, 'xsjOXavhwC', '1672413241', '1672413263', '1672413282', 0, 3),
(111131, 111118, 111111, 'dea8w39HGt', '1672413313', '1672413322', '1672413449', 0, 3),
(111132, 111118, 111114, 'q9ZgxufWcp', '1672414969', '1672414989', '1672415357', 0, 3),
(111133, 111127, 21, 'jTN0OEQQST', '1673080985', '1673081020', '', 0, 2),
(111134, 111127, 111111, 'kDMTKjm2vY', '1673081158', '1673081171', '', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `create_date` varchar(30) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`id`, `userid`, `create_date`, `status`) VALUES
(16, 8, '1671581194', 1),
(111136, 111123, '1671986857', 1),
(111140, 111126, '1672412439', 1),
(111141, 111118, '1672413092', 1),
(111146, 111127, '1673091620', 1),
(111147, 1, '1673175212', 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(6) NOT NULL,
  `name` varchar(60) NOT NULL,
  `value` varchar(60) NOT NULL,
  `lastupdate` varchar(25) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `lastupdate`, `status`) VALUES
(1, 'user_register', '0', '7777', 1),
(2, 'user_borrow', '1', '7777', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(300) NOT NULL,
  `salt` varchar(100) NOT NULL,
  `type` int(2) NOT NULL,
  `create_date` varchar(20) NOT NULL,
  `link_secret` varchar(25) NOT NULL,
  `validuntil` varchar(25) NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `salt`, `type`, `create_date`, `link_secret`, `validuntil`, `status`) VALUES
(1, 'Odai', 'odai1@yahoo.com', '30cfb83f737be9ed9443c9b3cab2ae584dd42c4e', 'fuRT2DL64bvfinM0YBRQ', 1, '1671188834', '', '', 1),
(111128, 'Khaled', '3190601075@std.wise.edu.jo', 'bac2280592ae53f170a5b4fd42687dbf5c05103e', 'GwJXp0n8J0Zo6oFAkTyi', 2, '1673176743', 'HSL0gshllsxlofDZJkqG', '1673263143', 1),
(111129, 'abdalla', '3190601076@std.wise.edu.jo', '3d2fd04ae72df9670eba25d51869c2c302827a2f', 'ckuxHGCT2v8e4DXbOFI4', 2, '1673176777', 'F3vWn7m3vGKRB0MRd6Ud', '1673263177', 1),
(111130, 'odai', '3190606097@std.wise.edu.jo', '31f515c57da721fc2db16543fa930f735fcad920', 'JHWbQ5SP4DO8h3UNiINU', 2, '1673176798', 'kRY5BRt04iR2Lh6Te7Ds', '1673263198', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookid` (`bookid`);

--
-- Indexes for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logins`
--
ALTER TABLE `logins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111123;

--
-- AUTO_INCREMENT for table `borrowing`
--
ALTER TABLE `borrowing`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111135;

--
-- AUTO_INCREMENT for table `logins`
--
ALTER TABLE `logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111148;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111131;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
