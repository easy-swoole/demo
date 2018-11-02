# TCP控制器
## 协议规则与解析
假定，客户端与服务端都是明文传输。控制格式为
```php
sericeName:actionName:args
```
## 实现解析器[Parser.php](https://github.com/easy-swoole/demo/blob/3.x/App/TcpController/Parser.php)
```php
<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/17 0017
 * Time: 9:10
 */
namespace App\TcpController;

use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Utility\CommandLine;

class Parser implements ParserInterface
{
    public function decode($raw, $client): ?Caller
    {
        // TODO: Implement decode() method.
        $list = explode(":",trim($raw));
        $bean = new Caller();
        $controller = array_shift($list);
        $controller = "App\\TcpController\\{$controller}";
        $bean->setControllerClass($controller);
        $bean->setAction(array_shift($list));
        $bean->setArgs($list);
        $bean->setArgs($list);
        return $bean;
    }

    public function encode(Response $response,$client): ?string
    {
        return $response;
    }
}
```
## 实现一个控制服务[Test.php](https://github.com/easy-swoole/demo/blob/3.x/App/TcpController/Test.php)
```php
<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/17 0017
 * Time: 9:15
 */
namespace App\TcpController;

use App\Rpc\RpcServer;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use http\Env\Response;

class Test extends Controller{

    function actionNotFound(?string $actionName)
    {
        $this->response()->setMessage("{$actionName} not found");
    }

    public function index(){
        $this->response()->setMessage(time());
    }

    public function args()
    {
        var_dump($this->caller()->getArgs());
    }

    public function delay()
    {
        $client = $this->caller()->getClient();
        TaskManager::async(function ()use($client){
            sleep(1);
            ServerManager::getInstance()->getSwooleServer()->send($client->getFd(),'this is delay message at '.time());
        });
    }

    public function close()
    {
        $this->response()->setMessage('you are goging to close');
        $client = $this->caller()->getClient();
        TaskManager::async(function ()use($client){
            sleep(2);
            ServerManager::getInstance()->getSwooleServer()->send($client->getFd(),'this is delay message at '.time());
        });
    }

    public function who()
    {
        $this->response()->setMessage('you fd is '.$this->caller()->getClient()->getFd());
    }
}


```

## 开启子服务
在EasySwooleEvent中注册。
```php
public static function mainServerCreate(EventRegister $register)
{
      $server = ServerManager::getInstance()->getSwooleServer();
      $subPort = $server->addListener(Config::getInstance()->getConf('MAIN_SERVER.HOST'), 9503, SWOOLE_TCP);

      $socketConfig = new \EasySwoole\Socket\Config();
      $socketConfig->setType($socketConfig::TCP);
      $socketConfig->setParser(new \App\TcpController\Parser());
      //设置解析异常时的回调,默认将抛出异常到服务器
      $socketConfig->setOnExceptionHandler(function ($server,$throwable,$raw,$client,$response){
          $server->send($client->getFd(),'bye');
          $server->close($client->getFd());
      });
      $dispatch = new \EasySwoole\Socket\Dispatcher($socketConfig);

      $subPort->set(
          ['open_length_check'   => false]//不验证数据包
      );
      $subPort->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
          $dispatch->dispatch($server, $data, $fd, $reactor_id);
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
```php
<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/17 0017
 * Time: 15:35
 */

namespace App\HttpController;


use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;

class Tcp extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
    }
    /*
       * 请调用Test=>who，获取到fd再进行http调用
       * http://ip:9501/tpc/push/index.html?fd=xxxx
       */
    public function push()
    {
        $fd = intval($this->request()->getRequestParam('fd'));
        $info = ServerManager::getInstance()->getSwooleServer()->connection_info($fd);
        if(is_array($info)){
            ServerManager::getInstance()->getSwooleServer()->send($fd,'push in http at '.time());
        }else{
            $this->response()->write("fd {$fd} not exist");
        }
    }
}
```

> 实际生产中，一般是用户TCP连接上来后，做验证，然后以userName=>fd的格式，存在redis中，需要http，或者是其他地方，
比如定时器往某个连接推送的时候，就是以userName去redis中取得对应的fd，再send。注意，通过addServer形式创建的子服务器，
>以再完全注册自己的网络事件，你可以注册onclose事件，然后在连接断开的时候，删除userName=>fd对应。