-- =====================================================
-- MUNU - Borçlarım/Giderler Modülü Güncelleme
-- Bu SQL'i phpMyAdmin'de çalıştırın
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. ALACAKLILAR (Kişiler/Firmalar) TABLOSU
-- =====================================================
CREATE TABLE IF NOT EXISTS `creditors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `creditor_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `total_debt` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Varsayılan alacaklılar
INSERT INTO `creditors` (`creditor_name`, `notes`) VALUES 
('Genel Gider', 'Kategorisiz genel giderler için'),
('Market', 'Market alışverişleri'),
('Akaryakıt', 'Benzin/Mazot giderleri');

-- =====================================================
-- 2. GİDER KATEGORİLERİ TABLOSU
-- =====================================================
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) DEFAULT 'fa-tag',
  `color` VARCHAR(20) DEFAULT '#6b7280',
  `sort_order` INT(11) DEFAULT 0,
  `status` ENUM('aktif','pasif') DEFAULT 'aktif',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Varsayılan gider kategorileri
INSERT INTO `expense_categories` (`category_name`, `icon`, `color`, `sort_order`) VALUES 
('Yemek', 'fa-utensils', '#ef4444', 1),
('Ulaşım', 'fa-car', '#3b82f6', 2),
('Market', 'fa-shopping-cart', '#22c55e', 3),
('Fatura', 'fa-file-invoice', '#f59e0b', 4),
('Kira', 'fa-home', '#8b5cf6', 5),
('Sağlık', 'fa-heart-pulse', '#ec4899', 6),
('Eğitim', 'fa-graduation-cap', '#06b6d4', 7),
('Eğlence', 'fa-gamepad', '#a855f7', 8),
('Giyim', 'fa-shirt', '#14b8a6', 9),
('Diğer', 'fa-ellipsis', '#6b7280', 99);

-- =====================================================
-- 3. MY_DEBTS TABLOSUNU GÜNCELLE
-- =====================================================
-- Yeni sütunlar ekle
ALTER TABLE `my_debts` 
ADD COLUMN IF NOT EXISTS `creditor_id` INT(11) DEFAULT NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `category_id` INT(11) DEFAULT NULL AFTER `creditor_id`,
ADD COLUMN IF NOT EXISTS `expense_type` ENUM('borc','gider') DEFAULT 'borc' AFTER `category_id`;

-- Mevcut kayıtları "Genel Gider" alacaklısına ve "Diğer" kategorisine bağla
UPDATE `my_debts` SET `creditor_id` = 1, `category_id` = 10 WHERE `creditor_id` IS NULL;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- GÜNCELLEME TAMAMLANDI!
-- =====================================================

