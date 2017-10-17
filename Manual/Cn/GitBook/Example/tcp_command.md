# 自定义TCP命令解析
直接上实例代码。
## 创建TCP路由与解析规则
```
namespace App\Socket\Tcp;


use Core\Component\Di;
use Core\Component\Logger;
use Core\Component\Socket\Client\TcpClient;
use Core\Component\Socket\Command\Command;
use Core\Component\Socket\Dispatcher;
use Core\Component\Socket\Response;
use Core\Swoole\AsyncTaskManager;

class Init
{
    static function dispatcher(){
        $dispatcher = new Dispatcher();
        $dispatcher->setCommandParser(new Parser());
        //注册一个命令回调
        $dispatcher->registerCommand("hello",function (Command $command){
            Logger::getInstance()->console("client say hello ".$command->getMessage());
            $client = $command->getClient();
            Response::response($client,"res\n");
            AsyncTaskManager::getInstance()->add(function ()use($client){
                sleep(3);
                Response::response($client,"delay res\n");
            });
        });
        //设置一个默认的处理
        $dispatcher->setDefaultHandler(function (Command $command){
            Response::response($command->getClient(),"unknow command\n");
        });
        //注入IOC
        Di::getInstance()->set("TCP_DISPATCHER",$dispatcher);
    }

    static function listen(\swoole_server $server){
        $listener = $server->addlistener("0.0.0.0",9502,SWOOLE_TCP);
        //混合监听tcp时    要重新设置包解析规则  才不会被HTTP覆盖，且端口不能与HTTP SERVER一致 HTTP本身就是TCP
        $listener->set(array(
            "open_eof_check"=>false,
            "package_max_length"=>2048,
        ));

        $listener->on("connect",function(\swoole_server $server,$fd){
            Logger::getInstance()->console("client connect",false);
        });

        $listener->on("receive",function(\swoole_server $server,$fd,$from_id,$data){
            //注意，这里需要自己创建对应的client
            $client = new TcpClient($server->getClientInfo($fd));
            $client->setFd($fd);
            $client->setReactorId($from_id);
            //进行包路由解析
            Di::getInstance()->get('TCP_DISPATCHER')->dispatch($client,$data);
        });

    }
}
```
```
namespace App\Socket\Tcp;


use Core\Component\Socket\Command\AbstractCommandParser;

class Parser extends AbstractCommandParser
{
    protected function handler($data)
    {
        // TODO: Implement handler() method.
        //这里其实就是对客户端发来的数据包做解析。解析出自己的命令
        $data = trim($data);
        $data = explode(",",$data);
        $this->getCommand()->setCommand(array_shift($data));
        $this->getCommand()->setMessage(array_shift($data));
    }

}
```
## 事件绑定
修改Conf/Event.php，在以下事件中做事件监听
```
 function frameInitialized()
 {
    // TODO: Implement frameInitialized() method.
    Init::dispatcher();
 }


 function beforeWorkerStart(\swoole_server $server)
 {
     // TODO: Implement beforeWorkerStart() method.
     Init::listen($server);
 }
```

## 测试
启动EasySwoole，执行：
```
telnet 127.0.0.1 9501
```
分别输入：
- hello
- hello,message
- abc

观察结果。

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
