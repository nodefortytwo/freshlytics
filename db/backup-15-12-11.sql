/*
SQLyog Community v8.6 RC2
MySQL - 5.1.53-community-log : Database - freshlytics
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`freshlytics` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `freshlytics`;

/*Table structure for table `join_query_tweet` */

DROP TABLE IF EXISTS `join_query_tweet`;

CREATE TABLE `join_query_tweet` (
  `jid` int(11) NOT NULL AUTO_INCREMENT,
  `qid` int(11) NOT NULL,
  `tid` bigint(20) NOT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`jid`,`qid`,`tid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

/*Table structure for table `query` */

DROP TABLE IF EXISTS `query`;

CREATE TABLE `query` (
  `qid` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `tags` longtext,
  `users` longtext,
  `tracks` longtext,
  `type` int(11) DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `heartbeat` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`qid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Table structure for table `tweet` */

DROP TABLE IF EXISTS `tweet`;

CREATE TABLE `tweet` (
  `tid` bigint(20) NOT NULL,
  `text` longtext,
  `uid` bigint(20) DEFAULT NULL,
  `screen_name` longtext,
  `created` int(11) DEFAULT NULL,
  `data` longblob,
  `deleted` int(11) DEFAULT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
