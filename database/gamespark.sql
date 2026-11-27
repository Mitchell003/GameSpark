-- Maak de GameSpark-database aan als deze nog niet bestaat.
CREATE DATABASE IF NOT EXISTS gamespark
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Gebruik de GameSpark-database voor de SQL-opdrachten hierna.
USE gamespark;

CREATE TABLE IF NOT EXISTS informatie (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    afbeelding VARCHAR(1000) NOT NULL,
    platform ENUM('pc', 'playstation', 'xbox', 'nintendo') NOT NULL,
    genre ENUM('action', 'adventure', 'rpg', 'strategy', 'horror') NOT NULL,
    releasejaar CHAR(4) NOT NULL,
    status ENUM('wishlist', 'inbezit', 'speeltnu', 'uitgespeeld') NOT NULL,
    beoordeling VARCHAR(280) NOT NULL
);

-- Voeg de unieke sleutel ook toe aan bestaande installaties wanneer deze nog ontbreekt.
SET @game_key_exists = (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'informatie'
      AND index_name = 'uq_informatie_naam_releasejaar'
);
SET @game_key_sql = IF(
    @game_key_exists = 0,
    'ALTER TABLE informatie ADD UNIQUE KEY uq_informatie_naam_releasejaar (naam, releasejaar)',
    'SELECT 1'
);
PREPARE add_game_key FROM @game_key_sql;
EXECUTE add_game_key;
DEALLOCATE PREPARE add_game_key;
