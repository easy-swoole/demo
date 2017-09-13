# 异步进程
EasySwoole支持在定时器、控制器处理中等多处位置使用异步进程。
Core\Swoole\AsyncTaskManager是对Swoole Task的封装实现。
## AbstractAsyncTask
Core\AbstractInterface\AbstractAsyncTask 定义了异步任务的接口实现，一个异步任务对象都应当基础AbstractAsyncTask。
```
class Task extends AbstractAsyncTask{
    function handler(\swoole_http_server $server, $taskId, $fromId)
    {
        // TODO: Implement handler() method.
        for($i=0;$i<=5;$i++){
            sleep(1);
        }
        Logger::getInstance()->log('task finish');   
    }

    function finishCallBack(\swoole_http_server $server, $task_id, $resultData)
    {
        // TODO: Implement finishCallBack() method.
    }

}
```
## 添加一个异步任务
```
 //Core\Swoole\AsyncTaskManager
 AsyncTaskManager::getInstance()->add(f
    unction (){
         sleep(1);
         Logger::getInstance()->console("async task run");
    }
 );
 //或者
 AsyncTaskManager::getInstance()->add(Task::class);
```
## 多个任务并发执行
Core\Component\Barrier 是对SWoole taskWaitMulti的封装实现。
```
   $barrier = new Barrier();
   $barrier->add("a",function (){
            usleep(50000);
            return time();
        }
   );
   $barrier->add("b",function (){
            sleep(2);
            return time();
        }
   );
   $barrier->add("c",function (){
            usleep(50000);
            return time();
        }
   );
   $result = $barrier->run(1);
```
> 注意：Barrier为阻塞等待执行，所有的任务会被分发到不同Task进程同步执行，
直到所有的任务执行结束或超时才返回全部结果。以上代码中，限制了三个任务的最大执行时间为1秒，
仅有a、b两个任务能够被执行并返回结果。