# 监听UDP命令
与监听TCP命令同理，在服务启动前事件中添加事件监听。
```
use Core\Component\Socket\Client\UdpClient;


function beforeWorkerStart(\swoole_server $server){
    $udp = $server->addlistener("0.0.0.0",9503,SWOOLE_UDP);
    //udp 请勿用receive事件,除非设置eof
    $udp->on('packet',function(\swoole_server $server, $data,$clientInfo){
         var_dump($data);
         $client = new UdpClient($clientInfo);
         $server->sendto($client->getAddress(),$client->getPort(),'hello');
    });
}
```

## 测试代码

```
$client = new swoole_client(SWOOLE_SOCK_UDP);
if (!$client->connect('127.0.0.1', 9503, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("hello\n");
echo $client->recv();
$client->close();
```

