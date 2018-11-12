# 日志

### log

- string `$str` 日志内容
- string `$category` 日志分类名 

```php
$log->log('message1');
$log2->log('message2');
```

### console

- string `$str` 调试内容
- int `$saveLog`  是否保存

```php
$log->console("message",false);
```

## 自定义日志存储

自定义日志处理类

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-11-12
 * Time: 上午9:27
 */

namespace App\Log;

use EasySwoole\Trace\AbstractInterface\LoggerWriterInterface;

class LogHandler implements LoggerWriterInterface
{

    function writeLog($obj, $logCategory, $timeStamp)
    {
        // TODO: Implement writeLog() method.
        echo date('Y-m-d H:i:s', $timeStamp)."\t".$obj.PHP_EOL;
    }
}
```

在框架初始化事件里注入日志存储处理

```php
function static initialize()
{
    // TODO: Implement frameInitialize() method.
    // 注入日志处理类
    Logger::getInstance()->setLoggerWriter(new LogHandler());
}
```

打印日志信息

```php
Logger::getInstance()->log('hello world....');
```

附上demo地址: <https://github.com/easy-swoole/demo/blob/3.x/App/HttpController/Log/Index.php>