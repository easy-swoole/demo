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
