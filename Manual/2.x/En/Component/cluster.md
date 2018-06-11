# Cluster

## How It Realize
Cluster.php
```php
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
        //init self node info
        $this->currentNode = new NodeBean($conf);
        if($this->currentNode->getEnable() && empty($this->currentNode->getToken())){
            Trigger::throwable(new \Exception('cluster token could not be empty and set cluster mode disable automatic'));
            $this->currentNode->setEnable(false) ;
        }
        if($this->currentNode->getEnable() && empty($this->currentNode->getListenAddress())){
            Trigger::throwable(new \Exception('cluster listenAddress could not be empty and set cluster mode disable automatic'));
            $this->currentNode->setEnable(false) ;
        }
        //init swoole table to record node info 
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
            //register default call back
            self::registerDefaultCallback();
            $name = Config::getInstance()->getConf('SERVER_NAME');
            //register cluster service process
            ProcessManager::getInstance()->addProcess("{$name}_Cluster_BaseService",BaseServiceProcess::class,['currentNode'=>$this->currentNode]);
            //register udp server
            $sub = ServerManager::getInstance()->addServer("{$name}_Cluster",$this->currentNode->getListenPort(),SWOOLE_SOCK_UDP,$this->currentNode->getListenAddress());
            $openssl = new Openssl($this->currentNode->getToken());
            EventHelper::register($sub,$sub::onPacket,function (\swoole_server $server, string $data, array $client_info)use($openssl){
                $data = $openssl->decrypt($data);
                $udpClient = new Udp($client_info);
                //try decode a package 
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
     * register call back
     */
    private static function registerDefaultCallback()
    {
        //register 
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
        
        MessageCallbackContainer::getInstance()->add(DefaultCallbackName::CLUSTER_NODE_SHUTDOWN,function (MessageBean $messageBean){
            $node = $messageBean->getFromNode();
            TableManager::getInstance()->get('ClusterNodeList')->del($node->getNodeId());
            //下线该服务的全部rpc服务
            Server::getInstance()->serverNodeOffLine($node);
            //正常下线
            NodeOffLienCallbackContainer::getInstance()->call($node,true);
        });
      
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

  
        BroadcastCallbackContainer::getInstance()->set(DefaultCallbackName::CLUSTER_NODE_BROADCAST,function (){
            $message = new MessageBean();
            $message->setCommand(DefaultCallbackName::CLUSTER_NODE_BROADCAST);
            Deliverer::broadcast($message);
        });

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