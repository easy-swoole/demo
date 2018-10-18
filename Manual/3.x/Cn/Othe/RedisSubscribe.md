
# 自定义进程实现redis订阅
## 实现代码
```
<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/10/18 0018
 * Time: 10:28
 */

namespace App\Process;


use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use Swoole\Process;

class Subscribe extends AbstractProcess
{
    public function run(Process $process)
    {
        // TODO: Implement run() method.
        $redis = new \Redis();//此处为伪代码，请自己建立连接或者维护
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
use App\Process;
use EasySwoole\Core\Swoole\Process\ProcessManager;

ServerManager::getInstance()->getSwooleServer()->addProcess((new Subscribe('sub'))->getProcess());
```