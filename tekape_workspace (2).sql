-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2026 at 02:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tekape_workspace`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$examplehashedpassword', 'admin@tekape.space', '2025-11-16 02:57:45'),
(3, 'bahlil', 'admin025', '', '2025-11-16 04:35:42'),
(7, 'rendra', 'bahlilganteng', 'bahlilasw@gmail.com1', '2025-12-03 03:24:27');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `date` date NOT NULL,
  `status` enum('held','pending','paid','cancelled','failed') DEFAULT 'pending',
  `payment_type` varchar(50) DEFAULT NULL,
  `total_price` double NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `gateway_order_id` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `name`, `email`, `phone`, `date`, `status`, `payment_type`, `total_price`, `created_at`, `expires_at`, `gateway_order_id`, `updated_at`) VALUES
(46, 1, 'Negro', 'Negro@gmail.com', '12312323123213', '2025-12-08', 'paid', 'bank_transfer', 211460, '2025-12-07 07:42:57', '2025-12-07 08:57:57', 'PAY-46-1765093379', '2025-12-07 07:43:07'),
(47, 1, 'Negroes', 'Negro@gmail.com', '12312323123213', '2025-12-11', 'paid', 'bank_transfer', 142640, '2025-12-10 04:11:56', '2025-12-10 05:26:56', 'PAY-47-1765339918', '2025-12-10 04:12:10'),
(48, 1, 'Igor', 'enroasdkal@gmadalddaw.com', '23123123123', '2025-12-11', 'paid', 'bank_transfer', 142640, '2025-12-10 04:44:55', '2025-12-10 05:59:55', 'PAY-48-1765341900', '2025-12-10 04:45:28'),
(49, 1, 'Igor', 'enroasdkal@gmadalddaw.com', '23123123123', '2025-12-11', 'paid', 'bank_transfer', 142640, '2025-12-10 05:29:13', '2025-12-10 06:44:13', 'PAY-49-1765344560', '2025-12-10 05:29:34'),
(50, 1, 'Igor', 'enroasdkal@gmadalddaw.com', '12312323123213', '2025-12-18', '', 'qris', 142640, '2025-12-17 04:27:22', '2025-12-17 05:42:22', 'PAY-50-1765945651', '2025-12-17 04:43:36'),
(51, 1, 'Igor', 'enroasdkal@gmadalddaw.com', '12312323123213', '2025-12-18', 'paid', 'bank_transfer', 142640, '2025-12-17 04:27:53', '2025-12-17 05:42:53', 'PAY-51-1765945677', '2025-12-17 04:28:22'),
(52, 1, 'Negroes', 'akbar.dwi01@upi.edu', '12312312312312', '2025-12-18', 'cancelled', 'bank_transfer', 211460, '2025-12-17 04:29:12', '2025-12-17 05:44:12', 'PAY-52-1765945754', '2025-12-17 04:37:45'),
(53, 1, 'Igor', 'akbar.dwi01@upi.edu', '12312323123213', '2025-12-18', 'cancelled', 'bank_transfer', 349100, '2025-12-17 04:38:11', '2025-12-17 05:53:11', 'PAY-53-1765946294', '2025-12-17 04:48:06'),
(54, 1, 'Negroes', 'akbar.dwi01@upi.edu', '12312323123213', '2025-12-18', 'paid', 'bank_transfer', 280280, '2025-12-17 04:48:34', '2025-12-17 06:03:34', 'PAY-54-1765946917', '2025-12-17 04:48:47'),
(55, 1, 'Harta Dinata', 'Negro@gmail.com', '12312323123213', '2026-01-06', 'cancelled', 'qris', 211460, '2026-01-05 16:27:02', '2026-01-05 17:42:02', 'PAY-55-1767630428', '2026-01-05 16:28:12'),
(56, 1, 'Roses', 'akbar.dwi01@upi.edu', '12312323123213', '2026-01-06', 'paid', 'bank_transfer', 280280, '2026-01-05 16:29:22', '2026-01-05 17:44:22', 'PAY-56-1767630564', '2026-01-05 16:29:36'),
(57, 1, 'zil', 'zil@gmail.com', '0823923', '2026-01-06', 'held', NULL, 142640, '2026-01-05 16:36:48', '2026-01-05 17:51:48', NULL, '2026-01-05 16:36:48'),
(58, 1, 'Z', 'zil@gamil.com', '09823', '2025-12-08', 'held', NULL, 73820, '2026-01-06 13:22:52', '2026-01-06 14:37:52', NULL, '2026-01-06 13:22:52'),
(59, 1, 'asdca', 'nama@gmail.com', '0813212', '2025-12-08', 'cancelled', 'echannel', 142640, '2026-01-06 13:39:01', '2026-01-06 14:54:01', 'PAY-59-1767706792', '2026-01-06 13:40:08'),
(60, 1, 'asdca', 'nama@gmail.com', '0813212', '2025-12-08', 'cancelled', 'echannel', 142640, '2026-01-06 13:49:35', '2026-01-06 15:04:35', 'PAY-60-1767707379', '2026-01-06 14:18:10');

-- --------------------------------------------------------

--
-- Table structure for table `booking_hours`
--

CREATE TABLE `booking_hours` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `hour` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_hours`
--

INSERT INTO `booking_hours` (`id`, `booking_id`, `hour`) VALUES
(109, 46, '10:00'),
(110, 46, '11:00'),
(111, 46, '12:00'),
(112, 47, '16:00'),
(113, 47, '17:00'),
(114, 48, '10:00'),
(115, 48, '11:00'),
(116, 49, '12:00'),
(117, 49, '13:00'),
(118, 50, '11:00'),
(119, 50, '12:00'),
(120, 51, '11:00'),
(121, 51, '12:00'),
(122, 52, '15:00'),
(123, 52, '16:00'),
(124, 52, '17:00'),
(125, 53, '13:00'),
(126, 53, '14:00'),
(127, 53, '15:00'),
(128, 53, '16:00'),
(129, 53, '17:00'),
(130, 54, '14:00'),
(131, 54, '15:00'),
(132, 54, '16:00'),
(133, 54, '17:00'),
(134, 55, '09:00'),
(135, 55, '10:00'),
(136, 55, '11:00'),
(137, 56, '09:00'),
(138, 56, '10:00'),
(139, 56, '11:00'),
(140, 56, '12:00'),
(141, 57, '15:00'),
(142, 57, '16:00'),
(143, 58, '13:00'),
(144, 59, '08:00'),
(145, 59, '09:00'),
(146, 60, '08:00'),
(147, 60, '09:00');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `alamat` varchar(2000) NOT NULL,
  `kapasitas` varchar(10) NOT NULL,
  `ukuran` varchar(100) NOT NULL,
  `harga_per_jam` varchar(100) NOT NULL,
  `price` double NOT NULL,
  `imageloc` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `nama_ruangan`, `alamat`, `kapasitas`, `ukuran`, `harga_per_jam`, `price`, `imageloc`) VALUES
(1, 'Meeting Room Lodaya', 'Jl.Lodaya, Bandung', '6 Pax', '24 m²', '62.000', 62000, 'lokasi1.webp'),
(2, 'Room Meeting Sunda', 'Jl.Sunda, Bandung.', '12 Pax', '32 m²', '87.000', 87000, 'lokasi2.webp'),
(3, 'Room Meeting Katamso', 'Jl.Katamso, Bandung', '40 Pax', '50 m²', '200.000', 200000, 'lokasi3.webp');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `telepon`, `alamat`, `created_at`) VALUES
(1, 'Ah siapa', 'ah_siapa@gmail.com', '$2y$10$examplehashedpassword', '08123456789', 'Jl. Contoh No. 1', '2025-11-16 02:57:45'),
(2, 'luqman', 'luqman@gmail.com', '$2y$10$13Lsu7SyL7IjtySF4Sq/5OXEtswyRawMrjlYlVvoFDKqJZDRNWjwa', '02225568', 'bandung', '2025-11-16 03:24:33'),
(3, 'ahmad', 'ahmad@gmail.com', '$2y$10$YrGVjMyEkeyIzAI/PvAfEevyVviHu0EgXIQEQJrxIzTaKEBmABduq', '081934', 'bandung', '2025-12-03 03:02:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `gateway_order_id` (`gateway_order_id`);

--
-- Indexes for table `booking_hours`
--
ALTER TABLE `booking_hours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `booking_hours`
--
ALTER TABLE `booking_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
