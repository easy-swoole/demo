<?php
/**
 * Created by PhpStorm.
 * User: Apple
 * Date: 2018/11/7 0007
 * Time: 16:24
 */

namespace App\HttpController;


use App\Task\QuickTaskTest;
use App\Task\TaskTest;
use EasySwoole\EasySwoole\Console\TcpService;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Spl\SplBean;
use Swoole\Process;

class Task extends Controller
{
    protected $a=1;
    static $b;
    function index()
    {
        $this->response()->write("task");
//        $result = TaskManager::async(function (){
//            echo "执行task异步任务(回调函数)\n";
//        });
//        var_dump($result);
        $result = TaskManager::async(new TaskTest());
        $result = TaskManager::async(QuickTaskTest::class);
        var_dump($result);
//        $result = TaskManager::sync(function (){
//            echo "执行task同步任务(回调函数)\n";
//        });
//        var_dump($result);
//        $result = TaskManager::sync(new TaskTest());
//        var_dump($result);


        // TODO: Implement index() method.
    }

    function test(){

    }

}