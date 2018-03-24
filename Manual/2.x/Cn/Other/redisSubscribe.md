# 自定义进程实现redis订阅
## 实现代码
```
namespace App;


use EasySwoole\Core\Swoole\Process\AbstractProcess;
use Swoole\Process;

class Subscribe extends AbstractProcess
{

    public function run(Process $process)
    {
        // TODO: Implement run() method.
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis->subscribe(['ch1'],function (){
            var_dump(func_get_args());
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

接下来，需要做的事情，就是到EasySwooleEvent.php的主服务创建事件中，注册该进程即可。
```
use App\Subscribe;
use EasySwoole\Core\Swoole\Process\ProcessManager;

ProcessManager::getInstance()->addProcess('sub',Subscribe::class);
```