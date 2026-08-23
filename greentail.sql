-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2026 at 12:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `greentail`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_image` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_image`) VALUES
(7, 'เสื้อผ้า', 'https://contents.mediadecathlon.com/p2887539/k$62889a01966582b0c4af9bb8da9445fc/%E0%B9%80%E0%B8%AA%E0%B8%B7%E0%B9%89%E0%B8%AD%E0%B9%82%E0%B8%95%E0%B9%89%E0%B8%84%E0%B8%A5%E0%B8%B7%E0%B9%88%E0%B8%99%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%A2%E0%B8%B9%E0%B8%A7%E0%B8%B5-%E0%B9%81%E0%B8%82%E0%B8%99%E0%B8%A2%E0%B8%B2%E0%B8%A7%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B8%9C%E0%B8%B9%E0%B9%89%E0%B8%8A%E0%B8%B2%E0%B8%A2-%E0%B8%AA%E0%B8%B5%E0%B8%82%E0%B8%B2%E0%B8%A7-%E0%B8%A5%E0%B8%B2%E0%B8%A2%E0'),
(8, 'เต็นท์', 'https://contents.mediadecathlon.com/p2579082/k$684efb64d5468fb773577cdf1cb57a08/%E0%B9%80%E0%B8%95%E0%B9%87%E0%B8%99%E0%B8%97%E0%B9%8C%E0%B8%95%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B9%81%E0%B8%84%E0%B8%A1%E0%B8%9B%E0%B9%8C%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A-%E0%B8%84%E0%B8%99-8513471.jpg?f=768x0&format=auto'),
(9, 'หมวก', 'https://contents.mediadecathlon.com/p2422658/sq/k$75697bbe95a8911c52343f6277ff6610/%E0%B8%AB%E0%B8%A1%E0%B8%A7%E0%B8%81%E0%B9%80%E0%B8%97%E0%B8%A3%E0%B8%84%E0%B8%81%E0%B8%B4%E0%B9%89%E0%B8%87%E0%B8%9B%E0%B9%89%E0%B8%AD%E0%B8%87%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%A3%E0%B8%B1%E0%B8%87%E0%B8%AA%E0%B8%B5%E0%B8%A2%E0%B8%B9%E0%B8%A7%E0%B8%B5%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B8%99%E0%B9%89%E0%B8%B3%E0%B8%95%E0%B8%B2%E0%B8%A5-8788741.jpg?f=480x480&format=auto'),
(11, 'กระเป๋า', 'https://contents.mediadecathlon.com/p2951811/sq/k$6aed8cdacc35504d0232494cb40b3044/%E0%B9%80%E0%B8%9B%E0%B9%89%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B9%80%E0%B8%97%E0%B8%A3%E0%B8%84%E0%B8%81%E0%B8%B4%E0%B9%89%E0%B8%87%E0%B8%82%E0%B8%99%E0%B8%B2%E0%B8%94-%E0%B8%A5%E0%B8%B4%E0%B8%95%E0%B8%A3%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-8735043.jpg?f=480x480&format=auto'),
(12, 'รองเท้า', 'https://contents.mediadecathlon.com/p2457687/sq/k$6d4b0725f7b994e0472f0ce96cecb2aa/%E0%B8%A3%E0%B8%AD%E0%B8%87%E0%B9%80%E0%B8%97%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2-%E0%B8%82%E0%B9%89%E0%B8%AD%E0%B8%81%E0%B8%A5%E0%B8%B2%E0%B8%87-%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B8%9C%E0%B8%B9%E0%B9%89%E0%B8%8A%E0%B8%B2%E0%B8%A2-%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B8%94%E0%B8%B3-8756701.jpg?f=480x480&format=auto'),
(15, 'ไม้เท้าเดินป่า', 'https://www.petenpaul.com/wp-content/uploads/2017/12/Naturehike-Camping-Trekking-Poles-Nordic-Walking-Stick-Ultralight-Climbing-Pole-Hiking-Sticks-Carbon-Cane-Walking-Stick.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `fullname`, `address`, `phone`, `payment_method`, `total_price`, `status`, `created_at`) VALUES
(4, 4, 'prae', 'หอพักเรือนเพ็ญ 803 มหาสารคาม', '0654799814', 'เก็บเงินปลายทาง', 1900.00, '', '2026-08-16 22:11:29'),
(5, 4, 'prae', 'หอพักเรือนเพ็ญ 803 มหาสารคาม', '0654799814', 'โอนเงินผ่านธนาคาร', 180.00, 'pending', '2026-08-22 12:33:02'),
(6, 4, 'prae', 'หอพักเรือนเพ็ญ 803 มหาสารคาม', '0654799814', 'โอนเงินผ่านธนาคาร', 1850.00, 'pending', '2026-08-23 08:38:44');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `color`, `quantity`, `price`) VALUES
(1, 4, 10, '', 1, 1900.00),
(2, 5, 9, '', 1, 180.00),
(3, 6, 6, '', 1, 1850.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `detail_image` varchar(500) DEFAULT NULL,
  `colors` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `name`, `description`, `price`, `stock`, `image`, `detail_image`, `colors`) VALUES
(3, 15, 'ไม้เท้าเดินป่า', 'ไม้เท้าเดินป่าราคาย่อมเยารุ่น MT100 (สีฟ้า)', 220.00, 10, 'https://contents.mediadecathlon.com/p2439600/k$7c045ba875f008bd2de09cbcd2d67f40/%E0%B9%84%E0%B8%A1%E0%B9%89%E0%B9%80%E0%B8%97%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B8%A3%E0%B8%B2%E0%B8%84%E0%B8%B2%E0%B8%A2%E0%', 'https://contents.mediadecathlon.com/p2439599/k$f9096b8009a85dda4d790dcf18793fcc/%E0%B9%84%E0%B8%A1%E0%B9%89%E0%B9%80%E0%B8%97%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B8%A3%E0%B8%B2%E0%B8%84%E0%B8%B2%E0%B8%A2%E0%B9%88%E0%B8%AD%E0%B8%A1%E0%B9%80%E0%B8%A2%E0%B8%B2%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B8%9F%E0%B9%89%E0%B8%B2-8807204.jpg?f=768x0&format=auto', ''),
(5, 9, 'หมวกเดินป่า', 'หมวกเดินป่ารุ่น 500 M (สีเทา)', 340.00, 10, 'https://contents.mediadecathlon.com/p3061026/k$764bbb15313df3f8ca2d18fbc9450591/%E0%B8%AB%E0%B8%A1%E0%B8%A7%E0%B8%81%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B9%80%E0%B8%97%E0', 'https://contents.mediadecathlon.com/p3061069/k$2ede54f0a0c26f7edbad8603abf4a009/%E0%B8%AB%E0%B8%A1%E0%B8%A7%E0%B8%81%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B9%80%E0%B8%97%E0%B8%B2-8931360.jpg?f=1920x0&format=auto', ''),
(6, 12, 'รองเท้าเดินป่า', 'รองเท้าเดินป่ากันน้ำข้อกลางสำหรับผู้หญิงรุ่น MH100 (สีเบจ)', 1850.00, 10, 'https://contents.mediadecathlon.com/p3141112/k$80962075786ad6696b5b1010cf6ab1fa/%E0%B8%A3%E0%B8%AD%E0%B8%87%E0%B9%80%E0%B8%97%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%99%E0%B9%89%E0%', 'https://contents.mediadecathlon.com/p3141113/k$5be5e0f0a4abc7482ec6fab0629ca4f7/%E0%B8%A3%E0%B8%AD%E0%B8%87%E0%B9%80%E0%B8%97%E0%B9%89%E0%B8%B2%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2%E0%B8%81%E0%B8%B1%E0%B8%99%E0%B8%99%E0%B9%89%E0%B8%B3%E0%B8%82%E0%B9%89%E0%B8%AD%E0%B8%81%E0%B8%A5%E0%B8%B2%E0%B8%87%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B8%9C%E0%B8%B9%E0%B9%89%E0%B8%AB%E0%B8%8D%E0%B8%B4%E0%B8%87%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B', ''),
(8, 11, 'กระเป๋าเป้เดินป่า', 'กระเป๋าเป้เดินป่า ขนาด 38 ลิตร รุ่น MH500 (สีน้ำตาล)', 2220.00, 10, 'https://contents.mediadecathlon.com/p2861730/k$964c2d4d9cb77384422644f04c9c020e/%E0%B8%81%E0%B8%A3%E0%B8%B0%E0%B9%80%E0%B8%9B%E0%B9%8B%E0%B8%B2-%E0%B9%80%E0%B8%9B%E0%B9%89-%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2-%E0%B8%82%E0%B8%99%', 'https://contents.mediadecathlon.com/p2861728/k$ebbd05a469b5c19fc1194ddbbafd565f/%E0%B8%81%E0%B8%A3%E0%B8%B0%E0%B9%80%E0%B8%9B%E0%B9%8B%E0%B8%B2-%E0%B9%80%E0%B8%9B%E0%B9%89-%E0%B9%80%E0%B8%94%E0%B8%B4%E0%B8%99%E0%B8%9B%E0%B9%88%E0%B8%B2-%E0%B8%82%E0%B8%99%E0%B8%B2%E0%B8%94-%E0%B8%A5%E0%B8%B4%E0%B8%95%E0%B8%A3-%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B8%99%E0%B9%89%E0%B8%B3%E0%B8%95%E0%B8%B2%E0%B8%A5-8920386.jpg?f=768x0&format=auto', ''),
(9, 7, 'เสื้อวิ่ง', 'เสื้อวิ่งสำหรับผู้หญิงรุ่น Run Sun Protect', 180.00, 10, 'https://contents.mediadecathlon.com/p2789901/k$d11aae927ebe543a0aacef6b1958211f/%E0%B9%80%E0%B8%AA%E0%B8%B7%E0%B9%89%E0%B8%AD%E0%B8%A7%E0%B8%B4%E0%B9%88%E0%B8%87%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B8%9C%E0%B8%B9%E0%B9%89%E0%B8%AB%E0%', 'https://contents.mediadecathlon.com/p2789903/k$0a8c28dff4d6d64c18ae1f2bb47557ac/%E0%B9%80%E0%B8%AA%E0%B8%B7%E0%B9%89%E0%B8%AD%E0%B8%A7%E0%B8%B4%E0%B9%88%E0%B8%87%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A%E0%B8%9C%E0%B8%B9%E0%B9%89%E0%B8%AB%E0%B8%8D%E0%B8%B4%E0%B8%87%E0%B8%A3%E0%B8%B8%E0%B9%88%E0%B8%99-8913669.jpg?f=768x0&format=auto', ''),
(10, 8, 'เต็นท์ตั้งแคมป์สำหรับ 3 คน', 'เต็นท์ตั้งแคมป์สำหรับ 3 คน (สีขาว)', 1900.00, 10, 'https://contents.mediadecathlon.com/p2302879/k$48bdf665c00cedcfd68775614d6e9169/%E0%B9%80%E0%B8%95%E0%B9%87%E0%B8%99%E0%B8%97%E0%B9%8C%E0%B8%95%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B9%81%E0%B8%84%E0%B8%A1%E0%B8%9B%E0%B9%8C%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%', 'https://contents.mediadecathlon.com/p2302877/k$17e6b6a0c0d64450293dfe3cc7526fb7/%E0%B9%80%E0%B8%95%E0%B9%87%E0%B8%99%E0%B8%97%E0%B9%8C%E0%B8%95%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B9%81%E0%B8%84%E0%B8%A1%E0%B8%9B%E0%B9%8C%E0%B8%AA%E0%B8%B3%E0%B8%AB%E0%B8%A3%E0%B8%B1%E0%B8%9A-%E0%B8%84%E0%B8%99-%E0%B8%AA%E0%B8%B5%E0%B8%82%E0%B8%B2%E0%B8%A7-8576111.jpg?f=768x0&format=auto', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'แพร', 'prae@gmail.com', '$2y$10$nlJkQLD2cwJ8HW8ufVDEruMSxGPvTjxyY08MR2WuDfW3Qx/QBnrNy', '0654799814', 'หอพักเฮือนเพ็ญ 803 มหาสารคาม', 'admin', '2026-08-16 19:42:32'),
(4, 'prae', 'praewa@gmail.com', '$2y$10$W1Tp3RiyRM4LTzSYQ3nUROPK.7.9QJrBA1RVLCEMguJdOnX0hOxMy', '0654799814', 'หอพักเฮือนเพ็ญ 803 มหาสารคาม', 'user', '2026-08-16 21:39:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
