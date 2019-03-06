# EASYSWOOLE DEMO

## 如何运行DEMO

安装项目时请不要覆盖默认的配置文件以及EasySwooleEvent事件注册文件

```bash
git clone https://github.com/easy-swoole/demo.git demo
cd demo && composer install
php vendor/bin/easyswoole install
php easyswoole start
```
## 请先查看dev.php,修改数据库配置,防止报错
请新增一个test数据库,以及运行以下sql
 ````sql
 CREATE TABLE `member` (
   `member_id` int(11) NOT NULL AUTO_INCREMENT,
   `mobile` varchar(255) DEFAULT NULL,
   `name` varchar(255) DEFAULT NULL,
   `password` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`member_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 ````

## 请先认真阅读手册 再进行体验

- [EASYSWOOLE在线手册](https://www.easyswoole.com)
- QQ交流群
    - VIP群 579434607 （本群需要付费599元）
    - EasySwoole官方一群 633921431(已满)
    - EasySwoole官方二群 709134628
    
- 商业支持：
    - QQ 291323003
    - EMAIL admin@fosuss.com