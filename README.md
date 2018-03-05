# demo
克隆该项目后，请先执行
- composer install
- 安装easySwoole（安装easyswoole教程请看文档）

## 使用前准备
请确保数修改好数据库配置，并存在以下表
```
CREATE TABLE `user_list` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `session` varchar(45) DEFAULT NULL,
  `addTime` int(11) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userId_UNIQUE` (`userId`),
  UNIQUE KEY `account_UNIQUE` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```