<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/10/24 0024
 * Time: 15:58
 */

namespace App\Task;

use App\Model\User\UserModelWithDb;
use EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask;

class TaskTest extends AbstractAsyncTask
{
    protected function run($taskData,$taskId,$fromWorkerId,$flags = null)
    {
        echo "执行task模板任务\n";
        // TODO: Implement run() method.
    }

    function finish($result, $task_id)
    {
        echo "task模板任务完成\n";
        return 1;
        // TODO: Implement finish() method.
    }


}