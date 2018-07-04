执行任务事件
------

```
function onTask(\swoole_server $server, $taskId, $workerId,$callBackObj);
```

在`task_worker`进程内被调用，可以用以下方法向task_worker进程投递新的任务

```
AsyncTaskManager::getInstance()->add(Runner::class);
```
当前的Task进程在调用onTask回调函数时会将进程状态切换为忙碌，这时将不再接收新的Task，当onTask函数返回时会将进程状态切换为空闲然后继续接收新的Task

> 在onTask函数中 return字符串，表示将此内容返回给worker进程。worker进程中会触发onFinish函数，表示投递的task已完成。

