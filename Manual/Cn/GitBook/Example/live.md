# 直播
首先  声明一下，直播的前端代码是抄袭自http://www.workerman.net/camera 。本人并无鄙视workman的意思，只是为了告诉某些无脑喷，easySwoole也可以实现直播。
## 相关代码
### 更改easySwoole运行模式
修改/Conf/Config.php
```
"SERVER_TYPE"=>\Core\Swoole\Config::SERVER_TYPE_WEB_SOCKET,//
```
### 注册相关事件
在Conf/Event.php的beforeWorkerStart事件中注册相关回调。
```
        $server->on("message",function (\swoole_websocket_server $server, \swoole_websocket_frame $frame){
            /*
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
            $data = $frame->data;
            AsyncTaskManager::getInstance()->add(function ()use($list,$data){
                foreach ( $list as $fd) {
                    Server::getInstance()->getServer()->push($fd,$data);
                }
            });
        });

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
            $response->status(101);
            $response->end();
//            Server::getInstance()->getServer()->push($request->fd,"hello world,your fd is ".$request->fd);
        });
```
### 前端界面
/App/Static/camera.html
```
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>live cam 录像页面</title>
</head>
<body>
<video autoplay id="sourcevid" style="width:320;height:240px"></video>
<br>
提示：最好用火狐测试，谷歌浏览器升级了安全策略，谷歌浏览器只能在https下才能利用html5打开摄像头。

<canvas id="output" style="display:none"></canvas>

<script type="text/javascript" charset="utf-8">

    var socket = new WebSocket("ws://"+document.domain+":9501");
    var back = document.getElementById('output');
    var backcontext = back.getContext('2d');
    var video = document.getElementsByTagName('video')[0];

    var success = function(stream){
        video.src = window.URL.createObjectURL(stream);
    }

    socket.onopen = function(){
        draw();
    }

    var draw = function(){
        try{
            backcontext.drawImage(video,0,0, back.width, back.height);
        }catch(e){
            if (e.name == "NS_ERROR_NOT_AVAILABLE") {
                return setTimeout(draw, 100);
            } else {
                throw e;
            }
        }
        if(video.src){
            socket.send(back.toDataURL("image/jpeg", 0.5));
        }
        setTimeout(draw, 100);
    }
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
        navigator.mozGetUserMedia || navigator.msGetUserMedia;
    navigator.getUserMedia({video:true, audio:false}, success, console.log);
</script>
</body>
</html>

```

/App/Static/index.html
```
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>live cam 接收页面</title>
</head>
<body>
<img id="receiver" style="width:320px;height:240px"/>
<br><br>如果显示空白，说明当前没有人在直播，<a href="/camera.html" target="_blank">点击这里直播</a>
<script type="text/javascript" charset="utf-8">
    var receiver_socket = new WebSocket("ws://"+document.domain+":9501");
    var image = document.getElementById('receiver');
    receiver_socket.onmessage = function(data)
    {
        image.src=data.data;
    }
</script>
</body>
</html>

```

### 新建控制器
```
namespace App\Controller;


use Core\AbstractInterface\AbstractController;
use Core\Http\Message\Status;

class Index extends AbstractController
{
    function index()
    {
        // TODO: Implement index() method.
        $content = file_get_contents(ROOT."/App/Static/index.html");
        $this->response()->write($content);
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFound() method.
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }

    function camera(){
        $content = file_get_contents(ROOT."/App/Static/camera.html");
        $this->response()->write($content);
    }


}
```

### 代码准备完毕

执行 php server start 启动easySwoole
- http://localhost:9501/  直播观看界面
- http://localhost:9501/camera/index.html  直播界面

![example](/Resource/Usage/live.png)


> 注意：本代码仅为实例代码，展示了基础原理。未做房间号、缓存等处理，请勿用于生产环境

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
