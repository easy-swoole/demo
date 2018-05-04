# RPC服务
EasySwoole 提供开放式的RPC服务。RPC服务分为三级模式：服务=>服务组=>行为。每个服务可以单独现在Openssl加密。
支持超时、成功、失败回调（即最基础的熔断保护和服务降级）
## 示例代码
### 服务端
服务A
```php
namespace App\RpcController\A;


use EasySwoole\Core\Component\Rpc\AbstractInterface\AbstractRpcService;

class G extends AbstractRpcService
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->setArgs([12,3]);
    }
}
```
> 服务A中存在G服务组，G服务组中实现了Index行为。

服务B
```php
namespace App\RpcController\B;


use EasySwoole\Core\Component\Rpc\AbstractInterface\AbstractRpcService;

class Index extends AbstractRpcService
{

    function index()
    {
        // TODO: Implement index() method.
        var_dump('hit');
        $this->response()->setResult('this is b index');
    }
}
```

服务绑定

```php
use EasySwoole\Core\Component\Rpc\Server;
public static function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // TODO: Implement mainServerCreate() method.
    Server::getInstance()->addService('A',9502)
                ->addService('B',9503,'password123')
                ->attach();
}
```

## 客户端
客户端测试代码
```php
require_once 'vendor/autoload.php';

\EasySwoole\Core\Core::getInstance()->initialize();
//注册服务，让RPC服务管理中心知道当前系统中存在哪些服务

$ServiceManager = \EasySwoole\Core\Component\Rpc\Server::getInstance();
$ServiceManager->updateServiceNode(new \EasySwoole\Core\Component\Rpc\Common\ServiceNode(
    [
        'serviceName'=>'A',
        'port'=>9502
    ]
));

$ServiceManager->updateServiceNode(new \EasySwoole\Core\Component\Rpc\Common\ServiceNode(
    [
        'serviceName'=>'B',
        'port'=>9503,
        'encryptToken'=>'password123'
    ]
));


//创建RPC客户端
$client = new \EasySwoole\Core\Component\Rpc\Client();

//调用A服务中G服务组的index行为
$client->addCall('A','g','index')->setFailCall(function(\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('11fail',$response);
})->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('11success',$response);
});

//调用A服务中G服务组的c行为
$client->addCall('A','g','c')->setFailCall(function(\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('22fail',$response);
})->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('22success',$response);
});
//调用A服务中c服务组的c行为
$client->addCall('A','c','c')->setFailCall(function(\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('33fail',$response);
})->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('33success',$response);
});
//调用c服务中c服务组的c行为
$client->addCall('c','c','c')->setFailCall(function(\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('44fail',$response);
})->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('44success',$response);
});

//调用B服务中c服务组的index行为
$client->addCall('B','c','index')->setFailCall(function(\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('55fail',$response);
})->setSuccessCall(function (\EasySwoole\Core\Component\Rpc\Client\ServiceResponse $response){
    var_dump('55success',$response);
});

//执行调用
$client->call();
```
> 在没有集群模式时，可以在EasySwooleEvent的主服务启动事件中，注册好存在的服务信息，以后客户端可以直接调用服务，不需要继续再做服务发现注册。
再集群模式时，有服务自动发现。

## 原理讲解
### 服务端
服务端实现关键代码
```
namespace EasySwoole\Core\Component\Rpc;


use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Openssl;
use EasySwoole\Core\Component\Rpc\Common\Parser;
use EasySwoole\Core\Component\Rpc\Common\ServiceResponse;
use EasySwoole\Core\Component\Rpc\Common\Status;
use EasySwoole\Core\Component\Rpc\Server\ServiceManager;
use EasySwoole\Core\Component\Rpc\Server\ServiceNode;
use EasySwoole\Core\Component\Trigger;
use EasySwoole\Core\Socket\Client\Tcp;
use EasySwoole\Core\Socket\Response;
use EasySwoole\Core\Swoole\EventHelper;
use EasySwoole\Core\Swoole\ServerManager;

class Server
{
    use Singleton;

    private $list = [];
    private $controllerNameSpace = 'App\\RpcController\\';
    
    private $protocolSetting = [
        'open_length_check' => true,
        'package_length_type'   => 'N',
        'package_length_offset' => 0,
        'package_body_offset'   => 4,
        'package_max_length'    => 1024*64,
        'heartbeat_idle_time' => 5,
        'heartbeat_check_interval' => 30,
    ];
    //可以自定义分包协议，这部分功能的parser 暂未分离，提前预留
    function setProtocolSetting(array $data)
    {
        $this->protocolSetting = $data;
        return $this;
    }
    //自定义RPC控制器名称空间
    function setControllerNameSpace(string $nameSpace):Server
    {
        $this->controllerNameSpace = $nameSpace;
        return $this;
    }
    //添加一个服务
    function addService(string $serviceName,int $port,$encryptToken = null,string $address = '0.0.0.0')
    {
        //一个EasySwoole服务上不允许同名服务
        $this->list[$serviceName] = [
            'serviceName'=>$serviceName,
            'port'=>$port,
            'encryptToken'=>$encryptToken,
            'address'=>$address
        ];
        return $this;
    }
    //绑定到主服务
    public function attach()
    {
        foreach ($this->list as $name => $item){
            $node = new ServiceNode();
            $node->setPort($item['port']);
            $node->setServiceName($name);
            $node->setEncryptToken($item['encryptToken']);
            ServiceManager::getInstance()->addServiceNode($node);

            $sub = ServerManager::getInstance()->addServer("RPC_SERVER_{$name}",$item['port'],SWOOLE_TCP,$item['address'],$this->protocolSetting);

            $nameSpace = $this->controllerNameSpace.ucfirst($item['serviceName']);
            EventHelper::register($sub,$sub::onReceive,function (\swoole_server $server, int $fd, int $reactor_id, string $data)use($item,$nameSpace){
                $response = new ServiceResponse();
                $client = new Tcp($fd,$reactor_id);
                //解包，获得原始完整字符串
                $data = Parser::unPack($data);
                $openssl = null;
                //若有加密配置，则对数据包解密
                if(!empty($item['encryptToken'])){
                    $openssl = new Openssl($item['encryptToken']);
                }
                if($openssl){
                    $data = $openssl->decrypt($data);
                }
                if($data !== false){
                    //看看能否成功解析出命令
                    $caller = Parser::decode($data,$client);
                    if($caller){
                        $response->arrayToBean($caller->toArray());
                        $response->setArgs(null);
                        $group = ucfirst($caller->getServiceGroup());
                        //搜索有没有完整的服务=>服务组控制器
                        $controller = "{$nameSpace}\\{$group}";
                        if(!class_exists($controller)){
                            $response->setStatus(Status::SERVICE_GROUP_NOT_FOUND);
                            //若没有，则搜索有没有完整的服务Index控制器（默认Index服务组）
                            $controller = "{$nameSpace}\\Index";
                            if(!class_exists($controller)){
                                $controller = null;
                            }else{
                                $response->setStatus(Status::OK);
                            }
                        }
                        if($controller){
                            try{
                                (new $controller($client,$caller,$response));
                            }catch (\Throwable $throwable){
                                Trigger::throwable($throwable);
                                $response->setStatus(Status::SERVICE_ERROR);
                            }
                        }else{
                            $response->setStatus(Status::SERVICE_NOT_FOUND);
                        }
                    }else{
                        $response->setStatus(Status::PACKAGE_DECODE_ERROR);
                    }
                }else{
                    $response->setStatus(Status::PACKAGE_ENCRYPT_DECODED_ERROR);
                }
                //进行json打包，并放回给客户端
                $response = json_encode($response->toArray(),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                if($openssl){
                    $response =  $openssl->encrypt($response);
                }
                Response::response($client,Parser::pack($response));
            });
        }
    }
}
```

### 客户端
客户端基于swoole client + socket select实现的伪异步客户端。

## 跨平台调用
json请求结构体
```
namespace EasySwoole\Core\Component\Rpc\Common;


use EasySwoole\Core\Component\Spl\SplBean;

class ServiceCaller extends SplBean
{
    protected $serviceName;
    protected $serviceGroup;
    protected $serviceAction;
    protected $args = null;

    /**
     * @return mixed
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param mixed $serviceName
     */
    public function setServiceName($serviceName): void
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @return mixed
     */
    public function getServiceGroup()
    {
        return $this->serviceGroup;
    }

    /**
     * @param mixed $serviceGroup
     */
    public function setServiceGroup($serviceGroup): void
    {
        $this->serviceGroup = $serviceGroup;
    }

    /**
     * @return mixed
     */
    public function getServiceAction()
    {
        return $this->serviceAction;
    }

    /**
     * @param mixed $serviceAction
     */
    public function setServiceAction($serviceAction): void
    {
        $this->serviceAction = $serviceAction;
        $this->initialize();
    }

    /**
     * @return null
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param null $args
     */
    public function setArgs($args): void
    {
        $this->args = $args;
    }

    protected function initialize(): void
    {
        if(empty($this->serviceAction)){
            $this->serviceAction = 'index';
        }
    }

}
```

json相应结构体
```

namespace EasySwoole\Core\Component\Rpc\Client;


use EasySwoole\Core\Component\Rpc\Server\ServiceNode;

class ServiceResponse extends \EasySwoole\Core\Component\Rpc\Common\ServiceResponse
{
    protected $responseNode = null;

    /**
     * @return null
     */
    public function getResponseNode():?ServiceNode
    {
        return $this->responseNode;
    }

    /**
     * @param null $responseNode
     */
    public function setResponseNode($responseNode): void
    {
        $this->responseNode = $responseNode;
    }

}
```

默认状态码规则
```
namespace EasySwoole\Core\Component\Rpc\Common;


class Status
{
    const OK = 1;//rpc调用成功
    const SERVICE_REJECT_REQUEST = 0;//服务端拒绝执行，比如缺参数，或是恶意调用
    const SERVICE_NOT_FOUND = -1;//服务端告诉客户端没有该服务
    const SERVICE_GROUP_NOT_FOUND = -2;//服务端告诉客户端该服务不存在该服务组（服务控制器）
    const SERVICE_ACTION_NOT_FOUND = -3;//服务端告诉客户端没有该action
    const SERVICE_ERROR = -4;//服务端告诉客户端服务端出现了错误
    const PACKAGE_ENCRYPT_DECODED_ERROR = -5;//服务端告诉客户端发过来的包openssl解密失败
    const PACKAGE_DECODE_ERROR = -6;//服务端告诉客户端发过来的包无法成功解码为ServiceCaller
    const CLIENT_WAIT_RESPONSE_TIMEOUT = -7;//客户端等待响应超时
    const CLIENT_CONNECT_FAIL = -8;//客户端连接到服务端失败
    const CLIENT_SERVER_NOT_FOUND = -9;//客户端无法找到该服务
}
```

默认tcp协议包体规则
```
[
'open_length_check' => true,
 'package_length_type' => 'N',
 'package_length_offset' => 0,
 'package_body_offset' => 4,
 'package_max_length' => 1024 * 64
]
```

### PHP示例代码
```
$opensslKey = null;
$opensslMethod = 'DES-EDE3';

//构造服务调用
$data = [
     'serviceName'=>'A',//服务名称
     'serviceGroup'=>'G',//服务组（RPC服务控制器名称）
     'serviceAction'=>'index',//服务行为名（RPC服务控制器action名称）
     'args'=>[
         'a'=>1,
         'b'=>2
     ]
];
$fp = stream_socket_client('tcp://127.0.0.1:9502');
//数据打包
$sendStr = json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

if($opensslKey){
    $sendStr = openssl_encrypt($sendStr,$opensslMethod,$opensslKey);
}

fwrite($fp,pack('N', strlen($sendStr)).$sendStr);
//需要超时机制的请自己用sock time out
$data = fread($fp,65533);
//做长度头部校验
$len = unpack('N',$data);
$data = substr($data,'4');
if(strlen($data) != $len[1]){
    echo 'data error';
}else{
    if($opensslKey){
        $data = openssl_decrypt($data,$opensslMethod,$opensslKey);
    }
    $json = json_decode($data,true);
    //这就是服务端返回的结果，
    var_dump($json);
}
fclose($fp);
```


### NodeJs 示例代码
```
var net = require('net');
var pack = require('php-pack').pack;
var unpack = require('php-pack').unpack;
var json = {
    serviceName:'A',
    serviceGroup:'G',
    serviceAction:'index',
    args:[]
};

var send = JSON.stringify(json);

send = Buffer.concat([pack("N",send.length), Buffer.from(send)]);

var client = new net.Socket();
client.connect(9502, '127.0.0.1', function() {
    console.log('Connected');
    client.write(send);

});

client.on('data', function(data) {
    console.log('Received: ' + data);
    var ret = JSON.parse(data.toString().substr(4));
    console.log('status: ' +  ret.status);
    client.destroy()
});

client.on('close', function() {
    console.log('Connection closed');
});
client.on('error',function (error) {
    console.log(error);
});
```