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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carousel`
--

LOCK TABLES `carousel` WRITE;
/*!40000 ALTER TABLE `carousel` DISABLE KEYS */;
INSERT INTO `carousel` VALUES (3,'Panorama','Toto je na lanzarote','e222133a8ccd0ebce750f92a2deb865cfa370c2c.jpg',1,0),(4,'Moře','Pohled na moře','3dd251987d81c0e798dd0f59502126bb35dc6160.jpg',1,1),(5,'Vodopád','Velký pěkný vodopád','fae6e90b04c889d09dca33d0bff8400715d0ca36.jpg',1,3),(6,'Zákusky','Jeden velký stál 5€, malý za 1.5€','8046571112385af24473533e325c268a55aca0c9.jpg',1,4),(7,'Sevilla','Zde se natáčely mimo jiné i hvězdné války','25743d88e90d9329a178f9b6d22d2f7a9f8c6e9b.jpg',1,2),(8,'Katedrála','Epický pohled','5f9115c8ca8f71ca9a5451763eb5f6d8777081b5.jpg',1,5);
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
  `description` text COLLATE utf8_czech_ci,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lecture_type`
--

LOCK TABLES `lecture_type` WRITE;
/*!40000 ALTER TABLE `lecture_type` DISABLE KEYS */;
INSERT INTO `lecture_type` VALUES (1,'děti','popis pro děti',50),(2,'dospělí','poipasfdf',150);
/*!40000 ALTER TABLE `lecture_type` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lectures`
--

LOCK TABLES `lectures` WRITE;
/*!40000 ALTER TABLE `lectures` DISABLE KEYS */;
INSERT INTO `lectures` VALUES (2,2,1,1567317600,1567320300,10,'dsfasgvsfv',1),(3,2,2,1567320600,1567323300,15,'dfgsdgdf',1),(4,2,1,1567494900,1567499400,7,'gsdgdfbdsfgd',1),(5,2,2,1568186400,1568189100,15,'gsdgsdhbsbhgf',1),(6,2,1,1568207400,1568210400,4,'gdfsgdgfg',0),(8,2,1,1567750200,1567752600,10,'gdsfgbdsgbdf',1),(9,2,1,1567767600,1567770300,10,'gbdshbfdjgjmgh',1);
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
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `first_login` int(11) DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL,
  `banned` int(1) DEFAULT '0',
  `active` int(1) DEFAULT '1',
  `check_code` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `checked` int(1) DEFAULT '0',
  `disabled` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@email.cz','$argon2i$v=19$m=1024,t=2,p=2$Z0t3TFdPNmlvNTJ3bW91WA$5EM9iHXwBaxCdv/k3x2nXsfUpRLGbUZ8CIlscSFunAM',2,'hlavní admin',NULL,1568904187,0,0,NULL,0,0),(2,'cvicitel@email.cz','$argon2i$v=19$m=1024,t=2,p=2$Z0t3TFdPNmlvNTJ3bW91WA$5EM9iHXwBaxCdv/k3x2nXsfUpRLGbUZ8CIlscSFunAM',2,'cvičitel',NULL,NULL,0,0,NULL,0,0),(3,'uzivatel@email.cz','$argon2i$v=19$m=1024,t=2,p=2$Z0t3TFdPNmlvNTJ3bW91WA$5EM9iHXwBaxCdv/k3x2nXsfUpRLGbUZ8CIlscSFunAM',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(4,'email2','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(5,'email3','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(6,'email4','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(7,'email5','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(8,'email6','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(9,'email7','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(10,'email8','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(11,'email9','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(12,'email10','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(13,'email11','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(14,'email12','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(15,'email13','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(16,'email14','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(17,'email15','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(18,'email16','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(19,'email17','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(20,'email18','$argon2i$v=19$m=1024,t=2,p=2$YkpmUDBWcnU1a05Kb0FtVw$xAdfBDwgksvTF0aCZ/VtTHOvSHALNXDkoCkcBAEn0oY',1,'uživatel',NULL,NULL,0,0,NULL,0,0),(21,'admin2@email.cz','$argon2i$v=19$m=1024,t=2,p=2$Z0t3TFdPNmlvNTJ3bW91WA$5EM9iHXwBaxCdv/k3x2nXsfUpRLGbUZ8CIlscSFunAM',1,'Petr Štechmüllerr',1568389453,1568904114,0,0,'8536163d3089db241affb6b91a8dd74af92d9ebf186ba71a6530e6c367aa45f6ce7d7fa0ebb4dfa350bf6e5c2fdc1f6353e2b705c82ac0563be7dc765c8c3f1b',0,0);
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

-- Dump completed on 2019-09-19 18:40:28
