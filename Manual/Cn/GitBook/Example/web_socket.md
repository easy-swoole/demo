# Web Socket
EasySwoole的Web Socket 其实是由 swoole_websocket_server实现。若想使用WebSocket,请修改/Conf/Config.php,改变服务模式。
```
"SERVER_TYPE"=>\Core\Swoole\Config::SERVER_TYPE_WEB_SOCKET
```

> 开启WebSocket模式之后，需要注册onMessage事件。

## 相关事件注册
EasySwoole的beforeWorkerStart事件，可以对Server做一些列的补充操作。
```
 $server->on("message",function (\swoole_websocket_server $server, \swoole_websocket_frame $frame){
            Logger::getInstance()->console("receive data ".$frame->data);
            $json = json_decode($frame->data,1);
            if(is_array($json)){
                if($json['action'] == 'who'){
                    //可以获取bind后的uid
                    //var_dump($server->connection_info($frame->fd));
                    $server->push($frame->fd,"your fd is ".$frame->fd);
                }else{
                    $server->push($frame->fd,"this is server and you say :".$json['content']);
                }
            }else{
                $server->push($frame->fd,"command error");
            }
        }
 );
```
> 注册message事件后，客户端发送过来的全部数据包都会被该函数处理。

```
$server->on("handshake",function (\swoole_http_request $request, \swoole_http_response $response){
            //自定定握手规则，没有设置则用系统内置的（只支持version:13的）
            if (!isset($request->header['sec-websocket-key']))
            {
                //'Bad protocol implementation: it is not RFC6455.'
                $response->end();
                return false;
            }
            if (0 === preg_match('#^[+/0-9A-Za-z]{21}[AQgw]==$#', $request->header['sec-websocket-key'])
                || 16 !== strlen(base64_decode($request->header['sec-websocket-key']))
            )
            {
                //Header Sec-WebSocket-Key is illegal;
                $response->end();
                return false;
            }

            $key = base64_encode(sha1($request->header['sec-websocket-key']
                . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true));
            $headers = array(
                'Upgrade'               => 'websocket',
                'Connection'            => 'Upgrade',
                'Sec-WebSocket-Accept'  => $key,
                'Sec-WebSocket-Version' => '13',
                'KeepAlive'             => 'off',
            );
            foreach ($headers as $key => $val)
            {
                $response->header($key, $val);
            }
            //注意 一定要有101状态码，协议规定
            $response->status(101);
            Logger::getInstance()->console('fd is '.$request->fd);
            //再做标记，保证唯一性，此操作可选
            Server::getInstance()->getServer()->bind($request->fd,time().Random::randNumStr(6));
            $response->end();
        }
);
```

> Swoole支持自定义WebSocket 握手规则。若对此握手规则有疑问的，请自行百度RFC规范，查看关于WebSocket的规定。

```
 $server->on("close",function ($ser,$fd){
            Logger::getInstance()->console("client {$fd} close");
        }
 );
```
> 当客户端与服务的断开链接时，均会触发此操作，注意：HTTP 协议也会触发该请求，可以通过server->connection_info()函数来判定链接类型。

## HTTP对WebSocket操作
### 模拟简单的WebSocket客户端
在/App/Static/Template 目录下建立一个websocket_client.html
```
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div>
    <div>
        <p>info below</p>
        <ul  id="line">

        </ul>
    </div>
    <div>
        <select id="action">
            <option value="who">who</option>
            <option value="hello">hello</option>
        </select>
        <input type="text" id="says">
        <button onclick="say()">发送</button>
    </div>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script>
    var wsServer = 'ws://127.0.0.1:9501';
    var websocket = new WebSocket(wsServer);
    window.onload = function () {
        websocket.onopen = function (evt) {
            addLine("Connected to WebSocket server.");
        };

        websocket.onclose = function (evt) {
            addLine("Disconnected");
        };

        websocket.onmessage = function (evt) {
            addLine('Retrieved data from server: ' + evt.data);
        };

        websocket.onerror = function (evt, e) {
            addLine('Error occured: ' + evt.data);
        };
    };
    function addLine(data) {
        $("#line").append("<li>"+data+"</li>");
    }
    function say() {
        var content = $("#says").val();
        var action = $("#action").val();
        $("#says").val('');
        websocket.send(JSON.stringify({
            action:action,
            content:content
        }));
    }
</script>
</html>
```

### 建立对应的测试控制器
```
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/9/27
 * Time: 上午11:58
 */

namespace App\Controller;

use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;
use Core\Component\Logger;
use Core\Swoole\AsyncTaskManager;
use Core\Swoole\Server;

class WebSocket extends AbstractController
{

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write(file_get_contents(ROOT."/App/Static/Template/websocket_client.html"));
    }
    function push(){
        /*
         * url :/webSocket/push/index.html?fd=xxxx
         */
        $fd = $this->request()->getRequestParam("fd");
        $info =  Server::getInstance()->getServer()->connection_info($fd);
        if($info['websocket_status']){
            Logger::getInstance()->console("push data to client {$fd}");
            Server::getInstance()->getServer()->push($fd,"data from server at ".time());
            $this->response()->write("push to fd :{$fd}");
        }else{
            $this->response()->write("fd {$fd} not a websocket");
        }
    }
    function connectionList(){
        /*
         * url:/webSocket/connectionList/index.html
         * 注意   本example未引入redis来做fd信息记录，因此每次采用遍历的形式来获取结果，
         * 仅供思路参考，不建议在生产环节使用
         */
        $list = array();
        foreach (Server::getInstance()->getServer()->connections as $connection){
            $info =  Server::getInstance()->getServer()->connection_info($connection);
            if($info['websocket_status']){
                $list[] = $connection;
            }
        }
        $this->response()->writeJson(200,$list,"this is all websocket list");
    }
    function broadcast(){
        /*
         * url :/webSocket/broadcast/index.html?fds=xx,xx,xx
         */
        $fds = $this->request()->getRequestParam("fds");
        $fds = explode(",",$fds);
        AsyncTaskManager::getInstance()->add(function ()use ($fds){
            foreach ( $fds as $fd) {
                Server::getInstance()->getServer()->push($fd,"this is broadcast");
            }
        });
        $this->response()->write('broadcast to all client');
    }
}
```

> 注意：客户端断线问题要处理好，否则会遇见向一个不存在链接推送数据，导致底层发出waring的问题。此问题不会导致服务出错，但对于业务逻辑与保障数据送达方面，会有影响。

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>
