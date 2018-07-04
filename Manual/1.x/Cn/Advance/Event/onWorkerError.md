服务异常事件
------

```
function onWorkerError(\swoole_server $server,$worker_id,$worker_pid,$exit_code);
```

当worker/task_worker进程发生异常后会在Manager进程内回调此函数

- $worker_id是异常进程的编号
- $worker_pid是异常进程的ID
- $exit_code退出的状态码，范围是 1 ～255
- 此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。
- 通过记录日志或者发送报警的信息来提示开发者进行相应的处理。
