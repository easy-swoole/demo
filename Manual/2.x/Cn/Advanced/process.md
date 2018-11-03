# 自定义进程
EasySwoole中支持添加用户自定义的swoole process。

## 抽象父类
任何的自定义进程，都应该继承自EasySwoole\Core\Swoole\Process\AbstractProcess,
AbstractProcess实现代码如下：
```
<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/17
 * Time: 上午11:28
 */

namespace EasySwoole\Core\Swoole\Process;


use EasySwoole\Core\Swoole\Memory\TableManager;
use EasySwoole\Core\Swoole\ServerManager;
use EasySwoole\Core\Swoole\Time\Timer;
use Swoole\Process;

abstract class AbstractProcess
{
    private $swooleProcess;
    private $processName;
    private $async = null;
    private $args = [];
    function __construct(string $processName,$async = true,array $args)
    {
        $this->async = $async;
        $this->args = $args;
        $this->processName = $processName;
        $this->swooleProcess = new \swoole_process([$this,'__start'],false,2);
        ServerManager::getInstance()->getServer()->addProcess($this->swooleProcess);
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
            $key = md5($this->processName);
            $pid = TableManager::getInstance()->get('process_hash_map')->get($key);
            if($pid){
                return $pid['pid'];
            }else{
                return null;
            }
        }
    }


    function __start(Process $process)
    {
        if(PHP_OS != 'Darwin'){
            $process->name($this->getProcessName());
        }
        TableManager::getInstance()->get('process_hash_map')->set(
            md5($this->processName),['pid'=>$this->swooleProcess->pid]
        );
        ProcessManager::getInstance()->setProcess($this->getProcessName(),$this);
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);
        }
        Process::signal(SIGTERM,function ()use($process){
            $this->onShutDown();
            TableManager::getInstance()->get('process_hash_map')->del(md5($this->processName));
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

    public function getProcessName()
    {
        return $this->processName;
    }

    public abstract function run(Process $process);
    public abstract function onShutDown();
    public abstract function onReceive(string $str,...$args);

}
```

## 进程管理器
ProcessManager，实现代码如下：
```
namespace EasySwoole\Core\Swoole\Process;


use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Swoole\Memory\TableManager;
use EasySwoole\Core\Swoole\ServerManager;
use Swoole\Table;


class ProcessManager
{
    use Singleton;
    private $processList = [];

    function __construct()
    {
        TableManager::getInstance()->add(
            'process_hash_map',[
                'pid'=>[
                    'type'=>Table::TYPE_INT,
                    'size'=>10
                ]
            ],256
        );
    }

    public function addProcess(string $processName,string $processClass,$async = true,array $args = []):bool
    {
        if(ServerManager::getInstance()->isStart()){
            trigger_error('you can not add a process after server start');
            return false;
        }
        $key = md5($processName);
        if(!isset($this->processList[$key])){
            try{
                $process = new $processClass($processName,$async,$args);
                $this->processList[$key] = $process;
                return true;
            }catch (\Throwable $throwable){
                trigger_error($throwable->getMessage().$throwable->getTraceAsString());
                return false;
            }
        }else{
            trigger_error('you can not add the same name process : '.$processName);
            return false;
        }
    }

    public function getProcessByName(string $processName):?AbstractProcess
    {
        $key = md5($processName);
        if(isset($this->processList[$key])){
            return $this->processList[$key];
        }else{
            return null;
        }
    }


    public function getProcessByPid(int $pid):?AbstractProcess
    {
        $table = TableManager::getInstance()->get('process_hash_map');
        foreach ($table as $key => $item){
            if($item['pid'] == $pid){
                return $this->processList[$key];
            }
        }
        return null;
    }


    public function setProcess(string $processName,AbstractProcess $process)
    {
        $key = md5($processName);
        $this->processList[$key] = $process;
    }

    public function reboot(string $processName):bool
    {
        $p = $this->getProcessByName($processName);
        if($p){
            \swoole_process::kill($p->getPid(),SIGTERM);
            return true;
        }else{
            return false;
        }
    }

    public function writeByProcessName(string $name,string $data):bool
    {
        $process = $this->getProcessByName($name);
        if($process){
            return (bool)$process->getProcess()->write($data);
        }else{
            return false;
        }
    }

    public function readByProcessName(string $name,float $timeOut = 0.1):?string
    {
        $process = $this->getProcessByName($name);
        if($process){
            $process = $process->getProcess();
            $read = array($process);
            $write = [];
            $error = [];
            $ret = swoole_select($read, $write,$error, $timeOut);
            if($ret){
                return $process->read(64 * 1024);
            }else{
                return null;
            }
        }else{
            return null;
        }
    }

}
```

## 异步任务投递

由于自定义进程的特殊性，不能直接调用Swoole的异步任务相关方法进行异步任务投递，框架已经封装好了相关的方法方便异步任务投递，请看下面的例子

```php
    public function run(Process $process)
    {
        // 直接投递闭包
        TaskManager::processAsync(function () {
            echo "process async task run on closure!\n";
        });

        // 投递任务类
        $taskClass = new TaskClass('task data');
        TaskManager::processAsync($taskClass);
    }
```

## 实例
我们以demo中的自定义进程例子来说明：
```
namespace App\Process;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use Swoole\Process;
class Test extends AbstractProcess
{
    //进程start的时候会执行的事件
    public function run(Process $process)
    {
        // TODO: Implement run() method.
        //添加进程内定时器
        $this->addTick(2000,function (){
            var_dump('this is '.$this->getProcessName().' process tick');
        });
    }
    //当进程关闭的时候会执行该事件
    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }
    //当有信息发给该进程的时候，会执行此进程
    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
        var_dump('process rec'.$str);
    }
}
```
以上代码[直达连接](https://github.com/easy-swoole/demo/blob/master/Application/Process/Test.php)，
至于如何使用（测试），请见demo中的EasySwooleEvent.php
