# 微聊

EASYSWOOLE 聊天室DEMO

## 在线体验

[在线DEMO演示站](http://chat.evalor.cn/)

## 安装

安装时遇到提示是否覆盖 `EasySwooleEvent.php` 请选择否 (输入 n 回车)

```bash
git clone https://github.com/easy-swoole/demo.git
git checkout 3.x-chat
composer install
easyswoole install
cp sample.env dev.env
```

## 配置

修改 `dev.env` 内的配置项

```ini
SYSTEM.WS_SERVER_PATH = # 你的ws服务地址 如 : ws://127.0.0.1:9501
REDIS.HOST = 127.0.0.1  # redis服务器地址
REDIS.PORT = 6379       # redis服务器端口
REDIS.AUTH =            # redis服务器密码 (如果没有密码请注释本行)
```

## 启动

```bash
php easyswoole start
```