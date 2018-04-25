# RPC服务
## 服务端
服务A
```php
namespace Rpc;

use EasySwoole\Core\Component\Rpc\AbstractInterface\AbstractRpcService;

class A extends AbstractRpcService
{
    function b(){
        var_dump('as');
    }
}
```

服务Hello
```php
namespace Rpc;


use EasySwoole\Core\Component\Rpc\AbstractInterface\AbstractRpcService;

class Hello extends AbstractRpcService
{
    function hello(){
        $this->response()->setResult([
            'result'=>time(),
            'rec'=>$this->request()->getArgs()
        ]);
    }

    function test()
    {
        $this->response()->setArgs(['asas']);
    }
}
```

服务绑定

```php
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // TODO: Implement mainServerCreate() method.
    $server = Server::getInstance();
    $server->addService('hello',Hello::class);
    $server->addService('a',A::class);
    $server->attach(9502);
}
```

## 客户端
客户端测试代码
```php
//注册默认服务
$node = new \EasySwoole\Core\Component\Rpc\Server\ServiceNode();
$node->setServiceName('hello');
$node->setPort(9502);
\EasySwoole\Core\Component\Rpc\Server\ServiceManager::getInstance()->addServiceNode($node);


$node = new \EasySwoole\Core\Component\Rpc\Server\ServiceNode();
$node->setServiceName('a');
$node->setPort(9502);
\EasySwoole\Core\Component\Rpc\Server\ServiceManager::getInstance()->addServiceNode($node);


//var_dump(\EasySwoole\Core\Component\Rpc\Server\ServiceManager::getInstance()->getServiceNode('hello'));
//return;
//
$client = new \EasySwoole\Core\Component\Rpc\Client();

$action = $client->addCall('hello','hello')->setArgs([
    'a'=>1,
    'b'=>'b'
])->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ResponseObj $obj){
    var_dump($obj);
})->setFailCall(function (\EasySwoole\Core\Component\Rpc\Client\ResponseObj $obj){
    var_dump('fail',$obj);
});


$action = $client->addCall('hello','miss')->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ResponseObj $obj){
    var_dump($obj);
})->setFailCall(function (){
    var_dump('fail');
});


$action = $client->addCall('a','b')->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ResponseObj $obj){
    var_dump($obj);
})->setFailCall(function (\EasySwoole\Core\Component\Rpc\Client\ResponseObj $obj){
    var_dump('fail',$obj);
});

$client->call();
```
> 集群模式时，有服务自动发现。