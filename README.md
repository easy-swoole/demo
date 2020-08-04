# 微聊

EASYSWOOLE 聊天室DEMO

## 在线体验

[在线DEMO演示站](http://chat.evalor.cn/)

## 安装

安装时遇到提示是否覆盖 `EasySwooleEvent.php` 请选择否 (输入 n 回车)

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-chat
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload
```

## 配置

修改 `dev.php` 内的配置项，改为自己服务器的信息

```ini
'HOST' => 'http://127.0.0.1:9501',
'WEBSOCKET_HOST' => 'ws://127.0.0.1:9501',
```

## 启动

```bash
php easyswoole start
```
