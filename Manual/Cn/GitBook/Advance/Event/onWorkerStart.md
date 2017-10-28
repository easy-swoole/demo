服务启动事件
------

```
function onWorkerStart(\swoole_server $server,$workerId);
```

此事件在`Worker`进程/`Task`进程启动时发生。这里创建的对象可以在进程生命周期内使用，需要注意的是

- Task进程也会触发此事件
- 发生致命错误或者代码中主动调用`exit`时，`Worker`/`Task`进程会退出，管理进程会重新创建新的进程，也会触发本事件
- `onWorkerStart`/`onStart`是并发执行的，没有先后顺序
- 事件回调带有`$server`参数，可以通过`$server->taskworker`来判断当前是`Worker`进程还是`Task`进程

> 注意: $workerId是一个从0-$worker_num之间的数字，表示这个Worker进程的ID，$workerId和进程PID没有任何关系

可以在此事件中将自定义的逻辑添加到EventLoop以及向Task投递任务

下面的示例利用`inotify`拓展实现当文件被修改时，自动Reload服务

```
//请确定有inotify拓展
if ($workerid == 0) {
    // 递归获取所有目录和文件
    $a = function ($dir) use (&$a) {
        $data = array();
        if (is_dir($dir)) {
            //是目录的话，先增当前目录进去
            $data[] = $dir;
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                $data = array_merge($data, $a($dir . "/" . $file));
            }
        } else {
            $data[] = $dir;
        }
        return $data;
    };
    $list = $a(ROOT . "/App");
    $notify = inotify_init();
    // 为所有目录和文件添加inotify监视
    foreach ($list as $item) {
        inotify_add_watch($notify, $item, IN_CREATE | IN_DELETE | IN_MODIFY);
    }
    // 加入EventLoop
    swoole_event_add($notify, function () use ($notify) {
        $events = inotify_read($notify);
        if (!empty($events)) {
            //注意更新多个文件的间隔时间处理,防止一次更新了10个文件，重启了10次，懒得做了，反正原理在这里
            Server::getInstance()->getServer()->reload();
        }
    });
}
```
      
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4c8d895ff3b25bddb6fa4185c8651cc3";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<script>
(function(){
    var bp = document.createElement('script');
    var curProtocol = window.location.protocol.split(':')[0];
    if (curProtocol === 'https') {
        bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';        
    }
    else {
        bp.src = 'http://push.zhanzhang.baidu.com/push.js';
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();
</script>