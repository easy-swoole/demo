<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;
use co;
use Swoole\Process;
use Throwable;

class ProcessOne extends AbstractProcess
{
    public function run($arg)
    {
        // TODO: Implement run() method.
        Logger::getInstance()->console($this->getProcessName().' start');
        while (1) {
            co::sleep(5);
            Logger::getInstance()->console($this->getProcessName().' run');
        }
    }

    public function onShutDown()
    {
        // 进程退出 可以做一些清理工作
    }

    public function onPipeReadable(Process $process)
    {
        var_dump($process->read());
    }

    protected function onException(Throwable $throwable, ...$args)
    {
        // 进程内异常捕获
    }
}
