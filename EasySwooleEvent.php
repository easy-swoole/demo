<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace EasySwoole\EasySwoole;

use App\Process\ProcessOne;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('processOne'); // 进程名称
        $processConfig->setArg(['a','b','c']);
        $processConfig->setProcessGroup('processGroup'); // 进程组名称 可以不设置
        $processConfig->setEnableCoroutine(true); // 自定义进程开启coroutine
        $myProcess = new ProcessOne($processConfig);
        ServerManager::getInstance()->getSwooleServer()->addProcess($myProcess->getProcess());

        // 注入di 方便其它进程往此进程通过管道写入数据
        Di::getInstance()->set('processOne', $myProcess->getProcess());
    }
}
