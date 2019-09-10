-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: joga
-- ------------------------------------------------------
-- Server version	5.7.27-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `carousel`
--

DROP TABLE IF EXISTS `carousel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_czech_ci DEFAULT '',
  `path` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `view_order` int(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carousel`
--

LOCK TABLES `carousel` WRITE;
/*!40000 ALTER TABLE `carousel` DISABLE KEYS */;
INSERT INTO `carousel` VALUES (1,'test','sdfasfa','1ee114e53b78a8dac5826e46f59ce4f3b93c1565.png',1,1),(2,'rfgsdgd','dfsgdfgdf','bfc9cbeb5160e9df5e16b01d90f4988a4c7e27bd.png',1,0);
/*!40000 ALTER TABLE `carousel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `informations`
--

DROP TABLE IF EXISTS `informations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `informations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `content` text COLLATE utf8_czech_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `informations`
--

LOCK TABLES `informations` WRITE;
/*!40000 ALTER TABLE `informations` DISABLE KEYS */;
INSERT INTO `informations` VALUES (1,0,'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse fermentum tempus odio congue bibendum. Phasellus eu mi eu turpis accumsan malesuada. Donec at purus elementum, sodales mi eu, fermentum nisl. Morbi erat nulla, euismod sit amet ligula et, dictum ultricies mauris. Interdum et malesuada fames ac ante ipsum primis in faucibus. Curabitur viverra mi vitae dui sagittis vehicula. Integer dictum accumsan odio id ullamcorper. Suspendisse mauris nibh, vehicula ac porta non, dignissim at augue. Donec rutrum ut dui sed posuere.\n\nCras tristique, purus id volutpat iaculis, ante erat auctor mi, non condimentum tortor libero a nunc. Nunc malesuada dolor ut dolor ultricies elementum. Duis quis gravida quam. Ut mattis condimentum enim, nec facilisis erat pulvinar ut. Nunc cursus ut odio eget consequat. Sed at mauris id ipsum luctus euismod. Donec ac diam quis ipsum pharetra posuere. Vivamus non dictum dolor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin mattis sollicitudin tortor, vel pulvinar mi molestie ut. Donec ut justo eu lacus rutrum aliquet ut eu mi. Suspendisse potenti. Ut tempor urna a elit luctus scelerisque. Integer in gravida lacus. Curabitur scelerisque mi sit amet ullamcorper ornare. Donec blandit vestibulum elementum.'),(2,1,'Maecenas ultricies luctus est, sed dapibus ante suscipit eget. Nullam a vehicula lacus. Quisque quis dui et lacus elementum feugiat. Integer finibus quam magna, in mollis ligula efficitur sed. Nam vulputate metus quis leo auctor, sit amet venenatis dui maximus. Nam eu cursus ex, nec venenatis nisl. Integer ornare tortor non risus ornare dapibus. Integer et consectetur ex. Aliquam tincidunt felis ut varius tristique. Integer at sollicitudin elit. Donec venenatis viverra erat, non viverra arcu faucibus ac.\n\nCurabitur ac iaculis augue. Nunc scelerisque augue nisl, id porta nunc ultrices a. Aenean consequat interdum egestas. Cras a purus a sem rhoncus commodo vel vel ante. Fusce et eros ac odio luctus tempor in sed tellus. Aliquam elementum quis justo a egestas. Vivamus auctor eget dui ut eleifend. Donec vitae convallis tortor. Morbi pharetra non erat ut maximus. Phasellus ac rutrum enim. Nam vitae tellus dignissim, aliquam risus eu, ultrices nibh. Cras vehicula ipsum eu diam semper finibus. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Suspendisse at rhoncus mi. Nulla varius faucibus odio id dapibus. Proin hendrerit elementum magna nec gravida.');
/*!40000 ALTER TABLE `informations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lecture_reservations`
--

DROP TABLE IF EXISTS `lecture_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lecture_reservations` (
  `lecture` int(11) NOT NULL,
  `client` int(11) NOT NULL,
  PRIMARY KEY (`lecture`,`client`),
  KEY `fk_client` (`client`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecture_reservations`
--

LOCK TABLES `lecture_reservations` WRITE;
/*!40000 ALTER TABLE `lecture_reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `lecture_reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lecture_type`
--

DROP TABLE IF EXISTS `lecture_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lecture_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecture_type`
--

LOCK TABLES `lecture_type` WRITE;
/*!40000 ALTER TABLE `lecture_type` DISABLE KEYS */;
INSERT INTO `lecture_type` VALUES (1,'děti',50),(2,'dospělí',150);
/*!40000 ALTER TABLE `lecture_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lecture_types`
--

DROP TABLE IF EXISTS `lecture_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lecture_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `price` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecture_types`
--

LOCK TABLES `lecture_types` WRITE;
/*!40000 ALTER TABLE `lecture_types` DISABLE KEYS */;
INSERT INTO `lecture_types` VALUES (1,'děti',150);
/*!40000 ALTER TABLE `lecture_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lectures`
--

DROP TABLE IF EXISTS `lectures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainer` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `time_start` int(11) NOT NULL,
  `time_end` int(11) NOT NULL,
  `max_persons` int(11) DEFAULT NULL,
  `place` text COLLATE utf8_czech_ci NOT NULL,
  `published` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_trainer` (`trainer`),
  KEY `fk_type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lectures`
--

LOCK TABLES `lectures` WRITE;
/*!40000 ALTER TABLE `lectures` DISABLE KEYS */;
/*!40000 ALTER TABLE `lectures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT 'uživatel',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'email0','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',2,'hlavní admin'),(2,'email','$argon2i$v=19$m=1024,t=2,p=2$MmcvL3hJSUtvS1JyTHdWMw$7HtCPUhEcVLlgns6EJmqkUASf9S1K2GuRfEm6yMWgjE',2,'cvičitel'),(3,'account','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(4,'email2','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(5,'email3','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(6,'email4','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(7,'email5','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(8,'email6','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(9,'email7','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(10,'email8','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(11,'email9','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(12,'email10','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(13,'email11','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(14,'email12','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(15,'email13','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(16,'email14','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(17,'email15','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(18,'email16','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(19,'email17','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel'),(20,'email18','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-10 19:01:40
