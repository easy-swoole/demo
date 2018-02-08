# TCP控制器
## 协议规则与解析
假定，客户端与服务端都是明文传输。控制格式为
```
sericeName:actionName:args
```
## 实现解析器
```
namespace Tcp;

use EasySwoole\Core\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Core\Socket\Common\CommandBean;

class Parser implements ParserInterface
{

    public function decode($raw, $client): ?CommandBean
    {
        // TODO: Implement decode() method.
        $list = explode(":",trim($raw));
        $bean = new CommandBean();
        $controller = array_shift($list);
        if($controller == 'test'){
            $bean->setControllerClass(Test::class);
        }
        $bean->setAction(array_shift($list));
        $bean->setArg('test',array_shift($list));
        return $bean;
    }

    public function encode(string $raw, $client, $commandBean): ?string
    {
        // TODO: Implement encode() method.
        return $raw."\n";
    }
}
```
## 实现一个控制服务
```
namespace Tcp;


use EasySwoole\Core\Socket\Response;
use EasySwoole\Core\Socket\TcpController;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Task\TaskManager;

class Test extends TcpController
{
    function actionNotFound(?string $actionName)
    {
        $this->response()->write("{$actionName} not found");
    }

    function test()
    {
        $this->response()->write(time());
    }

    function args()
    {
        var_dump($this->request()->getArgs());
    }

    function delay()
    {
        $client = $this->client();
        TaskManager::async(function ()use($client){
            sleep(1);
            Response::response($client,'this is delay message at '.time());//为了保持协议一致，实际生产环境请调用Parser encoder
        });
    }

    function close()
    {
        $this->response()->write('you are goging to close');
        $client = $this->client();
        TaskManager::async(function ()use($client){
            sleep(2);
            ServerManager::getInstance()->getServer()->close($client->getFd());
        });
    }
    
    function who()
    {
        $this->response()->write('you fd is '.$this->client()->getFd());
    }
}
```

## 开启子服务
在EasySwooleEvent中注册。
```
  public function mainServerCreate(ServerManager $server,EventRegister $register): void
    {
        // TODO: Implement mainServerCreate() method.
        $tcp = $server->addServer('tcp',9502);
        $tcp->registerDefaultOnReceive(new \Tcp\Parser(),function($errorType,$clientData,$client){
            //第二个回调是可有可无的，当无法正确解析，或者是解析出来的控制器不在的时候会调用
            TaskManager::async(function ()use($client){
                sleep(3);
                \EasySwoole\Core\Socket\Response::response($client,"Bye");
                ServerManager::getInstance()->getServer()->close($client->getFd());
            });
            return "{$errorType} and going to close";
        });
    }
```

## 测试
```
telnet 127.0.0.1 9502
Trying 127.0.0.1...
Connected to localhost.
Escape character is '^]'.
test
 not found
test:test
1518078988
test:args

test:delay

this is delay message at 1518079006
TARGET_CONTROLLER_NOT_FOUND and going to close
ByeConnection closed by foreign host.

```

## HTTP往TCP推送
HTTP控制器
```

namespace App\HttpController;


use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Swoole\ServerManager;

class Tcp extends Controller
{

    function index()
    {
        // TODO: Implement index() method.
        $this->actionNotFound(null);
    }

    /*
     * 请调用who，获取fd
     * http://ip:9501/tpc/push/index.html?fd=xxxx
     */
    function push()
    {
        $fd = intval($this->request()->getRequestParam('fd'));
        $info = ServerManager::getInstance()->getServer()->connection_info($fd);
        if(is_array($info)){
            ServerManager::getInstance()->getServer()->send($fd,'push in http at '.time());
        }else{
            $this->response()->write("fd {$fd} not exist");
        }
    }
}
```

> 实际生产中，一般是用户TCP连接上来后，做验证，然后以userName=>fd的格式，存在redis中，需要http，或者是其他地方，
比如定时器往某个连接推送的时候，就是以userName去redis中取得对应的fd，再send。注意，通过addServer形式创建的子服务器，
可以再完全注册自己的网络事件，你可以注册onclose事件，然后在连接断开的时候，删除userName=>fd对应。