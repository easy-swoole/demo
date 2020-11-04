<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace EasySwoole\EasySwoole;

use EasySwoole\Component\Timer;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\Task\TaskManager;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        $register->add($register::onWorkerStart, function (\Swoole\Server $server, int $workerId) {
            // 在定时器中使用task
            Timer::getInstance()->loop(1000, function () use ($workerId) {
                TaskManager::getInstance()->sync(function () use ($workerId) {
                    var_dump('sync---' . $workerId . PHP_EOL);
                });
            });
        });
    }
}
