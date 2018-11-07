<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/1 0001
 * Time: 11:30
 */

namespace App\Process;


use App\Task\TaskTest;
use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use Swoole\Process;

class ProcessTaskTest extends AbstractProcess
{
    public function run(Process $process)
    {
        $result = TaskManager::processAsync(new TaskTest());
        var_dump($result);
        echo "task_test_process is run.\n";

        // TODO: Implement run() method.
    }

    public function onShutDown()
    {
        echo "task_test_process is onShutDown.\n";
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        echo "task_test_process is onReceive.\n";
        // TODO: Implement onReceive() method.
    }

}