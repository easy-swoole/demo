# TCP长链接
EasySwoole支持自定义tcp长链接。
## 设置监听
在Event.php的beforeWorkerStart事件监听链接。
```
 $listener = $server->addlistener("0.0.0.0",9502,SWOOLE_TCP);
        //混合监听tcp时    要重新设置包解析规则  才不会被HTTP覆盖，且端口不能与HTTP SERVER一致 HTTP本身就是TCP
        $listener->set(array(
            "open_eof_check"=>false,
            "package_max_length"=>2048,
        ));
        $listener->on("connect",function(\swoole_server $server,$fd){
            Logger::getInstance()->console("client connect");
        });
        $listener->on("receive",function(\swoole_server $server,$fd,$from_id,$data){
            Logger::getInstance()->console("received data :".$data);
            $server->send($fd,"swoole ".$data ." at time: ".time());
            //模拟其他地方调用向该链接发送信息   $fd是关键，是一个数字，可以缓存在redis等其他地方
            AsyncTaskManager::getInstance()->add(function()use($fd){
                sleep(3);
                Server::getInstance()->getServer()->send($fd,"this is delay message"." at time: ".time());
            });
        });
        $listener->on("close",function (\swoole_server $server,$fd){
            Logger::getInstance()->console("client close");
        }
 );
```

启动服务：php server start 即可成功监听TCP。测试:

> telnet 127.0.0.1 9502

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>