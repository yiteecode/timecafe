-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 04:47 PM
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
-- Database: `time-cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_features`
--

CREATE TABLE `about_features` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_features`
--

INSERT INTO `about_features` (`id`, `title`, `description`, `icon`, `active`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'ፈጣን አገልግሎት ያገኛሉ', 'This is test description for testing purpose.', 'star', 1, 0, '2025-01-28 16:17:34', '2025-02-01 09:14:19');

-- --------------------------------------------------------

--
-- Table structure for table `about_section`
--

CREATE TABLE `about_section` (
  `id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `subheading` varchar(255) DEFAULT NULL,
  `main_content` text NOT NULL,
  `mission` text DEFAULT NULL,
  `vision` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_section`
--

INSERT INTO `about_section` (`id`, `heading`, `subheading`, `main_content`, `mission`, `vision`, `image`, `video_url`, `updated_at`) VALUES
(1, '2 About Time Cafe', '1 Our Story', '1 Welcome to Time Cafe, a professional and esteemed cafe rooted in the heart of Ethiopia. With over 12 years of experience, Time Cafe has established itself as a beloved destination for coffee enthusiasts, food lovers, and those seeking a cozy and welcoming atmosphere.\r\n\r\nOur journey began with a vision to create a space where tradition meets innovation, offering the finest Ethiopian coffee and a menu that blends local flavors with international inspiration. Over the years, we have grown to serve our community with dedication, opening two branches in Hawassa and Addis Ababa, two of Ethiopia\'s vibrant cities.\r\n\r\nAt Time Cafe, we are committed to delivering excellence in every cup of coffee and every dish we serve. Our skilled baristas and chefs take pride in using the freshest ingredients to craft flavors that delight the senses. From our signature coffee brews to our delicious pastries and meals, we aim to provide an experience that feels like home.\r\n\r\nAs a proudly Ethiopian brand, we embrace the rich coffee culture of our country while continuously striving to innovate and expand. Whether you\'re stopping by for a quick coffee, a family gathering, or a business meeting, Time Cafe is the perfect place to unwind and enjoy the best that Ethiopia has to offer.\r\n\r\nThank you for being part of our journey. We look forward to welcoming you to one of our branches and sharing moments that matter.', '1 To provide exceptional culinary and coffee experiences by blending Ethiopia\'s rich heritage with innovative hospitality, ensuring every guest feels valued and at home.', '1 To be Ethiopia’s leading cafe brand, known for excellence, community connection, and expanding our branches to share the vibrant culture and flavors of Ethiopia with the world.', '6798feeb080b8_about-2.jpg', '', '2025-02-01 08:59:59');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL DEFAULT 'admin',
  `phone` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `created_at`, `username`, `role`, `phone`, `full_name`) VALUES
(3, 'yiteecode@gmail.com', 'password', '2025-01-24 15:33:04', 'admin', 'admin', NULL, NULL),
(5, 'admin@timecafe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-01-24 15:40:55', 'yiteecode', 'super_admin', NULL, 'dawit');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `people` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `name`, `email`, `phone`, `booking_date`, `booking_time`, `people`, `message`, `created_at`) VALUES
(1, 'Dawit Tilahun', 'yiteecode@gmail.com', '098376847', '2025-01-09', '13:52:00', 3, 'dfgfgh', '2025-01-24 10:50:13'),
(10, 'sjdnm,cj', 'sdnfsd@gmakos.com', 'skdhna', '2025-01-24', '15:53:00', 2, 'dsk', '2025-01-24 12:55:32'),
(11, 'Eden Mola', 'yiteecode@gmail.com', '098376847', '2025-01-21', '15:59:00', 3, 'sd', '2025-01-24 12:56:04'),
(12, 'Eden Mola', 'yiteecode@gmail.com', '098376847', '2025-01-22', '15:59:00', 3, 'ds', '2025-01-24 12:56:48'),
(13, 'Eden Mola', 'sahf@gmail.com', '098376847', '2025-01-07', '20:02:00', 4, 'bgnm', '2025-01-24 13:02:52'),
(14, 'Eden Mola', 'sahf@gmail.com', '938', '2025-01-01', '16:08:00', 4, 'kda', '2025-01-24 13:05:30'),
(16, 'Eden Mola', 'sahf@gmail.com', '938', '2025-01-10', '19:08:00', 4, 'k', '2025-01-24 13:08:38');

-- --------------------------------------------------------

--
-- Table structure for table `chefs`
--

CREATE TABLE `chefs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chefs`
--

INSERT INTO `chefs` (`id`, `name`, `profession`, `description`, `image`, `active`, `sort_order`, `created_at`, `updated_at`, `facebook`, `instagram`, `twitter`) VALUES
(20, 'Friyay Haylu 1', 'master chef', 'Here is our the best chef over 10 years of expriance at time cafe', '679725f667e91.jpg', 1, 1, '2025-01-27 06:21:42', '2025-01-28 15:38:02', 'https://facebook.com/', 'https://instagram.com/', 'https://twitter.com/'),
(21, 'Friyay Haylu', 'master chef', 'Here is our the best chef over 10 years of expriance at time cafe', '6797262ad02b2.jpg', 1, 2, '2025-01-27 06:22:34', '2025-01-27 06:51:58', 'https://facebook.com/', 'https://instagram.com/', 'https://twitter.com/'),
(25, 'melaku damete', 'master chef', 'He has 10 years of expriance in different organization ', '6798fa72c61cc.jpg', 1, 3, '2025-01-28 15:40:36', '2025-01-28 15:40:36', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `is_read`) VALUES
(39, 'Eden Mola', 'sahf@gmail.com', 'appreciate your work.', 'This is test message, this should send to the admin panel.', '2025-01-28 20:27:39', 0);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image`, `title`, `description`, `active`, `sort_order`, `created_at`, `updated_at`) VALUES
(20, 'gallery_6794daee3bce4_gallery-8.jpg', 'gallery-8', '', 1, 8, '2025-01-25 12:37:02', '2025-01-25 12:37:02'),
(21, 'gallery_6795b8e8cbef8_menu-item-1.png', 'menu-item-1', 'fnsd', 1, 9, '2025-01-26 04:24:08', '2025-01-26 04:24:08'),
(29, '6796982f10a68_menu-item-6.png', 'new test', 'sdjkf', 1, 0, '2025-01-26 20:16:47', '2025-01-26 20:16:47'),
(30, '6798a8f6d18e4_menu-item-1.png', 'ፈጣን አገልግሎት ያገኛሉ', 'ፈጣን አገልግሎት ያገኛሉ', 1, 0, '2025-01-28 09:52:54', '2025-01-28 09:52:54'),
(31, '6798f9c695185_menu-item-1.png', 'ፈጣን አገልግሎት ያገኛሉ', 'test description', 1, 0, '2025-01-28 15:37:42', '2025-01-28 15:37:42');

-- --------------------------------------------------------

--
-- Table structure for table `hero_section`
--

CREATE TABLE `hero_section` (
  `id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `subheading` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_section`
--

INSERT INTO `hero_section` (`id`, `heading`, `subheading`, `video_url`, `hero_image`, `updated_at`) VALUES
(1, 'Welcome to Hawassa Time Cafe', 'Our Special Sandwich is deferent, come and test this amaizing sandwich and try more our menus from the list.', 'hero_6793e00c0d809_1 Minute Restaurant Promo Video .mp4', '679dd68db36e5_timesandwich.png', '2025-02-01 08:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `name`, `description`, `sort_order`, `active`, `updated_at`) VALUES
(1, 'Breakfast', 'Morning delights', 0, 1, '2025-01-24 17:28:12'),
(2, 'Lunch', 'Midday favorites', 0, 1, '2025-01-24 17:28:12'),
(3, 'Dinner', 'Evening specialties', 0, 1, '2025-01-24 17:28:12'),
(4, 'Beverages', 'Refreshing drinks', 0, 1, '2025-01-24 17:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image`, `active`, `sort_order`, `created_at`, `updated_at`) VALUES
(5, 1, 'makiyato', 'fsjkdjf', 12.00, 'menu_6794c84bb36ec_cafe_image-1.jpg', 0, 0, '2025-01-25 11:17:31', '2025-01-26 20:01:46'),
(7, 1, 'ab', 'Ingredients: Chicken Meet, Vegetables, Spices and lemon', 250.00, 'menu_679547c295213_menu-item-2.png', 0, 0, '2025-01-25 20:21:22', '2025-01-28 06:02:08'),
(10, 2, 'Test menu item', 'test description', 45.00, 'menu_6795b4c6db319_menu-item-4.png', 1, 0, '2025-01-26 04:06:30', '2025-01-28 09:27:31'),
(16, 3, 'testing', 'test disc', 234.00, '679894064016c_hero-img.png', 1, 0, '2025-01-26 05:14:17', '2025-01-28 08:23:34'),
(17, 1, 'ab', 'dskljnz', 235.00, '67961d11e33e3_menu-item-6.png', 1, 0, '2025-01-26 11:31:29', '2025-01-28 15:36:49'),
(19, 1, 'doro wet', 'fdsf', 343.00, '679694eb9be11.png', 1, 1, '2025-01-26 20:02:51', '2025-01-28 16:37:16'),
(20, 2, 'Spagety', 'sjfklanf  skfslmfv', 200.00, '6797e794d42d8.png', 1, 1, '2025-01-27 20:07:48', '2025-01-27 20:07:48'),
(21, 2, 'Spagety', 'uisdjkmd', 200.00, '67987354ecafe.png', 1, 2, '2025-01-28 06:04:04', '2025-01-28 06:04:04'),
(22, 3, 'Spagety', 'ghhvnm', 200.00, '67989f438cc4e_menu-item-1.png', 1, 0, '2025-01-28 09:11:31', '2025-01-28 09:11:31'),
(23, 3, 'fgjh', 'dth m', 345.00, '67989f750a4cc_menu-item-6.png', 1, 0, '2025-01-28 09:12:21', '2025-01-28 09:12:21'),
(24, 2, 'Test menu item', 'test description', 45.00, '6798a635aa5b3_menu-item-6.png', 1, 0, '2025-01-28 09:41:09', '2025-01-28 09:41:09'),
(25, 2, 'Tegabino', 'it prepars using shiro paw', 200.00, '679907435c71f_menu-item-3.png', 1, 0, '2025-01-28 16:35:15', '2025-01-28 16:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','confirmed','delivered','cancelled') DEFAULT 'pending',
  `special_instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `menu_item_id`, `customer_name`, `email`, `phone`, `address`, `quantity`, `status`, `special_instructions`, `created_at`) VALUES
(1, 23, 'Eden Mola', 'sahf@gmail.com', '09383892', 'ajdfksjf', 1, '', 'sankofa', '2025-01-28 22:31:14'),
(2, 23, 'Eden Mola', 'sahf@gmail.com', '09383892', 'fjhkkhgvhc', 1, 'pending', 'ghjkgbn', '2025-01-28 22:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `id` int(11) NOT NULL,
  `stat_value` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `review` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `why_us`
--

CREATE TABLE `why_us` (
  `id` int(11) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `why_us_items`
--

CREATE TABLE `why_us_items` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_features`
--
ALTER TABLE `about_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `about_section`
--
ALTER TABLE `about_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chefs`
--
ALTER TABLE `chefs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_section`
--
ALTER TABLE `hero_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `stats`
--
ALTER TABLE `stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `why_us`
--
ALTER TABLE `why_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `why_us_items`
--
ALTER TABLE `why_us_items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_features`
--
ALTER TABLE `about_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `about_section`
--
ALTER TABLE `about_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `chefs`
--
ALTER TABLE `chefs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stats`
--
ALTER TABLE `stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `why_us`
--
ALTER TABLE `why_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `why_us_items`
--
ALTER TABLE `why_us_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
