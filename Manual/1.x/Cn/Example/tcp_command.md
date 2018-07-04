# 自定义TCP命令解析
EasySwoole支持用户进行自定义格式的命令解析与路由。以下我们将以最基础的例子作为讲解。
## 建立自定义命令解析类
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
    }
}
```
在AbstractCommandParser的接口定义中，我们需要实现parser方法，parser的三参数分别为：
- 解析后的命令包
- 客户端
- 原始数据

比如,我现在定义的规则就是（命令,数据信息）,那么我的解析规则就为：
```
function parser(Command $result, AbstractClient $client, $rawData)
{
     // TODO: Implement parser() method.
     $data = trim($rawData);
     $data = explode(',',$data);
     $result->setCommand(array_shift($data));
     $result->setMessage(array_shift($data));
}
```
## 定义命令注册类
```
namespace App\Sock;


use Core\Component\Socket\AbstractInterface\AbstractCommandRegister;
use Core\Component\Socket\Common\CommandList;

class Register extends AbstractCommandRegister
{

    function register(CommandList $commandList)
    {
        // TODO: Implement register() method.
    }
}
```
在AbstractCommandRegister接口中，我们必须实现register方法。举例，我们注册三个实验方法：
```
namespace App\Sock;


use Core\Component\Logger;
use Core\Component\Socket\AbstractInterface\AbstractCommandRegister;
use Core\Component\Socket\Client\TcpClient;
use Core\Component\Socket\Common\Command;
use Core\Component\Socket\Common\CommandList;
use Core\Component\Socket\Response;
use Core\Swoole\AsyncTaskManager;
use Core\Swoole\Server;

class Register extends AbstractCommandRegister
{

    function register(CommandList $commandList)
    {
        // TODO: Implement register() method.
        $commandList->addCommandHandler('hello',function (Command $request,TcpClient $client){
            $message = $request->getMessage();
            Logger::getInstance()->console('message is '.$message,false);
            AsyncTaskManager::getInstance()->add(function ()use($client){
                sleep(2);
                Response::response($client,"this is delay message for hello\n");
            });
            return "response for hello\n";
        });

        $commandList->addCommandHandler('close',function (Command $request,TcpClient $client){
            Response::response($client,"you are going to disconnect\n");
            Server::getInstance()->getServer()->close($client->getFd(),$client->getReactorId());
        });

        $commandList->setDefaultHandler(function (){
           return "unkown command\n";
        });
    }
}
```

## 添加事件监听
在EasySwoole的启动前事件中：
```
use App\Sock\Parser;
use App\Sock\Register;
use Core\Component\Socket\Dispatcher;


function beforeWorkerStart(\swoole_server $server){
    $listener = $server->addlistener('0.0.0.0',9502,SWOOLE_TCP);
    $listener->set(array(
        "open_eof_check"=>false,
        "package_max_length"=>2048,
    ));
    $listener->on("receive",function(\swoole_server $server,$fd,$from_id,$data){
         Dispatcher::getInstance(Register::class,Parser::class)->dispatchTCP($fd,$from_id,$data);
    });
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
- abc，
- close

观察结果。

