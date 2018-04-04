# 如何实现队列消费/自定义进程
可能我们会经常遇见需要不断消费队列内内容的场景，我们以EasySwoole中自定义进程的方式，来实现这一功能。
## 实现代码
### 定义消费进程逻辑
```
namespace App;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use Swoole\Process;

class Consumer extends AbstractProcess
{
    private $isRun = false;
    public function run(Process $process)
    {
        // TODO: Implement run() method.
        /*
         * 举例，消费redis中的队列数据
         * 定时500ms检测有没有任务，有的话就while死循环执行
         */
        $this->addTick(500,function (){
            if(!$this->isRun){
                $this->isRun = true;
                $redis = new \redis();//此处为伪代码，请自己建立连接或者维护redis连接
                while (true){
                    try{
                        $task = $redis->lPop('task_list');
                        if($task){
                            // do you task
                        }else{
                            break;
                        }
                    }catch (\Throwable $throwable){
                        break;
                    }
                }
                $this->isRun = false;
            }
            var_dump($this->getProcessName().' task run check');
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
    }
}
```

### 注册消费进程
在EasySwoole的全局事件中，注册消费进程。
```
use App\Consumer;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use \EasySwoole\Core\Swoole\ServerManager;

public function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // TODO: Implement mainServerCreate() method.
    $allNum = 3;
    for ($i = 0 ;$i < $allNum;$i++){
        ProcessManager::getInstance()->addProcess("consumer_{$i}",Consumer::class);
    }
}
```


> 爬虫例子：https://github.com/easy-swoole/spider