CREATE DATABASE  IF NOT EXISTS `event_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `event_db`;
-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: localhost    Database: event_db
-- ------------------------------------------------------
-- Server version	5.7.13-log

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
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `name` varchar(45) NOT NULL,
  `date` varchar(45) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `location` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `time` varchar(45) DEFAULT NULL,
  `event_type` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `App_by_A` int(11) DEFAULT NULL,
  `App_by_SA` int(11) DEFAULT NULL,
  `created_by` varchar(24) NOT NULL,
  `university_id` int(15) DEFAULT NULL,
  `rso_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `created_by_eventA_idx` (`created_by`),
  KEY `university_key_idx` (`university_id`),
  KEY `rso_key_idx` (`rso_id`),
  CONSTRAINT `created_by_eventA` FOREIGN KEY (`created_by`) REFERENCES `admin` (`admin_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `rso_key` FOREIGN KEY (`rso_id`) REFERENCES `rso` (`name`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `university_key` FOREIGN KEY (`university_id`) REFERENCES `university` (`university_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES ('e1showNoType','7/1/2016','entertainment',NULL,'desc1',NULL,NULL,NULL,NULL,NULL,NULL,'u2',NULL,NULL),('e2',NULL,NULL,NULL,NULL,NULL,NULL,'Private',NULL,NULL,NULL,'u2',NULL,NULL),('e3',NULL,NULL,NULL,NULL,NULL,NULL,'Private',NULL,NULL,1,'u2',NULL,NULL),('e4show',NULL,NULL,NULL,NULL,NULL,NULL,'Private',NULL,NULL,1,'u2',1,NULL),('e5',NULL,NULL,NULL,NULL,NULL,NULL,'Public',NULL,NULL,NULL,'u2',NULL,NULL),('e6show',NULL,NULL,NULL,NULL,NULL,NULL,'Public',NULL,NULL,1,'u2',NULL,NULL),('e7',NULL,NULL,NULL,NULL,NULL,NULL,'RSO',NULL,NULL,NULL,'u2',NULL,NULL),('e8showifmem',NULL,NULL,NULL,NULL,NULL,NULL,'RSO',NULL,NULL,NULL,'u2',NULL,'rso1');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-18 13:16:46
