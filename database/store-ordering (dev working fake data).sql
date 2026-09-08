-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 23, 2026 at 01:48 PM
-- Server version: 8.0.36
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store-ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 'Added product: GRILL', '2026-07-10 13:04:56'),
(2, 1, 'Deactivated product: GRILL', '2026-07-10 13:56:42'),
(3, 1, 'Deactivated product: ', '2026-07-10 14:14:46'),
(4, 1, 'Deactivated product: ', '2026-07-10 14:15:01'),
(5, 1, 'Deactivated product: ', '2026-07-10 14:15:13'),
(6, 1, 'Deactivated product: ', '2026-07-10 14:17:00'),
(7, 1, 'Deactivated product: ', '2026-07-10 14:17:02'),
(8, 1, 'Deactivated product: ', '2026-07-10 14:17:03'),
(9, 1, 'Changed Order SW621389changed fromPending to Preparing', '2026-07-10 17:37:09'),
(10, 1, 'Changed Order SW494897changed fromPending to Preparing', '2026-07-10 17:37:12'),
(11, 1, 'Changed Order SW621389 from Preparing to Ready', '2026-07-10 17:38:52'),
(12, 1, 'Changed Order SW134452 from Preparing to Ready', '2026-07-10 17:42:39'),
(13, 1, 'Updated product: Coffee', '2026-07-10 18:40:28'),
(14, 1, 'Updated product: Cool Drink', '2026-07-10 19:21:05'),
(15, 1, 'Updated product: Rooibos', '2026-07-10 19:23:09'),
(16, 1, 'Deactivated product: fhfdxghfg', '2026-07-10 19:24:58'),
(17, 1, 'Deactivated product: fhfdxghfg', '2026-07-10 19:24:58'),
(18, 1, 'Changed Order SW630927 from Pending to Preparing', '2026-07-10 19:27:25'),
(19, 1, 'Changed Order SW621389 from Ready to Collected', '2026-07-10 19:27:40'),
(20, 1, 'Changed Order SW134452 from Ready to Collected', '2026-07-10 19:27:52'),
(21, 1, 'Changed Order SW630927 from Preparing to Ready', '2026-07-10 19:27:56'),
(22, 1, 'Changed Order SW630927 from Ready to Ready', '2026-07-10 19:27:56'),
(23, 1, 'Changed Order SW183884 from Ready to Collected', '2026-07-10 19:28:07'),
(24, 1, 'Updated product: Fries', '2026-07-10 21:06:19'),
(25, 1, 'Changed Order SW331780 from Preparing to Ready', '2026-07-10 21:24:39'),
(26, 1, 'Changed Order SW996268 from Ready to Collected', '2026-07-10 21:24:46'),
(27, 1, 'Updated product: Merch', '2026-07-10 21:30:59'),
(28, 1, 'Updated product: Merch', '2026-07-10 21:30:59'),
(29, 1, 'Updated product: Dagwood', '2026-07-10 21:31:30'),
(30, 1, 'Updated product: Chips', '2026-07-10 21:32:11'),
(31, 1, 'Updated product: Sphatlho', '2026-07-10 21:33:09'),
(32, 1, 'Changed Order  from Pending to Preparing', '2026-07-10 21:59:52'),
(33, 1, 'Changed Order  from Preparing to Preparing', '2026-07-10 21:59:52'),
(34, 1, 'Changed Order  from Preparing to Ready', '2026-07-10 21:59:54'),
(35, 1, 'Changed Order  from Ready to Ready', '2026-07-10 21:59:54'),
(36, 1, 'Changed Order  from Ready to Collected', '2026-07-10 21:59:56'),
(37, 1, 'Changed Order SW929414 from Pending to Preparing', '2026-07-10 22:15:49'),
(38, 1, 'Changed Order SW929414 from Preparing to Preparing', '2026-07-10 22:15:49'),
(39, 1, 'Changed Order SW929414 from Preparing to Ready', '2026-07-10 22:15:51'),
(40, 1, 'Changed Order SW929414 from Ready to Collected', '2026-07-10 22:15:53'),
(41, 1, 'Changed Order SW967759 from Pending to Preparing', '2026-07-10 22:18:56'),
(42, 1, 'Changed Order SW967759 from Preparing to Ready', '2026-07-10 22:18:58'),
(43, 1, 'Changed Order SW967759 from Ready to Collected', '2026-07-10 22:19:00'),
(44, 1, 'Changed Order SW260534 from Pending to Preparing', '2026-07-10 22:59:28'),
(45, 1, 'Changed Order SW260534 from Preparing to Ready', '2026-07-10 22:59:29'),
(46, 1, 'Changed Order SW260534 from Ready to Collected', '2026-07-10 22:59:31'),
(47, 1, 'Changed Order SW490624 from Ready to Cancelled', '2026-07-10 23:06:26'),
(48, 1, 'Changed Order SW928749 from Pending to Preparing', '2026-07-10 23:39:52'),
(49, 1, 'Changed Order SW928749 from Preparing to Preparing', '2026-07-10 23:39:53'),
(50, 1, 'Changed Order SW928749 from Preparing to Ready', '2026-07-10 23:39:57'),
(51, 1, 'Changed Order SW350061 from Pending to Preparing', '2026-07-10 23:39:59'),
(52, 1, 'Changed Order SW350061 from Preparing to Preparing', '2026-07-10 23:40:00'),
(53, 1, 'Updated product: Rooibos', '2026-07-11 00:53:01'),
(54, 1, 'Changed Order SW812330 from Pending to Preparing', '2026-07-11 11:13:48'),
(55, 1, 'Changed Order SW812330 from Preparing to Ready', '2026-07-11 11:22:55'),
(56, 1, 'Changed Order SW812330 from Ready to Collected', '2026-07-11 11:24:53'),
(57, 1, 'Changed Order SW350061 from Preparing to Ready', '2026-07-11 11:26:06'),
(58, 1, 'Changed Order SW350061 from Ready to Collected', '2026-07-11 11:26:09'),
(59, 1, 'Changed Order SW928749 from Ready to Collected', '2026-07-11 11:26:15'),
(60, 1, 'Added product: tytryt', '2026-07-11 11:32:15'),
(61, 1, 'Changed Order SW780128 from Pending to Preparing', '2026-07-11 11:33:55'),
(62, 1, 'Changed Order SW780128 from Preparing to Ready', '2026-07-11 11:49:52'),
(63, 1, 'Changed Order SW691309 from Pending to Preparing', '2026-07-11 11:50:16'),
(64, 1, 'Changed Order SW691309 from Preparing to Ready', '2026-07-11 11:50:19'),
(65, 1, 'Changed Order SW691309 from Ready to Collected', '2026-07-11 11:50:23'),
(66, 1, 'Changed Order SW031127 from Pending to Preparing', '2026-07-11 11:55:09'),
(67, 1, 'Changed Order SW031127 from Preparing to Ready', '2026-07-11 11:55:14'),
(68, 1, 'Changed Order SW031127 from Ready to Collected', '2026-07-11 11:55:19'),
(69, 1, 'Updated product: Rooibos', '2026-07-11 15:00:56'),
(70, 1, 'Updated product: Rooibos', '2026-07-11 15:00:56'),
(71, 1, 'Deactivated product: tytryt', '2026-07-11 15:20:28'),
(72, 1, 'Changed Order SW780128 from Ready to Collected', '2026-07-11 15:23:50'),
(73, 1, 'Changed Order SW494897 from Preparing to Ready', '2026-07-11 15:24:32'),
(74, 1, 'Updated product: tytryt', '2026-07-11 17:51:07'),
(75, 1, 'Changed Order SW368827 from Pending to Preparing', '2026-07-11 18:59:44'),
(76, 1, 'Changed Order SW191125 from Pending to Preparing', '2026-07-11 19:05:59'),
(77, 1, 'Changed Order SW368827 from Preparing to Ready', '2026-07-11 19:46:12'),
(78, 1, 'Changed Order SW191125 from Preparing to Ready', '2026-07-11 19:46:13'),
(79, 1, 'Changed Order SW191125 from Ready to Ready', '2026-07-11 19:46:14'),
(80, 1, 'Changed Order SW191125 from Ready to Collected', '2026-07-11 22:24:42'),
(81, 1, 'Changed Order SW282881 from Pending to Preparing', '2026-07-11 22:25:05'),
(82, 1, 'Changed Order SW282881 from Preparing to Preparing', '2026-07-11 22:25:05'),
(83, 1, 'Changed Order SW282881 from Preparing to Ready', '2026-07-11 22:25:08'),
(84, 1, 'Changed Order SW282881 from Ready to Ready', '2026-07-11 22:25:08'),
(85, 1, 'Changed Order SW282881 from Ready to Collected', '2026-07-11 22:25:10'),
(86, 1, 'Deactivated product: tytryt', '2026-07-11 22:30:42'),
(87, 1, 'Cancelled Purchase Order ID 1', '2026-07-13 01:55:49'),
(88, 1, 'Cancelled Purchase Order ID 2', '2026-07-13 01:56:12'),
(89, 1, 'Cancelled Purchase Order ID 5', '2026-07-13 01:56:39'),
(90, 1, 'Cancelled Purchase Order ID 6', '2026-07-13 01:56:43'),
(91, 1, 'Cancelled Purchase Order ID 7', '2026-07-13 01:56:46'),
(92, 1, 'Cancelled Purchase Order ID 8', '2026-07-13 01:56:49'),
(93, 1, 'Cancelled Purchase Order ID 3', '2026-07-13 17:14:30'),
(94, 1, 'Changed Order SW917412 from Pending to Preparing', '2026-07-13 17:49:27'),
(95, 1, 'Changed Order SW917412 from Preparing to Ready', '2026-07-13 17:49:29'),
(96, 1, 'Changed Order SW917412 from Ready to Collected', '2026-07-13 17:49:31'),
(97, 1, 'Updated product: Rooibos', '2026-07-13 23:08:41'),
(98, 1, 'Updated product: Rooibos', '2026-07-13 23:08:42'),
(99, 1, 'Updated product: Coffee', '2026-07-13 23:13:27'),
(100, 1, 'Updated product: Rooibos', '2026-07-13 23:13:37'),
(101, 1, 'Updated product: Pizza', '2026-07-13 23:14:59'),
(102, 1, 'Updated product: Cheese Burger', '2026-07-13 23:16:44'),
(103, 1, 'Updated product: Plate - Pap & Wors', '2026-07-13 23:26:00'),
(104, 1, 'Updated product: Pap & Wors', '2026-07-13 23:36:31'),
(105, 1, 'Changed Order SW630927 from Ready to Collected', '2026-07-14 01:09:17'),
(106, 1, 'Changed Order SW368827 from Ready to Collected', '2026-07-14 01:09:33'),
(107, 1, 'Changed Order SW331780 from Ready to Collected', '2026-07-14 01:10:13'),
(108, 1, 'Changed Order SW494897 from Ready to Collected', '2026-07-14 01:10:38'),
(109, 1, 'Changed Order SW626556 from Pending to Preparing', '2026-07-14 01:15:19'),
(110, 1, 'Changed Order SW626556 from Preparing to Ready', '2026-07-14 01:15:23'),
(111, 1, 'Changed Order SW626556 from Ready to Collected', '2026-07-14 01:15:34'),
(112, 1, 'Changed Order SW051690 from Pending to Preparing', '2026-07-14 01:16:15'),
(113, 1, 'Changed Order SW051690 from Preparing to Ready', '2026-07-14 01:17:14'),
(114, 1, 'Changed Order SW051690 from Ready to Collected', '2026-07-14 01:17:24'),
(115, 1, 'Updated product: Rooibos', '2026-07-14 02:56:26'),
(116, 1, 'Cancelled Purchase Order ID 12', '2026-07-14 12:14:05'),
(117, 1, 'Updated Purchase Order ID 15', '2026-07-14 12:14:28'),
(118, 1, 'Changed Order SW460408 from Pending to Preparing', '2026-07-14 13:11:23'),
(119, 1, 'Changed Order SW460408 from Preparing to Ready', '2026-07-14 13:11:45'),
(120, 1, 'Changed Order SW460408 from Ready to Collected', '2026-07-14 13:11:55'),
(121, 1, 'Changed Order SW899619 from Pending to Preparing', '2026-07-14 14:10:48'),
(122, 1, 'Changed Order SW899619 from Preparing to Ready', '2026-07-14 14:10:55'),
(123, 1, 'Changed Order SW899619 from Ready to Collected', '2026-07-14 14:11:07'),
(124, 5, 'Changed Order SW991040 from Pending to Preparing', '2026-07-14 14:25:37'),
(125, 5, 'Changed Order SW991040 from Preparing to Ready', '2026-07-14 14:26:07'),
(126, 1, 'Changed Order SW991040 from Ready to Collected', '2026-07-14 20:46:41'),
(127, 1, 'Changed Order SW402779 from Pending to Preparing', '2026-07-14 22:02:26'),
(128, 1, 'Changed Order SW402779 from Preparing to Ready', '2026-07-14 22:02:28'),
(129, 1, 'Changed Order SW402779 from Ready to Collected', '2026-07-14 22:03:35'),
(130, 1, 'Changed Order SW947335 from Pending to Preparing', '2026-07-14 22:05:58'),
(131, 1, 'Changed Order SW947335 from Preparing to Ready', '2026-07-14 22:06:20'),
(132, 1, 'Changed Order SW947335 from Ready to Collected', '2026-07-14 22:58:04'),
(133, 1, 'Changed Order SW368562 from Pending to Preparing', '2026-07-14 22:59:26'),
(134, 1, 'Changed Order SW368562 from Preparing to Ready', '2026-07-14 22:59:34'),
(135, 1, 'Changed Order SW368562 from Ready to Collected', '2026-07-14 22:59:39'),
(136, 1, 'Changed Order SW373058 from Pending to Preparing', '2026-07-14 23:00:07'),
(137, 1, 'Changed Order SW674461 from Pending to Preparing', '2026-07-14 23:00:20'),
(138, 1, 'Changed Order SW373058 from Preparing to Ready', '2026-07-14 23:00:22'),
(139, 1, 'Changed Order SW373058 from Ready to Ready', '2026-07-14 23:00:22'),
(140, 1, 'Changed Order SW016976 from Pending to Preparing', '2026-07-14 23:48:23'),
(141, 1, 'Changed Order SW804103 from Pending to Preparing', '2026-07-14 23:48:39'),
(142, 1, 'Changed Order SW804103 from Preparing to Ready', '2026-07-14 23:49:02'),
(143, 1, 'Changed Order SW016976 from Preparing to Ready', '2026-07-14 23:49:11'),
(144, 1, 'Changed Order SW016976 from Ready to Ready', '2026-07-14 23:49:11'),
(145, 1, 'Changed Order SW016976 from Ready to Collected', '2026-07-14 23:49:21'),
(146, 1, 'Changed Order SW504422 from Pending to Preparing', '2026-07-14 23:49:33'),
(147, 1, 'Changed Order SW504422 from Preparing to Preparing', '2026-07-14 23:49:33'),
(148, 1, 'Changed Order SW804103 from Ready to Collected', '2026-07-14 23:49:35'),
(149, 1, 'Changed Order SW373058 from Ready to Collected', '2026-07-14 23:49:48'),
(150, 1, 'Changed Order SW504422 from Preparing to Ready', '2026-07-15 00:11:55'),
(151, 1, 'Changed Order SW674461 from Preparing to Ready', '2026-07-15 00:12:00'),
(152, 1, 'Changed Order SW674461 from Ready to Collected', '2026-07-15 00:12:11'),
(153, 1, 'Changed Order SW504422 from Ready to Collected', '2026-07-15 00:12:18'),
(154, 1, 'Changed Order SW982780 from Pending to Preparing', '2026-07-15 00:39:47'),
(155, 1, 'Changed Order SW982780 from Preparing to Ready', '2026-07-15 00:39:57'),
(156, 1, 'Changed Order SW982780 from Ready to Collected', '2026-07-15 00:40:04'),
(157, 1, 'Changed Order SW735383 from Pending to Preparing', '2026-07-15 00:40:35'),
(158, 1, 'Changed Order SW735383 from Preparing to Ready', '2026-07-15 00:40:41'),
(159, 1, 'Changed Order SW735383 from Ready to Collected', '2026-07-15 00:40:55'),
(160, 1, 'Changed Order SW441270 from Pending to Preparing', '2026-07-15 01:06:01'),
(161, 1, 'Changed Order SW769101 from Pending to Preparing', '2026-07-15 01:06:20'),
(162, 1, 'Changed Order SW769101 from Preparing to Ready', '2026-07-15 01:06:38'),
(163, 1, 'Changed Order SW441270 from Preparing to Ready', '2026-07-15 01:06:44'),
(164, 1, 'Changed Order SW769101 from Ready to Collected', '2026-07-15 01:06:55'),
(165, 1, 'Changed Order SW441270 from Ready to Collected', '2026-07-15 01:06:59'),
(166, 1, 'Changed Order SW455283 from Pending to Preparing', '2026-07-15 01:32:20'),
(167, 1, 'Changed Order SW455283 from Preparing to Ready', '2026-07-15 01:32:25'),
(168, 1, 'Changed Order SW455283 from Ready to Ready', '2026-07-15 01:32:25'),
(169, 1, 'Changed Order SW455283 from Ready to Collected', '2026-07-15 01:32:32'),
(170, 1, 'Changed Order SW098796 from Pending to Preparing', '2026-07-15 01:44:18'),
(171, 1, 'Changed Order SW305555 from Pending to Preparing', '2026-07-15 01:44:24'),
(172, 1, 'Changed Order SW434440 from Pending to Preparing', '2026-07-15 01:44:51'),
(173, 1, 'Changed Order SW434440 from Preparing to Ready', '2026-07-15 01:45:06'),
(174, 1, 'Changed Order SW434440 from Ready to Ready', '2026-07-15 01:45:06'),
(175, 1, 'Changed Order SW098796 from Preparing to Ready', '2026-07-15 01:53:43'),
(176, 1, 'Changed Order SW098796 from Ready to Collected', '2026-07-15 01:55:52'),
(177, 1, 'Changed Order SW305555 from Preparing to Ready', '2026-07-15 01:56:03'),
(178, 1, 'Changed Order SW305555 from Ready to Collected', '2026-07-15 01:56:24'),
(179, 1, 'Changed Order SW434440 from Ready to Collected', '2026-07-15 01:58:00'),
(180, 1, 'Changed Order SW722276 from Pending to Preparing', '2026-07-15 02:12:54'),
(181, 1, 'Received Purchase Order ID 16', '2026-07-15 02:37:40'),
(182, 1, 'Created Purchase Order. PO-20260715024010', '2026-07-15 02:40:11'),
(183, 1, 'Received Purchase Order ID 17', '2026-07-15 02:40:17'),
(184, 1, 'Received Purchase Order ID 11', '2026-07-15 02:40:26'),
(185, 1, 'Updated Purchase Order ID 15', '2026-07-15 11:23:41'),
(186, 1, 'Updated Purchase Order ID 15', '2026-07-15 11:23:42'),
(187, 1, 'Received Purchase Order ID 4', '2026-07-15 13:42:11'),
(188, 1, 'Cancelled Purchase Order ID 13', '2026-07-15 17:44:17'),
(189, 1, 'Updated Purchase Order ID 15', '2026-07-15 17:44:25'),
(190, 1, 'Changed Order SW553916 from Pending to Preparing', '2026-07-15 21:57:11'),
(191, 1, 'Changed Order SW553916 from Preparing to Ready', '2026-07-15 21:57:13'),
(192, 1, 'Changed Order SW722276 from Preparing to Ready', '2026-07-15 22:03:21'),
(193, 1, 'Changed Order SW154495 from Pending to Preparing', '2026-07-16 17:53:37'),
(194, 1, 'Added product: Lays Chips', '2026-07-16 19:17:01'),
(195, 1, 'Updated product: Lays Chips', '2026-07-16 20:51:28'),
(196, 1, 'Changed Order SW789642 from Pending to Preparing', '2026-07-16 21:55:12'),
(197, 1, 'Changed Order SW789642 from Preparing to Ready', '2026-07-16 21:55:14'),
(198, 1, 'Changed Order SW320392 from Pending to Preparing', '2026-07-16 22:00:01'),
(199, 1, 'Changed Order SW320392 from Preparing to Ready', '2026-07-16 22:00:02'),
(200, 1, 'Changed Order SW154495 from Preparing to Ready', '2026-07-16 22:03:46'),
(201, 1, 'Changed Order SW315560 from Pending to Preparing', '2026-07-16 22:04:19'),
(202, 1, 'Changed Order SW315560 from Preparing to Ready', '2026-07-16 22:04:21'),
(203, 1, 'Changed Order SW206685 from Pending to Preparing', '2026-07-16 22:06:52'),
(204, 1, 'Changed Order SW206685 from Preparing to Ready', '2026-07-16 22:06:53'),
(205, 1, 'Changed Order SW294401 from Pending to Preparing', '2026-07-16 22:10:05'),
(206, 1, 'Changed Order SW294401 from Preparing to Ready', '2026-07-16 22:10:06'),
(207, 1, 'Changed Order SW294401 from Ready to Collected', '2026-07-16 22:10:09'),
(208, 1, 'Changed Order SW729475 from Pending to Preparing', '2026-07-16 22:25:06'),
(209, 1, 'Changed Order SW729475 from Preparing to Ready', '2026-07-16 22:25:08'),
(210, 1, 'Changed Order SW360682 from Pending to Preparing', '2026-07-16 22:26:11'),
(211, 1, 'Changed Order SW360682 from Preparing to Ready', '2026-07-16 22:26:13'),
(212, 1, 'Changed Order SW609173 from Pending to Preparing', '2026-07-16 22:28:33'),
(213, 1, 'Changed Order SW609173 from Preparing to Ready', '2026-07-16 22:28:34'),
(214, 1, 'Changed Order SW609173 from Ready to Collected', '2026-07-16 22:28:35'),
(215, 1, 'Created Purchase Order. PO-20260716223130', '2026-07-16 22:31:33'),
(216, 1, 'Received Purchase Order ID 18', '2026-07-16 22:31:43'),
(217, 1, 'Created Purchase Order. PO-20260716223340', '2026-07-16 22:33:40'),
(218, 1, 'Received Purchase Order ID 19', '2026-07-16 22:33:45'),
(219, 1, 'Changed Order SW168992 from Pending to Preparing', '2026-07-17 11:47:59'),
(220, 1, 'Changed Order SW168992 from Preparing to Ready', '2026-07-17 11:48:04'),
(221, 1, 'Changed Order SW168992 from Ready to Collected', '2026-07-17 11:48:08'),
(222, 1, 'Changed Order SW227824 from Pending to Preparing', '2026-07-17 11:48:17'),
(223, 1, 'Changed Order SW227824 from Preparing to Ready', '2026-07-17 11:48:20'),
(224, 1, 'Changed Order SW227824 from Ready to Collected', '2026-07-17 11:48:23'),
(225, 4, 'Changed Order SW410331 from Pending to Preparing', '2026-07-17 11:51:44'),
(226, 5, 'Changed Order SW410331 from Preparing to Ready', '2026-07-17 11:55:09'),
(227, 4, 'Changed Order SW410331 from Ready to Collected', '2026-07-17 11:55:43'),
(228, 1, 'Changed Order SW591615 from Pending to Preparing', '2026-07-17 13:18:02'),
(229, 1, 'Changed Order SW591615 from Preparing to Ready', '2026-07-17 13:18:35'),
(230, 1, 'Changed Order SW591615 from Ready to Collected', '2026-07-17 13:18:37'),
(231, 1, 'Changed Order SW477586 from Pending to Preparing', '2026-07-17 13:24:08'),
(232, 1, 'Changed Order SW477586 from Preparing to Ready', '2026-07-17 13:24:10'),
(233, 1, 'Changed Order SW477586 from Ready to Collected', '2026-07-17 13:24:12'),
(234, 1, 'Changed Order SW587232 from Pending to Preparing', '2026-07-17 14:01:42'),
(235, 1, 'Changed Order SW587232 from Preparing to Ready', '2026-07-17 14:01:44'),
(236, 1, 'Changed Order SW587232 from Ready to Collected', '2026-07-17 14:01:47'),
(237, 1, 'Created Purchase Order. PO-20260717140436-393', '2026-07-17 14:04:37'),
(238, 1, 'Received Purchase Order ID 20', '2026-07-17 14:04:42'),
(239, 1, 'Changed Order SW612365 from Pending to Preparing', '2026-07-17 14:07:18'),
(240, 1, 'Changed Order SW612365 from Preparing to Ready', '2026-07-17 14:44:09'),
(241, 1, 'Changed Order SW612365 from Ready to Collected', '2026-07-17 14:44:11'),
(242, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:02:21'),
(243, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:04:32'),
(244, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:20:10'),
(245, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:20:18'),
(246, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:20:34'),
(247, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:29:30'),
(248, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:29:39'),
(249, 1, 'Updated Purchase Order ID 15', '2026-07-17 23:30:26'),
(250, 1, 'Updated product: Cup of Coffee', '2026-07-18 22:36:05'),
(251, 1, 'Added product: fgsf', '2026-07-18 22:56:19'),
(252, 1, 'Changed Order SW665440 from Preparing to Preparing', '2026-07-19 18:54:16'),
(253, 1, 'Updated Purchase Order ID 15', '2026-07-19 20:30:54'),
(254, 1, 'Updated Purchase Order ID 15', '2026-07-19 20:31:16'),
(255, 1, 'Updated Purchase Order ID 15', '2026-07-19 20:32:06'),
(256, 1, 'Created Purchase Order. PO-20260720235602-775', '2026-07-20 23:56:03'),
(257, 1, 'Received Purchase Order ID 21', '2026-07-20 23:56:11'),
(258, 1, 'Updated product: Cookies', '2026-07-21 00:30:58'),
(259, 1, 'Changed Order SW665440 from Preparing to Ready', '2026-07-21 11:31:00'),
(260, 1, 'Changed Order SW855661 from Pending to Preparing', '2026-07-21 11:31:02'),
(261, 1, 'Cancelled Purchase Order ID 15', '2026-07-21 17:45:00'),
(262, 1, 'Created Purchase Order. PO-20260721201843-775', '2026-07-21 20:18:43'),
(263, 1, 'Received Purchase Order ID 22', '2026-07-21 20:18:52'),
(264, 1, 'Created Purchase Order. PO-20260721204109-850', '2026-07-21 20:41:09'),
(265, 1, 'Received Purchase Order ID 23', '2026-07-21 20:56:13'),
(266, 1, 'Changed Order SW505608 from Pending to Preparing', '2026-07-22 20:18:16'),
(267, 1, 'Changed Order SW855661 from Preparing to Ready', '2026-07-22 20:18:21'),
(268, 1, 'Changed Order SW855661 from Ready to Collected', '2026-07-22 20:18:24'),
(269, 1, 'Changed Order SW665440 from Ready to Collected', '2026-07-22 20:18:26'),
(270, 1, 'Changed Order SW505608 from Preparing to Ready', '2026-07-22 20:18:36'),
(271, 1, 'Changed Order SW505608 from Ready to Collected', '2026-07-22 20:18:40'),
(272, 1, 'Changed Order SW234237 from Pending to Preparing', '2026-07-22 21:18:53'),
(273, 1, 'Changed Order SW234237 from Preparing to Preparing', '2026-07-22 21:18:54'),
(274, 1, 'Changed Order SW234237 from Preparing to Ready', '2026-07-22 21:18:55'),
(275, 1, 'Changed Order SW212679 from Pending to Preparing', '2026-07-23 15:29:38'),
(276, 1, 'Changed Order SW212679 from Preparing to Ready', '2026-07-23 15:29:41'),
(277, 1, 'Changed Order SW841365 from Pending to Preparing', '2026-07-23 16:12:58'),
(278, 1, 'Changed Order SW841365 from Preparing to Ready', '2026-07-23 17:18:20'),
(279, 1, 'Changed Order SW888776 from Pending to Preparing', '2026-07-24 17:26:30'),
(280, 1, 'Changed Order SW888776 from Preparing to Ready', '2026-07-24 17:26:33'),
(281, 1, 'Changed Order SW212679 from Ready to Collected', '2026-07-24 19:38:35'),
(282, 1, 'Changed Order SW994333 from Pending to Preparing', '2026-07-24 19:39:00'),
(283, 1, 'Changed Order SW888776 from Ready to Collected', '2026-07-24 19:39:02'),
(284, 1, 'Changed Order SW994333 from Preparing to Ready', '2026-07-24 19:39:05'),
(285, 1, 'Changed Order SW994333 from Ready to Collected', '2026-07-24 19:39:08'),
(286, 1, 'Changed Order SW807185 from Pending to Preparing', '2026-07-24 19:48:05'),
(287, 1, 'Changed Order SW807185 from Preparing to Ready', '2026-07-24 19:48:07'),
(288, 1, 'Changed Order SW807185 from Ready to Collected', '2026-07-24 19:48:09'),
(289, 1, 'Changed Order SW900231 from Pending to Preparing', '2026-07-24 19:49:07'),
(290, 1, 'Changed Order SW900231 from Preparing to Ready', '2026-07-24 19:49:10'),
(291, 1, 'Changed Order SW900231 from Ready to Collected', '2026-07-24 19:49:14'),
(292, 1, 'Changed Order SW504398 from Pending to Preparing', '2026-07-24 19:53:53'),
(293, 1, 'Changed Order SW504398 from Preparing to Cancelled', '2026-07-24 19:54:00'),
(294, 1, 'Changed Order SW640929 from Pending to Preparing', '2026-07-24 19:54:22'),
(295, 1, 'Changed Order SW640929 from Preparing to Ready', '2026-07-24 19:54:26'),
(296, 1, 'Changed Order SW640929 from Ready to Collected', '2026-07-24 19:55:09'),
(297, 1, 'Changed Order SW234237 from Ready to Collected', '2026-07-24 19:55:53'),
(298, 1, 'Changed Order SW192221 from Pending to Preparing', '2026-07-24 22:29:52'),
(299, 1, 'Changed Order SW192221 from Preparing to Preparing', '2026-07-24 22:29:52'),
(300, 1, 'Changed Order SW192221 from Preparing to Ready', '2026-07-24 22:29:53'),
(301, 1, 'Changed Order SW192221 from Ready to Collected', '2026-07-24 22:29:54'),
(302, 1, 'Changed Order SW788609 from Pending to Preparing', '2026-07-25 20:35:05'),
(303, 1, 'Changed Order SW788609 from Preparing to Ready', '2026-07-25 20:35:14'),
(304, 1, 'Changed Order SW788609 from Ready to Collected', '2026-07-25 20:35:28'),
(305, 1, 'Changed Order SW241696 from Pending to Preparing', '2026-07-25 20:42:25'),
(306, 1, 'Changed Order SW241696 from Preparing to Ready', '2026-07-25 20:42:38'),
(307, 1, 'Changed Order SW241696 from Ready to Collected', '2026-07-25 20:42:50'),
(308, 1, 'Changed Order SW758205 from Pending to Preparing', '2026-08-08 18:47:58'),
(309, 1, 'Changed Order SW758205 from Preparing to Ready', '2026-08-08 18:48:00'),
(310, 1, 'Changed Order SW758205 from Ready to Collected', '2026-08-08 18:48:02'),
(311, 1, 'Changed Order SW586971 from Pending to Preparing', '2026-08-08 18:48:04'),
(312, 1, 'Changed Order SW586971 from Preparing to Ready', '2026-08-08 18:48:06'),
(313, 1, 'Changed Order SW586971 from Ready to Collected', '2026-08-08 18:48:07'),
(314, 1, 'Changed Order SW640361 from Pending to Preparing', '2026-08-08 18:48:49'),
(315, 1, 'Changed Order SW640361 from Preparing to Ready', '2026-08-08 18:48:51'),
(316, 1, 'Changed Order SW640361 from Ready to Collected', '2026-08-08 18:48:57'),
(317, 1, 'Changed Order SW328444 from Pending to Preparing', '2026-08-08 18:52:26'),
(318, 1, 'Changed Order SW461957 from Pending to Preparing', '2026-08-08 18:52:27'),
(319, 1, 'Changed Order SW328444 from Preparing to Ready', '2026-08-08 18:52:29'),
(320, 1, 'Changed Order SW461957 from Preparing to Ready', '2026-08-08 18:52:32'),
(321, 1, 'Changed Order SW328444 from Ready to Collected', '2026-08-08 18:52:51'),
(322, 1, 'Changed Order SW461957 from Ready to Collected', '2026-08-08 18:52:52'),
(323, 1, 'Changed Order SW841365 from Ready to Collected', '2026-08-09 11:20:51'),
(324, 1, 'Changed Order SW715609 from Pending to Preparing', '2026-08-09 11:20:54'),
(325, 1, 'Changed Order SW715609 from Preparing to Ready', '2026-08-09 11:26:31'),
(326, 1, 'Changed Order SW715609 from Ready to Collected', '2026-08-09 11:26:41'),
(327, 1, 'Created Purchase Order. PO-20260809231718-708', '2026-08-09 23:17:20'),
(328, 1, 'Received Purchase Order ID 24', '2026-08-09 23:17:28'),
(329, 1, 'Updated Product: Pizza', '2026-08-12 07:20:50'),
(330, 1, 'Changed Order SW837759 from Pending to Preparing', '2026-08-12 23:37:12'),
(331, 1, 'Changed Order SW787681 from Pending to Preparing', '2026-08-12 23:37:14'),
(332, 1, 'Changed Order SW837759 from Preparing to Ready', '2026-08-12 23:37:16'),
(333, 1, 'Changed Order SW787681 from Preparing to Ready', '2026-08-12 23:37:18'),
(334, 1, 'Changed Order SW119586 from Pending to Preparing', '2026-08-12 23:37:22'),
(335, 1, 'Changed Order SW119586 from Preparing to Ready', '2026-08-12 23:37:25'),
(336, 1, 'Changed Order SW787681 from Ready to Collected', '2026-08-12 23:37:28'),
(337, 1, 'Changed Order SW119586 from Ready to Collected', '2026-08-12 23:37:47'),
(338, 1, 'Changed Order SW837759 from Ready to Collected', '2026-08-12 23:37:55'),
(339, 1, 'Stock adjustment: Increase 10 x Damaged (Product ID: 5)', '2026-08-13 22:19:06'),
(340, 1, 'Stock adjustment: Decrease 50 x Expired (Product ID: 2)', '2026-08-15 11:27:51'),
(341, 1, 'Updated Product: Cool Drink', '2026-08-15 11:29:04'),
(342, 1, 'Changed Order SW763694 from Pending to Preparing', '2026-08-15 15:47:20'),
(343, 1, 'Changed Order SW763694 from Preparing to Ready', '2026-08-15 15:47:25'),
(344, 1, 'Changed Order SW763694 from Ready to Collected', '2026-08-15 19:10:32'),
(345, 1, 'Changed Order SW323880 from Pending to Preparing', '2026-08-15 19:11:17'),
(346, 1, 'Changed Order SW323880 from Preparing to Ready', '2026-08-15 19:11:19'),
(347, 1, 'Changed Order SW323880 from Ready to Collected', '2026-08-15 19:11:21');

-- --------------------------------------------------------

--
-- Table structure for table `audit_changes`
--

CREATE TABLE `audit_changes` (
  `id` int NOT NULL,
  `audit_id` int NOT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_general_ci,
  `new_value` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_changes`
--

INSERT INTO `audit_changes` (`id`, `audit_id`, `field_name`, `old_value`, `new_value`) VALUES
(1, 1, 'description', 'Assorted Cool Soft drink (500ml)    ', 'Assorted Cool Soft drink (500ml)'),
(2, 1, 'price', '18.50', '20'),
(3, 2, 'status', 'Pending', 'Preparing'),
(4, 3, 'status', 'Preparing', 'Ready'),
(5, 4, 'status', 'Ready', 'Collected'),
(6, 4, 'invoice_number', NULL, 'INV-000001'),
(7, 5, 'status', 'Pending', 'Preparing'),
(8, 6, 'status', 'Preparing', 'Ready'),
(9, 7, 'status', 'Ready', 'Collected'),
(10, 7, 'invoice_number', NULL, 'INV-000002'),
(11, 8, 'business_address', '[changed]', '[changed]');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `entity` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `entity_id` int NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `entity`, `entity_id`, `action`, `created_at`) VALUES
(1, 1, 'product', 5, 'UPDATE', '2026-08-15 13:29:04'),
(2, 1, 'order', 204, 'STATUS_CHANGE', '2026-08-15 17:47:20'),
(3, 1, 'order', 204, 'STATUS_CHANGE', '2026-08-15 17:47:24'),
(4, 1, 'order', 204, 'STATUS_CHANGE', '2026-08-15 21:10:31'),
(5, 1, 'order', 205, 'STATUS_CHANGE', '2026-08-15 21:11:17'),
(6, 1, 'order', 205, 'STATUS_CHANGE', '2026-08-15 21:11:19'),
(7, 1, 'order', 205, 'STATUS_CHANGE', '2026-08-15 21:11:21'),
(8, 1, 'business_settings', 1, 'UPDATE', '2026-08-23 13:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `business_settings`
--

CREATE TABLE `business_settings` (
  `id` int NOT NULL,
  `business_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `business_address` text COLLATE utf8mb4_general_ci NOT NULL,
  `vat_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `vat_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vat_rate` decimal(5,2) NOT NULL DEFAULT '15.00',
  `next_invoice_number` int NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_settings`
--

INSERT INTO `business_settings` (`id`, `business_name`, `business_address`, `vat_enabled`, `vat_number`, `vat_rate`, `next_invoice_number`, `created_at`, `updated_at`) VALUES
(1, 'SwiftOrder Demo Business', '587 Honey-Grove Street,\r\nPretoria East,\r\nGauteng,\r\nSouth Africa,\r\n0152', 0, NULL, 15.00, 3, '2026-08-15 21:09:52', '2026-08-23 13:35:38');

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_notes`
--

CREATE TABLE `goods_received_notes` (
  `id` int NOT NULL,
  `grn_number` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `purchase_order_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `received_by` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `received_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goods_received_notes`
--

INSERT INTO `goods_received_notes` (`id`, `grn_number`, `purchase_order_id`, `supplier_id`, `received_by`, `total`, `notes`, `received_at`) VALUES
(1, 'GRN-20260720235610-529', 21, 7, 1, 2000.00, NULL, '2026-07-20 23:56:10'),
(2, 'GRN-20260721201852-679', 22, 1, 1, 3500.00, NULL, '2026-07-21 20:18:52'),
(3, 'GRN-20260721205613-923', 23, 2, 1, 1250.00, 'Please do not include chocolate chip cookies as customers do not buy those.', '2026-07-21 20:56:13'),
(4, 'GRN-20260809231727-309', 24, 1, 1, 120.00, '', '2026-08-09 23:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_note_items`
--

CREATE TABLE `goods_received_note_items` (
  `id` int NOT NULL,
  `grn_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goods_received_note_items`
--

INSERT INTO `goods_received_note_items` (`id`, `grn_id`, `product_id`, `quantity`, `cost_price`, `line_total`) VALUES
(1, 1, 6, 100.00, 20.00, 2000.00),
(2, 2, 5, 100.00, 20.00, 2000.00),
(3, 2, 50, 100.00, 15.00, 1500.00),
(5, 3, 6, 50.00, 25.00, 1250.00),
(6, 4, 5, 10.00, 12.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `login_rate_limits`
--

CREATE TABLE `login_rate_limits` (
  `username_hash` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `window_started_at` datetime NOT NULL,
  `failed_attempts` int UNSIGNED NOT NULL DEFAULT '0',
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `items` text COLLATE utf8mb4_general_ci,
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `payment_method` enum('cash_pmt','card_pmt','eft_pmt') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'cash_pmt',
  `invoice_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_issued_at` datetime DEFAULT NULL,
  `business_name_at_invoice` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `business_address_at_invoice` text COLLATE utf8mb4_general_ci,
  `business_vat_number_at_invoice` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `request_id` char(36) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vat_enabled_at_sale` tinyint(1) NOT NULL DEFAULT '0',
  `vat_rate_at_sale` decimal(5,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `customer_name`, `items`, `total`, `created_at`, `status`, `payment_method`, `invoice_number`, `invoice_issued_at`, `business_name_at_invoice`, `business_address_at_invoice`, `business_vat_number_at_invoice`, `request_id`, `vat_enabled_at_sale`, `vat_rate_at_sale`, `vat_amount`, `subtotal`) VALUES
(54, 'SW151205', 'Greggor', 'Sphatlho x 2\r\nCool Drink x 2', 85.00, '2026-07-04 13:36:31', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(55, 'SW702500', 'Ally', 'Dagwood x 1\r\nChips x 1', 52.00, '2026-07-04 13:38:19', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(59, 'SW782539', 'biggie', 'Cool Drink x 1\r\nFries x 2', 98.50, '2026-07-04 14:10:40', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(60, 'SW369439', 'Jackson', 'Fries x 1\r\nSphatlho x 2\r\nCool Drink x 2', 125.00, '2026-07-04 16:38:14', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(62, 'SW565396', 'ross', 'Merch x 1', 120.00, '2026-07-05 10:57:51', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(91, 'SW929446', 'Khumo', 'Dagwood x 1\r\nCool Drink x 2', 72.00, '2026-07-06 21:18:51', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(92, 'SW490624', 'Junior', 'Chips x 1\r\nCoffee x 1\r\nDagwood x 1', 68.00, '2026-07-06 21:19:07', 'Cancelled', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(93, 'SW386913', 'Lucky', 'Cool Drink x 1', 18.50, '2026-07-06 21:45:09', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(94, 'SW000636', 'DSFSfgdfdf', 'Cool Drink x 1\r\nFries x 1\r\nDagwood x 1', 93.50, '2026-07-06 21:56:05', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(95, 'SW936450', 'AAAAAAAAAAAAAAAAAA', 'Fries x 1\r\nCool Drink x 1', 58.50, '2026-07-06 22:00:27', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(96, 'SW821422', 'asa', 'Dagwood x 1\nFries x 1', 75.00, '2026-07-06 22:59:48', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(98, 'SW664960', 'Steve', 'Coffee x 2\nFries x 3\nDagwood x 2\nCool Drink x 8', 370.00, '2026-07-06 23:00:15', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(99, 'SW554177', 'Emily', 'Fries x 2\nCool Drink x 1\nCoffee x 2', 130.50, '2026-07-06 23:00:30', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(101, 'SW448403', '786', 'Cool Drink x 1', 18.50, '2026-07-07 11:19:16', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(102, 'SW242598', 'sdfsd', 'Cool Drink x 2', 37.00, '2026-07-07 11:19:21', 'Cancelled', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(103, 'SW823589', 'ggfdsfg', 'Fries x 9\nCool Drink x 10\nDagwood x 5', 720.00, '2026-07-07 11:19:33', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(105, 'SW764629', 'cccccccccccc', 'Sphatlho x 2', 48.00, '2026-07-07 20:09:24', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(106, 'SW938866', 'dsFDFSADAF', 'Dagwood x 5', 175.00, '2026-07-07 20:41:31', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(107, 'SW340903', 'asc', 'Fries x 1\nDagwood x 1\nCool Drink x 1', 93.50, '2026-07-07 20:59:09', 'Cancelled', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(108, 'SW264811', 'SDFSDF', 'Fries x 2\nCool Drink x 1', 98.50, '2026-07-07 20:59:16', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(109, 'SW800170', 'ASFFFFAS', 'Dagwood x 1\nSphatlho x 2', 83.00, '2026-07-07 20:59:24', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(110, 'SW514964', 'fdsafd', 'Cool Drink x 5', 92.50, '2026-07-07 21:05:24', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(111, 'SW495235', 'DSFS', 'Dagwood x 3', 105.00, '2026-07-07 21:05:32', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(112, 'SW905152', 'hjghghwtter', 'Cool Drink x 3\nSphatlho x 2', 103.50, '2026-07-07 21:05:42', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(113, 'SW589049', 'fsdgfdger', 'Fries x 1\nCool Drink x 1', 58.50, '2026-07-07 21:05:49', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(114, 'SW736242', 'DSFS', 'Cool Drink x 3\nFries x 6', 295.50, '2026-07-07 23:58:09', 'Cancelled', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(116, 'SW592482', 'dd', 'Sphatlho x 1', 24.00, '2026-07-09 16:36:49', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(117, 'SW996268', 'ZED', 'Fries x 1\nDagwood x 3\nCool Drink x 1', 163.50, '2026-07-09 17:19:03', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(118, 'SW630927', 'Craig', 'Merch x 3\nChips x 1', 377.00, '2026-07-09 17:19:23', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(119, 'SW331780', 'Lerato Nkosi', 'Fries x 1\nCool Drink x 2\nChips x 2\nDagwood x 2', 181.00, '2026-07-09 17:20:01', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(120, 'SW494897', 'Tiny', 'Fries x 3', 120.00, '2026-07-09 18:20:00', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(121, 'SW822372', 'Junior Sithole', 'Coffee x 4\nChips x 4\nDagwood x 4', 272.00, '2026-07-09 18:20:29', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(122, 'SW134452', 'Donna Adams', 'Cool Drink x 2\nFries x 2\nSphatlho x 2', 165.00, '2026-07-09 19:53:23', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(123, 'SW310855', 'Colin Moremi', 'Merch x 5', 600.00, '2026-07-09 20:47:11', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(124, 'SW151070', 'doony', 'Cool Drink x 2', 37.00, '2026-07-10 14:14:20', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(125, 'SW744545', 'Paniki', 'Dagwood x 2\nCool Drink x 2\nChips x 1', 124.00, '2026-07-10 14:16:54', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(126, 'SW621389', 'jgfhghh', 'Merch x 12', 1440.00, '2026-07-10 14:18:23', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(127, 'SW547813', 'Palesa', 'Dagwood x 2', 70.00, '2026-07-10 21:59:22', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(128, 'SW967759', 'Craig', 'Chips x 3', 51.00, '2026-07-10 22:06:52', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(129, 'SW929414', 'ALex Msomi', 'Coffee x 3', 48.00, '2026-07-10 22:14:40', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(130, 'SW260534', 'Clyde Lithole', 'Dagwood x 7', 245.00, '2026-07-10 22:59:20', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(131, 'SW928749', 'Innocent Mabasa', 'Dagwood x 2\nRooibos x 1\nCool Drink x 1\nMerch x 2', 353.50, '2026-07-10 23:38:56', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(132, 'SW350061', 'Jake Paul', 'Rooibos x 1', 25.00, '2026-07-10 23:39:15', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(133, 'SW812330', 'Harold Moonsamy', 'Chips x 1', 17.00, '2026-07-10 23:39:36', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(134, 'SW780128', 'ujujujujujujujuj', 'Dagwood x 2', 70.00, '2026-07-11 11:26:27', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(135, 'SW691309', 'Makaveli', 'Cool Drink x 2\nCoffee x 3\nDagwood x 2', 155.00, '2026-07-11 11:50:09', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(136, 'SW031127', 'lolo', 'Cool Drink x 1', 18.50, '2026-07-11 11:54:42', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(137, 'SW368827', 'Tenpenny', 'Dagwood x 1\nSphatlho x 1', 59.00, '2026-07-11 18:59:33', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(138, 'SW191125', 'Grey', 'Rooibos x 1', 25.00, '2026-07-11 18:59:55', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(139, 'SW282881', 'DSFS', 'Rooibos x 2\nDagwood x 1', 85.00, '2026-07-11 22:24:58', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(140, 'SW917412', 'Chris', 'Rooibos x 2\nCool Drink x 3\nDagwood x 2\nCoffee x 2', 207.50, '2026-07-13 17:49:19', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(141, 'SW626556', 'Isaac Maluleka', 'Cheese Burger x 2\nCool Drink x 2\nFries x 2\nChips x 2', 231.00, '2026-07-14 01:15:04', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(142, 'SW051690', 'Paul Lerothodi', 'Rooibos x 1', 25.00, '2026-07-14 01:15:57', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(143, 'SW460408', 'David Mareka', 'Fries x 2\nCheese Burger x 1\nPizza x 1\nCool Drink x 2', 202.00, '2026-07-14 13:10:47', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(144, 'SW899619', 'Craig Phoswa', 'Cheese Burger x 6\nCool Drink x 6', 351.00, '2026-07-14 14:08:58', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(145, 'SW991040', 'DSFS', 'Cool Drink x 1', 18.50, '2026-07-14 14:24:19', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(146, 'SW402779', 'hjghgh', 'Cool Drink x 2', 37.00, '2026-07-14 22:01:47', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(147, 'SW947335', 'DSFS', 'Cheese Burger x 1', 40.00, '2026-07-14 22:05:18', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(148, 'SW674461', 'fer4', 'Cool Drink x 4', 74.00, '2026-07-14 22:58:36', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(149, 'SW373058', 'wer', 'Sphatlho x 2\nFries x 1', 88.00, '2026-07-14 22:58:51', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(150, 'SW368562', 'Andre', 'Pizza x 4\nFries x 2', 260.00, '2026-07-14 22:59:08', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(151, 'SW804103', 'sadads', 'Cool Drink x 1\nRooibos x 2', 68.50, '2026-07-14 23:47:18', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(152, 'SW504422', 'DSFS', 'Pap & Wors x 6', 270.00, '2026-07-14 23:47:24', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(153, 'SW016976', 'Nebula', 'Cool Drink x 1\nCoffee x 3\nCheese Burger x 1', 106.50, '2026-07-14 23:48:02', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(154, 'SW735383', 'hjghgh', 'Cool Drink x 4', 74.00, '2026-07-15 00:38:39', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(155, 'SW982780', 'Cliffors', 'Coffee x 2\nPizza x 4\nPap & Wors x 4\nCheese Burger x 1\nRooibos x 2', 482.00, '2026-07-15 00:39:30', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(156, 'SW769101', 'Elias', 'Fries x 2\nSphatlho x 2\nPap & Wors x 1', 173.00, '2026-07-15 01:05:14', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(157, 'SW441270', 'hjghgh', 'Fries x 3\nPizza x 1', 165.00, '2026-07-15 01:05:51', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(158, 'SW434440', 'DSFS', 'Coffee x 3\nCool Drink x 3', 103.50, '2026-07-15 01:27:02', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(159, 'SW455283', 'Greg', 'Cheese Burger x 2\nRooibos x 2', 130.00, '2026-07-15 01:32:07', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(160, 'SW305555', 'Gwen Moroka', 'Pizza x 3\nPap & Wors x 2\nCheese Burger x 1', 265.00, '2026-07-15 01:43:33', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(161, 'SW098796', 'Moses Nkani', 'Pizza x 1\nPap & Wors x 1\nFries x 1\nSphatlho x 3', 202.00, '2026-07-15 01:44:05', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(162, 'SW722276', 'hjghgh', 'Chips x 1', 17.00, '2026-07-15 01:58:14', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(163, 'SW553916', 'Busi', 'Coffee x 3', 48.00, '2026-07-15 21:57:02', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(164, 'SW154495', 'DSFS', 'Cool Drink x 1\nCoffee x 2', 50.50, '2026-07-16 13:53:32', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(165, 'SW320392', 'Mike', 'Cheese Burger x 2\nCool Drink x 2', 117.00, '2026-07-16 13:53:49', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(166, 'SW789642', 'Ricky', 'Coffee x 3', 48.00, '2026-07-16 21:54:55', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(167, 'SW315560', 'asa', 'Cool Drink x 1', 18.50, '2026-07-16 22:04:11', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(168, 'SW206685', 'Jerry', 'Rooibos x 1\nCheese Burger x 3\nCool Drink x 1\nCoffee x 3', 211.50, '2026-07-16 22:06:46', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(169, 'SW294401', 'Morty', 'Sphatlho x 1\nPizza x 3\nFries x 2\nPap & Wors x 2', 329.00, '2026-07-16 22:09:53', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(170, 'SW729475', 'Summer', 'Lays Chips x 6', 300.00, '2026-07-16 22:24:56', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(171, 'SW360682', 'DSFS', 'Cool Drink x 1\nCoffee x 1\nCheese Burger x 2', 114.50, '2026-07-16 22:26:00', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(172, 'SW609173', 'huhg', 'Rooibos x 1', 25.00, '2026-07-16 22:27:50', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(173, 'SW227824', 'Sharon', 'Rooibos x 1\nCheese Burger x 2', 105.00, '2026-07-16 22:28:24', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(174, 'SW168992', 'thapelo', 'Fries x 1\nSphatlho x 1', 64.00, '2026-07-17 11:38:36', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(175, 'SW410331', 'kfd;gdfg', 'Coffee x 1', 16.00, '2026-07-17 11:51:13', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(176, 'SW591615', 'Lloyd', 'Cool Drink x 2\nCheese Burger x 1', 77.00, '2026-07-17 13:17:34', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(177, 'SW477586', 'Kurtis Blow', 'Coffee x 1\nCheese Burger x 2\nRooibos x 1\nCool Drink x 1', 139.50, '2026-07-17 13:24:02', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(178, 'SW587232', 'DSFS', 'Cheese Burger x 3', 120.00, '2026-07-17 13:58:47', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(179, 'SW612365', 'DSFS', 'Sphatlho x 1\nFries x 1', 64.00, '2026-07-17 14:06:05', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(180, 'SW994333', 'sdad', 'Pap & Wors x 3', 135.00, '2026-07-18 20:11:21', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(181, 'SW715609', 'sads', 'Rooibos x 2\nCheese Burger x 1\nCool Drink x 1', 108.50, '2026-07-18 21:19:12', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(182, 'SW841365', 'Joel', 'Pap & Wors x 1\nCheese Burger x 1', 85.00, '2026-07-18 21:23:25', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(183, 'SW855661', 'gfhfg', 'fgsf x 1\nCheese Burger x 2\nRooibos x 1', 139.00, '2026-07-19 00:27:50', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(184, 'SW665440', 'asa', 'fgsf x 1', 34.00, '2026-07-19 16:37:30', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(185, 'SW505608', 'Kabelo Adams', 'Cookies x 7\nCheese Burger x 2\nRooibos x 1\nCup of Coffee x 4\nCool Drink x 4', 481.00, '2026-07-22 20:18:09', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(186, 'SW234237', 'Peter Sithole', 'Rooibos x 2\nCheese Burger x 6', 290.00, '2026-07-22 21:18:48', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(187, 'SW212679', 'Bongani', 'Cookies x 1', 34.00, '2026-07-22 21:54:46', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(188, 'SW888776', 'Danny', 'Cookies x 1\nCheese Burger x 1', 74.00, '2026-07-24 17:25:46', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(189, 'SW807185', 'Clyde', 'Cookies x 4', 136.00, '2026-07-24 19:47:54', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(190, 'SW900231', 'Clyde 2', 'Cookies x 5', 170.00, '2026-07-24 19:49:04', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(191, 'SW504398', 'Kabelo', 'Cool Drink x 2', 37.00, '2026-07-24 19:53:46', 'Cancelled', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(192, 'SW640929', 'Greg', 'Cookies x 3', 102.00, '2026-07-24 19:54:11', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(193, 'SW192221', 'Michael', 'Cookies x 2\nCheese Burger x 2', 148.00, '2026-07-24 22:29:45', 'Collected', 'card_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(194, 'SW788609', 'Vusi', 'Sphatlho x 1\nFries x 1', 64.00, '2026-07-25 20:34:33', 'Collected', 'card_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(195, 'SW241696', 'Palesa Maluleka', 'Cheese Burger x 2\nCookies x 1', 114.00, '2026-07-25 20:42:15', 'Collected', 'card_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(196, 'SW586971', 'Morty', 'Cheese Burger x 3\nCookies x 1', 154.00, '2026-08-08 18:46:55', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(197, 'SW758205', 'George', 'Cookies x 4', 136.00, '2026-08-08 18:47:11', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(198, 'SW640361', 'Ricky', 'Cool Drink x 3\nCheese Burger x 3', 175.50, '2026-08-08 18:48:43', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(199, 'SW461957', 'Nelly', 'Rooibos x 3', 75.00, '2026-08-08 18:49:36', 'Collected', 'eft_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(200, 'SW328444', 'Eddy', 'Cheese Burger x 3', 120.00, '2026-08-08 18:49:49', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(201, 'SW837759', 'joe', 'Cup of Coffee x 1\nRooibos x 2\nCheese Burger x 3', 186.00, '2026-08-11 09:16:07', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(202, 'SW787681', 'bucky', 'Cookies x 1\nCheese Burger x 1', 74.00, '2026-08-11 16:41:33', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(203, 'SW119586', 'Greg', 'Cookies x 1\nCheese Burger x 1', 74.00, '2026-08-12 23:36:55', 'Collected', 'cash_pmt', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(204, 'SW763694', 'greg', 'Rooibos x 1, Cheese Burger x 2,', 105.00, '2026-08-15 15:46:41', 'Collected', 'cash_pmt', 'INV-000001', '2026-08-15 21:10:31', NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00),
(205, 'SW323880', 'Isaac', 'Cup of Coffee x 1, Cool Drink x 2,', 56.00, '2026-08-15 19:11:11', 'Collected', 'cash_pmt', 'INV-000002', '2026-08-15 21:11:21', NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_name_at_sale` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name_at_sale`, `quantity`, `price`) VALUES
(1, 93, 5, 'Cool Drink', 1, 18.50),
(2, 94, 5, 'Cool Drink', 1, 18.50),
(3, 94, 3, 'Fries', 1, 40.00),
(4, 94, 2, 'Pizza', 1, 35.00),
(5, 95, 3, 'Fries', 1, 40.00),
(6, 95, 5, 'Cool Drink', 1, 18.50),
(7, 96, 2, 'Pizza', 1, 35.00),
(8, 96, 3, 'Fries', 1, 40.00),
(12, 98, 40, 'Cup of Coffee', 2, 16.00),
(13, 98, 3, 'Fries', 3, 40.00),
(14, 98, 2, 'Pizza', 2, 35.00),
(15, 98, 5, 'Cool Drink', 8, 18.50),
(16, 99, 3, 'Fries', 2, 40.00),
(17, 99, 5, 'Cool Drink', 1, 18.50),
(18, 99, 40, 'Cup of Coffee', 2, 16.00),
(22, 101, 5, 'Cool Drink', 1, 18.50),
(23, 102, 5, 'Cool Drink', 2, 18.50),
(24, 103, 3, 'Fries', 9, 40.00),
(25, 103, 5, 'Cool Drink', 10, 18.50),
(26, 103, 2, 'Pizza', 5, 35.00),
(30, 105, 1, 'Sphatlho', 2, 24.00),
(31, 106, 2, 'Pizza', 5, 35.00),
(32, 107, 3, 'Fries', 1, 40.00),
(33, 107, 2, 'Pizza', 1, 35.00),
(34, 107, 5, 'Cool Drink', 1, 18.50),
(35, 108, 3, 'Fries', 2, 40.00),
(36, 108, 5, 'Cool Drink', 1, 18.50),
(37, 109, 2, 'Pizza', 1, 35.00),
(38, 109, 1, 'Sphatlho', 2, 24.00),
(39, 110, 5, 'Cool Drink', 5, 18.50),
(40, 111, 2, 'Pizza', 3, 35.00),
(41, 112, 5, 'Cool Drink', 3, 18.50),
(42, 112, 1, 'Sphatlho', 2, 24.00),
(43, 113, 3, 'Fries', 1, 40.00),
(44, 113, 5, 'Cool Drink', 1, 18.50),
(45, 114, 5, 'Cool Drink', 3, 18.50),
(46, 114, 3, 'Fries', 6, 40.00),
(51, 116, 1, 'Sphatlho', 1, 24.00),
(52, 117, 3, 'Fries', 1, 40.00),
(53, 117, 2, 'Pizza', 3, 35.00),
(54, 117, 5, 'Cool Drink', 1, 18.50),
(55, 118, 8, 'Cheese Burger', 3, 120.00),
(56, 118, 6, 'Chips', 1, 17.00),
(57, 119, 3, 'Fries', 1, 40.00),
(58, 119, 5, 'Cool Drink', 2, 18.50),
(59, 119, 6, 'Chips', 2, 17.00),
(60, 119, 2, 'Pizza', 2, 35.00),
(61, 120, 3, 'Fries', 3, 40.00),
(62, 121, 40, 'Cup of Coffee', 4, 16.00),
(63, 121, 6, 'Chips', 4, 17.00),
(64, 121, 2, 'Pizza', 4, 35.00),
(65, 122, 5, 'Cool Drink', 2, 18.50),
(66, 122, 3, 'Fries', 2, 40.00),
(67, 122, 1, 'Sphatlho', 2, 24.00),
(68, 123, 8, 'Cheese Burger', 5, 120.00),
(69, 124, 5, 'Cool Drink', 2, 18.50),
(70, 125, 2, 'Pizza', 2, 35.00),
(71, 125, 5, 'Cool Drink', 2, 18.50),
(72, 125, 6, 'Chips', 1, 17.00),
(73, 126, 8, 'Cheese Burger', 12, 120.00),
(74, 127, 2, 'Pizza', 2, 35.00),
(75, 128, 6, 'Chips', 3, 17.00),
(76, 129, 40, 'Cup of Coffee', 3, 16.00),
(77, 130, 2, 'Pizza', 7, 35.00),
(78, 131, 2, 'Pizza', 2, 35.00),
(79, 131, 47, 'Rooibos', 1, 25.00),
(80, 131, 5, 'Cool Drink', 1, 18.50),
(81, 131, 8, 'Cheese Burger', 2, 120.00),
(82, 132, 47, 'Rooibos', 1, 25.00),
(83, 133, 6, 'Chips', 1, 17.00),
(84, 134, 2, 'Pizza', 2, 35.00),
(85, 135, 5, 'Cool Drink', 2, 18.50),
(86, 135, 40, 'Cup of Coffee', 3, 16.00),
(87, 135, 2, 'Pizza', 2, 35.00),
(88, 136, 5, 'Cool Drink', 1, 18.50),
(89, 137, 2, 'Pizza', 1, 35.00),
(90, 137, 1, 'Sphatlho', 1, 24.00),
(91, 138, 47, 'Rooibos', 1, 25.00),
(92, 139, 47, 'Rooibos', 2, 25.00),
(93, 139, 2, 'Pizza', 1, 35.00),
(94, 140, 47, 'Rooibos', 2, 25.00),
(95, 140, 5, 'Cool Drink', 3, 18.50),
(96, 140, 2, 'Pizza', 2, 35.00),
(97, 140, 40, 'Cup of Coffee', 2, 16.00),
(98, 141, 8, 'Cheese Burger', 2, 40.00),
(99, 141, 5, 'Cool Drink', 2, 18.50),
(100, 141, 3, 'Fries', 2, 40.00),
(101, 141, 6, 'Chips', 2, 17.00),
(102, 142, 47, 'Rooibos', 1, 25.00),
(103, 143, 3, 'Fries', 2, 40.00),
(104, 143, 8, 'Cheese Burger', 1, 40.00),
(105, 143, 2, 'Pizza', 1, 45.00),
(106, 143, 5, 'Cool Drink', 2, 18.50),
(107, 144, 8, 'Cheese Burger', 6, 40.00),
(108, 144, 5, 'Cool Drink', 6, 18.50),
(109, 145, 5, 'Cool Drink', 1, 18.50),
(110, 146, 5, 'Cool Drink', 2, 18.50),
(111, 147, 8, 'Cheese Burger', 1, 40.00),
(112, 148, 5, 'Cool Drink', 4, 18.50),
(113, 149, 1, 'Sphatlho', 2, 24.00),
(114, 149, 3, 'Fries', 1, 40.00),
(115, 150, 2, 'Pizza', 4, 45.00),
(116, 150, 3, 'Fries', 2, 40.00),
(117, 151, 5, 'Cool Drink', 1, 18.50),
(118, 151, 47, 'Rooibos', 2, 25.00),
(119, 152, 49, 'Pap & Wors', 6, 45.00),
(120, 153, 5, 'Cool Drink', 1, 18.50),
(121, 153, 40, 'Cup of Coffee', 3, 16.00),
(122, 153, 8, 'Cheese Burger', 1, 40.00),
(123, 154, 5, 'Cool Drink', 4, 18.50),
(124, 155, 40, 'Cup of Coffee', 2, 16.00),
(125, 155, 2, 'Pizza', 4, 45.00),
(126, 155, 49, 'Pap & Wors', 4, 45.00),
(127, 155, 8, 'Cheese Burger', 1, 40.00),
(128, 155, 47, 'Rooibos', 2, 25.00),
(129, 156, 3, 'Fries', 2, 40.00),
(130, 156, 1, 'Sphatlho', 2, 24.00),
(131, 156, 49, 'Pap & Wors', 1, 45.00),
(132, 157, 3, 'Fries', 3, 40.00),
(133, 157, 2, 'Pizza', 1, 45.00),
(134, 158, 40, 'Cup of Coffee', 3, 16.00),
(135, 158, 5, 'Cool Drink', 3, 18.50),
(136, 159, 8, 'Cheese Burger', 2, 40.00),
(137, 159, 47, 'Rooibos', 2, 25.00),
(138, 160, 2, 'Pizza', 3, 45.00),
(139, 160, 49, 'Pap & Wors', 2, 45.00),
(140, 160, 8, 'Cheese Burger', 1, 40.00),
(141, 161, 2, 'Pizza', 1, 45.00),
(142, 161, 49, 'Pap & Wors', 1, 45.00),
(143, 161, 3, 'Fries', 1, 40.00),
(144, 161, 1, 'Sphatlho', 3, 24.00),
(145, 162, 6, 'Chips', 1, 17.00),
(146, 163, 40, 'Cup of Coffee', 3, 16.00),
(147, 164, 5, 'Cool Drink', 1, 18.50),
(148, 164, 40, 'Cup of Coffee', 2, 16.00),
(149, 165, 8, 'Cheese Burger', 2, 40.00),
(150, 165, 5, 'Cool Drink', 2, 18.50),
(151, 166, 40, 'Cup of Coffee', 3, 16.00),
(152, 167, 5, 'Cool Drink', 1, 18.50),
(153, 168, 47, 'Rooibos', 1, 25.00),
(154, 168, 8, 'Cheese Burger', 3, 40.00),
(155, 168, 5, 'Cool Drink', 1, 18.50),
(156, 168, 40, 'Cup of Coffee', 3, 16.00),
(157, 169, 1, 'Sphatlho', 1, 24.00),
(158, 169, 2, 'Pizza', 3, 45.00),
(159, 169, 3, 'Fries', 2, 40.00),
(160, 169, 49, 'Pap & Wors', 2, 45.00),
(161, 170, 50, 'Lays Chips', 6, 50.00),
(162, 171, 5, 'Cool Drink', 1, 18.50),
(163, 171, 40, 'Cup of Coffee', 1, 16.00),
(164, 171, 8, 'Cheese Burger', 2, 40.00),
(165, 172, 47, 'Rooibos', 1, 25.00),
(166, 173, 47, 'Rooibos', 1, 25.00),
(167, 173, 8, 'Cheese Burger', 2, 40.00),
(168, 174, 3, 'Fries', 1, 40.00),
(169, 174, 1, 'Sphatlho', 1, 24.00),
(170, 175, 40, 'Cup of Coffee', 1, 16.00),
(171, 176, 5, 'Cool Drink', 2, 18.50),
(172, 176, 8, 'Cheese Burger', 1, 40.00),
(173, 177, 40, 'Cup of Coffee', 1, 16.00),
(174, 177, 8, 'Cheese Burger', 2, 40.00),
(175, 177, 47, 'Rooibos', 1, 25.00),
(176, 177, 5, 'Cool Drink', 1, 18.50),
(177, 178, 8, 'Cheese Burger', 3, 40.00),
(178, 179, 1, 'Sphatlho', 1, 24.00),
(179, 179, 3, 'Fries', 1, 40.00),
(180, 180, 49, 'Pap & Wors', 3, 45.00),
(181, 181, 47, 'Rooibos', 2, 25.00),
(182, 181, 8, 'Cheese Burger', 1, 40.00),
(183, 181, 5, 'Cool Drink', 1, 18.50),
(184, 182, 49, 'Pap & Wors', 1, 45.00),
(185, 182, 8, 'Cheese Burger', 1, 40.00),
(186, 183, 51, 'Cookies', 1, 34.00),
(187, 183, 8, 'Cheese Burger', 2, 40.00),
(188, 183, 47, 'Rooibos', 1, 25.00),
(189, 184, 51, 'Cookies', 1, 34.00),
(190, 185, 51, 'Cookies', 7, 34.00),
(191, 185, 8, 'Cheese Burger', 2, 40.00),
(192, 185, 47, 'Rooibos', 1, 25.00),
(193, 185, 40, 'Cup of Coffee', 4, 16.00),
(194, 185, 5, 'Cool Drink', 4, 18.50),
(195, 186, 47, 'Rooibos', 2, 25.00),
(196, 186, 8, 'Cheese Burger', 6, 40.00),
(197, 187, 51, 'Cookies', 1, 34.00),
(198, 188, 51, 'Cookies', 1, 34.00),
(199, 188, 8, 'Cheese Burger', 1, 40.00),
(200, 189, 51, 'Cookies', 4, 34.00),
(201, 190, 51, 'Cookies', 5, 34.00),
(202, 191, 5, 'Cool Drink', 2, 18.50),
(203, 192, 51, 'Cookies', 3, 34.00),
(204, 193, 51, 'Cookies', 2, 34.00),
(205, 193, 8, 'Cheese Burger', 2, 40.00),
(206, 194, 1, 'Sphatlho', 1, 24.00),
(207, 194, 3, 'Fries', 1, 40.00),
(208, 195, 8, 'Cheese Burger', 2, 40.00),
(209, 195, 51, 'Cookies', 1, 34.00),
(210, 196, 8, 'Cheese Burger', 3, 40.00),
(211, 196, 51, 'Cookies', 1, 34.00),
(212, 197, 51, 'Cookies', 4, 34.00),
(213, 198, 5, 'Cool Drink', 3, 18.50),
(214, 198, 8, 'Cheese Burger', 3, 40.00),
(215, 199, 47, 'Rooibos', 3, 25.00),
(216, 200, 8, 'Cheese Burger', 3, 40.00),
(217, 201, 40, 'Cup of Coffee', 1, 16.00),
(218, 201, 47, 'Rooibos', 2, 25.00),
(219, 201, 8, 'Cheese Burger', 3, 40.00),
(220, 202, 51, 'Cookies', 1, 34.00),
(221, 202, 8, 'Cheese Burger', 1, 40.00),
(222, 203, 51, 'Cookies', 1, 34.00),
(223, 203, 8, 'Cheese Burger', 1, 40.00),
(224, 204, 47, 'Rooibos', 1, 25.00),
(225, 204, 8, 'Cheese Burger', 2, 40.00),
(226, 205, 40, 'Cup of Coffee', 1, 16.00),
(227, 205, 5, 'Cool Drink', 2, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Other',
  `status` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `stock` int NOT NULL DEFAULT '0'
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `status`, `stock`) VALUES
(1, 'Sphatlho', 'Home made Kota with various ingredients    ', 24.00, 'Sphatlho.jpg', 'Meals', 'Active', 997),
(2, 'Pizza', 'Choose from a wide range of toppings    ', 40.00, '1783984499_Pizza.jpg', 'Meals', 'Active', 960),
(3, 'Fries', 'Large portion of Fries/Chips    ', 40.00, 'Fries.jpg', 'Sides', 'Active', 997),
(5, 'Cool Drink', 'Assorted Cool Soft drink (500ml)', 20.00, 'Cooldrink.jpg', 'Drinks', 'Active', 1107),
(6, 'Chips', 'Assorted potato chips    ', 17.00, 'Chips.jpg', 'Snacks', 'Active', 544),
(8, 'Cheese Burger', 'Delicious cheeseburger with steak patty.', 40.00, '1783984604_Burger.png', 'Meals', 'Active', 961),
(40, 'Cup of Coffee', 'A Hot cup of coffee                                                    ', 16.00, '1783984407_Coffee.jpg', 'Drinks', 'Active', 993),
(47, 'Rooibos', 'Hot                                ', 25.00, '1783984417_Tea.png', 'Drinks', 'Active', 986),
(49, 'Pap & Wors', 'Freshly cooked Pap & Wors Meal.    ', 45.00, '1783985160_Plate.jpg', 'Meals', 'Active', 996),
(50, 'Lays Chips', 'Assorted Lays Chips Bag.', 50.00, '1784229420_Chips2.jpg', 'Snacks', 'Active', 1100),
(51, 'Cookies', 'A bucket of freshly baked cookies (1 Litre Bucket)', 34.00, '1784593858_IMG-20191227-WA0012.jpg', 'Meals', 'Active', 67);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `po_number` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Draft','Pending','Received','Cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  `total` decimal(10,2) DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `supplier_id`, `po_number`, `status`, `total`, `notes`, `created_at`) VALUES
(1, 1, 'PO-20260712005112', 'Cancelled', 0.00, 'onpsndvpouvwsderw', '2026-07-12 00:51:12'),
(2, 2, 'PO-20260712005334', 'Cancelled', 0.00, 'Last batch of materials contained damaged goods - pre-discussed refund requested promptly.', '2026-07-12 00:53:34'),
(3, 1, 'PO-20260712104526', 'Cancelled', 400.00, '', '2026-07-12 10:45:26'),
(4, 11, 'PO-20260712160110', 'Received', 1000.00, 'Need 100 palletes', '2026-07-12 16:01:10'),
(5, 7, 'PO-20260712204658', 'Cancelled', 0.00, '', '2026-07-12 20:46:58'),
(6, 7, 'PO-20260712204659', 'Cancelled', 0.00, '', '2026-07-12 20:46:59'),
(7, 1, 'PO-20260712214649', 'Cancelled', 0.00, '', '2026-07-12 21:46:49'),
(8, 1, 'PO-20260712214650', 'Cancelled', 0.00, '', '2026-07-12 21:46:50'),
(11, 3, 'PO-20260713170942', 'Received', 1600.00, 'Please be sure to check for damaged packs, we have received returns and damaged stock loss behind it. \r\n- Keenan -', '2026-07-13 17:09:42'),
(12, 1, 'PO-20260713174405', 'Cancelled', 100.00, '', '2026-07-13 17:44:05'),
(13, 7, 'PO-20260713174809', 'Cancelled', 500.00, '', '2026-07-13 17:48:09'),
(15, 3, 'PO-20260713174835', 'Cancelled', 1000.00, '', '2026-07-13 17:48:35'),
(16, 11, 'PO-20260713175902', 'Received', 1800.00, 'No garlic please. Prefet the pork palony for filling.', '2026-07-13 17:59:02'),
(17, 1, 'PO-20260715024010', 'Received', 1760.00, '', '2026-07-15 02:40:10'),
(18, 1, 'PO-20260716223130', 'Received', 105000.00, 'Please sure to double check on inventory.', '2026-07-16 22:31:30'),
(19, 11, 'PO-20260716223340', 'Received', 100000.00, '', '2026-07-16 22:33:40'),
(20, 2, 'PO-20260717140436-393', 'Received', 250.00, '', '2026-07-17 14:04:36'),
(21, 7, 'PO-20260720235602-775', 'Received', 2000.00, '', '2026-07-20 23:56:02'),
(22, 1, 'PO-20260721201843-775', 'Received', 3500.00, 'Please include Fanta Orange x 24 (2 liters) in next delivery. Thank you in advance.', '2026-07-21 20:18:43'),
(23, 2, 'PO-20260721204109-850', 'Received', 1250.00, 'Please do not include chocolate chip cookies as customers do not buy those.', '2026-07-21 20:41:09'),
(24, 1, 'PO-20260809231718-708', 'Received', 120.00, '', '2026-08-09 23:17:19');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int NOT NULL,
  `purchase_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `product_id`, `quantity`, `cost_price`, `line_total`) VALUES
(1, 3, 2, 20, 20.00, 400.00),
(2, 4, 8, 10, 100.00, 1000.00),
(3, 11, 1, 50, 12.00, 600.00),
(4, 11, 3, 100, 10.00, 1000.00),
(5, 12, 5, 10, 10.00, 100.00),
(6, 13, 8, 10, 50.00, 500.00),
(9, 16, 2, 120, 15.00, 1800.00),
(17, 17, 5, 20, 18.00, 360.00),
(18, 17, 6, 100, 14.00, 1400.00),
(25, 18, 8, 1000, 35.00, 35000.00),
(26, 18, 40, 1000, 10.00, 10000.00),
(27, 18, 5, 1000, 16.00, 16000.00),
(28, 18, 50, 1000, 25.00, 25000.00),
(29, 18, 47, 1000, 19.00, 19000.00),
(30, 19, 2, 1000, 32.00, 32000.00),
(31, 19, 49, 1000, 30.00, 30000.00),
(32, 19, 1, 1000, 18.00, 18000.00),
(33, 19, 3, 1000, 20.00, 20000.00),
(34, 20, 2, 10, 25.00, 250.00),
(53, 15, 40, 100, 10.00, 1000.00),
(54, 21, 6, 100, 20.00, 2000.00),
(55, 22, 5, 100, 20.00, 2000.00),
(56, 22, 50, 100, 15.00, 1500.00),
(57, 23, 6, 50, 25.00, 1250.00),
(58, 24, 5, 10, 12.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `user_id` int NOT NULL,
  `adjustment_type` enum('Increase','Decrease') COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `available_stock` int NOT NULL,
  `reason` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_adjustments`
--

INSERT INTO `stock_adjustments` (`id`, `product_id`, `user_id`, `adjustment_type`, `quantity`, `available_stock`, `reason`, `notes`, `created_at`) VALUES
(1, 5, 1, 'Decrease', 2, 0, 'Damaged', '', '2026-07-11 16:16:17'),
(2, 47, 1, 'Increase', 10, 0, 'New Delivery', '', '2026-07-11 16:19:17'),
(3, 3, 1, 'Decrease', 300, 200, 'Correction', '', '2026-07-11 17:05:26'),
(4, 1, 1, 'Decrease', 100, 149, 'Expired', '', '2026-07-14 02:57:34'),
(5, 1, 1, 'Decrease', 100, 49, 'Expired', '', '2026-07-14 02:57:34'),
(6, 1, 1, 'Increase', 100, 149, 'New Delivery', 'New Delivery came in, P/O check didn\'t verify this.', '2026-07-14 03:00:30'),
(7, 1, 1, 'Decrease', 9, 140, 'Correction', '', '2026-07-14 03:01:15'),
(8, 2, 1, 'Increase', 120, 290, 'Purchase Order Receipt', 'PO ID: 16', '2026-07-15 02:37:40'),
(9, 5, 1, 'Increase', 20, 187, 'Purchase Order Receipt', 'PO ID: 17', '2026-07-15 02:40:17'),
(10, 6, 1, 'Increase', 100, 394, 'Purchase Order Receipt', 'PO ID: 17', '2026-07-15 02:40:17'),
(11, 1, 1, 'Increase', 50, 183, 'Purchase Order Receipt', 'PO ID: 11', '2026-07-15 02:40:26'),
(12, 3, 1, 'Increase', 100, 283, 'Purchase Order Receipt', 'PO ID: 11', '2026-07-15 02:40:26'),
(13, 8, 1, 'Increase', 10, 43, 'Purchase Order Receipt', 'PO ID: 4', '2026-07-15 13:42:10'),
(14, 1, 1, 'Decrease', 1, 0, 'Order Collected', 'Order: SW294401', '2026-07-16 22:10:08'),
(15, 2, 1, 'Decrease', 3, 0, 'Order Collected', 'Order: SW294401', '2026-07-16 22:10:08'),
(16, 3, 1, 'Decrease', 2, 0, 'Order Collected', 'Order: SW294401', '2026-07-16 22:10:08'),
(17, 49, 1, 'Decrease', 2, 0, 'Order Collected', 'Order: SW294401', '2026-07-16 22:10:08'),
(18, 47, 1, 'Decrease', 1, 0, 'Order Collected', 'Order: SW609173', '2026-07-16 22:28:35'),
(19, 8, 1, 'Increase', 1000, 1002, 'Purchase Order Receipt', 'PO ID: 18', '2026-07-16 22:31:42'),
(20, 40, 1, 'Increase', 1000, 1001, 'Purchase Order Receipt', 'PO ID: 18', '2026-07-16 22:31:42'),
(21, 5, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 18', '2026-07-16 22:31:42'),
(22, 50, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 18', '2026-07-16 22:31:42'),
(23, 47, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 18', '2026-07-16 22:31:42'),
(24, 2, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 19', '2026-07-16 22:33:44'),
(25, 49, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 19', '2026-07-16 22:33:44'),
(26, 1, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 19', '2026-07-16 22:33:44'),
(27, 3, 1, 'Increase', 1000, 1000, 'Purchase Order Receipt', 'PO ID: 19', '2026-07-16 22:33:44'),
(28, 3, 1, 'Decrease', 1, 999, 'Order Collected', 'Order: SW168992', '2026-07-17 11:48:08'),
(29, 1, 1, 'Decrease', 1, 999, 'Order Collected', 'Order: SW168992', '2026-07-17 11:48:08'),
(30, 47, 1, 'Decrease', 1, 999, 'Order Collected', 'Order: SW227824', '2026-07-17 11:48:23'),
(31, 8, 1, 'Decrease', 2, 1000, 'Order Collected', 'Order: SW227824', '2026-07-17 11:48:23'),
(32, 40, 4, 'Decrease', 1, 1000, 'Order Collected', 'Order: SW410331', '2026-07-17 11:55:43'),
(33, 5, 1, 'Decrease', 2, 998, 'Order Collected', 'Order: SW591615', '2026-07-17 13:18:37'),
(34, 8, 1, 'Decrease', 1, 999, 'Order Collected', 'Order: SW591615', '2026-07-17 13:18:37'),
(35, 40, 1, 'Decrease', 1, 999, 'Order Collected', 'Order: SW477586', '2026-07-17 13:24:11'),
(36, 8, 1, 'Decrease', 2, 997, 'Order Collected', 'Order: SW477586', '2026-07-17 13:24:11'),
(37, 47, 1, 'Decrease', 1, 998, 'Order Collected', 'Order: SW477586', '2026-07-17 13:24:11'),
(38, 5, 1, 'Decrease', 1, 997, 'Order Collected', 'Order: SW477586', '2026-07-17 13:24:12'),
(39, 8, 1, 'Decrease', 3, 994, 'Order Collected', 'Order: SW587232', '2026-07-17 14:01:47'),
(40, 2, 1, 'Increase', 10, 1010, 'Purchase Order Receipt', 'PO ID: 20', '2026-07-17 14:04:41'),
(41, 1, 1, 'Decrease', 1, 998, 'Order Collected', 'Order: SW612365', '2026-07-17 14:44:11'),
(42, 3, 1, 'Decrease', 1, 998, 'Order Collected', 'Order: SW612365', '2026-07-17 14:44:11'),
(43, 51, 1, 'Decrease', 1, 3, 'Order Collected', 'Order: SW665440', '2026-07-19 18:54:16'),
(44, 6, 1, 'Increase', 100, 494, 'Purchase Order Receipt', 'PO ID: 21', '2026-07-20 23:56:10'),
(45, 5, 1, 'Increase', 100, 1097, 'Purchase Order Receipt', 'PO ID: 22', '2026-07-21 20:18:52'),
(46, 50, 1, 'Increase', 100, 1100, 'Purchase Order Receipt', 'PO ID: 22', '2026-07-21 20:18:52'),
(53, 6, 1, 'Increase', 50, 544, 'Purchase Order Receipt', 'PO ID: 23', '2026-07-21 20:56:13'),
(54, 51, 1, 'Decrease', 1, 99, 'Order Collected', 'Order: SW855661', '2026-07-22 20:18:23'),
(55, 8, 1, 'Decrease', 2, 992, 'Order Collected', 'Order: SW855661', '2026-07-22 20:18:23'),
(56, 47, 1, 'Decrease', 1, 997, 'Order Collected', 'Order: SW855661', '2026-07-22 20:18:23'),
(57, 51, 1, 'Decrease', 1, 98, 'Order Collected', 'Order: SW665440', '2026-07-22 20:18:26'),
(58, 51, 1, 'Decrease', 7, 91, 'Order Collected', 'Order: SW505608', '2026-07-22 20:18:40'),
(59, 8, 1, 'Decrease', 2, 990, 'Order Collected', 'Order: SW505608', '2026-07-22 20:18:40'),
(60, 47, 1, 'Decrease', 1, 996, 'Order Collected', 'Order: SW505608', '2026-07-22 20:18:40'),
(61, 40, 1, 'Decrease', 4, 995, 'Order Collected', 'Order: SW505608', '2026-07-22 20:18:40'),
(62, 5, 1, 'Decrease', 4, 1093, 'Order Collected', 'Order: SW505608', '2026-07-22 20:18:40'),
(98, 51, 1, 'Decrease', 1, 90, 'Order Collected', 'Order: SW212679', '2026-07-24 19:38:34'),
(99, 51, 1, 'Decrease', 1, 89, 'Order Collected', 'Order: SW888776', '2026-07-24 19:39:02'),
(100, 8, 1, 'Decrease', 1, 989, 'Order Collected', 'Order: SW888776', '2026-07-24 19:39:02'),
(101, 49, 1, 'Decrease', 3, 997, 'Order Collected', 'Order: SW994333', '2026-07-24 19:39:08'),
(102, 51, 1, 'Decrease', 4, 85, 'Order Collected', 'Order: SW807185', '2026-07-24 19:48:09'),
(103, 51, 1, 'Decrease', 5, 80, 'Order Collected', 'Order: SW900231', '2026-07-24 19:49:14'),
(104, 51, 1, 'Decrease', 3, 77, 'Order Collected', 'Order: SW640929', '2026-07-24 19:55:09'),
(105, 47, 1, 'Decrease', 2, 994, 'Order Collected', 'Order: SW234237', '2026-07-24 19:55:53'),
(106, 8, 1, 'Decrease', 6, 983, 'Order Collected', 'Order: SW234237', '2026-07-24 19:55:53'),
(107, 51, 1, 'Decrease', 2, 75, 'Order Collected', 'Order: SW192221', '2026-07-24 22:29:54'),
(108, 8, 1, 'Decrease', 2, 981, 'Order Collected', 'Order: SW192221', '2026-07-24 22:29:54'),
(109, 1, 1, 'Decrease', 1, 997, 'Order Collected', 'Order: SW788609', '2026-07-25 20:35:28'),
(110, 3, 1, 'Decrease', 1, 997, 'Order Collected', 'Order: SW788609', '2026-07-25 20:35:28'),
(111, 8, 1, 'Decrease', 2, 979, 'Order Collected', 'Order: SW241696', '2026-07-25 20:42:50'),
(112, 51, 1, 'Decrease', 1, 74, 'Order Collected', 'Order: SW241696', '2026-07-25 20:42:50'),
(113, 51, 1, 'Decrease', 4, 70, 'Order Collected', 'Order: SW758205', '2026-08-08 18:48:02'),
(114, 8, 1, 'Decrease', 3, 976, 'Order Collected', 'Order: SW586971', '2026-08-08 18:48:07'),
(115, 51, 1, 'Decrease', 1, 69, 'Order Collected', 'Order: SW586971', '2026-08-08 18:48:07'),
(116, 5, 1, 'Decrease', 3, 1090, 'Order Collected', 'Order: SW640361', '2026-08-08 18:48:56'),
(117, 8, 1, 'Decrease', 3, 973, 'Order Collected', 'Order: SW640361', '2026-08-08 18:48:56'),
(118, 8, 1, 'Decrease', 3, 970, 'Order Collected', 'Order: SW328444', '2026-08-08 18:52:51'),
(119, 47, 1, 'Decrease', 3, 991, 'Order Collected', 'Order: SW461957', '2026-08-08 18:52:52'),
(120, 49, 1, 'Decrease', 1, 996, 'Order Collected', 'Order: SW841365', '2026-08-09 11:20:50'),
(121, 8, 1, 'Decrease', 1, 969, 'Order Collected', 'Order: SW841365', '2026-08-09 11:20:50'),
(122, 47, 1, 'Decrease', 2, 989, 'Order Collected', 'Order: SW715609', '2026-08-09 11:26:41'),
(123, 8, 1, 'Decrease', 1, 968, 'Order Collected', 'Order: SW715609', '2026-08-09 11:26:41'),
(124, 5, 1, 'Decrease', 1, 1089, 'Order Collected', 'Order: SW715609', '2026-08-09 11:26:41'),
(125, 5, 1, 'Increase', 10, 1099, 'Purchase Order Receipt', 'PO ID: 24', '2026-08-09 23:17:27'),
(126, 51, 1, 'Decrease', 1, 68, 'Order Collected', 'Order: SW787681', '2026-08-12 23:37:27'),
(127, 8, 1, 'Decrease', 1, 967, 'Order Collected', 'Order: SW787681', '2026-08-12 23:37:27'),
(128, 51, 1, 'Decrease', 1, 67, 'Order Collected', 'Order: SW119586', '2026-08-12 23:37:46'),
(129, 8, 1, 'Decrease', 1, 966, 'Order Collected', 'Order: SW119586', '2026-08-12 23:37:46'),
(130, 40, 1, 'Decrease', 1, 994, 'Order Collected', 'Order: SW837759', '2026-08-12 23:37:55'),
(131, 47, 1, 'Decrease', 2, 987, 'Order Collected', 'Order: SW837759', '2026-08-12 23:37:55'),
(132, 8, 1, 'Decrease', 3, 963, 'Order Collected', 'Order: SW837759', '2026-08-12 23:37:55'),
(133, 5, 1, 'Increase', 10, 1109, 'Damaged', '', '2026-08-13 22:19:06'),
(134, 2, 1, 'Decrease', 50, 960, 'Expired', '', '2026-08-15 11:27:51'),
(145, 47, 1, 'Decrease', 1, 986, 'Order Collected', 'Order: SW763694', '2026-08-15 19:10:31'),
(146, 8, 1, 'Decrease', 2, 961, 'Order Collected', 'Order: SW763694', '2026-08-15 19:10:31'),
(147, 40, 1, 'Decrease', 1, 993, 'Order Collected', 'Order: SW323880', '2026-08-15 19:11:21'),
(148, 5, 1, 'Decrease', 2, 1107, 'Order Collected', 'Order: SW323880', '2026-08-15 19:11:21');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `company_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_person` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_name`, `contact_person`, `phone`, `email`, `address`, `notes`, `status`, `created_at`) VALUES
(1, 'Coca-Cola Beaverages SA', 'John Cena', '0139874001', 'john@ccbsa.co.za', 'Pretoria, Rosslyn', 'Soft Driknks Supplier', 'Active', '2026-07-11 21:16:19'),
(2, 'Tiger Brands SA', 'Maria Venter', '0891043362', 'mventer@tigerbsa.co.za', 'Johannesburg, Marshalltown', 'Food Supplier', 'Active', '2026-07-11 21:16:19'),
(3, 'Grekor Logistics International', 'Andrew Jackson Jr', '0749874001', 'jandrew250@grekor.com', 'Pretoria North, Amandasig', 'Frozen Products', 'Active', '2026-07-11 21:16:19'),
(7, 'Maggie\'s Nail Bar', 'Mmabatho Margaret Moagi', '0764391791', 'moagimmabatho0@gmail.com', '1068 Khululeka street, Soshanguve', '', 'Active', '2026-07-11 23:44:57'),
(11, 'Khumo Construction', 'Khumo Moagi', '0746580971', 'khumom1@outlook.com', 'Bosman, Pretoria', 'Call supplier, ask for Angie.', 'Active', '2026-07-12 16:00:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('Admin','Manager','Cashier','Kitchen') COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `profile_image`, `role`, `status`, `created_at`) VALUES
(1, 'Administrator', 'admin', '$2y$10$QE0AwwuA82PYZLm1/vM3NOWpob2g2BVH6E/b3vLKqIFj9PIsDLerK', 'user_6a63da01bbffc3.18590857.jpg', 'Admin', 'Active', '2026-07-07 21:55:29'),
(3, 'Khumo Keanan Moagi', 'khumo', '$2y$10$v3BjFA.5XbfwdRPHB4NVUevNhaw7wskjslp5nFrv9Kd6UOFmSQzvS', 'user_6a5e63a62d6151.70718113.jpg', 'Cashier', 'Active', '2026-07-08 20:52:14'),
(4, 'Brian Mahlangu', 'brian', '$2y$10$TKIIMndsxYnp8I/wDUp2xus.jzeSr9VRA9fMhrC5XsR9.gHHJO/06', 'user_6a5f5bcc460156.25106992.jpg', 'Manager', 'Active', '2026-07-09 02:28:28'),
(5, 'Keenan', 'keenan', '$2y$10$gy42VI/sruOPiOfKuUN/xOrvztCpYRzWHNLV9d6JWx6x0a9cV8Slm', 'user_6a5e5234a6e4e4.40993251.jpg', 'Kitchen', 'Active', '2026-07-09 02:35:16'),
(11, 'Vusi Sithole', 'vusi', '$2y$10$wiH/R.UYOk8mCjZUl5jHSupcr2QXkJwk/sOLCTuUrL8fFgiyoZZXm', 'user_6a651f1a5a75b6.03174657.jpg', 'Kitchen', 'Active', '2026-07-25 20:39:54');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `login_at` datetime NOT NULL,
  `last_activity_at` datetime NOT NULL,
  `logout_at` datetime DEFAULT NULL,
  `status` enum('ACTIVE','LOGGED_OUT','TIMED_OUT','TERMINATED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `login_at`, `last_activity_at`, `logout_at`, `status`) VALUES
(1, 1, '2026-08-14 19:36:11', '2026-08-14 19:36:11', '2026-08-15 16:49:05', 'TIMED_OUT'),
(2, 1, '2026-08-14 20:41:54', '2026-08-14 21:03:40', '2026-08-15 16:49:05', 'TIMED_OUT'),
(3, 1, '2026-08-14 21:03:48', '2026-08-14 21:03:51', '2026-08-15 16:49:05', 'TIMED_OUT'),
(4, 1, '2026-08-14 21:28:26', '2026-08-14 21:28:27', '2026-08-15 16:49:05', 'TIMED_OUT'),
(5, 1, '2026-08-14 21:29:27', '2026-08-14 21:29:27', '2026-08-15 16:49:05', 'TIMED_OUT'),
(6, 1, '2026-08-14 21:34:33', '2026-08-14 21:34:38', '2026-08-15 16:49:05', 'TIMED_OUT'),
(7, 1, '2026-08-14 21:51:43', '2026-08-14 21:51:44', '2026-08-15 16:49:05', 'TIMED_OUT'),
(8, 3, '2026-08-14 21:55:09', '2026-08-14 21:55:09', '2026-08-15 16:49:05', 'TIMED_OUT'),
(9, 1, '2026-08-14 21:55:51', '2026-08-14 21:55:51', '2026-08-15 16:49:05', 'TIMED_OUT'),
(10, 1, '2026-08-14 21:59:00', '2026-08-15 13:29:38', '2026-08-15 13:29:38', 'TERMINATED'),
(11, 1, '2026-08-14 22:09:40', '2026-08-14 22:09:55', '2026-08-14 22:09:55', 'TIMED_OUT'),
(12, 1, '2026-08-14 22:28:04', '2026-08-14 22:28:08', '2026-08-14 22:28:08', 'LOGGED_OUT'),
(13, 1, '2026-08-14 22:33:31', '2026-08-14 23:13:25', '2026-08-14 23:13:25', 'TIMED_OUT'),
(14, 1, '2026-08-14 23:13:32', '2026-08-15 01:53:27', '2026-08-15 01:53:27', 'TERMINATED'),
(15, 3, '2026-08-14 23:14:13', '2026-08-15 01:18:36', '2026-08-15 01:18:36', 'TIMED_OUT'),
(16, 1, '2026-08-14 23:16:09', '2026-08-14 23:37:49', '2026-08-14 23:37:49', 'LOGGED_OUT'),
(17, 1, '2026-08-15 01:19:16', '2026-08-15 02:13:31', '2026-08-15 02:13:31', 'LOGGED_OUT'),
(18, 1, '2026-08-15 11:51:32', '2026-08-15 12:56:23', '2026-08-15 12:56:23', 'TIMED_OUT'),
(19, 1, '2026-08-15 12:56:42', '2026-08-15 16:40:11', '2026-08-15 16:40:11', 'TIMED_OUT'),
(20, 1, '2026-08-15 16:40:48', '2026-08-15 20:50:39', '2026-08-15 20:50:39', 'TIMED_OUT'),
(21, 1, '2026-08-15 21:10:28', '2026-08-15 21:57:12', '2026-08-15 21:57:12', 'TIMED_OUT'),
(22, 1, '2026-08-15 23:11:58', '2026-08-16 00:38:11', '2026-08-16 16:19:47', 'TIMED_OUT'),
(23, 1, '2026-08-16 16:19:37', '2026-08-16 17:28:21', '2026-08-16 17:28:21', 'TIMED_OUT'),
(24, 1, '2026-08-17 01:56:35', '2026-08-17 01:56:42', '2026-08-17 01:56:42', 'LOGGED_OUT'),
(25, 1, '2026-08-17 01:56:48', '2026-08-17 04:19:01', '2026-08-17 04:19:01', 'TIMED_OUT'),
(26, 1, '2026-08-23 03:20:28', '2026-08-23 03:20:29', '2026-08-23 13:38:21', 'TIMED_OUT'),
(27, 1, '2026-08-23 13:11:08', '2026-08-23 13:46:27', '2026-08-23 13:46:27', 'TIMED_OUT'),
(28, 1, '2026-08-23 13:48:35', '2026-08-23 14:11:25', NULL, 'ACTIVE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `audit_changes`
--
ALTER TABLE `audit_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_changes_audit_id` (`audit_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_log_user_id` (`user_id`),
  ADD KEY `idx_audit_log_entity` (`entity`),
  ADD KEY `idx_audit_log_entity_id` (`entity_id`),
  ADD KEY `idx_audit_log_created_at` (`created_at`);

--
-- Indexes for table `business_settings`
--
ALTER TABLE `business_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_goods_received_notes_grn_number` (`grn_number`),
  ADD UNIQUE KEY `uq_goods_received_notes_purchase_order` (`purchase_order_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `received_by` (`received_by`);

--
-- Indexes for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `login_rate_limits`
--
ALTER TABLE `login_rate_limits`
  ADD PRIMARY KEY (`username_hash`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD UNIQUE KEY `uq_orders_invoice_number` (`invoice_number`),
  ADD UNIQUE KEY `uq_orders_request_id` (`request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_status_created` (`status`,`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_order_items_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_stock` (`status`,`stock`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_purchase_order_product` (`purchase_order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_product_created` (`product_id`,`created_at`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_sessions_user_id` (`user_id`),
  ADD KEY `idx_user_sessions_status` (`status`),
  ADD KEY `idx_user_sessions_last_activity` (`last_activity_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `audit_changes`
--
ALTER TABLE `audit_changes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `business_settings`
--
ALTER TABLE `business_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `audit_changes`
--
ALTER TABLE `audit_changes`
  ADD CONSTRAINT `fk_audit_changes_audit` FOREIGN KEY (`audit_id`) REFERENCES `audit_log` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_audit_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD CONSTRAINT `goods_received_notes_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `goods_received_notes_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `goods_received_notes_ibfk_3` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `goods_received_note_items`
--
ALTER TABLE `goods_received_note_items`
  ADD CONSTRAINT `goods_received_note_items_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goods_received_note_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `stock_adjustments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk_user_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
