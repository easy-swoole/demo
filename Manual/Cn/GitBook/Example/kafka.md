# kafka
本例子以[https://github.com/weiboad/kafka-php](https://github.com/weiboad/kafka-php)作为客户端。使用composer安装时请先看EasySwoole文档中自动加载的章节，
为EasySwoole引入composer。

## 如何在EasySwoole中添加自定义阻塞进程
EasySwoole支持在beforeWorker事件中添加自定义进程参与swoole底层的事件循环，具体实例代码为：
```
    $server->addProcess(new \swoole_process(function (){
            while(1){
            }
    }));
```

## kafka消费者
```
    $server->addProcess(new \swoole_process(function (){
            $config = \Kafka\ConsumerConfig::getInstance();
            $config->setMetadataRefreshIntervalMs(10000);
            $config->setMetadataBrokerList('0.0.0.0:9092');
            $config->setGroupId('test');
            $config->setBrokerVersion('0.9.0.1');
            $config->setTopics(array('test'));
            $consumer = new \Kafka\Consumer();
            $consumer->start(function($topic, $part, $message) {
                Logger::getInstance()->log($message);
            });
    }));
```

我们向该topic发生一个消息，可见
```
array(3) {
  ["offset"]=>
  int(0)
  ["size"]=>
  int(19)
  ["message"]=>
  array(6) {
    ["crc"]=>
    int(2275900082)
    ["magic"]=>
    int(0)
    ["attr"]=>
    int(0)
    ["timestamp"]=>
    int(0)
    ["key"]=>
    string(0) ""
    ["value"]=>
    string(5) "hello"
  }
}
```
> 以上例子仅为示例，具体使用请见kafka-php文档

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
