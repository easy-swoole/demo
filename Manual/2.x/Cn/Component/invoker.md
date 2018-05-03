# Invoker

EasySwoole为了让框架支持函数超时处理和swoole1.x与2.x,封装了一个Invoker。

## 实现代码
```
namespace EasySwoole\Core\Component;


use EasySwoole\Core\Swoole\ServerManager;
use \Swoole\Process;
use \Swoole\Coroutine;

class Invoker
{
    /*
     *  Async::set([
          'enable_signalfd' => false,
       ]);
     */
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
            $ret = self::callUserFunc($callable,...$params);
            Process::alarm(-1);
            return $ret;
        }
        catch(\Throwable $throwable)
        {
            throw $throwable;
        }
    }


    public static function callUserFunc(callable $callable,...$params)
    {
        if(ServerManager::getInstance()->isCoroutine()){
            return Coroutine::call_user_func($callable,...$params);
        }else{
            return call_user_func($callable,...$params);
        }
    }

    public static function callUserFuncArray(callable $callable,array $params)
    {
        if(ServerManager::getInstance()->isCoroutine()){
            return Coroutine::call_user_func_array($callable,$params);
        }else{
            return call_user_func_array($callable,$params);
        }
    }
}
```

## 使用实例

### 限制函数执行时间
```
try{
    \EasySwoole\Core\Component\Invoker::exec(function (){
        sleep(2);
    });
}catch (Throwable $throwable){
    echo $throwable->getMessage();
}
```