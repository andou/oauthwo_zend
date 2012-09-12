-- MySQL dump 10.13  Distrib 5.1.63, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: oauthwo
-- ------------------------------------------------------
-- Server version	5.1.63-0+squeeze1

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
-- Table structure for table `authorization_codes`
--

DROP TABLE IF EXISTS `authorization_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authorization_codes` (
  `authorization_code` varchar(1000) NOT NULL,
  `client_id` varchar(22) NOT NULL,
  `resource_owner_id` varchar(500) NOT NULL,
  `scopes` varchar(200) NOT NULL,
  `generation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`authorization_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `client_id` varchar(22) NOT NULL,
  `client_secret` varchar(40) NOT NULL,
  `client_type` enum('web','user-agent','native') NOT NULL,
  `redirect_uri` varchar(500) NOT NULL,
  `client_name` varchar(500) NOT NULL,
  `client_description` longtext,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`client_id`),
  UNIQUE KEY `client_secret` (`client_secret`),
  UNIQUE KEY `redirect_uri` (`redirect_uri`),
  UNIQUE KEY `client_name` (`client_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refresh_tokens`
--

DROP TABLE IF EXISTS `refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refresh_tokens` (
  `refresh_token` varchar(1000) NOT NULL,
  `client_id` varchar(22) NOT NULL,
  `resource_owner_id` varchar(500) NOT NULL,
  `scopes` varchar(200) NOT NULL,
  `generation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refresh_token`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_server`
--

DROP TABLE IF EXISTS `resource_server`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource_server` (
  `resource_server_id` varchar(22) NOT NULL,
  `resource_server_secret` varchar(40) NOT NULL,
  `reference_type` set('cf','mail','nickname') NOT NULL,
  `resource_server_name` varchar(500) NOT NULL,
  `resource_server_endpoint_uri` varchar(1024) NOT NULL,
  PRIMARY KEY (`resource_server_id`),
  UNIQUE KEY `resource_server_secret` (`resource_server_secret`),
  UNIQUE KEY `resource_server_name` (`resource_server_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scope`
--

DROP TABLE IF EXISTS `scope`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scope` (
  `resource_server_id` varchar(22) NOT NULL,
  `scope_id` varchar(100) NOT NULL,
  `scope_description` varchar(250) NOT NULL,
  PRIMARY KEY (`resource_server_id`,`scope_id`),
  UNIQUE KEY `scope_id` (`scope_id`),
  CONSTRAINT `scope_ibfk_3` FOREIGN KEY (`resource_server_id`) REFERENCES `resource_server` (`resource_server_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` varchar(500) NOT NULL,
  `user_password` char(32) NOT NULL,
  `creation_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_reference`
--

DROP TABLE IF EXISTS `user_reference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_reference` (
  `user_id` varchar(500) NOT NULL,
  `resource_server_id` varchar(22) NOT NULL,
  `user_reference` varchar(200) NOT NULL,
  PRIMARY KEY (`user_id`,`resource_server_id`),
  KEY `resource_server_id` (`resource_server_id`),
  CONSTRAINT `user_reference_ibfk_1` FOREIGN KEY (`resource_server_id`) REFERENCES `resource_server` (`resource_server_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_reference_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-03 14:56:41
