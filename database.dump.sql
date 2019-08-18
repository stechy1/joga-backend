-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Počítač: 127.0.0.1:3306
-- Vytvořeno: Ned 18. srp 2019, 12:21
-- Verze serveru: 5.7.23
-- Verze PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `joga`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `carousel`
--

DROP TABLE IF EXISTS `carousel`;
CREATE TABLE IF NOT EXISTS `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT '',
  `path` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `view_order` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `carousel`
--

INSERT INTO `carousel` (`id`, `name`, `description`, `path`, `enabled`, `view_order`) VALUES
(1, 'aaaa', 'fsdafasdf', '89710f6995e0d3ae27708d524a201cde38574b8f.png', 1, 0),
(2, 'sdfasfsadfsa', 'fasfsdaf', 'fde4ebbf4fa3accdd9ef02d98423644a2d305afe.png', 1, 4),
(3, 'gfdgs', 'gsdgfds', '96f81ab4a8ee6929b4e7beb448d75a719dbd3024.png', 1, 1),
(4, 'fsadf', 'fsafsfsdfsgtghrfthbr', 'ffd8f8562851e5ea92e6f3634dc62281a4431103.png', 1, 3),
(5, 'bwaefrwaedfrw', 'sfgvsdfgvgvrtrt', '7e61fe8e7b98f12c553d8a2c55690ceb15c60d9d.png', 1, 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `informations`
--

DROP TABLE IF EXISTS `informations`;
CREATE TABLE IF NOT EXISTS `informations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `content` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `informations`
--

INSERT INTO `informations` (`id`, `type`, `content`) VALUES
(1, 0, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse fermentum tempus odio congue bibendum. Phasellus eu mi eu turpis accumsan malesuada. Donec at purus elementum, sodales mi eu, fermentum nisl. Morbi erat nulla, euismod sit amet ligula et, dictum ultricies mauris. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur viverra mi vitae dui sagittis vehicula. Integer dictum accumsan odio id ullamcorper. Suspendisse mauris nibh, vehicula ac porta non, dignissim at augue. Donec rutrum ut dui sed posuere.\n\nCras tristique, purus id volutpat iaculis, ante erat auctor mi, non condimentum tortor libero a nunc. Nunc malesuada dolor ut dolor ultricies elementum. Duis quis gravida quam. Ut mattis condimentum enim, nec facilisis erat pulvinar ut. Nunc cursus ut odio eget consequat. Sed at mauris id ipsum luctus euismod. Donec ac diam quis ipsum pharetra posuere. Vivamus non dictum dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin mattis sollicitudin tortor, vel pulvinar mi molestie ut. Donec ut justo eu lacus rutrum aliquet ut eu mi. Suspendisse potenti. Ut tempor urna a elit luctus scelerisque. Integer in gravida lacus. Curabitur scelerisque mi sit amet ullamcorper ornare. Donec blandit vestibulum elementum.'),
(2, 1, 'Maecenas ultricies luctus est, sed dapibus ante suscipit eget. Nullam a vehicula lacus. Quisque quis dui et lacus elementum feugiat. Integer finibus quam magna, in mollis ligula efficitur sed. Nam vulputate metus quis leo auctor, sit amet venenatis dui maximus. Nam eu cursus ex, nec venenatis nisl. Integer ornare tortor non risus ornare dapibus. Integer et consectetur ex. Aliquam tincidunt felis ut varius tristique. Integer at sollicitudin elit. Donec venenatis viverra erat, non viverra arcu faucibus ac.\n\nCurabitur ac iaculis augue. Nunc scelerisque augue nisl, id porta nunc ultrices a. Aenean consequat interdum egestas. Cras a purus a sem rhoncus commodo vel vel ante. Fusce et eros ac odio luctus tempor in sed tellus. Aliquam elementum quis justo a egestas. Vivamus auctor eget dui ut eleifend. Donec vitae convallis tortor. Morbi pharetra non erat ut maximus. Phasellus ac rutrum enim. Nam vitae tellus dignissim, aliquam risus eu, ultrices nibh. Cras vehicula ipsum eu diam semper finibus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse at rhoncus mi. Nulla varius faucibus odio id dapibus. Proin hendrerit elementum magna nec gravida.');

-- --------------------------------------------------------

--
-- Struktura tabulky `lectures`
--

DROP TABLE IF EXISTS `lectures`;
CREATE TABLE IF NOT EXISTS `lectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `start_time` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `max_persons` int(11) DEFAULT NULL,
  `place` text COLLATE utf8_czech_ci NOT NULL,
  `published` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_trainer` (`trainer`),
  KEY `fk_type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `lectures`
--

INSERT INTO `lectures` (`id`, `trainer`, `type`, `start_time`, `duration`, `max_persons`, `place`, `published`) VALUES
(11, 2, 1, 1565762400, 45, 10, 'Tělocvična školka', 0),
(12, 2, 1, 1565935200, 45, 10, 'Tělocvična školka', 0),
(10, 2, 1, 1564639200, 45, 10, 'dsaf', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `lecture_reservations`
--

DROP TABLE IF EXISTS `lecture_reservations`;
CREATE TABLE IF NOT EXISTS `lecture_reservations` (
  `lecture` int(11) NOT NULL,
  `client` int(11) NOT NULL,
  PRIMARY KEY (`lecture`,`client`),
  KEY `fk_client` (`client`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `lecture_reservations`
--

INSERT INTO `lecture_reservations` (`lecture`, `client`) VALUES
(1, 3),
(1, 6);

-- --------------------------------------------------------

--
-- Struktura tabulky `lecture_type`
--

DROP TABLE IF EXISTS `lecture_type`;
CREATE TABLE IF NOT EXISTS `lecture_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `lecture_type`
--

INSERT INTO `lecture_type` (`id`, `name`, `price`) VALUES
(1, 'děti', 50),
(2, 'dospělí', 150);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT 'uživatel',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `name`) VALUES
(1, 'email0', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 2, 'hlavní admin'),
(2, 'email', '$argon2i$v=19$m=1024,t=2,p=2$MmcvL3hJSUtvS1JyTHdWMw$7HtCPUhEcVLlgns6EJmqkUASf9S1K2GuRfEm6yMWgjE', 2, 'cvičitel'),
(3, 'account', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(4, 'email2', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(5, 'email3', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(6, 'email4', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(7, 'email5', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(8, 'email6', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(9, 'email7', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(10, 'email8', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(11, 'email9', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(12, 'email10', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(13, 'email11', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(14, 'email12', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(15, 'email13', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(16, 'email14', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(17, 'email15', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(18, 'email16', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(19, 'email17', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel'),
(20, 'email18', '$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY', 1, 'uživatel');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
