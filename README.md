# EasySwooleSocketDemo

本`demo`为`EasySwoole`如何注册`Socket`服务器。

初始化：

`composer install`

框架目录：

```bash
- App
 - Parser
    TcpParser.php - tcp解析器
    UdpParser.php - udp解析器
    WebsocketParser.php - websocket解析器
 - TcpController tcp控制器目录
 - UdpController udp控制器目录
 - WebSocketController websocket控制器目录
 WebsocketEvent.php websocket自定义握手
- Test
 tcp.php tcp客户端
 udp.php udp客户端
 websocket.php websocket客户端
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