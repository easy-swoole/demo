##服务端

###参数配置:
    - $servicePort=9601                           服务端监听端口
    - $serviceId= EasySwoole\Utility\Random::character(8)                           
                                                  服务id (\EasySwoole\Rpc\Config()时将随机生成)
    - $listenHost='0.0.0.0'                       监听ip
    - $subServerMode=true                         是否为子服务模式
    - $enableBroadcast=false                      是否开启udp广播(开启后会定时udp广播该服务节点 并且会新监听udp端口用于获取其他服务器广播数据)
    - $broadcastListenPort=9602                   udp监听端口
    - $broadcastList=null                         udp广播地址
    - $maxNodes=2048                              最大服务节点数量
    - $maxPackage=1024*64                         tcp监听服务最大数据包长度
    - $secretKey=''                               tcp监听服务数据包加密串
    - $heartbeat_idle_time=30                     tcp监听服务连接最大允许空闲的时间
    - $heartbeat_check_interval=5                 tcp监听服务心跳检测时间间隔
    - $ipWhiteList=null                           白名单列表
    

###服务端控制器示例:
```php
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/8/4
 * Time: 下午12:32
 */

namespace App\Rpc;


use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Rpc\AbstractInterface\AbstractService;
use EasySwoole\Rpc\Bean\Response;
use EasySwoole\Rpc\Rpc;

class ServiceOne extends AbstractService
{
    function funcOne()
    {
        $arg = $this->getCaller()->getArgs();//获取传输的参数
        $this->getResponse()->setMessage('call at '.time());//同步响应给调用的客户端
    }

    function task(){
              $this->getResponse()->setStatus(\EasySwoole\Rpc\Bean\Response::STATUS_RESPONSE_DETACH);//设置状态码为不响应给客户端    
        /*
         * 如果是异步响应，请手动构建数据包
         */
        $fd = $this->getCaller()->getClient()->getFd();//获取调用客户端的fd
        TaskManager::async(function ()use($fd){
            $res = new Response();
            $res->setMessage('this is task response');
            $res->setStatus(Response::STATUS_SERVICE_OK);
            ServerManager::getInstance()->getSwooleServer()->send($fd,Rpc::dataPack($res->__toString()));//异步发送给客户端
            
        });
    }
}

```

    
###服务端配置示例:
```php
<?php
$conf = new \EasySwoole\Rpc\Config();
$conf->setSubServerMode(true);//设置为子务模式
/*
 * 开启服务自动广播，可以修改广播地址，实现定向ip组广播
 */
$conf->setEnableBroadcast(true);
$conf->getBroadcastList()->set([
    '255.255.255.255:9602'
]);

//新增ip白名单
$conf->setIpWhiteList()->set(['127.0.0.1','192.168.0.216']);//默认允许127.0.0.1的


/*
 * 注册配置项和服务注册
 */
RpcServer::getInstance($conf, Trigger::getInstance());
try {
    //注册服务
    RpcServer::getInstance()->registerService('serviceOne', \App\Rpc\ServiceOne::class);
    RpcServer::getInstance()->registerService('serviceTwo', \App\Rpc\RpcTwo::class);
    
    //注册一个tcp服务作为rpc通讯
    RpcServer::getInstance()->attach(ServerManager::getInstance()->getSwooleServer());
} catch (\Throwable $throwable) {
    Logger::getInstance()->console($throwable->getMessage());
}

/*
 * 手动注册/刷新节点(注册服务之后,需要注册节点进Rpc服务中才可被调用,如果不想手动注册,请开启服务广播进行广播注册)
 */
$node = new ServiceNode();//新增一个节点对象
$node->setServiceName('serviceOne');//设置服务名(需要先注册该服务)
$node->setServiceId($conf->getServiceId());//设置服务id
var_dump($conf->getServiceId());
$node->setIp('127.0.0.1');//设置访问ip
$node->setPort('9601');//设置访问端口
$node->setLastHeartBeat(time());//时间为0则代表服务下线
RpcServer::getInstance()->refreshServiceNode($node);//注册服务节点:serviceOne
$node->setServiceName('serviceTwo');
RpcServer::getInstance()->refreshServiceNode($node);//注册服务节点:serviceTwo
```
