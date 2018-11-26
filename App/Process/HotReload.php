<?php
/**
 * Created by PhpStorm.
 * User: evalor
 * Date: 2018-11-26
 * Time: 23:18
 */

namespace App\Process;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Process\AbstractProcess;
use EasySwoole\Utility\File;
use Swoole\Process;
use Swoole\Table;
use Swoole\Timer;

/**
 * 暴力热重载
 * Class HotReload
 * @package App\Process
 */
class HotReload extends AbstractProcess
{
    /** @var \swoole_table $table */
    protected $table;
    protected $isReady = false;

    /**
     * 启动定时器进行循环扫描
     * @param Process $process
     */
    public function run(Process $process)
    {
        $this->table = new Table(2048);
        $this->table->column('mtime', Table::TYPE_INT, 4);
        $this->table->create();
        $this->runComparison();
        Timer::tick(1000, function () {
            $this->runComparison();
        });
    }

    /**
     * 扫描文件变更
     */
    private function runComparison()
    {
        $startTime = microtime(true);
        $doReload = false;
        $files = File::scanDirectory(EASYSWOOLE_ROOT . '/App');
        if (isset($files['files'])) {
            foreach ($files['files'] as $file) {
                $currentTime = filemtime($file);
                $inode = fileinode($file);
                if (!$this->table->exist($inode)) {
                    $doReload = true;
                    $this->table->set($inode, ['mtime' => $currentTime]);
                } else {
                    $oldTime = $this->table->get($inode)['mtime'];
                    if ($oldTime != $currentTime) {
                        $doReload = true;
                    }
                    $this->table->set($inode, ['mtime' => $currentTime]);
                }
            }
        }
        if ($doReload) {
            $count = $this->table->count();
            $time = date('Y-m-d H:i:s');
            $usage = round(microtime(true) - $startTime, 3);
            if (!$this->isReady == false) {
                echo "severReload at {$time} use : {$usage} s total: {$count} files\n";
                ServerManager::getInstance()->getSwooleServer()->reload();
            } else {
                echo "hot reload ready at {$time} use : {$usage} s total: {$count} files\n";
                $this->isReady = true;
            }
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