<?php
/**
 * Created by PhpStorm.
 * User: eValor
 * Date: 2018-11-27
 * Time: 19:21
 */

namespace App\Process;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use EasySwoole\Utility\File;
use Swoole\Process;

class InotifyHotReload extends AbstractProcess
{
    // 请确保系统扩展有inotify 否则无法使用
    public function run(Process $process)
    {
        if (extension_loaded('inotify')) {

            // 因为进程独立 且当前是自定义进程 全局变量只有该进程使用
            // 在确定不会造成污染的情况下 也可以合理使用全局变量

            global $lastReloadTime;
            global $inotifyResource;

            $lastReloadTime = 0;
            $files = File::scanDirectory(EASYSWOOLE_ROOT . '/App');
            $files = array_merge($files['files'], $files['dirs']);

            $inotifyResource = inotify_init();

            // 为当前所有的目录和文件添加事件监听
            foreach ($files as $item) {
                inotify_add_watch($inotifyResource, $item, IN_CREATE | IN_DELETE | IN_MODIFY);
            }

            // 加入事件循环
            swoole_event_add($inotifyResource, function () {
                global $lastReloadTime;
                global $inotifyResource;
                $events = inotify_read($inotifyResource);
                if ($lastReloadTime < time() && !empty($events)) { // 限制1s内不能进行重复reload
                    $lastReloadTime = time();
                    ServerManager::getInstance()->getSwooleServer()->reload();
                }
            });
        } else {
            echo "hot reload is unavailable because ext-inotify was not loaded";
        }
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
    }
}