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

实现LoggerWriterInterface接口

```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-10-17
 * Time: 下午3:39
 */

namespace App\Model;


use EasySwoole\Trace\AbstractInterface\LoggerWriterInterface;

class Handler implements LoggerWriterInterface
{

    function writeLog($obj, $logCategory, $timeStamp)
    {
        // TODO: Implement writeLog() method.
        
    }
}
```

在框架初始化事件里注入日志存储处理

```php
function static initialize()
{
    // TODO: Implement frameInitialize() method.
    Di::getInstance()->set('LOGGER_WRITER', Handler::class);
}
```