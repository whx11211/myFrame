# Host: 127.0.0.1  (Version 5.7.14)
# Date: 2017-12-27 14:14:45
# Generator: MySQL-Front 6.0  (Build 2.20)


#
# Structure for table "loginlist"
#

DROP TABLE IF EXISTS `loginlist`;
CREATE TABLE `loginlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL DEFAULT '0',
  `loginIp` varchar(50) NOT NULL DEFAULT '',
  `loginTime` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '登录时间 UNIX时间戳',
  PRIMARY KEY (`id`),
  KEY `loginIpIdx` (`loginIp`),
  KEY `UserIdIdx` (`userId`),
  KEY `loginTimeIdx` (`loginTime`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

#
# Data for table "loginlist"
#

/*!40000 ALTER TABLE `loginlist` DISABLE KEYS */;
INSERT INTO `loginlist` VALUES (1,1,'::1','1510992113'),(2,1,'::1','1511142649'),(3,1,'::1','1511228022'),(4,1,'::1','1511233092'),(10,1,'::1','1511244164'),(11,1,'::1','1511244813'),(12,1,'::1','1511244982'),(13,1,'::1','1511244988'),(14,1,'::1','1511245002'),(15,1,'::1','1511248454'),(16,1,'::1','1511248467'),(17,1,'::1','1511248485'),(18,1,'::1','1511253728'),(19,1,'::1','1511313968'),(20,1,'::1','1511339717'),(21,1,'::1','1511339807'),(22,1,'::1','1511339878'),(23,1,'::1','1511437146'),(24,1,'::1','1511492407'),(25,1,'::1','1511783140'),(26,1,'::1','1511868099'),(27,1,'::1','1512041704'),(28,1,'::1','1512044669'),(29,1,'::1','1512045429'),(30,1,'::1','1512126830'),(31,1,'::1','1512127506'),(32,1,'::1','1512193274'),(33,1,'::1','1512196489'),(34,1,'::1','1512197439'),(35,2,'::1','1512202288'),(36,1,'::1','1512375563'),(37,2,'::1','1512387316'),(38,12,'::1','1512388559'),(39,12,'::1','1512388795'),(40,2,'::1','1512548047'),(41,2,'::1','1512645524'),(42,2,'::1','1512697219'),(43,2,'::1','1512800951'),(44,2,'::1','1512955815'),(45,2,'::1','1512956758'),(46,2,'::1','1512992730'),(47,2,'::1','1512994680'),(48,2,'::1','1512994714'),(49,2,'::1','1513078292'),(50,2,'::1','1513078303'),(51,2,'::1','1513078372'),(52,2,'::1','1513078432'),(53,2,'::1','1513078672'),(54,2,'::1','1513315311'),(55,2,'::1','1513685146');
/*!40000 ALTER TABLE `loginlist` ENABLE KEYS */;

#
# Structure for table "menu"
#

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sort` int(11) unsigned NOT NULL DEFAULT '0',
  `func` varchar(128) NOT NULL DEFAULT '',
  `parent` int(11) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(32) NOT NULL DEFAULT '',
  `desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='功能表';

#
# Data for table "menu"
#

/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,1001,'System',0,'fa fa-cogs','系统管理'),(2,1100,'System/user',1,'fa fa-user','用户管理'),(3,1300,'System/loginList',1,'fa fa-list-alt','登录记录'),(6,2001,'tt',0,'fa  fa-plane','2221'),(7,2100,'ffd',6,'','44'),(8,2200,'334',7,'fa fa-wechat',''),(9,2300,'444',7,'fa fa-steam',''),(10,1200,'System/role',1,'fa  fa-registered',''),(11,2300,'2211',6,'','');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

#
# Structure for table "role"
#

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) NOT NULL DEFAULT '',
  `roleDesc` varchar(1024) NOT NULL DEFAULT '',
  `privileges` varchar(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='角色表';

#
# Data for table "role"
#

/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'超级管理员','','1,2,10,3,6,7,8,9,11'),(3,'运营组','','6,7,8,9,11');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

#
# Structure for table "user"
#

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userName` varchar(64) NOT NULL DEFAULT '',
  `pwd` char(32) NOT NULL DEFAULT '',
  `lastLoginTime` int(11) unsigned NOT NULL DEFAULT '0',
  `roleId` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login_name_idx` (`userName`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户表';

#
# Data for table "user"
#

INSERT INTO `user` VALUES (1,'admin','92d9ea2abcbefbd549a110fd115e4875',1512375563,1),(2,'whx','cc57e350f2a2bb83e2da4bbf623aa045',1513685146,1),(12,'test','60474c9c10d7142b7508ce7a50acf414',1512388795,1),(13,'whxe','cc57e350f2a2bb83e2da4bbf623aa045',0,3);
