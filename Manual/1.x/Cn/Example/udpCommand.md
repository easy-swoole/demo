# UDP命令解析
与TCP命令解析同理，直接上代码
## 解析器
```
namespace App\Sock;


use Core\Component\Socket\AbstractInterface\AbstractClient;
use Core\Component\Socket\AbstractInterface\AbstractCommandParser;
use Core\Component\Socket\Common\Command;

class Parser extends AbstractCommandParser
{

    function parser(Command $result, AbstractClient $client, $rawData)
    {
        // TODO: Implement parser() method.
        $data = trim($rawData);
        $data = explode(',',$data);
        $result->setCommand(array_shift($data));
        $result->setMessage(array_shift($data));
    }
}
```

## 命令注册
```
namespace App\Sock;


use Core\Component\Logger;
use Core\Component\Socket\AbstractInterface\AbstractCommandRegister;
use Core\Component\Socket\Client\UdpClient;
use Core\Component\Socket\Common\Command;
use Core\Component\Socket\Common\CommandList;
use Core\Component\Socket\Response;
use Core\Swoole\AsyncTaskManager;

class Register extends AbstractCommandRegister
{

    function register(CommandList $commandList)
    {
        // TODO: Implement register() method.
        $commandList->addCommandHandler('hello',function (Command $request,UdpClient $client){
            $message = $request->getMessage();
            Logger::getInstance()->console('message is '.$message,false);
            AsyncTaskManager::getInstance()->add(function ()use($client){
                sleep(2);
                Response::response($client,"this is delay message for hello\n");
            });
            return "response for hello\n";
        });

        $commandList->setDefaultHandler(function (){
           return "unkown command\n";
        });
    }
}
```
> 注意，UDP的回调客户端类型是UdpClient

## 事件监听
```
use App\Sock\Parser;
use App\Sock\Register;
use Core\Component\Socket\Dispatcher;


function beforeWorkerStart(\swoole_server $server)
{
    // TODO: Implement beforeWorkerStart() method.
    $udp = $server->addlistener("0.0.0.0",9503,SWOOLE_UDP);
    //udp 请勿用receive事件
    $udp->on('packet',function(\swoole_server $server, $data,$clientInfo){
           Dispatcher::getInstance(Register::class,Parser::class)->dispatchUDP($data,$clientInfo);
    });
}
```

## 测试代码
```
$client = new swoole_client(SWOOLE_SOCK_UDP);
if (!$client->connect('127.0.0.1', 9503, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("hello\n");
echo $client->recv();
$client->close();
```

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
