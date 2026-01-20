-- =====================================================
-- MUNU - Müşteri Takip ve Ön Muhasebe Sistemi
-- Veritabanı Kurulum Dosyası
-- Tarih: 2026-01-02
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- Veritabanını seç (zaten varsa)
-- CREATE DATABASE IF NOT EXISTS `munu_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- USE `munu_db`;

-- =====================================================
-- TABLOLARI SIFIRLA (DİKKAT: TÜM VERİLER SİLİNİR!)
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `my_debts`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. KULLANICILAR TABLOSU
-- =====================================================
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `role` ENUM('yonetici','personel') DEFAULT 'personel',
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Varsayılan yönetici (şifre: 1234)
INSERT INTO `users` (`username`, `password`, `full_name`, `role`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistem Yöneticisi', 'yonetici');

-- =====================================================
-- 2. MÜŞTERİLER TABLOSU
-- =====================================================
CREATE TABLE `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `address` TEXT,
  `city` VARCHAR(50),
  `tax_number` VARCHAR(20) COMMENT 'Vergi No (Kurumsal için)',
  `tax_office` VARCHAR(100) COMMENT 'Vergi Dairesi',
  `customer_type` ENUM('bireysel','kurumsal') DEFAULT 'bireysel',
  `balance` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Pozitif: Müşteri borçlu, Negatif: Biz borçluyuz',
  `notes` TEXT,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_customer_name` (`customer_name`),
  INDEX `idx_phone` (`phone`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 3. İŞLEMLER TABLOSU (Borç / Tahsilat)
-- =====================================================
CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `transaction_type` ENUM('borc','tahsilat') NOT NULL COMMENT 'borc: Müşteriye borç yazıldı, tahsilat: Müşteriden ödeme alındı',
  `amount` DECIMAL(15,2) NOT NULL,
  `description` TEXT,
  `transaction_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE,
  INDEX `idx_customer_id` (`customer_id`),
  INDEX `idx_transaction_date` (`transaction_date`),
  INDEX `idx_type` (`transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 4. ÜRÜNLER / STOK TABLOSU
-- =====================================================
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_code` VARCHAR(50) UNIQUE,
  `product_name` VARCHAR(200) NOT NULL,
  `category` VARCHAR(100),
  `unit` VARCHAR(20) DEFAULT 'adet' COMMENT 'adet, kg, lt, paket vb.',
  `stock_quantity` INT DEFAULT 0,
  `min_stock_level` INT DEFAULT 5 COMMENT 'Bu seviyenin altına düşerse uyarı verilir',
  `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `description` TEXT,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_product_name` (`product_name`),
  INDEX `idx_category` (`category`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 5. BENİM BORÇLARIM TABLOSU (Tedarikçilere / 3. Şahıslara)
-- =====================================================
CREATE TABLE `my_debts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `creditor_name` VARCHAR(255) NOT NULL COMMENT 'Kime borçluyuz (Toptancı, Tedarikçi vb.)',
  `amount` DECIMAL(15,2) NOT NULL,
  `description` TEXT,
  `due_date` DATE COMMENT 'Son ödeme tarihi',
  `status` ENUM('odenmedi','odendi') DEFAULT 'odenmedi',
  `paid_date` DATE COMMENT 'Ödeme yapıldığı tarih',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX `idx_creditor` (`creditor_name`),
  INDEX `idx_status` (`status`),
  INDEX `idx_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- ÖRNEK VERİLER (İSTERSENİZ SİLEBİLİRSİNİZ)
-- =====================================================

-- Örnek Müşteriler
INSERT INTO `customers` (`customer_name`, `phone`, `customer_type`, `balance`) VALUES 
('Ahmet Yılmaz', '5301234567', 'bireysel', 1500.00),
('Mehmet Demir', '5327654321', 'bireysel', 750.50),
('ABC Ticaret Ltd.', '5551112233', 'kurumsal', 5200.00);

-- Örnek İşlemler
INSERT INTO `transactions` (`customer_id`, `transaction_type`, `amount`, `description`, `transaction_date`) VALUES 
(1, 'borc', 2000.00, 'Mal alımı', CURDATE()),
(1, 'tahsilat', 500.00, 'Nakit ödeme', CURDATE()),
(2, 'borc', 750.50, 'Hizmet bedeli', CURDATE()),
(3, 'borc', 5200.00, 'Toptan mal alımı', CURDATE());

-- Örnek Ürünler
INSERT INTO `products` (`product_code`, `product_name`, `category`, `stock_quantity`, `min_stock_level`, `unit_price`) VALUES 
('URN001', 'Beyaz Ekmek', 'Fırın Ürünleri', 50, 10, 5.00),
('URN002', 'Ayran 250ml', 'İçecekler', 3, 20, 8.00),
('URN003', 'Pirinç 1kg', 'Bakliyat', 25, 5, 45.00);

-- Örnek Borçlarım
INSERT INTO `my_debts` (`creditor_name`, `amount`, `description`, `due_date`) VALUES 
('Toptancı Ali', 3500.00, 'Aylık mal alımı', DATE_ADD(CURDATE(), INTERVAL 15 DAY)),
('XYZ Dağıtım', 1200.00, 'İçecek alımı', DATE_ADD(CURDATE(), INTERVAL 7 DAY));

COMMIT;

-- =====================================================
-- KURULUM TAMAMLANDI!
-- 
-- Tablolar:
--   1. users       - Sistem kullanıcıları
--   2. customers   - Müşteriler
--   3. transactions - Borç/Tahsilat işlemleri
--   4. products    - Ürünler ve stok
--   5. my_debts    - Kendi borçlarımız
--
-- Varsayılan Giriş:
--   Kullanıcı: admin
--   Şifre: 1234
-- =====================================================


