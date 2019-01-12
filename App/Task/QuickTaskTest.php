<?php
namespace App\Task;
use EasySwoole\EasySwoole\Swoole\Task\QuickTaskInterface;

class QuickTaskTest implements QuickTaskInterface
{
    static function run(\swoole_server $server,int $taskId,int $fromWorkerId,$flags = null)
    {
        echo "快速任务模板";

        // TODO: Implement run() method.
    }
}