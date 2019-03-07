<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/1 0001
 * Time: 11:10
 */

namespace App\HttpController;


use App\Task\Test;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
    }

    function task()
    {
        \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async(function () {
            echo "执行异步任务1...\n";
            return true;
        }, function () {
            echo "异步任务执行完毕2...\n";
        });

        // 在定时器中投递的例子
        $a = \EasySwoole\Component\Timer::getInstance()->loop(1000, function () {
            \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async(function () {
                echo "执行异步任务3...\n";
            });
        });
        $this->response()->write('执行异步任务成功');
    }

    function templateTask(){
        // 实例化任务模板类 并将数据带进去 可以在任务类$taskData参数拿到数据
        $taskClass = new Test('taskData');
        \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async($taskClass);
        $this->response()->write('执行模板异步任务成功');
    }
    function quickTask(){
        $result =  \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async(\App\Task\QuickTaskTest::class);
        $this->response()->write('执行快速任务成功');
    }

    function multiTaskConcurrency(){
        // 多任务并发
        $tasks[] = function () { sleep(1);return 'this is 1'; }; // 任务1
        $tasks[] = function () { sleep(2);return 'this is 2'; };     // 任务2
        $tasks[] = function () { sleep(3);return 'this is 3'; }; // 任务3

        $results = \EasySwoole\EasySwoole\Swoole\Task\TaskManager::barrier($tasks, 3);

        var_dump($results);
        $this->response()->write('执行并发任务成功');

    }
}