-- =====================================================
-- MUNU - Karalama Defteri / Notlar Modülü
-- Bu SQL'i phpMyAdmin'de çalıştırın
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- NOTLAR / HATIRLATICILAR TABLOSU
-- =====================================================
CREATE TABLE IF NOT EXISTS `notes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT DEFAULT NULL,
  `note_type` ENUM('not','hatirlatici','siparis','gorev') DEFAULT 'not',
  `priority` ENUM('dusuk','normal','yuksek','acil') DEFAULT 'normal',
  `due_date` DATETIME DEFAULT NULL,
  `reminder_date` DATETIME DEFAULT NULL,
  `is_pinned` TINYINT(1) DEFAULT 0,
  `color` VARCHAR(20) DEFAULT '#fef3c7',
  `status` ENUM('aktif','tamamlandi','iptal') DEFAULT 'aktif',
  `completed_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Örnek notlar
INSERT INTO `notes` (`title`, `content`, `note_type`, `priority`, `due_date`, `color`) VALUES 
('Hoş Geldiniz!', 'Karalama defterine hoş geldiniz. Buraya notlarınızı, hatırlatıcılarınızı ve yapılacaklar listenizi ekleyebilirsiniz.', 'not', 'normal', NULL, '#dbeafe'),
('Örnek Sipariş Notu', 'A firmasından 50 adet ürün sipariş et', 'siparis', 'yuksek', DATE_ADD(NOW(), INTERVAL 3 DAY), '#fef3c7'),
('Örnek Hatırlatıcı', 'Fatura ödemesini unutma!', 'hatirlatici', 'acil', DATE_ADD(NOW(), INTERVAL 1 DAY), '#fee2e2');

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- GÜNCELLEME TAMAMLANDI!
-- =====================================================

