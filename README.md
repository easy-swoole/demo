# EASYSWOOLE DEMO

## 关于具体 demo 在哪

`demo/3.x` 分支对应了 `EasySwoole 3.x` 版本的 `demo`，`3.x` 主要是 `EasySwoole` 基础使用的例子，其他使用请看 `3.x` 对应的分支。

## 如何运行 `DEMO`

安装项目时请不要覆盖默认的配置文件（`dev.php` / `produce.php`）以及 `EasySwooleEvent` 事件注册文件（`EasySwooleEvent.php`）

### 安装 EasySwoole

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload
```

### 配置数据库
在 `dev.php` 中的 `MYSQL` 配置项中配置数据库

### 安装项目数据库
运行 `/Doc/sql.sql` 文件

## 启动项目

### 启动项目

```
php easyswoole server start
```

### 访问 `url`

```bash
管理员登陆: http://127.0.0.1:9501/Api/Admin/Auth/login?account=xsk&password=123456 
公共请求banner: http://127.0.0.1:9501/Api/Common/Banner/getAll
会员登陆: http://127.0.0.1:9501/Api/User/Auth/login?userAccount=xsk&userPassword=123456
```

## 请先认真阅读手册 再进行体验

- [EASYSWOOLE 在线手册](https://www.easyswoole.com)
- QQ 交流群
    - VIP 群 579434607 （本群需要付费599元）
    - EasySwoole 官方一群 633921431(已满)
    - EasySwoole 官方二群 709134628(已满)
    - EasySwoole 官方三群 932625047(已满)
    - EasySwoole 官方四群 779897753(已满)
    - EasySwoole 官方五群 853946743
    
- 商业支持：
    - QQ 291323003
    - EMAIL admin@fosuss.com    
