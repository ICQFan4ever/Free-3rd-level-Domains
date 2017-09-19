-- phpMyAdmin SQL Dump
-- version 4.7.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 19, 2017 at 09:29 AM
-- Server version: 5.5.40-MariaDB-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `free_domains`
--

-- --------------------------------------------------------

--
-- Table structure for table `activated_promo_codes`
--

CREATE TABLE `activated_promo_codes` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_code` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` bigint(20) NOT NULL,
  `domain` tinytext,
  `type` enum('en','ru') DEFAULT 'en',
  `token` tinytext,
  `time` int(11) DEFAULT NULL,
  `counter` int(11) DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `time` int(11) DEFAULT NULL,
  `text` text
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` int(11) NOT NULL,
  `title` tinytext,
  `code` tinytext,
  `time_valid` int(11) DEFAULT NULL,
  `command` text
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shortener`
--

CREATE TABLE `shortener` (
  `id` int(11) NOT NULL,
  `rand` tinytext,
  `time` int(11) DEFAULT NULL,
  `url` mediumtext
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `subdomains`
--

CREATE TABLE `subdomains` (
  `id` bigint(20) NOT NULL,
  `id_user` bigint(20) DEFAULT NULL,
  `name` tinytext,
  `type` enum('NS','CNAME','A') DEFAULT NULL,
  `id_domain` int(11) DEFAULT NULL,
  `ns1` tinytext,
  `ns2` tinytext,
  `cname` tinytext,
  `a` tinytext,
  `time_created` int(11) DEFAULT NULL,
  `time_updated` int(11) DEFAULT NULL,
  `hidden` int(11) DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `id_referer` bigint(20) DEFAULT '0',
  `login` tinytext,
  `password` tinytext,
  `email` tinytext,
  `name` tinytext,
  `sid` tinytext,
  `vip` tinyint(4) DEFAULT '0',
  `max_domains` tinyint(4) DEFAULT '5',
  `level` enum('1','2') DEFAULT '1',
  `time_reg` int(11) DEFAULT NULL,
  `last_visit` int(11) DEFAULT NULL,
  `ip` bigint(20) DEFAULT NULL,
  `ua` text,
  `onpage` int(11) DEFAULT '10',
  `keyword` tinytext,
  `keyword_question` tinytext,
  `email_verified` enum('0','1') DEFAULT '0'
) ENGINE=Aria DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activated_promo_codes`
--
ALTER TABLE `activated_promo_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shortener`
--
ALTER TABLE `shortener`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subdomains`
--
ALTER TABLE `subdomains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activated_promo_codes`
--
ALTER TABLE `activated_promo_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shortener`
--
ALTER TABLE `shortener`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `subdomains`
--
ALTER TABLE `subdomains`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
