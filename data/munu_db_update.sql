-- MUNU Veritabanı Kurulum
-- Tüm satırları seçip çalıştırın

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `transaction_details`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `receivables`;
DROP TABLE IF EXISTS `stock_movements`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `user_sessions`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `my_debts`;

CREATE TABLE `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(50) DEFAULT NULL,
  `tax_number` VARCHAR(20) DEFAULT NULL,
  `tax_office` VARCHAR(100) DEFAULT NULL,
  `customer_type` ENUM('bireysel','kurumsal') DEFAULT 'bireysel',
  `balance` DECIMAL(15,2) DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `customer_id` INT NOT NULL,
  `transaction_type` ENUM('borc','tahsilat') NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `transaction_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_code` VARCHAR(50) DEFAULT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `unit` VARCHAR(20) DEFAULT 'adet',
  `stock_quantity` INT DEFAULT 0,
  `min_stock_level` INT DEFAULT 5,
  `unit_price` DECIMAL(15,2) DEFAULT 0.00,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `my_debts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `creditor_name` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `due_date` DATE DEFAULT NULL,
  `status` ENUM('odenmedi','odendi') DEFAULT 'odenmedi',
  `paid_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Basarili!' AS Sonuc;
