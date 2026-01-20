-- =====================================================
-- MUNU - Veritabanı Tam Kurulum
-- Tüm tablolar sıfırdan oluşturuluyor
-- Admin şifresi: 1234
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;

-- =====================================================
-- TÜM TABLOLARI SİL
-- =====================================================

DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `my_debts`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

-- =====================================================
-- 1. ROLES TABLOSU
-- =====================================================

CREATE TABLE `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`id`, `role_name`, `description`) VALUES
(1, 'yonetici', 'Tam yetki sahibi sistem yöneticisi'),
(2, 'personel', 'Sınırlı yetkili personel');

-- =====================================================
-- 2. USERS TABLOSU
-- =====================================================

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100),
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('yonetici','personel') DEFAULT 'personel',
  `role_id` INT DEFAULT 2,
  `phone` VARCHAR(20),
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin kullanıcısı (şifre: 1234)
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `role_id`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@munu.com', 'Sistem Yöneticisi', 'yonetici', 1);

-- =====================================================
-- 3. CUSTOMERS TABLOSU
-- =====================================================

CREATE TABLE `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `address` TEXT,
  `city` VARCHAR(50),
  `tax_number` VARCHAR(20),
  `tax_office` VARCHAR(100),
  `customer_type` ENUM('bireysel','kurumsal') DEFAULT 'bireysel',
  `balance` DECIMAL(15,2) DEFAULT 0.00,
  `notes` TEXT,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 4. TRANSACTIONS TABLOSU
-- =====================================================

CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `transaction_type` ENUM('borc','tahsilat') NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `description` TEXT,
  `transaction_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 5. PRODUCTS TABLOSU
-- =====================================================

CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_code` VARCHAR(50),
  `product_name` VARCHAR(200) NOT NULL,
  `category` VARCHAR(100),
  `unit` VARCHAR(20) DEFAULT 'adet',
  `stock_quantity` INT DEFAULT 0,
  `min_stock_level` INT DEFAULT 5,
  `unit_price` DECIMAL(15,2) DEFAULT 0.00,
  `description` TEXT,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 6. MY_DEBTS TABLOSU
-- =====================================================

CREATE TABLE `my_debts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `creditor_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `description` TEXT,
  `due_date` DATE,
  `status` ENUM('odenmedi','odendi') DEFAULT 'odenmedi',
  `paid_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- FOREIGN KEY KONTROLÜNÜ AÇ
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- KURULUM TAMAMLANDI!
-- 
-- Tablolar:
--   - roles (2 kayıt: yönetici, personel)
--   - users (1 kayıt: admin)
--   - customers (boş)
--   - transactions (boş)
--   - products (boş)
--   - my_debts (boş)
--
-- Giriş Bilgileri:
--   Kullanıcı: admin
--   Şifre: 1234
-- =====================================================

SELECT 'Veritabani basariyla olusturuldu!' AS Sonuc;

