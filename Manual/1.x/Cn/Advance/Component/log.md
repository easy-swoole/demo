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

