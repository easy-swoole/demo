# 异步任务

> 参考DEMO：[异步任务处理](https://github.com/HeKunTong/easyswoole3_demo/blob/master/App/HttpController/Index.php)

> 异步任务管理器类：EasySwoole\EasySwoole\Swoole\Task\TaskManager

在服务启动后的任意一个地方，都可以进行异步任务的投递，为了简化异步任务的投递，框架封装了任务管理器，用于投递同步/异步任务，投递任务有两种方式，一是直接投递闭包，二是投递任务模板类



## 直接投递闭包

任务比较简单的情况下可以直接投递闭包，任意地方包括控制器/定时器/服务启动后的各种回调中均可进行投递

```php
// 在控制器中投递的例子
function index()
{
    \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async(function () {
        echo "执行异步任务...\n";
        return true;
    }, function () {
        echo "异步任务执行完毕...\n";
    });
}

// 在定时器中投递的例子
\EasySwoole\EasySwoole\Swoole\Time\Timer::loop(1000, function () {
    \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async(function () {
        echo "执行异步任务...\n";
    });
});
```



## 投递任务模板类

当任务比较复杂，逻辑较多而且固定时，可以预先创建任务模板，并直接投递任务模板，以简化操作和方便在多个不同的地方投递相同的任务，首先需要创建一个任务模板

> 异步任务模板类：EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask

```php
class Task extends \EasySwoole\EasySwoole\Swoole\Task\AbstractAsyncTask
{

    /**
     * 执行任务的内容
     * @param mixed $taskData     任务数据
     * @param int   $taskId       执行任务的task编号
     * @param int   $fromWorkerId 派发任务的worker进程号
     * @author : evalor <master@evalor.cn>
     */
    function run($taskData, $taskId, $fromWorkerId)
    {
        // 需要注意的是task编号并不是绝对唯一
        // 每个worker进程的编号都是从0开始
        // 所以 $fromWorkerId + $taskId 才是绝度唯一的编号
        // !!! 任务完成需要 return 结果
    }

    /**
     * 任务执行完的回调
     * @param mixed $result  任务执行完成返回的结果
     * @param int   $task_id 执行任务的task编号
     * @author : evalor <master@evalor.cn>
     */
    function finish($result, $task_id)
    {
        // 任务执行完的处理
    }
}
```

然后同上例，一样可以在服务启动后的任何地方进行投递，只是将闭包换成任务模板类的实例进行投递

```php
// 在控制器中投递的例子
function index()
{
    // 实例化任务模板类 并将数据带进去 可以在任务类$taskData参数拿到数据
  	$taskClass = new Task('taskData');
    \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async($taskClass);
}

// 在定时器中投递的例子
\EasySwoole\EasySwoole\Swoole\Time\Timer::loop(1000, function () {
    \EasySwoole\EasySwoole\Swoole\Task\TaskManager::async($taskClass);
});
```

## 自定义进程投递

由于自定义进程的特殊性，不能直接调用Swoole的异步任务相关方法进行异步任务投递，框架已经封装好了相关的方法方便异步任务投递，请看下面的例子

```php
    public function run(Process $process)
    {
        // 直接投递闭包
        TaskManager::processAsync(function () {
            echo "process async task run on closure!\n";
        });

        // 投递任务类
        $taskClass = new TaskClass('task data');
        TaskManager::processAsync($taskClass);
    }
```

## 任务并发执行

有时需要同时执行多个异步任务，最典型的例子是数据采集，采集完多个数据后集中进行处理，这时可以进行并发任务投递，底层会将任务逐个进行投递并执行，所有任务执行完后返回一个结果集

```php
$tasks[] = function () { sleep(50000); }; // 任务1
$tasks[] = function () { sleep(2); };     // 任务2
$tasks[] = function () { sleep(50000); }; // 任务3

$results = \EasySwoole\EasySwoole\Swoole\Task\TaskManager::barrier($tasks, 0.5);
```

> 注意：Barrier为阻塞等待执行，所有的任务会被分发到不同Task进程同步执行， 直到所有的任务执行结束或超时才返回全部结果，默认的任务超时为0.5秒，所以只有任务1和任务3能成功执行并返回结果

## 类函数参考

```php
/**
 * 投递一个异步任务
 * @param mixed $task           需要投递的异步任务
 * @param mixed $finishCallback 任务执行完后的回调函数
 * @param int   $taskWorkerId   指定投递的Task进程编号 (默认随机投递给空闲进程)
 * @return bool 投递成功 返回整数 $task_id 投递失败 返回 false
 */
static function async($task,$finishCallback = null,$taskWorkerId = -1)
```

```php
/**
 * 投递一个同步任务
 * @param mixed $task         需要投递的异步任务
 * @param float $timeout      任务超时时间
 * @param int   $taskWorkerId 指定投递的Task进程编号 (默认随机投递给空闲进程)
 * @return bool|string 投递成功 返回整数 $task_id 投递失败 返回 false
 */
static function sync($task, $timeout = 0.5, $taskWorkerId = -1)
```

```php
/**
 * 并发执行多个任务
 * @param array $taskList 需要执行的任务列表
 * @param float $timeout  任务执行超时
 * @return array|bool 每个任务的执行结果
 */
static function barrier(array $taskList, $timeout = 0.5)
```

