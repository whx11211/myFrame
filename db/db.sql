#
# Database "my_frame"
#

CREATE DATABASE IF NOT EXISTS `my_frame`;
USE `my_frame`;

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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

#
# Data for table "loginlist"
#

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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='功能表';

#
# Data for table "menu"
#

/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` VALUES (1,1001,'System',0,'fa fa-cogs','系统管理'),(2,1100,'System/user',1,'fa fa-user','用户管理'),(3,1300,'System/loginList',1,'fa fa-list-alt','登录记录'),(6,10001,'tt',0,'fa  fa-plane','2221'),(7,10100,'ffd',6,'','44'),(8,10200,'334',7,'fa fa-wechat',''),(9,10300,'444',7,'fa fa-steam',''),(10,1200,'System/role',1,'fa  fa-registered',''),(11,10300,'2211',6,'',''),(12,2001,'MovieManage',0,'fa fa-feed','论坛管理'),(13,2200,'MovieManage/post',12,'fa fa-list-ul','帖子管理'),(14,2100,'MovieManage/user',12,'fa fa-users','论坛用户管理'),(15,2300,'MovieManage/repost',12,'fa fa-mail-reply-all ','回帖管理');
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
INSERT INTO `role` VALUES (1,'超级管理员','','1,2,10,3,12,14,13,15,6,7,8,9,11'),(3,'运营组','','6,7,8,9,11');
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户表';

#
# Data for table "user"
#

INSERT INTO `user` VALUES (1,'admin','92d9ea2abcbefbd549a110fd115e4875',1515721151,1),(2,'whx','cc57e350f2a2bb83e2da4bbf623aa045',1522215711,1),(12,'test','60474c9c10d7142b7508ce7a50acf414',1512388795,1);
