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

# Linux下
可以在`Event.php`里加入以下代码来热更新：
```
function onWorkerStart(\swoole_server $server, $workerId) {
	
	// 系统必须不是mac或者windows
	if (PHP_OS != 'Darwin' && !strpos(PHP_OS, 'WIN') && $workerId == 0) {

		// 递归获取所有目录和文件
		$a = function ($dir) use (&$a) {
			$data = array();
			if (is_dir($dir)) {
				//是目录的话，先增当前目录进去
				$data[] = $dir;
				$files  = array_diff(scandir($dir), array('.', '..'));
				foreach ($files as $file) {
					$data = array_merge($data, $a($dir . "/" . $file));
				}
			} else {
				$data[] = $dir;
			}
			return $data;
		};
		$list   = $a(ROOT . "/App");
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

