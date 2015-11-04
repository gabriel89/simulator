-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2015 at 06:22 PM
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
  `needs_product_count` int(11) NOT NULL DEFAULT '0',
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

INSERT INTO `nodes` (`id`, `name`, `link_to`, `needs_product`, `needs_product_count`, `has_product`, `has_product_count`, `money`, `is_producer`, `misc`) VALUES
(1, 'n0', 'n5,n22,n12,n17', 'P1', 42, 'P5', 57, 14, 0, ''),
(2, 'n1', 'n22,n26', 'P3', 32, 'P0', 79, 59.4, 0, ''),
(3, 'n2', 'n29', 'P6', 30, 'P5', 41, 86.2, 0, ''),
(4, 'n3', 'n17', 'P2', 82, 'P3', 71, 54.8, 0, ''),
(5, 'n4', 'n24,n29', 'P2', 19, 'P1', 50, 26.8, 0, ''),
(6, 'n5', 'n0,n28', 'P3', 90, 'P5', 62, 64.4, 0, ''),
(7, 'n6', 'n16,n25,n28,n29', 'P2', 36, 'P1', 17, 71.1, 0, ''),
(8, 'n7', 'n10,n30', 'P6', 31, 'P0', 49, 74.3, 0, ''),
(9, 'n8', 'n17,n23,n29', 'P5', 14, 'P5', 24, 80, 0, ''),
(10, 'n9', 'n20,n10,n17,n24', 'P1', 54, 'P2', 73, 56.7, 0, ''),
(11, 'n10', 'n7,n9,n22,n26,n27', 'P3', 0, 'P5', 78, 72.7, 0, ''),
(12, 'n11', 'n15,n19,n25', 'P6', 32, 'P6', 51, 67.1, 0, ''),
(13, 'n12', 'n0,n19', 'P3', 13, 'P5', 42, 86.1, 0, ''),
(14, 'n13', 'n25', 'P3', 27, 'P6', 29, 31.1, 0, ''),
(15, 'n14', 'n24,n29', 'P0', 8, 'P3', 1, 24.3, 0, ''),
(16, 'n15', 'n11,n21,n22,n28,n29', 'P3', 44, 'P2', 85, 17.4, 0, ''),
(17, 'n16', 'n6,n18,n23,n25', 'P5', 23, 'P0', 85, 7.3, 0, ''),
(18, 'n17', 'n3,n8,n0,n9,n19,n21,n27', 'P6', 77, 'P3', 65, 54.4, 0, ''),
(19, 'n18', 'n16,n26,n27', 'P0', 76, 'P1', 89, 64.4, 0, ''),
(20, 'n19', 'n11,n12,n17', 'P1', 82, 'P4', 47, 47.4, 0, ''),
(21, 'n20', 'n9,n28', 'P4', 48, 'P2', 36, 64.8, 0, ''),
(22, 'n21', 'n15,n17', 'P6', 71, 'P4', 86, 76.4, 0, ''),
(23, 'n22', 'n0,n1,n15,n10', 'P3', 19, 'P0', 5, 57.7, 0, ''),
(24, 'n23', 'n8,n16', 'P6', 63, 'P6', 12, 74.7, 0, ''),
(25, 'n24', 'n4,n14,n9', 'P3', 72, 'P5', 83, 73.9, 0, ''),
(26, 'n25', 'n6,n13,n11,n16', 'P5', 36, 'P5', 0, 28.3, 0, ''),
(27, 'n26', 'n1,n18,n10', 'P0', 72, 'P1', 74, 53.5, 0, ''),
(28, 'n27', 'n18,n10,n17', 'P4', 38, 'P4', 53, 34.5, 0, ''),
(29, 'n28', 'n5,n6,n15,n20,n29', 'P0', 75, 'P0', 28, 33.4, 0, ''),
(30, 'n29', 'n2,n4,n8,n15,n28,n6,n14', 'P2', 48, 'P1', 35, 86.6, 0, ''),
(31, 'n30', 'n7', 'P6', 27, 'P4', 73, 41.2, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

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
(1, 'P0', 1.46, ''),
(2, 'P1', 6.2, ''),
(3, 'P2', 1.3, ''),
(4, 'P3', 0.89, ''),
(5, 'P4', 7.55, ''),
(6, 'P5', 2.25, ''),
(7, 'P6', 0.45, '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
