<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/10/24 0024
 * Time: 15:58
 */

namespace App\Task;

use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class TaskTest extends AbstractAsyncTask
{
    function run($taskData, $taskId, $fromWorkerId)
    {
        return 1;
        // TODO: Implement run() method.
    }

    function finish($result, $task_id)
    {
        echo "异步任务完成\n";
        file_get_contents('http://x.cn/Trace');
        // TODO: Implement finish() method.
    }


}