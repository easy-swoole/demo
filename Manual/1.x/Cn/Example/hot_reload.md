服务热重启
------

在开发过程中经常需要更新文件，由于Swoole常驻内存的特性，文件在框架启动时已经载入了内存，当文件被修改时需要手动重启服务

可以将以下代码添加到`Event.php`的`onWorkerStart`事件中，实现文件更新后自动reload服务，请确定安装了inotify拓展

> 提醒: 在生产模式上线前一定要注意移除热重启，否则可能会造成不可预估的错误和异常

```
//请确定有inotify拓展
if ($workerId == 0) {
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

# Mac下调试方式
可以用brew安装fswatch去监听文件修改，利用php server reload命令来热更新代码。
如：
```
fswatch -o /Volumes/dev/www/项目/App | xargs -n1 ~/script/脚本.sh
```
只要项目下的App里的文件修改了就会自动运行`脚本.sh`。脚本内容如下：
```
cd /Volumes/dev/www/项目 && php server stop && php server start --d
echo "文件夹变动，已经执行 php server reload"
```
由于Mac下无法使用php的inotify.so，所以可以用以上介绍方法配合调试。

