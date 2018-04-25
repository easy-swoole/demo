# 日志

### log

- string `$str` 日志内容
- string `$category` 日志分类名 

```Php
$log->log('message1');
$log2->log('message2');
```

### console

- string `$str` 调试内容
- int `$saveLog`  是否保存

```Php
$log->console("message",false);
```

### consoleWithTrace

- string `$str` 调试内容
- int `$saveLog`  是否保存

### logWithTrace

- string `$str` 日志内容
- string `$category` 日志分类名 



## 自定义日志存储

实现LoggerWriterInterface接口

```Php
namespace App\Model;

use EasySwoole\Core\AbstractInterface\LoggerWriterInterface;

class Handler implements LoggerWriterInterface
{
    function writeLog($obj, $logCategory, $timeStamp)
    {
        // TODO: Implement writeLog() method.
    }
}
```

在框架初始化事件里注入日志存储处理

```Php
function static frameInitialize()
{
    // TODO: Implement frameInitialize() method.
    Di::getInstance()->set(SysConst::LOGGER_WRITER,Handler::class);
}
```