CREATE TABLE `admin_list` (
  `adminId` int(11) NOT NULL AUTO_INCREMENT,
  `adminName` varchar(15) DEFAULT NULL,
  `adminAccount` varchar(18) DEFAULT NULL,
  `adminPassword` varchar(32) DEFAULT NULL,
  `adminSession` varchar(32) DEFAULT NULL,
  `adminLastLoginTime` int(11) DEFAULT NULL,
  `adminLastLoginIp` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`adminId`),
  UNIQUE KEY `adminAccount` (`adminAccount`),
  KEY `adminSession` (`adminSession`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `admin_list` VALUES ('1', '仙士可', 'xsk', 'e10adc3949ba59abbe56e057f20f883e', '', '1566279458', '192.168.159.1');


CREATE TABLE `user_list` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(32) NOT NULL,
  `userAccount` varchar(18) NOT NULL,
  `userPassword` varchar(32) NOT NULL,
  `phone` varchar(18) NOT NULL,
  `addTime` int(11) DEFAULT NULL,
  `lastLoginIp` varchar(20) DEFAULT NULL,
  `lastLoginTime` int(10) DEFAULT NULL,
  `userSession` varchar(32) DEFAULT NULL,
  `state` tinyint(2) DEFAULT NULL,
  `money` int(10) NOT NULL DEFAULT '0' COMMENT '用户余额',
  `frozenMoney` int(10) NOT NULL DEFAULT '0' COMMENT '冻结余额',
  PRIMARY KEY (`userId`),
  UNIQUE KEY `pk_userAccount` (`userAccount`),
  KEY `userSession` (`userSession`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_list` VALUES ('1', 'xsk', 'xsk', 'e10adc3949ba59abbe56e057f20f883e', '18459537313', null, '192.168.199.113', '1561081989', '0e81873434f94d3217a3a7d6d04d1561', '1', '10000', '0');


CREATE TABLE `banner_list` (
  `bannerId` int(11) NOT NULL AUTO_INCREMENT,
  `bannerName` varchar(32) DEFAULT NULL,
  `bannerImg` varchar(255) NOT NULL COMMENT 'banner图片',
  `bannerDescription` varchar(255) DEFAULT NULL,
  `bannerUrl` varchar(255) DEFAULT NULL COMMENT '跳转地址',
  `state` tinyint(3) DEFAULT NULL COMMENT '状态0隐藏 1正常',
  PRIMARY KEY (`bannerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

