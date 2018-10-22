# Invoker

EasySwoole为了让框架支持函数超时处理,封装了一个Invoker。

## 实现代码
```
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/24
 * Time: 下午4:12
 */

namespace EasySwoole\Component;

use \Swoole\Process;

class Invoker
{
    public static function exec(callable $callable,$timeOut = 100 * 1000,...$params)
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGALRM, function () {
            Process::alarm(-1);
            throw new \RuntimeException('func timeout');
        });
        try
        {
            Process::alarm($timeOut);
            $ret = call_user_func($callable,...$params);
            Process::alarm(-1);
            return $ret;
        }
        catch(\Throwable $throwable)
        {
            throw $throwable;
        }
    }
}
```

## 使用实例

### 限制函数执行时间
```
try{
    \EasySwoole\Component\Invoker::exec(function (){
        sleep(2);
    });
}catch (Throwable $throwable){
    echo $throwable->getMessage();
}
```