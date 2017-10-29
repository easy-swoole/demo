# 自定义Event Loop
EasySwoole支持自定义添加一个socket资源参与系统底层的事件调度循环。例如在Conf/Event.php中的`onWorkerStart`事件中，添加以下代码:

```
if($workerId == 0){
    $listener = stream_socket_server(
        "udp://0.0.0.0:9504",
        $error,
        $errMsg,
        STREAM_SERVER_BIND
    );
    if ($errMsg) {
        throw new \Exception("cluster server bind error on msg :{$errMsg}");
    } else {
        //加入event loop
        swoole_event_add($listener, function ($listener) {
            $data = stream_socket_recvfrom($listener, 9504, 0, $client);
            var_dump($data);
            stream_socket_sendto($listener, "hello", 0, $client());
        }
        );
    }
}
```

启动EasySwoole，执行以下UDP客户端测试代码
```
$client = new swoole_client(SWOOLE_SOCK_UDP);
if (!$client->connect('127.0.0.1', 9504, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$client->send("hello\n");
echo $client->recv();
$client->close();
```

> 当客户端发送给服务端消息时，则会自动调用swoole_event_add中所注册的事件回调逻辑。


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

    