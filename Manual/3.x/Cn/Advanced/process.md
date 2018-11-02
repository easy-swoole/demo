# 自定义进程
EasySwoole中支持添加用户自定义的swoole process。  
demo地址:https://github.com/easy-swoole/demo/tree/3.x

## 抽象父类
任何的自定义进程，都应该继承自EasySwoole\EasySwoole\Swoole\Process\AbstractProcess,
AbstractProcess实现代码如下：
```php
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/7/29
 * Time: 下午3:37
 */

namespace EasySwoole\EasySwoole\Swoole\Process;


use EasySwoole\EasySwoole\Swoole\Time\Timer;
use Swoole\Process;

abstract class AbstractProcess
{
    private $swooleProcess;
    private $processName;
    private $async = null;
    private $args = [];

    final function __construct(string $processName,array $args = [],$async = true)
    {
        $this->async = $async;
        $this->args = $args;
        $this->processName = $processName;
        $this->swooleProcess = new \swoole_process([$this,'__start'],false,2);
    }

    public function getProcess():Process
    {
        return $this->swooleProcess;
    }

    /*
     * 仅仅为了提示:在自定义进程中依旧可以使用定时器
     */
    public function addTick($ms,callable $call):?int
    {
        return Timer::loop(
            $ms,$call
        );
    }

    public function clearTick(int $timerId)
    {
        Timer::clear($timerId);
    }

    public function delay($ms,callable $call):?int
    {
        return Timer::delay(
            $ms,$call
        );
    }

    /*
     * 服务启动后才能获得到pid
     */
    public function getPid():?int
    {
        if(isset($this->swooleProcess->pid)){
            return $this->swooleProcess->pid;
        }else{
            return null;
        }
    }

    function __start(Process $process)
    {
        if(PHP_OS != 'Darwin'){
            $process->name($this->getProcessName());
        }

        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
        }

        Process::signal(SIGTERM,function ()use($process){
            $this->onShutDown();
            swoole_event_del($process->pipe);
            $this->swooleProcess->exit(0);
        });
        if($this->async){
            swoole_event_add($this->swooleProcess->pipe, function(){
                $msg = $this->swooleProcess->read(64 * 1024);
                $this->onReceive($msg);
            });
        }
        $this->run($this->swooleProcess);
    }

    public function getArgs():array
    {
        return $this->args;
    }

    public function getArg($key)
    {
        if(isset($this->args[$key])){
            return $this->args[$key];
        }else{
            return null;
        }
    }

    public function getProcessName()
    {
        return $this->processName;
    }

    public abstract function run(Process $process);
    public abstract function onShutDown();
    public abstract function onReceive(string $str);

}
```

## 进程管理器
Helper，实现代码如下：
```php
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/9/19
 * Time: 下午7:11
 */

namespace EasySwoole\EasySwoole\Swoole\Process;


use EasySwoole\EasySwoole\ServerManager;

class Helper
{
    public static function addProcess(string $processName,string $processClass):bool
    {
        return ServerManager::getInstance()->getSwooleServer()->addProcess((new $processClass($processName))->getProcess());
    }
}
```

## 实例
我们以demo中的自定义进程例子来说明：
```php
<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18-9-26
 * Time: 上午11:21
 */

namespace App\Process;


use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use Swoole\Process;

class Test extends AbstractProcess
{

    public function run(Process $process)
    {
        // TODO: Implement run() method.
        $this->addTick(30000, function() {
            echo 'this is '.$this->getProcessName().' process tick'.PHP_EOL;
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
        echo 'process rec '.$str.PHP_EOL;
    }
}
```
以上代码[直达连接](https://github.com/easy-swoole/demo/blob/3.x/App/Process/ProcessTest.php)，
至于如何使用（测试），请见demo中的EasySwooleEvent.php