# EasySwooleSocketDemo

本`demo`为`EasySwoole`如何注册`Socket`服务器。

初始化：

`composer install`

安装框架：

`php vendor/bin easyswoole install`

一键回车即可。

框架目录：

```bash
├── App
│   ├── HttpController http控制器目录 可忽略
│   │   ├── Index.php
│   │   └── Router.php
│   ├── Parser socket 解析器
│   │   ├── TcpParser.php
│   │   ├── UdpParser.php
│   │   └── WebSocketParser.php
│   ├── TcpController tcp服务控制器
│   │   ├── Base.php
│   │   └── Index.php
│   ├── UdpController udp服务控制器
│   │   ├── Base.php
│   │   └── Index.php
│   ├── WebSocketController websocket服务控制器
│   │   ├── Base.php
│   │   └── Index.php
│   └── WebSocketEvent.php websocket的event
├── EasySwooleEvent.php 主框架Event
├── Test 测试客户端
│   ├── tcp.php
│   ├── udp.php
│   └── websocket.php
```

## Tcp

启动：

`php easyswoole server start -mode=tcp`

测试：

`php Test/tcp.php`

## Udp

提示: `udp`服务器为`EasySwoole`子服务。

启动：

`php easyswoole server start -mode=udp`

测试：

`php Test/udp.php`

## Websocket


启动：

`php easyswoole server start -mode=websocket`

测试：

`php Test/websocket.php`