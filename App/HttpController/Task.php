<?php declare(strict_types=1);
/**
 * This file is part of EasySwoole
 * @link     https://github.com/easy-swoole
 * @document https://www.easyswoole.com
 * @license https://github.com/easy-swoole/easyswoole/blob/3.x/LICENSE
 */

namespace App\HttpController;

use App\Task\TestTask;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\Controller;

class Task extends Controller
{
    public function index()
    {
        $task = TaskManager::getInstance();
        $task->async(function () {
            echo "异步调用task1\n";
        });
        $data = $task->sync(function () {
            echo "同步调用task1\n";
            return "可以返回调用结果\n";
        });

        // async 可注册成功回调
        $task->async(function () {
            echo 'async-----';
            return 'async success';
        }, function ($reply, $taskId, $workerIndex) {
            // reply 是执行结果
            var_dump($reply);
        });

        $this->writeJson(200, $data);
    }

    public function template()
    {
        $data = TaskManager::getInstance()->sync(new TestTask(['name' => 'easyswoole-one']));
        TaskManager::getInstance()->async(new TestTask(['name' => 'easyswoole-two']));
        $this->writeJson(200, $data);
    }

    public function status()
    {
        $this->writeJson(200, TaskManager::getInstance()->status());
    }
}
