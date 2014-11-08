-- MySQL dump 10.11
--
-- Host: localhost    Database: fw_db
-- ------------------------------------------------------
-- Server version	5.0.83-0ubuntu3

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
-- Table structure for table `configurations`
--

DROP TABLE IF EXISTS `configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `variable_type` varchar(20) character set utf8 collate utf8_bin NOT NULL,
  `key` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `value` text character set utf8 collate utf8_bin,
  `description` text character set utf8 collate utf8_bin,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configurations`
--

LOCK TABLES `configurations` WRITE;
/*!40000 ALTER TABLE `configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `domain_name` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `fixed` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `subdomain` (`domain_name`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (1,'fw',1),(13,'localhost',0),(20,'dsadas.com',0);
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `layout` varchar(50) character set utf8 collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `layout` (`layout`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Administrators','admins'),(3,'Users','index');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups_permissions`
--

DROP TABLE IF EXISTS `groups_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups_permissions` (
  `group_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`group_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups_permissions`
--

LOCK TABLES `groups_permissions` WRITE;
/*!40000 ALTER TABLE `groups_permissions` DISABLE KEYS */;
INSERT INTO `groups_permissions` VALUES (0,7),(0,8),(0,17),(0,18),(1,1),(1,6),(1,10),(3,5),(3,6),(3,8),(3,10);
/*!40000 ALTER TABLE `groups_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `domain_id` bigint(20) unsigned NOT NULL default '1',
  `friendly_url` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `real_url` varchar(255) character set utf8 collate utf8_bin default NULL,
  `title` varchar(255) character set utf8 collate utf8_bin default NULL,
  `description` text character set utf8 collate utf8_bin,
  `keywords` text character set utf8 collate utf8_bin,
  `enable_social_bookmarking` tinyint(1) unsigned NOT NULL default '0',
  `parent_id` bigint(20) NOT NULL default '0',
  `category_id` bigint(20) unsigned NOT NULL default '0',
  `lang` char(2) character set utf8 collate utf8_bin NOT NULL default 'en',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `subdomain_id_2` (`domain_id`,`friendly_url`),
  KEY `subdomain_id` (`domain_id`),
  KEY `enable_social_bookmarking` (`enable_social_bookmarking`),
  KEY `parent_id` (`parent_id`),
  KEY `category_id` (`category_id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links`
--

LOCK TABLES `links` WRITE;
/*!40000 ALTER TABLE `links` DISABLE KEYS */;
INSERT INTO `links` VALUES (1,1,'/','/home/showonepage/1.html','Home Page','Welcome the Framework Home Page','home page framework php developer',1,0,1,'en');
/*!40000 ALTER TABLE `links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `links_categories`
--

DROP TABLE IF EXISTS `links_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `links_categories` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `lang` char(2) character set utf8 collate utf8_bin NOT NULL default 'en',
  PRIMARY KEY  (`id`),
  KEY `lang` (`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `links_categories`
--

LOCK TABLES `links_categories` WRITE;
/*!40000 ALTER TABLE `links_categories` DISABLE KEYS */;
INSERT INTO `links_categories` VALUES (1,'Welcome','en'),(2,'Products','en');
/*!40000 ALTER TABLE `links_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` tinytext character set utf8 collate utf8_bin,
  `body` text character set utf8 collate utf8_bin,
  `contact_email` varchar(255) character set utf8 collate utf8_bin default NULL,
  `fixed` tinyint(4) NOT NULL default '0',
  `last_updated` bigint(20) unsigned NOT NULL default '0',
  `link_id` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fixed` (`fixed`),
  KEY `last_updated` (`last_updated`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'Welcome','Welcome Page<br>\r\n','',0,1192108502,0),(2,'Contact Us','Our Address<br>','a.magdy@birdict.com',0,1192108598,0);
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages_pictures`
--

DROP TABLE IF EXISTS `pages_pictures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages_pictures` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `page_id` bigint(20) unsigned NOT NULL default '0',
  `pic` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `is_default` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `page_id` (`page_id`),
  KEY `is_default` (`is_default`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages_pictures`
--

LOCK TABLES `pages_pictures` WRITE;
/*!40000 ALTER TABLE `pages_pictures` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages_pictures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `controller` varchar(50) character set utf8 collate utf8_bin NOT NULL,
  `action` varchar(50) character set utf8 collate utf8_bin NOT NULL,
  `extra_params` tinytext character set utf8 collate utf8_bin,
  `allow` tinyint(1) unsigned NOT NULL default '0',
  `description` text character set utf8 collate utf8_bin,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'*','*','',1,'Full Control'),(5,'user','*','',1,'User Manages his own account'),(6,'user','login','',0,'Deny Login for already logged in users'),(7,'user','login','',1,'Allow login for visitors'),(8,'home','*','',1,'Allow viewing the home page for all users'),(10,'page','delete','id=1',0,'Deny Deleting the home page'),(17,'ajax','*','',1,'Allow AJAX requests for visitors'),(18,'guestbook','*','',1,'Allow Guest Book Actions');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `see_also_links`
--

DROP TABLE IF EXISTS `see_also_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `see_also_links` (
  `name` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `url` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `link_id` bigint(20) unsigned NOT NULL,
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `see_also_links`
--

LOCK TABLES `see_also_links` WRITE;
/*!40000 ALTER TABLE `see_also_links` DISABLE KEYS */;
INSERT INTO `see_also_links` VALUES ('Yahoo','http://yahoo.com',1),('Hot Mail','http://www.hotmail.com',1);
/*!40000 ALTER TABLE `see_also_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `sid` char(32) character set utf8 collate utf8_bin NOT NULL,
  `last_accessed` bigint(20) unsigned NOT NULL default '0',
  `session_data` longtext character set utf8 collate utf8_bin,
  PRIMARY KEY  (`sid`),
  KEY `last_accessed` (`last_accessed`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('7890b3958afe500acfe34a9218584bc6',1274430529,'lang|s:2:\"en\";referrer|N;user_id|i:6;group_id|s:1:\"1\";__POST_REPITITION_STOPPER_TIMESTAMP|s:21:\"0.10633600 1274430496\";layout|s:6:\"admins\";__GET_REPITITION_STOPPER_OLD|a:11:{s:10:\"controller\";s:4:\"user\";s:6:\"action\";s:5:\"login\";s:7:\"CAKEPHP\";s:32:\"cb0abc953469f15e64a46a9732f2a7b2\";s:10:\"phpMyAdmin\";s:27:\"mwswxr4gfRQzwzWMiNnHSkmGgg0\";s:9:\"PHPSESSID\";s:32:\"7890b3958afe500acfe34a9218584bc6\";s:12:\"admin_panels\";s:42:\"plan_div|0#translation_div|0#account_div|1\";s:18:\"/trunk/user/login/\";s:0:\"\";s:35:\"__POST_REPITITION_STOPPER_TIMESTAMP\";s:21:\"0.10633600 1274430496\";s:19:\"__is_form_submitted\";s:3:\"yes\";s:4:\"user\";a:2:{s:5:\"email\";s:16:\"admin@domain.com\";s:8:\"password\";s:3:\"123\";}s:4:\"lang\";s:2:\"en\";}__GET_REPITITION_STOPPER_NEW|a:2:{s:6:\"action\";s:4:\"home\";s:10:\"controller\";s:4:\"user\";}');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `session_id` char(32) character set utf8 collate utf8_bin NOT NULL default '00000000000000000000000000000000',
  `email` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `password` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `name` tinytext character set utf8 collate utf8_bin,
  `registration_date` datetime NOT NULL,
  `registration_ip` int(10) unsigned NOT NULL default '0',
  `last_login_date` datetime default NULL,
  `last_login_ip` int(10) unsigned NOT NULL default '0',
  `active` tinyint(3) unsigned NOT NULL default '0',
  `activation_code` varchar(255) character set utf8 collate utf8_bin default NULL,
  `group_id` int(11) NOT NULL,
  `pic` varchar(255) character set utf8 collate utf8_bin default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `session_id` (`session_id`),
  KEY `last_login_date` (`last_login_date`),
  KEY `registration_date` (`registration_date`),
  KEY `active` (`active`),
  KEY `registration_ip` (`registration_ip`),
  KEY `last_login_ip` (`last_login_ip`),
  KEY `group_id` (`group_id`),
  KEY `password` (`password`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (6,'7890b3958afe500acfe34a9218584bc6','admin@domain.com','123','Admin','2007-02-22 19:28:53',2130706433,'2010-05-21 11:28:49',2130706433,1,NULL,1,NULL),(10,'a83c2fb72be6b46f85b3c48e9be8a548','user@domain.com','123','user','2008-07-19 18:30:47',2130706433,'2008-07-19 18:45:18',2130706433,1,'',3,'Array'),(11,'00000000000000000000000000000000','a.magdy@a1works.com','123','Ahmed Magdy','2009-09-29 11:16:31',2130706433,'2009-09-29 11:16:31',0,1,'',3,'1254219377_4ac1de712dbd5_me_small.jpg'),(12,'00000000000000000000000000000000','H.ADEL@BIRDICT.COM','123','H.ADEL@BIRDICT.COM','2009-09-29 12:30:27',2130706433,NULL,0,1,NULL,1,'');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_pics`
--

DROP TABLE IF EXISTS `users_pics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_pics` (
  `user_id` bigint(20) unsigned NOT NULL,
  `pic` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  PRIMARY KEY  (`user_id`,`pic`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_pics`
--

LOCK TABLES `users_pics` WRITE;
/*!40000 ALTER TABLE `users_pics` DISABLE KEYS */;
INSERT INTO `users_pics` VALUES (11,'1');
/*!40000 ALTER TABLE `users_pics` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-21 11:29:35
