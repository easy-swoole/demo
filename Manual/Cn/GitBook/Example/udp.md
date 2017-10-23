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

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>
