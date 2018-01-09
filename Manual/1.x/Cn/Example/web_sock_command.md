# 自定义WEB SOCKET 命令解析
EasySwoole为了方便用户使用WEB_SOCKET进行开发，同样封装了对Swoole的sock操作。
## 定义命令解析
```
namespace App\Model\WebSock;


use Core\Component\Socket\AbstractInterface\AbstractClient;
use Core\Component\Socket\AbstractInterface\AbstractCommandParser;
use Core\Component\Socket\Common\Command;

class Parser extends AbstractCommandParser
{

    function parser(Command $result, AbstractClient $client, $rawData)
    {
        // TODO: Implement parser() method.
        //这里的解析规则是与客户端匹配的，等会请看客户端代码
        $js = json_decode($rawData,1);
        if(is_array($js)){
            if(isset($js['action'])){
                $result->setCommand($js['action']);
            }
            if(isset($js['content'])){
                $result->setMessage($js['content']);
            }
        }
    }
}
```
## 定义命令注册类
```

namespace App\Model\WebSock;


use Core\Component\Socket\AbstractInterface\AbstractCommandRegister;
use Core\Component\Socket\Client\TcpClient;
use Core\Component\Socket\Common\Command;
use Core\Component\Socket\Common\CommandList;
use Core\Swoole\AsyncTaskManager;
use Core\Swoole\Server;

class Register extends AbstractCommandRegister
{

    function register(CommandList $commandList)
    {
        // TODO: Implement register() method.
        $commandList->addCommandHandler('who',function (Command $command,TcpClient $client){
            return 'your fd is '.$client->getFd();
        });
        $commandList->addCommandHandler('sendTo',function (Command $command,TcpClient $client){
            $dest = intval($command->getMessage());
            $info =  Server::getInstance()->getServer()->connection_info($dest);
            if($info['websocket_status']){
                Server::getInstance()->getServer()->push($dest,'you receive a message from '.$client->getFd());
                return 'send success';
            }else{
                return 'fd error';
            }
        });
        $commandList->addCommandHandler('broadcast',function (Command $command){
            /*
               * 注意   本example未引入redis来做fd信息记录，因此每次采用遍历的形式来获取结果，
               * 仅供思路参考，不建议在生产环节使用
             */
            $message = $command->getMessage();
            $list = array();
            foreach (Server::getInstance()->getServer()->connections as $fd){
                $info =  Server::getInstance()->getServer()->connection_info($fd);
                if($info['websocket_status']){
                    $list[] = $fd;
                }
            }
            //广播属于重任务，交给Task执行
            AsyncTaskManager::getInstance()->add(function ()use ($list,$message){
                foreach ( $list as $fd) {
                    Server::getInstance()->getServer()->push($fd,"this is broadcast :{$message}");
                }
            });
        });
    }
}
```
## 添加定时广播
```
function onWorkerStart(\swoole_server $server, $workerId)
    {
        // TODO: Implement onWorkerStart() method.
        //如何避免定时器因为进程重启而丢失
        //例如，我第一个进程，添加一个10秒的定时器
        if($workerId == 0){
            Timer::loop(3*1000,function (){
                /*
                * 注意   本example未引入redis来做fd信息记录，因此每次采用遍历的形式来获取结果，
                * 仅供思路参考，不建议在生产环节使用
                 */
                $list = array();
                foreach (Server::getInstance()->getServer()->connections as $fd){
                    $info =  Server::getInstance()->getServer()->connection_info($fd);
                    if($info['websocket_status']){
                        $list[] = $fd;
                    }
                }
                //广播属于重任务，交给Task执行
                AsyncTaskManager::getInstance()->add(function ()use ($list){
                    foreach ( $list as $fd) {
                        Server::getInstance()->getServer()->push($fd,"this is tick broadcast ");
                    }
                });
            });
        }
    }
```

## 添加监听
```
function beforeWorkerStart(\swoole_server $server)
    {
        // TODO: Implement beforeWorkerStart() method.
        $server->on("message",function (\swoole_websocket_server $server, \swoole_websocket_frame $frame){
            Dispatcher::getInstance(Register::class,Parser::class)->dispatchWEBSOCK($frame);
        });
    }
```
## 客户端代码
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
            <option value="who">当前fd</option>
            <option value="sendTo">发给指定fd</option>
            <option value="broadcast">广播</option>
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

## 建立客户端控制器
```
namespace App\Controller;


use Core\AbstractInterface\AbstractController;

class Index extends AbstractController
{

    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write(file_get_contents(ROOT."/App/Static/Template/client.html"));
    }

    function onRequest($actionName)
    {
        // TODO: Implement onRequest() method.
    }

    function actionNotFound($actionName = null, $arguments = null)
    {
        // TODO: Implement actionNotFound() method.
        $this->response()->withStatus(404);
    }

    function afterAction()
    {
        // TODO: Implement afterAction() method.
    }
}
```

>注意，请修改配置文件，使得EasySwoole为SERVER_TYPE_WEB_SOCKET模式


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
