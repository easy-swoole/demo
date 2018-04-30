# 分布式
EasySwoole 提供基础的对等模式分布式通讯支持。

## 知识储备

### UDP

#### 什么是UDP协议

什么是UDP协议请自行百度。

#### UDP广播地址
广播地址(Broadcast Address)是专门用于同时向网络中所有工作站进行发送的一个地址。在使用TCP/IP 协议的网络中，主机标识段host ID 为全1 的IP 地址为广播地址，广播的分组传送给host ID段所涉及的所有计算机。例如，对于10.1.1.0 （255.255.255.0 ）网段，其广播地址为10.1.1.255 （255 即为2 进制的11111111 ），当发出一个目的地址为10.1.1.255 的分组（封包）时，它将被分发给该网段上的所有计算机。

## 实现讲解

### 原理讲解
在开启集群模式的时候，EasySwoole会开启一个UDP子服务，用于集群的UDP信息广播，每当有收到信息，就会自动进行openssl解密，并根据解析后的Message，
执行Message回调容器中与Message Command字段匹配的事件。在BaseService的自定义进程中，每一个信息广播周期，都会执行广播回调容器内的全部事件，而EasySwoole注册了默认的集群节点广播、RPC广播（全部消息都openssl加密）。

### 核心代码讲解
Cluster.php
```
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/24
 * Time: 下午10:51
 */

namespace EasySwoole\Core\Component\Cluster;


use EasySwoole\Config;
use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Cluster\Callback\BroadcastCallbackContainer;
use EasySwoole\Core\Component\Cluster\Callback\DefaultCallbackName;
use EasySwoole\Core\Component\Cluster\Callback\ShutdownCallBackContainer;
use EasySwoole\Core\Component\Cluster\Common\BaseServiceProcess;
use EasySwoole\Core\Component\Cluster\Common\MessageBean;
use EasySwoole\Core\Component\Cluster\Common\NodeBean;
use EasySwoole\Core\Component\Cluster\Callback\MessageCallbackContainer;
use EasySwoole\Core\Component\Cluster\NetWork\Deliverer;
use EasySwoole\Core\Component\Cluster\NetWork\PacketParser;
use EasySwoole\Core\Component\Openssl;
use EasySwoole\Core\Component\Rpc\Common\ServiceNode;
use EasySwoole\Core\Component\Rpc\Server;
use EasySwoole\Core\Component\Trigger;
use EasySwoole\Core\Socket\Client\Udp;
use EasySwoole\Core\Swoole\EventHelper;
use EasySwoole\Core\Swoole\Memory\TableManager;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Core\Swoole\ServerManager;
use Swoole\Table;

class Cluster
{
    use Singleton;

    private $currentNode;

    function __construct()
    {
        $conf = Config::getInstance()->getConf('CLUSTER');
        //加载配置信息，并实例化当前节点
        $this->currentNode = new NodeBean($conf);
        if($this->currentNode->getEnable() && empty($this->currentNode->getToken())){
            Trigger::throwable(new \Exception('cluster token could not be empty and set cluster mode disable automatic'));
            $this->currentNode->setEnable(false) ;
        }
        if($this->currentNode->getEnable() && empty($this->currentNode->getListenAddress())){
            Trigger::throwable(new \Exception('cluster listenAddress could not be empty and set cluster mode disable automatic'));
            $this->currentNode->setEnable(false) ;
        }
        //初始化swoole table用于记录节点信息
        TableManager::getInstance()->add('ClusterNodeList',[
            'nodeName'=>[
                'type'=>Table::TYPE_STRING,'size'=>20
            ],
            'udpAddress'=>[
                'type'=>Table::TYPE_STRING,'size'=>16
            ],
            'udpPort'=>[
                'type'=>Table::TYPE_INT,'size'=>10
            ],
            'listenPort'=>[
                'type'=>Table::TYPE_STRING,'size'=>10
            ],
            'lastBeatBeatTime'=>[
                'type'=>Table::TYPE_INT,'size'=>10
            ]
        ]);
    }

    function run()
    {
        if($this->currentNode->getEnable()){
            //注册默认回调
            self::registerDefaultCallback();
            $name = Config::getInstance()->getConf('SERVER_NAME');
            //注册基础服务进程
            ProcessManager::getInstance()->addProcess("{$name}_Cluster_BaseService",BaseServiceProcess::class,['currentNode'=>$this->currentNode]);
            //开启UDP子服务，
            $sub = ServerManager::getInstance()->addServer("{$name}_Cluster",$this->currentNode->getListenPort(),SWOOLE_SOCK_UDP,$this->currentNode->getListenAddress());
            $openssl = new Openssl($this->currentNode->getToken());
            EventHelper::register($sub,$sub::onPacket,function (\swoole_server $server, string $data, array $client_info)use($openssl){
                $data = $openssl->decrypt($data);
                $udpClient = new Udp($client_info);
                //解析信息包并执行回调
                $message = PacketParser::unpack((string)$data,$udpClient);
                if($message){
                    MessageCallbackContainer::getInstance()->hook($message->getCommand(),$message);
                }
            });
        }
    }

    function currentNode():NodeBean
    {
        return $this->currentNode;
    }

    function allNodes():array
    {
        $ret = [];
        $list = TableManager::getInstance()->get('ClusterNodeList');
        $time = time();
        $ttl = $this->currentNode->getNodeTimeout();
        foreach ($list as $key => $item){
            $node = new NodeBean([
                'nodeId'=>$key,
                'nodeName'=>$item['nodeName'],
                'lastBeatBeatTime'=>$item['lastBeatBeatTime'],
                'udpInfo'=>[
                    'address'=>$item['udpAddress'],
                    'port'=>$item['udpPort']
                ],
                'listenPort'=>$item['listenPort']
            ]);
            if($time - $item['lastBeatBeatTime'] > $ttl){
                //异常下线
                NodeOffLienCallbackContainer::getInstance()->call($node,false);
                TableManager::getInstance()->get('ClusterNodeList')->del($key);
            }else{
                $ret[] = $node;
            }
        }
        return $ret;
    }

    function getNode($nodeId):?NodeBean
    {
        $item = TableManager::getInstance()->get('ClusterNodeList')->get($nodeId);
        if(is_array($item)){
            $ttl = $this->currentNode->getNodeTimeout();
            $node = new NodeBean([
                'nodeId'=>$nodeId,
                'nodeName'=>$item['nodeName'],
                'lastBeatBeatTime'=>$item['lastBeatBeatTime'],
                'udpInfo'=>[
                    'address'=>$item['udpAddress'],
                    'port'=>$item['udpPort']
                ],
                'listenPort'=>$item['listenPort']
            ]);
            if(time() - $item['lastBeatBeatTime'] > $ttl){
                NodeOffLienCallbackContainer::getInstance()->call($node,false);
                return null;
            }else{
                return $node;
            }
        }else{
            return null;
        }
    }
    /*
     * 注册默认服务
     */
    private static function registerDefaultCallback()
    {
        //集群节点广播回调
        MessageCallbackContainer::getInstance()->add(DefaultCallbackName::CLUSTER_NODE_BROADCAST,function (MessageBean $messageBean){
            $node = $messageBean->getFromNode();
            TableManager::getInstance()->get('ClusterNodeList')->set($node->getNodeId(),[
                'nodeName'=>$node->getNodeName(),
                'udpAddress'=>$node->getUdpInfo()->getAddress(),
                'udpPort'=>$node->getUdpInfo()->getPort(),
                'lastBeatBeatTime'=>time(),
                'listenPort'=>$node->getListenPort()
            ]);
        });
        //集群节点广播关机回调
        MessageCallbackContainer::getInstance()->add(DefaultCallbackName::CLUSTER_NODE_SHUTDOWN,function (MessageBean $messageBean){
            $node = $messageBean->getFromNode();
            TableManager::getInstance()->get('ClusterNodeList')->del($node->getNodeId());
            //下线该服务的全部rpc服务
            Server::getInstance()->serverNodeOffLine($node);
            //正常下线
            NodeOffLienCallbackContainer::getInstance()->call($node,true);
        });
        //RPC服务节点广播回调
        MessageCallbackContainer::getInstance()->add(DefaultCallbackName::RPC_SERVICE_BROADCAST,function (MessageBean $messageBean){
            $node = $messageBean->getFromNode();
            $list = $messageBean->getArgs();
            foreach ($list as $item){
                $serviceNode = new ServiceNode($item);
                //可达主机地址即为udp地址（真实地址）
                $serviceNode->setAddress($node->getUdpInfo()->getAddress());
                Server::getInstance()->updateServiceNode($serviceNode);
            }
        });

        //集群节点广播
        BroadcastCallbackContainer::getInstance()->set(DefaultCallbackName::CLUSTER_NODE_BROADCAST,function (){
            $message = new MessageBean();
            $message->setCommand(DefaultCallbackName::CLUSTER_NODE_BROADCAST);
            Deliverer::broadcast($message);
        });
        //RPC服务广播
        BroadcastCallbackContainer::getInstance()->set(DefaultCallbackName::RPC_SERVICE_BROADCAST,function (){
            $ret = Server::getInstance()->allLocalServiceNodes();
            $data = [];
            foreach ($ret as $item){
                $data[] = $item->toArray();
            }
            $message = new MessageBean();
            $message->setArgs($data);
            $message->setCommand(DefaultCallbackName::RPC_SERVICE_BROADCAST);
            Deliverer::broadcast($message);
        });
        //注册默认集群关机回调
        ShutdownCallBackContainer::getInstance()->set(DefaultCallbackName::CLUSTER_NODE_SHUTDOWN,function (){
            $message = new MessageBean();
            $message->setCommand(DefaultCallbackName::CLUSTER_NODE_SHUTDOWN);
            Deliverer::broadcast($message);
        });
    }

}
```

BaseServiceProcess.php
```
namespace EasySwoole\Core\Component\Cluster\Common;


use EasySwoole\Core\Component\Cluster\Callback\BroadcastCallbackContainer;
use EasySwoole\Core\Component\Cluster\Callback\ShutdownCallbackContainer;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use Swoole\Process;

class BaseServiceProcess extends AbstractProcess
{

    public function run(Process $process)
    {
        // TODO: Implement run() method.
        //定时执行广播周期回调事件
        $this->addTick($this->getArg('currentNode')->getBroadcastTTL()*1000,function (){
            BroadcastCallbackContainer::getInstance()->call();
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
        //守护模式的时候，正常关闭服务，会执行该回调
        ShutdownCallbackContainer::getInstance()->call();
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
    }
}
```

Deliverer.php  可以在任意位置，做UDP信息发送操作

```
namespace EasySwoole\Core\Component\Cluster\NetWork;


use EasySwoole\Core\Component\Cluster\Cluster;
use EasySwoole\Core\Component\Cluster\Common\MessageBean;
use EasySwoole\Core\Component\Cluster\Common\NodeBean;

class Deliverer
{
    /*
     * 调用此方法，请确保知晓节点的udp信息
     */
    public static function toNode(MessageBean $message,NodeBean $node)
    {
        $message = PacketParser::pack($message);
        //端口以监听地址为准，ip地址以udp地址为准
        Udp::sendTo($message,$node->getListenPort(),$node->getUdpInfo()->getAddress());
    }


    public static function toAllNode(MessageBean $message)
    {
        $message = PacketParser::pack($message);
        $nodes = Cluster::getInstance()->allNodes();
        foreach ($nodes as $node){
            Udp::sendTo($message,$node->getListenPort(),$node->getUdpInfo()->getAddress());
        }
    }

    public static function broadcast(MessageBean $message)
    {
        $message = PacketParser::pack($message);
        $addresses = Cluster::getInstance()->currentNode()->getBroadcastAddress();
        foreach ($addresses as $item){
            $item = explode(':',$item);
            Udp::broadcast($message,$item[1],$item[0]);
        }
    }
}
```

## 常见问题
### 如何实现跨机房通讯？
假设你在阿里云和腾讯云都各有一台机器，那么如何实现两台机器互联呢？那么请注意，EasySwoole允许设置多个广播地址，你可以设置
[ip1:port,ip2:port],注意，此刻ip1,ip2全部为公网ip，且都开放了端口。如果是全部都在同一机房内，那么仅需向255.255.255.255（默认）广播即可。具体的请以实际的路由网关规则为准。

### 某个节点异常下线怎么办
正常情况下，当有节点下线的时候，会触发默认注册的节点下线回调事件，全部节点都会收到节点下线通知，若某台机器意外关机，那么最大在经过配置项中的nodeTimeout以后，那么该节点会被清除，并执行回调。

### 如何注册自己的命令
EasySwoole\Core\Component\Cluster\Callback路径下的全部容器，都是单利模式，主进程中，在EasySwooleEvent.php的主服务创建事件中即可注册。

### 如何用对等模式实现主从模式功能
假设A-Z台机器，你需要A去收集其他的机器信息，那么你可以在A中部署独有服务，然后其他机器发送错误的时候，通过RPC,或者是UDP发送的方式，发送给A节点。
