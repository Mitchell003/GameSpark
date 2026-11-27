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
