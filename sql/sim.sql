-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2015 at 03:23 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sim`
--
CREATE DATABASE IF NOT EXISTS `sim` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `sim`;

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
CREATE TABLE IF NOT EXISTS `nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `link_to` varchar(255) DEFAULT NULL,
  `needs_product` varchar(255) NOT NULL,
  `has_product` varchar(255) NOT NULL,
  `money` float NOT NULL,
  `is_producer` tinyint(1) NOT NULL DEFAULT '0',
  `misc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `nodes`
--

INSERT INTO `nodes` (`id`, `name`, `link_to`, `needs_product`, `has_product`, `money`, `is_producer`, `misc`) VALUES
(1, 'n0', 'n5,n22,n12,n17', 'P6', 'P0', 18.3, 0, ''),
(2, 'n1', 'n22,n26', 'P6', 'P5', 69.5, 0, ''),
(3, 'n2', 'n29', 'P2', 'P6', 35.9, 0, ''),
(4, 'n3', 'n17', 'P2', 'P2', 21.2, 0, ''),
(5, 'n4', 'n24,n29', 'P2', 'P4', 81.3, 0, ''),
(6, 'n5', 'n0,n28', 'P2', 'P4', 27.1, 0, ''),
(7, 'n6', 'n16,n25,n28,n29', 'P4', 'P5', 32.5, 0, ''),
(8, 'n7', 'n10,n10', 'P4', 'P0', 76.9, 0, ''),
(9, 'n8', 'n17,n23,n29,n23', 'P3', 'P2', 54.6, 0, ''),
(10, 'n9', 'n20,n10,n17,n24', 'P3', 'P1', 39.8, 0, ''),
(11, 'n10', 'n7,n7,n9,n22,n26,n27', 'P1', 'P4', 85.1, 0, ''),
(12, 'n11', 'n15,n19,n25', 'P6', 'P0', 30.1, 0, ''),
(13, 'n12', 'n0,n19', 'P4', 'P3', 36.6, 0, ''),
(14, 'n13', 'n25', 'P1', 'P4', 61, 0, ''),
(15, 'n14', 'n24,n29', 'P1', 'P6', 3.9, 0, ''),
(16, 'n15', 'n11,n21,n22,n28,n29,n22', 'P5', 'P2', 60.5, 0, ''),
(17, 'n16', 'n6,n18,n23,n23,n25', 'P1', 'P1', 30.8, 0, ''),
(18, 'n17', 'n3,n8,n0,n9,n19,n21,n27', 'P1', 'P3', 35.4, 0, ''),
(19, 'n18', 'n16,n26,n27', 'P5', 'P6', 34.9, 0, ''),
(20, 'n19', 'n11,n12,n17', 'P6', 'P0', 16.2, 0, ''),
(21, 'n20', 'n9,n28', 'P0', 'P2', 86.4, 0, ''),
(22, 'n21', 'n15,n17', 'P2', 'P0', 85.9, 0, ''),
(23, 'n22', 'n0,n1,n15,n10,n15', 'P1', 'P4', 26, 0, ''),
(24, 'n23', 'n8,n16,n8,n16', 'P3', 'P2', 7.7, 0, ''),
(25, 'n24', 'n4,n14,n9', 'P5', 'P5', 65.3, 0, ''),
(26, 'n25', 'n6,n13,n11,n16', 'P2', 'P6', 16.8, 0, ''),
(27, 'n26', 'n1,n18,n10', 'P1', 'P2', 37.2, 0, ''),
(28, 'n27', 'n18,n10,n17', 'P4', 'P4', 60.4, 0, ''),
(29, 'n28', 'n5,n6,n15,n20,n29', 'P3', 'P0', 5.6, 0, ''),
(30, 'n29\r', NULL, 'P1', 'P3', 62.9, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` float NOT NULL,
  `misc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `value`, `misc`) VALUES
(1, 'P0', 1.79, ''),
(2, 'P1', 7.87, ''),
(3, 'P2', 3.92, ''),
(4, 'P3', 2.97, ''),
(5, 'P4', 1.87, ''),
(6, 'P5', 2.74, ''),
(7, 'P6', 6.32, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
