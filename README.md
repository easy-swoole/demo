# EASYSWOOLE DEMO

同时支持 `WebSocket` 和 `HTTP` 服务的 `demo`。

用户可以在 `App/WebSocketController` 下面写对应的 `WebSocket` 相关业务处理逻辑，在 `App/HttpController` 下面写对应的 `Http` 相关业务处理逻辑

## 安装

安装时遇到提示是否覆盖 `EasySwooleEvent.php`、`dev.php`、`produce.php` 时，请选择否 (输入 `N` 回车)

```bash
git clone https://github.com/easy-swoole/demo.git
cd demo
git checkout 3.x-ws-http
composer install
php vendor/easyswoole/easyswoole/bin/easyswoole install
composer dump-autoload -o
```

执行 `php vendor/easyswoole/easyswoole/bin/easyswoole install` 时，遇到提示时一直输入 `N` 然后回车即可安装成功。如下所示：

```bash
Index.php has already existed, do you want to replace it? [ Y / N (default) ] : N
Router.php has already existed, do you want to replace it? [ Y / N (default) ] : N
dev.php has already existed, do you want to replace it? [ Y / N (default) ] : N
produce.php has already existed, do you want to replace it? [ Y / N (default) ] : N
EasySwooleEvent.php has already existed, do you want to replace it? [ Y / N (default) ] : N
```

## 启动项目

```
php easyswoole server start
```

## 访问 `url`

```
HTTP 服务：
浏览器访问：http://localhost:9501/ (示例请求地址) 即可看到 `EasySwoole` 的欢迎界面。

WebSocket 服务：
使用 `EasySwoole` 官方提供的 `WebSocket` 客户端工具 (http://www.easyswoole.com/wstool.html) 连接服务地址：ws://localhost:9501 (示例请求地址)，即可看到服务端响应 'hello, welcome' 字符串。再向服务端发送 `{"controller":"Index","action":"index","param":[1,2]}` 即可看到服务端响应 "this is index" json字符串。
```

关于 `HTTP 服务` 和 `WebSocket 服务` 的使用，详细请看 [EasySwoole 官网](https://www.easyswoole.com) 的 [HTTP 服务章节](http://www.easyswoole.com/HttpServer/contorller.html) 和 [Socket 服务章节](http://www.easyswoole.com/Socket/tcp.html)。

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
