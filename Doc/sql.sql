/*
Navicat MySQL Data Transfer

Source Server         : x.cn
Source Server Version : 50562
Source Host           : x.cn:3306
Source Database       : demo

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2020-01-14 09:44:13
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for application_list
-- ----------------------------
DROP TABLE IF EXISTS `application_list`;
CREATE TABLE `application_list` (
                                  `appId` int(11) NOT NULL AUTO_INCREMENT,
                                  `appName` varchar(32) DEFAULT NULL,
                                  PRIMARY KEY (`appId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of application_list
-- ----------------------------
INSERT INTO `application_list` VALUES ('1', '测试应用(仙士可博客)');

-- ----------------------------
-- Table structure for user_application_login_list
-- ----------------------------
DROP TABLE IF EXISTS `user_application_login_list`;
CREATE TABLE `user_application_login_list` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                             `appId` int(11) DEFAULT NULL,
                                             `userId` int(11) DEFAULT NULL,
                                             `appSecret` varchar(32) DEFAULT NULL,
                                             `expireTime` int(11) DEFAULT NULL COMMENT '失效时间',
                                             PRIMARY KEY (`id`),
                                             UNIQUE KEY `appId` (`appId`,`userId`) USING BTREE,
                                             KEY `appId_2` (`appId`,`appSecret`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



-- ----------------------------
-- Records of user_application_login_list
-- ----------------------------
INSERT INTO `user_application_login_list` VALUES ('1', null, null, null, null);

-- ----------------------------
-- Table structure for user_list
-- ----------------------------
DROP TABLE IF EXISTS `user_list`;
CREATE TABLE `user_list` (
                           `userId` int(11) NOT NULL AUTO_INCREMENT,
                           `userAccount` varchar(18) DEFAULT NULL COMMENT '会员账号',
                           `userPassword` varchar(32) DEFAULT NULL COMMENT '会员密码',
                           `userKey` varchar(255) DEFAULT NULL COMMENT '用户登录标识',
                           PRIMARY KEY (`userId`),
                           KEY `userKey` (`userKey`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_list
-- ----------------------------
INSERT INTO `user_list` VALUES ('1', 'tioncico', 'e10adc3949ba59abbe56e057f20f883e', null);
