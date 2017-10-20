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

## 自定义日志存储
实现LoggerWriterInterface接口
```
namespace App\Model;


use Core\AbstractInterface\LoggerWriterInterface;

class Handler implements LoggerWriterInterface
{

    static function writeLog($obj, $logCategory, $timeStamp)
    {
        // TODO: Implement writeLog() method.
    }
}
```
在框架初始化后事件注入日志存储处理
```
function frameInitialized()
{
    // TODO: Implement frameInitialized() method.
    Di::getInstance()->set(SysConst::DI_LOGGER_WRITER,Handler::class);
}
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
