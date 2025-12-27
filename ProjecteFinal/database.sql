--
-- Estructura de la taula `usuaris`
--
CREATE TABLE `usuaris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_usuari` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasenya` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL DEFAULT 'usuari',
  `data_registre` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_usuari` (`nom_usuari`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Inserció de l'usuari administrador per defecte
--
INSERT INTO `usuaris` (`nom_usuari`, `email`, `contrasenya`, `rol`) VALUES
('admin', 'admin@gmail.com', '$2y$10$Z22OpmkhDrHkWZTVV1NRAO6Szm.UHYTscAc.F6vEjdBBBsUCcVRFS', 'admin');

-- --------------------------------------------------------

--
-- Estructura de la taula `categories`
--
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Inserció de dades d'exemple per a `categories`
--
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Elements', 'Substàncies bàsiques, gasos, líquids i sòlids.'),
(2, 'Edificis', 'Estructures construïbles pels duplicants.'),
(3, 'Aliments', 'Recursos comestibles per a la supervivència.');

-- --------------------------------------------------------

--
-- Estructura de la taula `items`
--
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Inserció de dades d'exemple per a `items`
--
INSERT INTO `items` (`id`, `name`, `description`, `image_path`, `category_id`) VALUES
(1, 'Water', 'Un líquid transparent i essencial per a la vida. Necessari per beure, per a la recerca i per a l''electròlisi.', 'export/ui_image/Water.png', 1),
(2, 'Algae', 'Un organisme simple que produeix oxigen quan es col·loca en un difusor. Font primària d''aire respirable al principi.', 'export/ui_image/Algae.png', 1),
(3, 'Copper', 'Un metall tou i conductor. S''utilitza per construir cables elèctrics bàsics i altres edificis inicials.', 'export/ui_image/Copper.png', 1),
(4, 'Sand', 'Material granular utilitzat principalment per a la filtració i la producció de vidre.', 'export/ui_image/Sand.png', 1),
(5, 'Outhouse', 'Un lavabo bàsic. No requereix aigua però produeix terra contaminada i malestar.', 'export/ui_image/Outhouse.png', 2),
(6, 'Manual Generator', 'Una roda de hàmster per a duplicants. Genera una petita quantitat d''energia quan un duplicant hi corre.', 'export/ui_image/ManualGenerator.png', 2),
(7, 'Microbe Musher', 'Permet crear aliments de baixa qualitat com el "Mush Bar" a partir de fang i aigua.', 'export/ui_image/MicrobeMusher.png', 2),
(8, 'Ration Box', 'Caixa d''emmagatzematge bàsica per a aliments. No refrigera.', 'export/ui_image/RationBox.png', 2),
(9, 'Mush Bar', 'Una barra alimentària poc apetitosa però funcional. Proporciona calories però redueix la moral.', 'export/ui_image/MushBar.png', 3),
(10, 'Mealwood', 'Una planta fàcil de cultivar que produeix "Meal Lice", un aliment bàsic per als duplicants.', 'export/ui_image/BasicPlantFood.png', 3),
(11, 'Oxygen Diffuser', 'Una màquina que converteix les algues en oxigen. Essencial per a la supervivència primerenca.', 'export/ui_image/MineralDeoxidizer.png', 2);

-- --------------------------------------------------------

--
-- Estructura de la taula `attributes`
--
CREATE TABLE `attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Inserció de dades d'exemple per a `attributes`
--
INSERT INTO `attributes` (`id`, `item_id`, `attribute_name`, `value`) VALUES
(1, 6, 'Power Output', '400 W'),
(2, 7, 'Requires Power', '60 W'),
(3, 1, 'State', 'Liquid'),
(4, 2, 'State', 'Solid');

-- --------------------------------------------------------

--
-- Estructura de la taula `recipes`
--
CREATE TABLE `recipes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_json_id` varchar(255) NOT NULL,
  `fabricator_item_id` int(11) NOT NULL,
  `output_item_id` int(11) NOT NULL,
  `output_item_quantity` decimal(10,2) NOT NULL,
  `crafting_time` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipe_json_id` (`recipe_json_id`),
  KEY `fabricator_item_id` (`fabricator_item_id`),
  KEY `output_item_id` (`output_item_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`fabricator_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`output_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de la taula `recipe_ingredients`
--
CREATE TABLE `recipe_ingredients` (
  `recipe_id` int(11) NOT NULL,
  `ingredient_item_id` int(11) NOT NULL,
  `ingredient_quantity` decimal(10,2) NOT NULL,
  PRIMARY KEY (`recipe_id`,`ingredient_item_id`),
  KEY `ingredient_item_id` (`ingredient_item_id`),
  CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipe_ingredients_ibfk_2` FOREIGN KEY (`ingredient_item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Configuració d'AUTO_INCREMENT per a les taules
--
ALTER TABLE `attributes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `categories` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `usuaris` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `recipes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
