-- database.sql
-- Esquema per a MySQL/MariaDB compatible amb XAMPP

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `detalls_comanda`;
DROP TABLE IF EXISTS `comandes`;
DROP TABLE IF EXISTS `plats`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `usuaris`;
DROP TABLE IF EXISTS `roles`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1) Taula de Rols
CREATE TABLE `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`name`) VALUES ('client'), ('cuiner'), ('admin');

-- 2) Taula d'Usuaris
CREATE TABLE `usuaris` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(120) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Taula de Categories de Menjar
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Taula de Plats (Menjar)
CREATE TABLE `plats` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `categoria_id` INT NOT NULL,
  `nom` VARCHAR(160) NOT NULL,
  `descripcio` TEXT,
  `preu` DECIMAL(10,2) NOT NULL,
  `imatge_url` VARCHAR(255),
  `disponible` BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (`categoria_id`) REFERENCES `categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Taula de Comandes
CREATE TABLE `comandes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuari_id` INT NOT NULL,
  `cuiner_id` INT DEFAULT NULL,
  `estat` ENUM('pendent', 'en_preparacio', 'llest', 'entregat') DEFAULT 'pendent',
  `total` DECIMAL(10,2) DEFAULT 0.00,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuari_id`) REFERENCES `usuaris`(`id`),
  FOREIGN KEY (`cuiner_id`) REFERENCES `usuaris`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6) Taula de Detalls de la Comanda
CREATE TABLE `detalls_comanda` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `comanda_id` INT NOT NULL,
  `plat_id` INT NOT NULL,
  `quantitat` INT NOT NULL DEFAULT 1,
  `preu_unitari` DECIMAL(10,2) NOT NULL,
  `es_vega` BOOLEAN DEFAULT FALSE,
  `sense_gluten` BOOLEAN DEFAULT FALSE,
  `comentaris` TEXT,
  FOREIGN KEY (`comanda_id`) REFERENCES `comandes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plat_id`) REFERENCES `plats`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- INSERCIÓ DE DADES INICIALS
-- ==========================================

-- Inserir l'usuari administrador (contrasenya: admin1234)
INSERT INTO `usuaris` (`nom`, `email`, `password_hash`, `role_id`) VALUES 
('admin', 'admin@gmail.com', '$2y$10$a8uepG6Tr4gv0olSmLJ2oO.neSuhSEv43h3V8RfwnYG2dhFl8SfyS', 3);

-- Inserir cuiners de prova
INSERT INTO `usuaris` (`nom`, `email`, `password_hash`, `role_id`) VALUES 
('Ferran', 'ferran@gmail.com', '$2y$10$24ApQB13f2Brq2g5JDYysupG9knKw5giF9Jd2zn9T6NFhDYOSyf4S', 2),
('Carme', 'carme@gmail.com', '$2y$10$24ApQB13f2Brq2g5JDYysupG9knKw5giF9Jd2zn9T6NFhDYOSyf4S', 2);

-- Inserir un usuari client de prova
INSERT INTO `usuaris` (`id`, `nom`, `email`, `password_hash`, `role_id`) VALUES 
(4, 'Joan Prova', 'joan@gmail.com', '$2y$10$a8uepG6Tr4gv0olSmLJ2oO.neSuhSEv43h3V8RfwnYG2dhFl8SfyS', 1);

-- Inserir categories
INSERT INTO `categories` (`nom`) VALUES ('Primers'), ('Segons'), ('Postres'), ('Begudes');

-- Inserir plats
INSERT INTO `plats` (`id`, `categoria_id`, `nom`, `descripcio`, `preu`) VALUES 
(1, 1, 'Amanida Verda', 'Enciam, tomàquet, ceba i olives', 6.50),
(2, 1, 'Sopa de Galets', 'Sopa tradicional catalana', 7.20),
(3, 2, 'Botifarra amb mongetes', 'Botifarra de pagès a la brasa', 12.50),
(4, 2, 'Paella Marinera', 'Arròs amb marisc fresc', 18.00),
(5, 3, 'Crema Catalana', 'Postres típiques amb sucre cremat', 4.50);

-- Inserir comandes de prova
-- Comanda 1: Només vegà i sense gluten
INSERT INTO `comandes` (`id`, `usuari_id`, `estat`, `total`, `notes`) VALUES (1, 4, 'pendent', 6.50, 'Volen la comanda ràpid');
INSERT INTO `detalls_comanda` (`comanda_id`, `plat_id`, `quantitat`, `preu_unitari`, `es_vega`, `sense_gluten`, `comentaris`) 
VALUES (1, 1, 1, 6.50, 1, 1, 'Molt important: Sense traces de gluten');

-- Comanda 2: Només vegà
INSERT INTO `comandes` (`id`, `usuari_id`, `estat`, `total`, `notes`) VALUES (2, 4, 'en_preparacio', 7.20, 'Sopa ben calenta');
INSERT INTO `detalls_comanda` (`comanda_id`, `plat_id`, `quantitat`, `preu_unitari`, `es_vega`, `sense_gluten`, `comentaris`) 
VALUES (2, 2, 1, 7.20, 1, 0, 'Sopa de galets vegetal');

-- Comanda 3: Només sense gluten
INSERT INTO `comandes` (`id`, `usuari_id`, `estat`, `total`, `notes`) VALUES (3, 4, 'llest', 18.00, '');
INSERT INTO `detalls_comanda` (`comanda_id`, `plat_id`, `quantitat`, `preu_unitari`, `es_vega`, `sense_gluten`, `comentaris`) 
VALUES (3, 4, 1, 18.00, 0, 1, 'Arros sense res de pa');

-- Comanda 4: Mix de tot
INSERT INTO `comandes` (`id`, `usuari_id`, `estat`, `total`) VALUES (4, 4, 'pendent', 23.50);
INSERT INTO `detalls_comanda` (`comanda_id`, `plat_id`, `quantitat`, `preu_unitari`, `es_vega`, `sense_gluten`) 
VALUES (4, 3, 1, 12.50, 0, 0); -- Botifarra normal
INSERT INTO `detalls_comanda` (`comanda_id`, `plat_id`, `quantitat`, `preu_unitari`, `es_vega`, `sense_gluten`) 
VALUES (4, 1, 1, 6.50, 1, 1);  -- Amanida V+SG
INSERT INTO `detalls_comanda` (`comanda_id`, `plat_id`, `quantitat`, `preu_unitari`, `es_vega`, `sense_gluten`) 
VALUES (4, 5, 1, 4.50, 0, 1);  -- Crema Catalana SG