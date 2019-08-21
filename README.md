# EASYSWOOLE DEMO
## 关于具体demo在哪
demo/3.x分支对应了EasySwoole3.x版本的demo,3.x主要是easyswoole基础使用的例子，其他使用请看3.x对应的分支。

## 如何运行DEMO

安装项目时请不要覆盖默认的配置文件以及EasySwooleEvent事件注册文件

### 安装easyswoole
```bash
git clone https://github.com/easy-swoole/demo.git demo
cd demo && composer install
php vendor/bin/easyswoole install
```
### 配置数据库
在dev.php中的MYSQL配置项中配置数据库
### 安装项目数据库
运行/Doc/sql.sql文件
### 启动项目
````
php easyswoole start
````
### 访问url
````
管理员登陆:127.0.0.1:9501/Api/Admin/Auth/login?account=xsk&password=123456 
公共请求banner:127.0.0.1:9501/Api/Common/Banner/getAll
会员登陆:127.0.0.1:9501/Api/User/Auth/login?userAccount=xsk&userPassword=123456    
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