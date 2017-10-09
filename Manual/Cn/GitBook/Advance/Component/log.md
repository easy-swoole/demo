# 日志
easySwoole提供了分类日志服务，以供记录运行信息方便调试。
```
$log = Logger::getInstance();
$log2 = Logger::getInstance('logcategory1');
```
## log
```
$log->log('message1');
$log2->log('message2');
```
## console
```
$log->console("message",false);
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