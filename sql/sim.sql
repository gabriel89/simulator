-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2015 at 05:12 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE IF NOT EXISTS `nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `link_to` varchar(255) DEFAULT NULL,
  `needs_product` varchar(255) NOT NULL,
  `has_product` varchar(255) NOT NULL,
  `has_product_count` int(11) NOT NULL DEFAULT '0',
  `money` float NOT NULL,
  `is_producer` tinyint(1) NOT NULL DEFAULT '0',
  `misc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `nodes`
--

INSERT INTO `nodes` (`id`, `name`, `link_to`, `needs_product`, `has_product`, `has_product_count`, `money`, `is_producer`, `misc`) VALUES
(1, 'n0', 'n5,n22,n12,n17', 'P4', 'P4', 50, 49.6, 0, ''),
(2, 'n1', 'n22,n26', 'P0', 'P0', 69, 27.2, 0, ''),
(3, 'n2', 'n29', 'P4', 'P0', 49, 63, 0, ''),
(4, 'n3', 'n17', 'P3', 'P2', 89, 13.7, 0, ''),
(5, 'n4', 'n24,n29', 'P0', 'P3', 68, 86.4, 0, ''),
(6, 'n5', 'n0,n28', 'P0', 'P0', 19, 88.1, 0, ''),
(7, 'n6', 'n16,n25,n28,n29', 'P5', 'P5', 13, 34, 0, ''),
(8, 'n7', 'n10,n30', 'P3', 'P5', 26, 62.8, 0, ''),
(9, 'n8', 'n17,n23,n29', 'P4', 'P5', 40, 13.7, 0, ''),
(10, 'n9', 'n20,n10,n17,n24', 'P5', 'P5', 36, 55.3, 0, ''),
(11, 'n10', 'n7,n9,n22,n26,n27', 'P3', 'P3', 35, 36.9, 0, ''),
(12, 'n11', 'n15,n19,n25', 'P3', 'P3', 6, 3, 0, ''),
(13, 'n12', 'n0,n19', 'P1', 'P1', 35, 51.2, 0, ''),
(14, 'n13', 'n25', 'P3', 'P0', 16, 51.7, 0, ''),
(15, 'n14', 'n24,n29', 'P5', 'P3', 41, 38.1, 0, ''),
(16, 'n15', 'n11,n21,n22,n28,n29', 'P4', 'P4', 54, 24.8, 0, ''),
(17, 'n16', 'n6,n18,n23,n25', 'P2', 'P4', 13, 4.9, 0, ''),
(18, 'n17', 'n3,n8,n0,n9,n19,n21,n27', 'P1', 'P5', 12, 25.5, 0, ''),
(19, 'n18', 'n16,n26,n27', 'P5', 'P0', 40, 45.2, 0, ''),
(20, 'n19', 'n11,n12,n17', 'P6', 'P1', 65, 84.8, 0, ''),
(21, 'n20', 'n9,n28', 'P3', 'P2', 63, 63.7, 0, ''),
(22, 'n21', 'n15,n17', 'P6', 'P6', 58, 13.9, 0, ''),
(23, 'n22', 'n0,n1,n15,n10', 'P2', 'P1', 52, 49.4, 0, ''),
(24, 'n23', 'n8,n16', 'P0', 'P6', 1, 58.3, 0, ''),
(25, 'n24', 'n4,n14,n9', 'P2', 'P4', 30, 53.5, 0, ''),
(26, 'n25', 'n6,n13,n11,n16', 'P1', 'P4', 44, 14.6, 0, ''),
(27, 'n26', 'n1,n18,n10', 'P6', 'P5', 72, 19.7, 0, ''),
(28, 'n27', 'n18,n10,n17', 'P1', 'P5', 7, 67.7, 0, ''),
(29, 'n28', 'n5,n6,n15,n20,n29', 'P3', 'P0', 32, 12.2, 0, ''),
(30, 'n29', 'n2,n4,n8,n15,n28,n6,n14', 'P3', 'P1', 54, 5.4, 0, ''),
(31, 'n30', 'n7', 'P2', 'P2', 26, 86, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` float NOT NULL,
  `max_value` float NOT NULL,
  `misc` varchar(255) NOT NULL,
  `rank` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `value`, `max_value`, `misc`) VALUES
(1, 'P0', 3.92, 0, ''),
(2, 'P1', 7.49, 0, ''),
(3, 'P2', 4.08, 0, ''),
(4, 'P3', 6.45, 0, ''),
(5, 'P4', 6.42, 0, ''),
(6, 'P5', 7.35, 0, ''),
(7, 'P6', 6.54, 0, ''),
(8, 'P7', 1.38, 0, ''),
(9, 'P8', 2.91, 0, ''),
(10, 'P9', 3.48, 0, ''),
(11, 'P10', 7.64, 0, ''),
(12, 'P11', 5.47, 0, ''),
(13, 'P12', 4.72, 0, ''),
(14, 'P13', 2.16, 0, ''),
(15, 'P14', 2.62, 0, ''),
(16, 'P15', 5.68, 0, ''),
(17, 'P16', 7.68, 0, ''),
(18, 'P17', 8.42, 0, ''),
(19, 'P18', 3.91, 0, ''),
(20, 'P19', 8.66, 0, ''),
(21, 'P20', 2.71, 0, ''),
(22, 'P21', 6.67, 0, ''),
(23, 'P22', 3.98, 0, ''),
(24, 'P23', 7.95, 0, ''),
(25, 'P24', 7.75, 0, ''),
(26, 'P25', 8.01, 0, ''),
(27, 'P26', 0.75, 0, ''),
(28, 'P27', 8.64, 0, ''),
(29, 'P28', 8.82, 0, ''),
(30, 'P29', 8.6, 0, ''),
(31, 'P30', 7.43, 0, ''),
(32, 'P31', 4.03, 0, ''),
(33, 'P32', 0.87, 0, ''),
(34, 'P33', 2.74, 0, ''),
(35, 'P34', 2.41, 0, ''),
(36, 'P35', 5.75, 0, ''),
(37, 'P36', 5.3, 0, ''),
(38, 'P37', 5.52, 0, ''),
(39, 'P38', 1.68, 0, ''),
(40, 'P39', 6.17, 0, ''),
(41, 'P40', 3.95, 0, ''),
(42, 'P41', 3.73, 0, ''),
(43, 'P42', 3.59, 0, ''),
(44, 'P43', 0.33, 0, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
