## 自定义命令  
console组件封装自己的自定义命令,demo地址:https://github.com/easy-swoole/demo/tree/3.x/App/Utility/ConsoleCommand  


### 代码示例
我们需要继承\EasySwoole\EasySwoole\Console\CommandInterface接口:

```php
<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2018/11/7 0007
 * Time: 16:14
 */

namespace App\Utility\ConsoleCommand;


use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class Test implements \EasySwoole\EasySwoole\Console\CommandInterface
{
    public function exec(Caller $caller, Response $response)
    {
        //调用命令时,会执行该方法
        $args = $caller->getArgs();//获取命令后面的参数
        $response->setMessage("你调用的命令参数为:".json_encode($args));
        // TODO: Implement exec() method.
    }

    public function help(Caller $caller, Response $response)
    {
        //调用 help Test时,会调用该方法
        $help = <<<HELP

用法 : Test [arg...]

参数 : 
  arg 
 
HELP;

        return $help;

        // TODO: Implement help() method.
    }

}
```

在EasySwooleEvent.php的initialize事件中进行注册:
```
 \EasySwoole\EasySwoole\Console\CommandContainer::getInstance()->set('Test',new Test());
```

使用php easyswoole console打开控制台,输入:Test tioncico,结果
```
 php easyswoole console
  ______                          _____                              _
 |  ____|                        / ____|                            | |
 | |__      __ _   ___   _   _  | (___   __      __   ___     ___   | |   ___
 |  __|    / _` | / __| | | | |  \___ \  \ \ /\ / /  / _ \   / _ \  | |  / _ \
 | |____  | (_| | \__ \ | |_| |  ____) |  \ V  V /  | (_) | | (_) | | | |  __/
 |______|  \__,_| |___/  \__, | |_____/    \_/\_/    \___/   \___/  |_|  \___|
                          __/ |
                         |___/
connect to tcp://127.0.0.1:9000 succeed 
Hello !EasySwoole
Test tioncico
你调用的命令为:["tioncico"]
```

### 在控制器中如何发送消息给console控制台  

```
if (\EasySwoole\EasySwoole\Config::getInstance()->getDynamicConf('CONSOLE.PUSH_LOG')) {
    \EasySwoole\EasySwoole\Console\TcpService::push('主动推送给控制台');
}
```

