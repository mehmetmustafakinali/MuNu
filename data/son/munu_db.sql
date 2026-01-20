-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 03 Oca 2026, 01:24:07
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `munu_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `creditors`
--

CREATE TABLE `creditors` (
  `id` int(11) NOT NULL,
  `creditor_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `total_debt` decimal(15,2) DEFAULT 0.00,
  `status` enum('aktif','pasif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `creditors`
--

INSERT INTO `creditors` (`id`, `creditor_name`, `phone`, `email`, `notes`, `total_debt`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Genel Gider', NULL, NULL, 'Kategorisiz genel giderler için', 0.00, 'aktif', '2026-01-02 23:45:04', '2026-01-02 23:45:04'),
(2, 'Market', NULL, NULL, 'Market alışverişleri', 0.00, 'aktif', '2026-01-02 23:45:04', '2026-01-02 23:45:04'),
(3, 'Akaryakıt', NULL, NULL, 'Benzin/Mazot giderleri', 250.00, 'aktif', '2026-01-02 23:45:04', '2026-01-02 23:50:52'),
(4, 'Hüseyin', '515151551', 'dsfds@ss.com', '', 0.00, 'aktif', '2026-01-02 23:51:22', '2026-01-02 23:51:22');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `tax_number` varchar(20) DEFAULT NULL,
  `tax_office` varchar(100) DEFAULT NULL,
  `customer_type` enum('bireysel','kurumsal') DEFAULT 'bireysel',
  `balance` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('aktif','pasif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `phone`, `email`, `address`, `city`, `tax_number`, `tax_office`, `customer_type`, `balance`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(2, 'ahmet', '23423', 'sss@ss.com', '', '', '', '', 'kurumsal', 1000.00, '', 'aktif', '2026-01-02 11:22:43', '2026-01-02 23:32:44'),
(3, 'Şaban Koşan', '525522255', 'dene@ss.com', '', '', '', '', 'bireysel', 0.00, '', 'aktif', '2026-01-02 20:47:28', '2026-01-02 20:47:28');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-tag',
  `color` varchar(20) DEFAULT '#6b7280',
  `sort_order` int(11) DEFAULT 0,
  `status` enum('aktif','pasif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `category_name`, `icon`, `color`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Yemek', 'fa-utensils', '#ef4444', 1, 'aktif', '2026-01-02 23:45:04'),
(2, 'Ulaşım', 'fa-car', '#3b82f6', 2, 'aktif', '2026-01-02 23:45:04'),
(3, 'Market', 'fa-shopping-cart', '#22c55e', 3, 'aktif', '2026-01-02 23:45:04'),
(4, 'Fatura', 'fa-file-invoice', '#f59e0b', 4, 'aktif', '2026-01-02 23:45:04'),
(5, 'Kira', 'fa-home', '#8b5cf6', 5, 'aktif', '2026-01-02 23:45:04'),
(6, 'Sağlık', 'fa-heart-pulse', '#ec4899', 6, 'aktif', '2026-01-02 23:45:04'),
(7, 'Eğitim', 'fa-graduation-cap', '#06b6d4', 7, 'aktif', '2026-01-02 23:45:04'),
(8, 'Eğlence', 'fa-gamepad', '#a855f7', 8, 'aktif', '2026-01-02 23:45:04'),
(9, 'Giyim', 'fa-shirt', '#14b8a6', 9, 'aktif', '2026-01-02 23:45:04'),
(10, 'Diğer', 'fa-ellipsis', '#6b7280', 99, 'aktif', '2026-01-02 23:45:04');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `my_debts`
--

CREATE TABLE `my_debts` (
  `id` int(11) NOT NULL,
  `creditor_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `expense_type` enum('borc','gider') DEFAULT 'borc',
  `creditor_name` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('odenmedi','odendi') DEFAULT 'odenmedi',
  `paid_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `my_debts`
--

INSERT INTO `my_debts` (`id`, `creditor_id`, `category_id`, `expense_type`, `creditor_name`, `amount`, `description`, `due_date`, `status`, `paid_date`, `created_at`, `updated_at`) VALUES
(1, 3, 9, 'borc', 'Akaryakıt', 250.00, '', '2026-01-01', 'odenmedi', NULL, '2026-01-02 23:50:52', '2026-01-02 23:50:52');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `note_type` enum('not','hatirlatici','siparis','gorev') DEFAULT 'not',
  `priority` enum('dusuk','normal','yuksek','acil') DEFAULT 'normal',
  `due_date` datetime DEFAULT NULL,
  `reminder_date` datetime DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `color` varchar(20) DEFAULT '#fef3c7',
  `status` enum('aktif','tamamlandi','iptal') DEFAULT 'aktif',
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notes`
--

INSERT INTO `notes` (`id`, `title`, `content`, `note_type`, `priority`, `due_date`, `reminder_date`, `is_pinned`, `color`, `status`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 'Hoş Geldiniz!', 'Karalama defterine hoş geldiniz. Buraya notlarınızı, hatırlatıcılarınızı ve yapılacaklar listenizi ekleyebilirsiniz.', 'not', 'normal', NULL, NULL, 0, '#dbeafe', 'aktif', NULL, '2026-01-03 00:10:18', '2026-01-03 00:10:18'),
(2, 'Örnek Sipariş Notu', 'A firmasından 50 adet ürün sipariş et', 'siparis', 'yuksek', '2026-01-06 03:10:18', NULL, 0, '#fef3c7', 'aktif', NULL, '2026-01-03 00:10:18', '2026-01-03 00:10:18'),
(3, 'Örnek Hatırlatıcı', 'Fatura ödemesini unutma!', 'hatirlatici', 'acil', '2026-01-04 03:10:18', NULL, 0, '#fee2e2', 'aktif', NULL, '2026-01-03 00:10:18', '2026-01-03 00:10:18'),
(4, 'Yarın Karpuz al', '', 'hatirlatici', 'normal', '2026-01-03 09:00:00', NULL, 0, '#fef3c7', 'aktif', NULL, '2026-01-03 00:17:58', '2026-01-03 00:17:58');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'adet',
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `status` enum('aktif','pasif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`) VALUES
(1, 'yonetici', 'Tam yetki sahibi sistem yöneticisi', '2026-01-02 13:51:39'),
(2, 'personel', 'Sınırlı yetkili personel', '2026-01-02 13:51:39');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `transaction_type` enum('borc','tahsilat') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `transactions`
--

INSERT INTO `transactions` (`id`, `customer_id`, `transaction_type`, `amount`, `description`, `transaction_date`, `created_at`) VALUES
(1, 2, 'borc', 1250.00, 'elden teslim', '2026-01-02', '2026-01-02 23:30:29'),
(2, 2, 'tahsilat', 250.00, 'Elden', '2026-01-02', '2026-01-02 23:32:44');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('yonetici','personel') DEFAULT 'personel',
  `role_id` int(11) DEFAULT 2,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `role_id`, `phone`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@munu.com', 'Sistem Yöneticisi', 'yonetici', 1, NULL, 1, NULL, '2026-01-02 13:51:39', '2026-01-02 13:51:39');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `creditors`
--
ALTER TABLE `creditors`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `my_debts`
--
ALTER TABLE `my_debts`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Tablo için indeksler `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `creditors`
--
ALTER TABLE `creditors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `my_debts`
--
ALTER TABLE `my_debts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
